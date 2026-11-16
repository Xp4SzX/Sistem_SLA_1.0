<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DinasAdmin;
use App\Models\Service;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ServiceDocumentController extends Controller
{
    private const WORD_NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    /** Generate a DOCX from the original SK template, changing only populated fields. */
    public function download(Service $service): BinaryFileResponse|Response
    {
        $user = request()->user();
        $canDownload = $user?->role === 'admin'
            || ($user?->role === 'admin_dinas' && (int) $user->department_id === (int) $service->department_id);
        abort_unless($canDownload, 403);

        return $this->generateDocument($service);
    }

    public function downloadForDepartment(Service $service): BinaryFileResponse|Response
    {
        $user = request()->user();
        abort_unless($user?->role === 'admin_dinas', 403);

        $dinasAdmin = DinasAdmin::where('email', $user->email)->first();
        $departmentName = $dinasAdmin?->nama_dinas ?? ($user->name ?? 'Pemerintah Daerah');
        abort_unless(
            $departmentName !== null
                && mb_strtolower(trim($departmentName)) === mb_strtolower(trim($service->department_name)),
            403
        );

        return $this->generateDocument($service, true);
    }

    private function generateDocument(Service $service, bool $singleService = false): BinaryFileResponse|Response
    {
        $template = resource_path('templates/sk-penetapan-standar-pelayanan-2026.docx');
        abort_unless(is_file($template), 404, 'Template SK tidak ditemukan.');

        $workingDirectory = storage_path('app/tmp/service-documents');
        if (! is_dir($workingDirectory) && ! mkdir($workingDirectory, 0775, true) && ! is_dir($workingDirectory)) {
            throw new RuntimeException('Folder dokumen sementara tidak dapat dibuat.');
        }

        $token = (string) Str::uuid();
        $docxPath = $workingDirectory.DIRECTORY_SEPARATOR.$token.'.docx';
        $downloadReady = false;

        try {
            $this->fillTemplate($template, $docxPath, $service, $singleService);

            if (! is_file($docxPath) || filesize($docxPath) === 0) {
                throw new RuntimeException('File Word (.docx) gagal digenerate atau kosong.');
            }

            $this->validateDocx($docxPath);

            $response = response()->download(
                $docxPath,
                'SK-Standar-Pelayanan-'.Str::slug($service->name).'.docx',
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
            )->deleteFileAfterSend(true);
            $downloadReady = true;

            return $response;
        } finally {
            if (! $downloadReady && is_file($docxPath)) {
                @unlink($docxPath);
            }
        }
    }

    private function validateDocx(string $path): void
    {
        if (! is_readable($path)) {
            throw new RuntimeException('File Word (.docx) tidak dapat dibaca oleh proses aplikasi.');
        }

        $zip = new ZipArchive;
        $status = $zip->open($path, ZipArchive::CHECKCONS);
        if ($status !== true) {
            throw new RuntimeException('Paket ZIP file Word (.docx) tidak valid (kode: '.$status.').');
        }

        try {
            $xml = $zip->getFromName('word/document.xml');
            if ($xml === false) {
                throw new RuntimeException('Isi dokumen Word tidak ditemukan di dalam file .docx.');
            }

            $document = new DOMDocument;
            if (! $document->loadXML($xml, LIBXML_NONET)) {
                throw new RuntimeException('Struktur XML dokumen Word yang dihasilkan tidak valid.');
            }
        } finally {
            $closed = $zip->close();
        }

        if (! $closed) {
            throw new RuntimeException('Paket file Word (.docx) gagal disimpan dengan benar.');
        }
    }

    private function fillTemplate(string $template, string $destination, Service $service, bool $singleService = false): void
    {
        if (! copy($template, $destination)) {
            throw new RuntimeException('Template SK tidak dapat disalin.');
        }

        $zip = new ZipArchive;
        if ($zip->open($destination) !== true) {
            throw new RuntimeException('Template SK tidak dapat dibaca.');
        }

        try {
            $xml = $zip->getFromName('word/document.xml');
            if ($xml === false) {
                throw new RuntimeException('Isi dokumen SK tidak ditemukan.');
            }

            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = true;
            if (! $dom->loadXML($xml, LIBXML_NONET)) {
                throw new RuntimeException('Struktur dokumen SK tidak valid.');
            }

            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('w', self::WORD_NS);
            $body = $xpath->query('/w:document/w:body')->item(0);
            if (! $body instanceof DOMElement) {
                throw new RuntimeException('Isi utama dokumen SK tidak ditemukan.');
            }

            $fields = $this->serviceFields($service);
            $serviceTables = [];
            $precedingHeading = null;
            foreach ($body->childNodes as $node) {
                if (! $node instanceof DOMElement) {
                    continue;
                }

                if ($node->localName === 'p') {
                    $paragraphText = trim($this->nodeText($xpath, $node));
                    if ($singleService && str_contains($this->normalize($paragraphText), 'terdiridari4layanan')) {
                        $this->replaceContainerText($xpath, $node, 'Standar Pelayanan yang ditetapkan adalah sebagai berikut:');
                    }
                    if ($paragraphText !== '') {
                        $precedingHeading = $node;
                    }

                    continue;
                }

                if ($node->localName !== 'tbl') {
                    continue;
                }

                if ($this->isConsiderationTable($xpath, $node)) {
                    $this->fillConsiderationTable($xpath, $node, $fields);
                } elseif ($this->isServiceTable($xpath, $node)) {
                    $serviceTables[] = ['table' => $node, 'heading' => $precedingHeading];
                }
            }

            $match = $this->findMatchingServiceTable($xpath, $serviceTables, $service, $singleService);
            if ($match !== null) {
                if ($singleService) {
                    foreach ($serviceTables as $index => $candidate) {
                        if ($index === $match) {
                            continue;
                        }
                        if ($candidate['heading'] instanceof DOMElement) {
                            $candidate['heading']->parentNode?->removeChild($candidate['heading']);
                        }
                        $candidate['table']->parentNode?->removeChild($candidate['table']);
                    }

                    $this->removeServiceSummary($xpath, $body, $service->name);
                    $this->fillDecisionServiceList($xpath, $body, $service->name);
                }

                $selected = $serviceTables[$match];
                $this->fillServiceTable($xpath, $selected['table'], $fields);
                if ($selected['heading'] instanceof DOMElement) {
                    $this->replaceContainerText($xpath, $selected['heading'], $service->name);
                }
            }

            if ($singleService) {
                $this->replaceDocumentDate($xpath, $body);
            }

            $zip->deleteName('word/document.xml');
            if (! $zip->addFromString('word/document.xml', $dom->saveXML())) {
                throw new RuntimeException('Dokumen SK yang telah diisi tidak dapat disimpan.');
            }
        } finally {
            $zip->close();
        }
    }

    private function removeServiceSummary(DOMXPath $xpath, DOMElement $body, string $serviceName): void
    {
        $serviceNames = [
            'pengajuanpembangunanataupengembanganaplikasi',
            'layananhosting',
            'layanansubdomain',
            'pelayananinformasipublik',
        ];
        $summaryNodes = [];
        $inServiceList = false;
        $keptService = false;

        foreach ($body->childNodes as $node) {
            if (! $node instanceof DOMElement || $node->localName !== 'p') {
                continue;
            }

            $text = $this->normalize($this->nodeText($xpath, $node));
            if (! $inServiceList && str_contains($text, 'adalahsebagaiberikut')) {
                $inServiceList = true;

                continue;
            }

            if (! $inServiceList) {
                continue;
            }

            if ($text === 'standarpelayanan') {
                break;
            }

            if ($text === '') {
                $summaryNodes[] = $node;

                continue;
            }

            if (in_array($text, $serviceNames, true)) {
                if (! $keptService) {
                    $this->replaceContainerText($xpath, $node, $serviceName);
                    $keptService = true;
                } else {
                    $summaryNodes[] = $node;
                }

                continue;
            }

            break;
        }

        foreach ($summaryNodes as $node) {
            $body->removeChild($node);
        }
    }

    private function fillDecisionServiceList(DOMXPath $xpath, DOMElement $body, string $serviceName): void
    {
        foreach ($xpath->query('./w:tbl/w:tr', $body) as $row) {
            $cells = $xpath->query('./w:tc', $row);
            if ($cells->length < 3 || $this->normalize($this->nodeText($xpath, $cells->item(0))) !== 'kedua') {
                continue;
            }

            $cell = $cells->item(2);
            $paragraphs = $xpath->query('./w:p', $cell);
            if ($paragraphs->length < 2) {
                return;
            }

            $this->replaceContainerText($xpath, $paragraphs->item(1), $serviceName);
            for ($index = $paragraphs->length - 1; $index > 1; $index--) {
                $paragraph = $paragraphs->item($index);
                $paragraph->parentNode?->removeChild($paragraph);
            }

            return;
        }
    }

    private function replaceDocumentDate(DOMXPath $xpath, DOMElement $body): void
    {
        $documentDate = now()->locale('id')->translatedFormat('j F Y');
        foreach ($xpath->query('.//w:t', $body) as $textNode) {
            $textNode->nodeValue = str_replace('25 Mei 2026', $documentDate, $textNode->nodeValue);
        }
    }

    /** @return array<string, string> */
    private function serviceFields(Service $service): array
    {
        return [
            'Menimbang' => (string) ($service->menimbang ?? ''),
            'Mengingat' => (string) ($service->mengingat ?? ''),
            'Dasar Hukum' => (string) ($service->dasar_hukum ?? ''),
            'Persyaratan Pelayanan' => (string) ($service->persyaratan ?? ''),
            'Sistem, Mekanisme, dan Prosedur' => (string) ($service->sistem_prosedur ?? ''),
            'Jangka Waktu Penyelesaian' => (string) ($service->jangka_waktu ?? ''),
            'Biaya/Tarif' => (string) ($service->biaya ?? ''),
            'Produk Pelayanan' => (string) ($service->produk_pelayanan ?? ''),
            'Sarana dan Prasarana dan/atau Fasilitas' => (string) ($service->sarana_prasarana ?? ''),
            'Kompetensi Pelaksana' => (string) ($service->kompetensi_pelaksana ?? ''),
            'Pengawasan Internal' => (string) ($service->pengawasan_internal ?? ''),
            'Penanganan, Pengaduan, Saran dan Masukan' => (string) ($service->penanganan_pengaduan ?? ''),
            'Jumlah Pelaksana' => (string) ($service->jumlah_pelaksana ?? ''),
            'Jaminan Pelayanan' => (string) ($service->jaminan_pelayanan ?? ''),
            'Jaminan Keamanan dan Keselamatan Pelayanan' => (string) ($service->jaminan_keamanan ?? ''),
            'Evaluasi Kinerja' => (string) ($service->evaluasi_kinerja ?? ''),
        ];
    }

    private function isConsiderationTable(DOMXPath $xpath, DOMElement $table): bool
    {
        $firstCell = $xpath->query('./w:tr[1]/w:tc[1]', $table)->item(0);
        $label = strtolower(trim($firstCell ? $this->nodeText($xpath, $firstCell) : ''));

        return in_array($label, ['menimbang', 'mengingat'], true);
    }

    private function isServiceTable(DOMXPath $xpath, DOMElement $table): bool
    {
        $headers = [];
        foreach ($xpath->query('./w:tr[1]/w:tc[position() <= 3]', $table) as $cell) {
            $headers[] = strtolower(trim($this->nodeText($xpath, $cell)));
        }

        return $headers === ['no', 'komponen', 'uraian'];
    }

    /** @param array<string, string> $fields */
    private function fillConsiderationTable(DOMXPath $xpath, DOMElement $table, array $fields): void
    {
        foreach (['Menimbang', 'Mengingat'] as $field) {
            $value = trim((string) ($fields[$field] ?? ''));
            if ($value === '') {
                continue;
            }

            $paragraphs = $this->considerationParagraphs($xpath, $table, $field);
            if ($paragraphs === []) {
                continue;
            }

            $lines = preg_split('/\r\n|\r|\n/', $value) ?: [$value];
            $cleaned = [];
            foreach ($lines as $line) {
                $trimmed = trim((string) $line);
                if ($trimmed === '') {
                    continue;
                }

                $cleaned[] = $this->wrapLongWords($this->stripListPrefix($trimmed));
            }

            if ($cleaned === []) {
                continue;
            }

            if (count($cleaned) > count($paragraphs)) {
                $lastParagraph = $paragraphs[array_key_last($paragraphs)];
                $lastRow = $lastParagraph['row'];

                for ($index = count($paragraphs); $index < count($cleaned); $index++) {
                    $newRow = $lastParagraph['row']->cloneNode(true);
                    if (! $newRow instanceof DOMElement) {
                        throw new RuntimeException('Baris Menimbang/Mengingat tidak dapat digandakan.');
                    }

                    $this->removeCopiedParagraphIds($xpath, $newRow);
                    $lastRow->parentNode?->insertBefore($newRow, $lastRow->nextSibling);
                    $cells = $xpath->query('./w:tc', $newRow);
                    if ($cells->length < 4) {
                        throw new RuntimeException('Struktur baris Menimbang/Mengingat tidak valid.');
                    }

                    $marker = $field === 'Menimbang'
                        ? $this->alphabeticMarker($index)
                        : ($index + 1).'.';
                    $this->replaceCellText($xpath, $cells->item(2), $marker);
                    $paragraphs[] = [
                        'row' => $newRow,
                        'cell' => $cells->item(3),
                    ];
                    $lastRow = $newRow;
                }
            }

            foreach ($paragraphs as $index => $paragraph) {
                if ($index >= count($cleaned)) {
                    $paragraph['row']->parentNode?->removeChild($paragraph['row']);

                    continue;
                }

                $line = $cleaned[$index];
                if ($index === count($paragraphs) - 1 && count($cleaned) > count($paragraphs)) {
                    $line = implode("\n", array_slice($cleaned, $index));
                }

                foreach ($xpath->query('./w:trPr/w:trHeight', $paragraph['row']) as $rowHeight) {
                    $rowHeight->parentNode?->removeChild($rowHeight);
                }

                $this->replaceCellText($xpath, $paragraph['cell'], $line);
            }
        }
    }

    private function alphabeticMarker(int $index): string
    {
        $number = $index + 1;
        $marker = '';
        while ($number > 0) {
            $number--;
            $marker = chr(97 + ($number % 26)).$marker;
            $number = intdiv($number, 26);
        }

        return $marker.'.';
    }

    private function removeCopiedParagraphIds(DOMXPath $xpath, DOMElement $row): void
    {
        $namespace = 'http://schemas.microsoft.com/office/word/2010/wordml';
        $xpath->registerNamespace('w14', $namespace);
        $row->removeAttributeNS($namespace, 'paraId');
        $row->removeAttributeNS($namespace, 'textId');

        foreach ($xpath->query('.//*[@w14:paraId or @w14:textId]', $row) as $element) {
            if ($element instanceof DOMElement) {
                $element->removeAttributeNS($namespace, 'paraId');
                $element->removeAttributeNS($namespace, 'textId');
            }
        }
    }

    private function stripListPrefix(string $value): string
    {
        $value = trim($value);

        return preg_replace('/^(?:[A-Za-z]|[0-9]+)[\)\.]\s*/u', '', $value) ?? $value;
    }

    private function wrapLongWords(string $value): string
    {
        $wrapped = preg_replace('/(\S{70})(?=\S)/u', "$1\n", $value);
        if ($wrapped === null) {
            throw new RuntimeException('Teks Menimbang/Mengingat tidak dapat diformat.');
        }

        return $wrapped;
    }

    /** @return list<array{row: DOMElement, cell: DOMElement}> */
    private function considerationParagraphs(DOMXPath $xpath, DOMElement $table, string $wantedField): array
    {
        $paragraphs = [];
        $currentField = null;
        foreach ($xpath->query('./w:tr', $table) as $row) {
            $cells = $xpath->query('./w:tc', $row);
            if ($cells->length < 4) {
                continue;
            }

            $label = strtolower(trim($this->nodeText($xpath, $cells->item(0))));
            if (in_array($label, ['menimbang', 'mengingat'], true)) {
                $currentField = ucfirst($label);
            }
            if ($currentField === $wantedField && $cells->item(3) instanceof DOMElement && $row instanceof DOMElement) {
                $paragraphs[] = [
                    'row' => $row,
                    'cell' => $cells->item(3),
                ];
            }
        }

        return $paragraphs;
    }

    /** @param array<string, string> $fields */
    private function fillServiceTable(DOMXPath $xpath, DOMElement $table, array $fields): void
    {
        foreach ($xpath->query('./w:tr[position() > 1]', $table) as $row) {
            $cells = $xpath->query('./w:tc', $row);
            if ($cells->length < 3) {
                continue;
            }

            $this->preventTableRowSplit($xpath, $row);

            $label = $this->normalize($this->nodeText($xpath, $cells->item(1)));
            $field = $this->fieldForLabel($label);
            if ($field !== null) {
                $value = (string) ($fields[$field] ?? '');
                $lines = preg_split('/\r\n|\r|\n/', $value) ?: [$value];
                $contentLines = [];
                foreach ($lines as $line) {
                    $trimmed = trim((string) $line);
                    if ($trimmed === '') {
                        continue;
                    }

                    $contentLines[] = $trimmed;
                }

                $this->replaceCellText($xpath, $cells->item(2), implode("\n", $contentLines), true);
            }
        }
    }

    private function preventTableRowSplit(DOMXPath $xpath, DOMElement $row): void
    {
        $rowProperties = $xpath->query('./w:trPr', $row)->item(0);
        if (! $rowProperties instanceof DOMElement) {
            $rowProperties = $row->ownerDocument->createElementNS(self::WORD_NS, 'w:trPr');
            $row->insertBefore($rowProperties, $row->firstChild);
        }

        if ($xpath->query('./w:cantSplit', $rowProperties)->length > 0) {
            return;
        }

        $cannotSplit = $row->ownerDocument->createElementNS(self::WORD_NS, 'w:cantSplit');
        $height = $xpath->query('./w:trHeight', $rowProperties)->item(0);
        if ($height !== null) {
            $rowProperties->insertBefore($cannotSplit, $height);

            return;
        }

        $rowProperties->appendChild($cannotSplit);
    }

    /** @param list<array{table: DOMElement, heading: ?DOMElement}> $serviceTables */
    private function findMatchingServiceTable(DOMXPath $xpath, array $serviceTables, Service $service, bool $singleService = false): ?int
    {
        $serviceName = $this->normalize($service->name);
        $serviceCode = $this->normalize($service->service_code ?? '');
        $serviceIdentity = $serviceName.$serviceCode;

        $isApplicationService = str_contains($serviceIdentity, 'aplikasi')
            || str_contains($serviceIdentity, 'pembangunan')
            || str_contains($serviceIdentity, 'pengembangan')
            || str_contains($serviceIdentity, 'sipd')
            || str_contains($serviceIdentity, 'monev')
            || str_starts_with($serviceCode, 'pemb');

        if (str_contains($serviceIdentity, 'hosting')) {
            $matchWords = ['hosting'];
        } elseif (str_contains($serviceIdentity, 'subdomain') || str_contains($serviceIdentity, 'domain') || str_starts_with($serviceCode, 'sub')) {
            $matchWords = ['subdomain'];
        } elseif ($singleService && $isApplicationService) {
            $matchWords = ['pengajuanpembangunan', 'pengembanganaplikasi'];
        } elseif (str_contains($serviceIdentity, 'informasi') || str_contains($serviceIdentity, 'publik')) {
            $matchWords = ['informasipublik'];
        } elseif ($isApplicationService) {
            $matchWords = ['pengajuanpembangunan', 'pengembanganaplikasi'];
        } else {
            $matchWords = [$serviceName];
        }

        foreach ($serviceTables as $index => $candidate) {
            $heading = $this->normalize($candidate['heading'] instanceof DOMElement
                ? $this->nodeText($xpath, $candidate['heading'])
                : '');
            foreach ($matchWords as $word) {
                if ($word !== '' && str_contains($heading, $word)) {
                    return $index;
                }
            }
        }

        return $serviceTables === [] ? null : 0;
    }

    private function fieldForLabel(string $label): ?string
    {
        $labels = [
            'dasarhukum' => 'Dasar Hukum',
            'persyaratanpelayanan' => 'Persyaratan Pelayanan',
            'sistemmekanismedanprosedur' => 'Sistem, Mekanisme, dan Prosedur',
            'jangkawaktupenyelesaian' => 'Jangka Waktu Penyelesaian',
            'jangkawaktupenyelesaiaan' => 'Jangka Waktu Penyelesaian',
            'biayatarif' => 'Biaya/Tarif',
            'produkpelayanan' => 'Produk Pelayanan',
            'saranadanprasaranadanataufasilitas' => 'Sarana dan Prasarana dan/atau Fasilitas',
            'kompetensipelaksana' => 'Kompetensi Pelaksana',
            'pengawasaninternal' => 'Pengawasan Internal',
            'penangananpengaduansarandanmasukan' => 'Penanganan, Pengaduan, Saran dan Masukan',
            'jumlahpelaksana' => 'Jumlah Pelaksana',
            'jaminanpelayanan' => 'Jaminan Pelayanan',
            'jaminankeamanandankeselamatanpelayanan' => 'Jaminan Keamanan dan Keselamatan Pelayanan',
            'evaluasikinerja' => 'Evaluasi Kinerja',
            'evauasikinerja' => 'Evaluasi Kinerja',
        ];

        return $labels[$label] ?? null;
    }

    private function replaceCellText(DOMXPath $xpath, DOMElement $container, string $value, bool $removeListFormatting = false): void
    {
        $document = $container->ownerDocument;
        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [''];

        $cellProperties = null;
        $styleNode = null;
        if ($container->localName === 'p') {
            $styleNode = $xpath->query('./w:pPr', $container)->item(0)?->cloneNode(true);
        } elseif ($container->localName === 'tc') {
            $cellProperties = $xpath->query('./w:tcPr', $container)->item(0)?->cloneNode(true);
            $firstParagraph = $xpath->query('./w:p', $container)->item(0);
            $styleNode = $firstParagraph ? $xpath->query('./w:pPr', $firstParagraph)->item(0)?->cloneNode(true) : null;
        }

        if ($removeListFormatting && $styleNode instanceof DOMElement) {
            foreach ($xpath->query('./w:numPr', $styleNode) as $numbering) {
                $numbering->parentNode?->removeChild($numbering);
            }

            foreach ($xpath->query('./w:pStyle[@w:val="ListParagraph"]', $styleNode) as $paragraphStyle) {
                $paragraphStyle->parentNode?->removeChild($paragraphStyle);
            }

            $justification = $xpath->query('./w:jc', $styleNode)->item(0);
            if ($justification instanceof DOMElement) {
                $justification->setAttributeNS(self::WORD_NS, 'w:val', 'left');
            } else {
                $justification = $document->createElementNS(self::WORD_NS, 'w:jc');
                $runProperties = $xpath->query('./w:rPr', $styleNode)->item(0);
                if ($runProperties !== null) {
                    $styleNode->insertBefore($justification, $runProperties);
                } else {
                    $styleNode->appendChild($justification);
                }
                $justification->setAttributeNS(self::WORD_NS, 'w:val', 'left');
            }
        }

        while ($container->firstChild) {
            $container->removeChild($container->firstChild);
        }

        if ($container->localName === 'tc') {
            if ($cellProperties instanceof DOMNode) {
                $container->appendChild($cellProperties);
            }

            $paragraph = $document->createElementNS(self::WORD_NS, 'w:p');
            if ($styleNode instanceof DOMNode) {
                $paragraph->appendChild($styleNode->cloneNode(true));
            }

            $this->appendParagraphText($document, $paragraph, $lines);
            $container->appendChild($paragraph);

            return;
        }

        if ($container->localName === 'p' && $styleNode instanceof DOMNode) {
            $container->appendChild($styleNode->cloneNode(true));
        }

        $this->appendParagraphText($document, $container, $lines);
    }

    private function appendParagraphText(DOMDocument $document, DOMElement $paragraph, array $lines): void
    {
        $run = null;

        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $lineBreak = $document->createElementNS(self::WORD_NS, 'w:br');
                $run ??= $document->createElementNS(self::WORD_NS, 'w:r');
                $run->appendChild($lineBreak);
                $paragraph->appendChild($run);
                $run = null;
            }

            $run ??= $document->createElementNS(self::WORD_NS, 'w:r');
            $text = $document->createElementNS(self::WORD_NS, 'w:t');
            if ($line !== '' && (str_starts_with($line, ' ') || str_ends_with($line, ' '))) {
                $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
            }
            $text->appendChild($document->createTextNode($line));
            $run->appendChild($text);
        }

        if ($run !== null) {
            $paragraph->appendChild($run);
        } elseif ($paragraph->childNodes->length === 0) {
            $emptyRun = $document->createElementNS(self::WORD_NS, 'w:r');
            $emptyText = $document->createElementNS(self::WORD_NS, 'w:t');
            $emptyText->appendChild($document->createTextNode(''));
            $emptyRun->appendChild($emptyText);
            $paragraph->appendChild($emptyRun);
        }
    }

    private function replaceContainerText(DOMXPath $xpath, DOMElement $container, string $value): void
    {
        $this->replaceCellText($xpath, $container, $value);
    }

    private function nodeText(DOMXPath $xpath, DOMElement $element): string
    {
        $text = '';
        foreach ($xpath->query('.//w:t', $element) as $node) {
            $text .= $node->nodeValue;
        }

        return $text;
    }

    private function normalize(string $text): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', $text) ?? '');
    }
}

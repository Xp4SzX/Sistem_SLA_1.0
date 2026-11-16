<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DinasAdmin;
use App\Models\Service;
use DOMDocument;
use DOMElement;
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
            $value = trim($fields[$field] ?? '');
            if ($value === '') {
                continue;
            }

            $paragraphs = $this->considerationParagraphs($xpath, $table, $field);
            if ($paragraphs === []) {
                continue;
            }

            $lines = preg_split('/\r\n|\r|\n/', $value) ?: [$value];
            foreach ($paragraphs as $index => $paragraph) {
                if ($index >= count($lines)) {
                    $paragraph['row']->parentNode?->removeChild($paragraph['row']);

                    continue;
                }

                $line = $lines[$index];
                if ($index === count($paragraphs) - 1 && count($lines) > count($paragraphs)) {
                    $line = implode("\n", array_slice($lines, $index));
                }

                $this->replaceCellText($xpath, $paragraph['cell'], $line);
            }
        }
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
                $paragraphs[] = ['row' => $row, 'cell' => $cells->item(3)];
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

            $label = $this->normalize($this->nodeText($xpath, $cells->item(1)));
            $field = $this->fieldForLabel($label);
            if ($field !== null) {
                $this->replaceCellText($xpath, $cells->item(2), $fields[$field]);
            }
        }
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

    private function replaceCellText(DOMXPath $xpath, DOMElement $container, string $value): void
    {
        $textNodes = $xpath->query('.//w:t', $container);
        if ($textNodes->length === 0) {
            return;
        }

        $firstText = $textNodes->item(0);
        foreach ($textNodes as $textNode) {
            $textNode->nodeValue = '';
        }

        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [''];
        $firstText->nodeValue = array_shift($lines) ?? '';
        if ($firstText->nodeValue !== '' && (str_starts_with($firstText->nodeValue, ' ') || str_ends_with($firstText->nodeValue, ' '))) {
            $firstText->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
        }

        $run = $firstText->parentNode;
        if ($run instanceof DOMElement && $run->localName === 'r') {
            $reference = $firstText->nextSibling;
            foreach ($lines as $line) {
                $break = $container->ownerDocument->createElementNS(self::WORD_NS, 'w:br');
                $run->insertBefore($break, $reference);
                $text = $container->ownerDocument->createElementNS(self::WORD_NS, 'w:t');
                if ($line !== '' && (str_starts_with($line, ' ') || str_ends_with($line, ' '))) {
                    $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
                }
                $text->appendChild($container->ownerDocument->createTextNode($line));
                $run->insertBefore($text, $reference);
            }
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

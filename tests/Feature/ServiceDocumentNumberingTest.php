<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\ServiceDocumentController;
use App\Models\Service;
use DOMDocument;
use DOMXPath;
use ReflectionMethod;
use Tests\TestCase;
use ZipArchive;

class ServiceDocumentNumberingTest extends TestCase
{
    public function test_service_table_keeps_numbers_entered_in_the_form(): void
    {
        $template = base_path('resources/templates/sk-penetapan-standar-pelayanan-2026.docx');
        $output = sys_get_temp_dir().'/sla-numbering-'.uniqid('', true).'.docx';
        $longText = str_repeat('abcdefghij', 10);
        $service = new Service([
            'name' => 'Layanan Sampel',
            'dasar_hukum' => "1. Peraturan pertama\n2. Peraturan kedua",
            'mengingat' => '1. '.$longText,
            'menimbang' => "a. Poin satu\nb. Poin dua\nc. Poin tiga\nd. Poin empat\ne. Poin lima",
        ]);

        try {
            $method = new ReflectionMethod(ServiceDocumentController::class, 'fillTemplate');
            $method->setAccessible(true);
            $method->invoke(new ServiceDocumentController(), $template, $output, $service, false);

            $zip = new ZipArchive();
            $this->assertTrue($zip->open($output) === true);
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            $this->assertNotFalse($xml);
            $this->assertStringContainsString('1. Peraturan pertama', $xml);
            $this->assertStringContainsString('2. Peraturan kedua', $xml);

            $document = new DOMDocument();
            $this->assertTrue($document->loadXML($xml));
            $xpath = new DOMXPath($document);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $cell = $xpath->query('//w:tr[w:tc[4][contains(., "abcdefghij")]]/w:tc[4]')->item(0);

            $this->assertNotNull($cell);
            $this->assertStringContainsString($longText, $cell->textContent);
            $this->assertGreaterThan(0, $xpath->query('.//w:br', $cell)->length);
            $this->assertSame(1, $xpath->query('./w:tcPr/w:tcW', $cell)->length);
            $this->assertSame(1, $xpath->query('./w:tcPr/w:gridSpan', $cell)->length);
            $this->assertSame(0, $xpath->query('./w:trPr/w:trHeight', $cell->parentNode)->length);

            $serviceRow = null;
            $serviceCell = null;
            foreach ($xpath->query('//w:tr') as $row) {
                $cells = $xpath->query('./w:tc', $row);
                if ($cells->length >= 3 && trim($cells->item(1)->textContent) === 'Dasar Hukum') {
                    $serviceRow = $row;
                    $serviceCell = $cells->item(2);

                    break;
                }
            }

            $this->assertNotNull($serviceRow);
            $this->assertNotNull($serviceCell);
            $this->assertStringContainsString('1. Peraturan pertama2. Peraturan kedua', $serviceCell->textContent);
            $this->assertSame(0, $xpath->query('.//w:numPr', $serviceCell)->length);
            $this->assertSame(0, $xpath->query('.//w:pStyle[@w:val="ListParagraph"]', $serviceCell)->length);
            $this->assertSame(1, $xpath->query('./w:trPr/w:cantSplit', $serviceRow)->length);
            $this->assertSame(1, $xpath->query('./w:p/w:pPr/w:jc[@w:val="left"]', $serviceCell)->length);

            $menimbangTable = $xpath->query('//w:tbl[w:tr[1]/w:tc[1][normalize-space(.)="Menimbang"]]')->item(0);
            $this->assertNotNull($menimbangTable);
            $markers = [];
            foreach ($xpath->query('./w:tr', $menimbangTable) as $row) {
                $cells = $xpath->query('./w:tc', $row);
                if ($cells->length >= 4) {
                    $markers[] = trim($cells->item(2)->textContent);
                }
            }

            $this->assertContains('d.', $markers);
            $this->assertContains('e.', $markers);
            $this->assertStringContainsString('Poin empat', $document->textContent);
            $this->assertStringContainsString('Poin lima', $document->textContent);
        } finally {
            if (is_file($output)) {
                unlink($output);
            }
        }
    }
}

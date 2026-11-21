<?php

namespace NFePHP\Gnre\Tests\DA;

use Com\Tecnick\Barcode\Barcode;
use NFePHP\DA\Legacy\Pdf;
use PHPUnit\Framework\TestCase;

/**
 * Smoke test das dependências da impressão (sped-da + tc-lib-barcode)
 */
class DependenciasTest extends TestCase
{
    public function testI25(): void
    {
        $png = (new Barcode())
            ->getBarcodeObj('I25', str_repeat('8', 44), -1, -12, 'black')
            ->getPngData();
        $this->assertStringStartsWith("\x89PNG", $png);
    }

    public function testPdf(): void
    {
        $pdf = new Pdf('P', 'mm', 'A4');
        $pdf->addPage();
        $this->assertStringStartsWith('%PDF', $pdf->getPdf());
    }
}

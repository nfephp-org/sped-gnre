<?php

namespace Sped\Gnre\Test\Render;

use PHPUnit\Framework\TestCase;
use Dompdf\Dompdf;

/**
 * @covers \Sped\Gnre\Render\Pdf
 */
class PdfTest extends TestCase
{
    public function testDeveCriarOpdfApartirDoHtml()
    {
        $dom = $this->createMock('\Dompdf\Dompdf');

        $html = $this->createMock('\Sped\Gnre\Render\Html');

        $pdf = $this->createMock('\Sped\Gnre\Render\Pdf');
        $pdf->expects($this->once())
                ->method('create')
                ->will($this->returnValue($dom));

        $domPdf = $pdf->create($html);

        $this->assertInstanceOf('\Dompdf\Dompdf', $domPdf);
    }

    public function testDeveRetornarUmaInstanciaDoDomPdf()
    {
        $dom = new CoveragePdf();
        $this->assertInstanceOf('\Dompdf\Dompdf', $dom->getDomPdf());
    }
}

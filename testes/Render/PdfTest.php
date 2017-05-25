<?php

namespace Sped\Gnre\Test\Render;

/**
 * @covers \Sped\Gnre\Render\Pdf
 */
class PdfTest extends \PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        define('DOMPDF_ENABLE_AUTOLOAD', false);

        require 'vendor/dompdf/dompdf/dompdf_config.inc.php';
    }

    public function testDeveCriarOpdfApartirDoHtml()
    {
        $dom = $this->createMock('\DOMPDF');

        $html = $this->createMock('\Sped\Gnre\Render\Html');


        $pdf = $this->createMock('\Sped\Gnre\Render\Pdf');
        $pdf->expects($this->once())
                ->method('create')
                ->will($this->returnValue($dom));

        $domPdf = $pdf->create($html);

        $this->assertInstanceOf('\DOMPDF', $domPdf);
    }

    public function testDeveRetornarUmaInstanciaDoDomPdf()
    {
        $dom = new CoveragePdf();
        $this->assertInstanceOf('\DOMPDF', $dom->getDomPdf());
    }
}

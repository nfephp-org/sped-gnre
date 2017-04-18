<?php

namespace Sped\Gnre\Render\Test;

/**
 * @covers \Sped\Gnre\Render\Pdf
 */
class PdfTest extends \PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        define('DOMPDF_ENABLE_AUTOLOAD', false);

        require 'vendor/dompdf/dompdf/dompdf_config.inc.php';
    }

    public function testDeveCriarOpdfApartirDoHtml() {
        $dom = $this->createMock('\DOMPDF');
        $dom->expects($this->once())
                ->method('render');

        $html = $this->createMock('\Sped\Gnre\Render\Html');
        $html->expects($this->once())
                ->method('getHtml')
                ->will($this->returnValue('<html><p>Guia GNRE</p></html>'));


        $pdf = $this->createMock('\Sped\Gnre\Render\Pdf', array('getDomPdf'));
        $pdf->expects($this->once())
                ->method('getDomPdf')
                ->will($this->returnValue($dom));

        $domPdf = $pdf->create($html);

        $this->assertInstanceOf('\DOMPDF', $domPdf);
    }
    
    public function testDeveRetornarUmaInstanciaDoDomPdf() {
        $dom = new CoveragePdf();
        $this->assertInstanceOf('\DOMPDF', $dom->getDomPdf());
    }

}

class CoveragePdf extends \Sped\Gnre\Render\Pdf {
    
    public function getDomPdf() {
        return parent::getDomPdf();
    }
    
}
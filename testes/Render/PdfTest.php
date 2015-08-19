<?php

namespace Gnre\Render\Test;

/**
 * @covers \Gnre\Render\Pdf
 */
class PdfTest extends \PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        define('DOMPDF_ENABLE_AUTOLOAD', false);

        require 'vendor/dompdf/dompdf/dompdf_config.inc.php';
    }

    public function testDeveCriarOpdfApartirDoHtml() {
        $dom = $this->getMock('\DOMPDF');
        $dom->expects($this->once())
                ->method('render');

        $html = $this->getMock('\Gnre\Render\Html');
        $html->expects($this->once())
                ->method('getHtml')
                ->will($this->returnValue('<html><p>Guia GNRE</p></html>'));


        $pdf = $this->getMock('\Gnre\Render\Pdf', array('getDomPdf'));
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

class CoveragePdf extends \Gnre\Render\Pdf {
    
    public function getDomPdf() {
        return parent::getDomPdf();
    }
    
}
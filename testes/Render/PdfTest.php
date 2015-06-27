<?php

namespace Gnre\Render\Test;

use Gnre\Render\Pdf;

/**
 * @covers \Gnre\Render\Pdf
 */
class PdfTest extends \PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        define('DOMPDF_ENABLE_AUTOLOAD', false);
        
        require 'vendor/dompdf/dompdf/dompdf_config.inc.php';
    }

    public function testDeveCriarOpdfApartirDoHtml() {
        $html = $this->getMock('\Gnre\Render\Html');
        $html->expects($this->once())
                ->method('getHtml')
                ->will($this->returnValue('<html><p>Guia GNRE</p></html>'));

        $pdf = new Pdf();
        $domPdf = $pdf->create($html);

        $this->assertInstanceOf('\DOMPDF', $domPdf);
    }

}

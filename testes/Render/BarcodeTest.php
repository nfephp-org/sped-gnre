<?php

namespace Sped\Gnre\Render\Test;

class BarcodeTest extends \PHPUnit_Framework_TestCase {

    public function testDeveSetarUmNumeroDeCodigoDeBarras() {
        $barcodeGnre = new \Sped\Gnre\Render\Barcode128();
        $barcodeGnre->setNumeroCodigoBarras('91910919190191091090109109190109');

        $this->assertEquals('91910919190191091090109109190109', $barcodeGnre->getNumeroCodigoBarras());
    }

    public function testDeveRetornarUmNumeroDeCodigoDeBarras() {
        $barcodeGnre = new \Sped\Gnre\Render\Barcode128();
        $this->assertNull($barcodeGnre->getNumeroCodigoBarras());

        $barcodeGnre->setNumeroCodigoBarras('91910919190191091090109109190109');

        $this->assertEquals('91910919190191091090109109190109', $barcodeGnre->getNumeroCodigoBarras());
    }

}

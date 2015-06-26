<?php

namespace Gnre\Render\Test;

class BarcodeTest extends \PHPUnit_Framework_TestCase {

    public function testDeveSetarUmNumeroDeCodigoDeBarras() {
        $barcodeGnre = new \Gnre\Render\Barcode128();
        $barcodeGnre->setNumeroCodigoBarras('91910919190191091090109109190109');

        $this->assertEquals('91910919190191091090109109190109', $barcodeGnre->getNumeroCodigoBarras());
    }

    public function testDeveRetornarUmNumeroDeCodigoDeBarras() {
        $barcodeGnre = new \Gnre\Render\Barcode128();
        $this->assertNull($barcodeGnre->getNumeroCodigoBarras());

        $barcodeGnre->setNumeroCodigoBarras('91910919190191091090109109190109');

        $this->assertEquals('91910919190191091090109109190109', $barcodeGnre->getNumeroCodigoBarras());
    }

    public function testDeveRetornarUmaImagemComEncode64() {
        $imagem = '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';
        $barcodeGnre = new \Gnre\Render\Barcode128();
        $barcodeGnre->setNumeroCodigoBarras('91910919190191091090109109190109');

        $this->assertEquals($imagem, $barcodeGnre->getCodigoBarrasBase64());
    }

}

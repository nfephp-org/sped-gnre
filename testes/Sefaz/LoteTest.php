<?php

/**
 * @covers Gnre\Sefaz\LoteGnre
 */
class LoteTest extends PHPUnit_Framework_TestCase {

    private $lote;

    public function setUp() {
        $this->lote = new Gnre\Sefaz\Lote();
    }

    public function tearDown() {
        $this->lote = null;
    }

    public function testAdicionarUmaGuiaAoLote() {
        $this->lote->addGuia(new Gnre\Sefaz\Guia());
        $this->assertEquals(1, count($this->lote->getGuias()));
    }

    public function testBuscarUmaGuiaEmEspecifico() {
        $this->lote->addGuia(new Gnre\Sefaz\Guia());
        $this->lote->addGuia(new Gnre\Sefaz\Guia());

        $this->assertInstanceOf('Gnre\Sefaz\Guia', $this->lote->getGuia(0));
        $this->assertInstanceOf('Gnre\Sefaz\Guia', $this->lote->getGuia(1));
    }

}

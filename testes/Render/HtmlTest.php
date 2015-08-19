<?php

namespace Gnre\Render\Test;

use Gnre\Render\Html;

/**
 * @covers \Gnre\Render\Html
 */
class HtmlTest extends \PHPUnit_Framework_TestCase {

    public function testDeveRetornarUmInstanciaDoBarCode() {
        $html = new Html();
        $this->assertInstanceOf('\Gnre\Render\Barcode128', $html->getBarCode());
    }

    public function testDeveRetornarUmaInstanciaDoSmartyFactory() {
        $html = new Html();
        $this->assertInstanceOf('\Gnre\Render\SmartyFactory', $html->getSmartyFactory());
    }

    public function testDeveDefinirUmObjetoDeCodigoDeBarrasParaSerUtilizado() {
        $barCode = new \Gnre\Render\Barcode128();
        $html = new Html();

        $this->assertInstanceOf('\Gnre\Render\Html', $html->setBarCode($barCode));
        $this->assertSame($barCode, $html->getBarCode());
    }

    public function testDeveRetornarNullSeNaoForCriadoOhtmlDaGuia() {
        $html = new \Gnre\Render\Html();
        $this->assertEmpty($html->getHtml());
    }

    public function testNaoDeveGerarOhtmlDoLoteQuandoOloteEvazio() {
        $html = new Html();
        $mkcLote = $this->getMock('\Gnre\Sefaz\Lote');
        $mkcLote->expects($this->once())
                ->method('getGuias');
        $mkcLote->expects($this->never())
                ->method('getGuia');

        $html->create($mkcLote);

        $this->assertEmpty($html->getHtml());
    }

    public function testDeveGerarOhtmlDoLoteQuandoPossuirGuias() {
        $smarty = $this->getMock('\Smarty');
        $smarty->expects($this->at(0))
                ->method('assign')
                ->with('guiaViaInfo');
        $smarty->expects($this->at(1))
                ->method('assign')
                ->with('barcode');
        $smarty->expects($this->at(2))
                ->method('assign')
                ->with('guia');
        $smarty->expects($this->at(3))
                ->method('fetch')
                ->with('gnre.tpl')
                ->will($this->returnValue('<html></html>'));

        $smartyFactory = $this->getMock('\Gnre\Render\SmartyFactory');
        $smartyFactory->expects($this->once())
                ->method('create')
                ->will($this->returnValue($smarty));

        $html = new Html();
        $html->setSmartyFactory($smartyFactory);

        $lote = new \Gnre\Sefaz\Lote();
        $lote->addGuia(new \Gnre\Sefaz\Guia());

        $html->create($lote);

        $this->assertNotEmpty($html->getHtml());
    }

}

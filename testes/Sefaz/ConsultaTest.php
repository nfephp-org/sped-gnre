<?php

/**
 * @covers Sped\Gnre\Sefaz\Consulta
 */
class ConsultaTest extends PHPUnit_Framework_TestCase {

    public function testDeveRetornarOsCabecalhosParaArequisicaoSoap() {
        $consulta = new Sped\Gnre\Sefaz\Consulta();
        $headersArray = $consulta->getHeaderSoap();

        $this->assertEquals('Content-Type: application/soap+xml;charset=utf-8;action="http://www.gnre.pe.gov.br/webservice/GnreResultadoLote"', $headersArray[0]);
        $this->assertEquals('SOAPAction: consultar', $headersArray[1]);
    }

    public function testDeveRetornarAacaoAserExecutadaNoSoap() {
        $consulta = new Sped\Gnre\Sefaz\Consulta();

        $this->assertEquals('https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLote', $consulta->soapAction());
    }

    public function testDeveRetornarXmlCompletoVazioParaRealizarAconsulta() {
        $dadosParaConsulta = file_get_contents(__DIR__ . '/../../exemplos/envelope-consultar-gnre.xml');
        
        $consulta = new Sped\Gnre\Sefaz\Consulta();
        $consulta->setEnvironment(12345678);
        $consulta->setRecibo(123);
        
        $this->assertXmlStringEqualsXmlString($dadosParaConsulta, $consulta->toXml());
    }

}

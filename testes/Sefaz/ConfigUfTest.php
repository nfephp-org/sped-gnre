<?php

namespace Sped\Gnre\Test\Sefaz;

use PHPUnit\Framework\TestCase;

/**
 * @covers Sped\Gnre\Sefaz\ConfigUf
 */
class ConfigUfTest extends TestCase
{

    public function testDeveRetornarOsCabecalhosParaArequisicaoSoap()
    {
        $consulta = new \Sped\Gnre\Sefaz\ConfigUf();
        $headersArray = $consulta->getHeaderSoap();

        $header = 'Content-Type: application/soap+xml;charset=utf-8;action="http://www.gnre.pe.gov.br/webservice/GnreConfigUF"';
        $this->assertEquals($header, $headersArray[0]);
        $this->assertEquals('SOAPAction: consultar', $headersArray[1]);
    }

    public function testDeveRetornarOsCabecalhosParaArequisicaoSoapAoWebserviceDeTestes()
    {
        $consulta = new \Sped\Gnre\Sefaz\ConfigUf();
        $consulta->utilizarAmbienteDeTeste(true);

        $headersArray = $consulta->getHeaderSoap();

        $header = 'Content-Type: application/soap+xml;charset=utf-8;action="http://www.testegnre.pe.gov.br/webservice/GnreConfigUF"';
        $this->assertEquals($header, $headersArray[0]);
        $this->assertEquals('SOAPAction: consultar', $headersArray[1]);
    }

    public function testDeveRetornarAacaoAserExecutadaNoSoap()
    {
        $consulta = new \Sped\Gnre\Sefaz\ConfigUf();

        $this->assertEquals('https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF', $consulta->soapAction());
    }

    public function testDeveRetornarXmlCompletoVazioParaRealizarAconsulta()
    {
        $dadosParaConsulta = file_get_contents(__DIR__ . '/../../exemplos/envelope-consulta-config-uf.xml');

        $consulta = new \Sped\Gnre\Sefaz\ConfigUf();
        $consulta->setEnvironment(1);
        $consulta->setEstado('PR');
        $consulta->setReceita(100099);

        $this->assertXmlStringEqualsXmlString($dadosParaConsulta, $consulta->toXml());
    }

    public function testDeveRetornarAactionAserExecutadaNoWebServiceDeProducao()
    {
        $consulta = new \Sped\Gnre\Sefaz\ConfigUf();

        $this->assertEquals($consulta->soapAction(), 'https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF');
    }

    public function testDeveRetornarAactionAserExecutadaNoWebServiceDeTestes()
    {
        $consulta = new \Sped\Gnre\Sefaz\ConfigUf();
        $consulta->utilizarAmbienteDeTeste(true);

        $this->assertEquals($consulta->soapAction(), 'https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF');
    }
}

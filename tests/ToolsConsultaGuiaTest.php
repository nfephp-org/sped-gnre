<?php

namespace NFePHP\Gnre\Tests;

use NFePHP\Common\Exception\ValidatorException;
use NFePHP\Gnre\Exception\InvalidArgumentException;
use NFePHP\Gnre\Tests\Common\SoapFake;
use NFePHP\Gnre\Tools;

class ToolsConsultaGuiaTest extends ToolsTestCase
{
    /**
     * @var SoapFake
     */
    private $fake;

    /**
     * @param array<string, mixed> $config
     */
    private function tools(array $config = []): Tools
    {
        $tools = new Tools($this->config($config), $this->certificado());
        $this->fake = new SoapFake();
        $tools->loadSoapClass($this->fake);
        return $tools;
    }

    private function loteConsulta(): string
    {
        return (string) file_get_contents(self::FIXTURES . '/xml/lote-consulta-guia.xml');
    }

    public function testEnviaLoteConsulta(): void
    {
        $tools = $this->tools();

        $tools->sefazEnviaLoteConsulta($this->loteConsulta());

        $p = $this->fake->getSendParams();
        $this->assertSame('https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcaoConsulta', $p['url']);
        $this->assertSame('processar', $p['operation']);
        $this->assertSame('http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcaoConsulta', $p['soapheader']->namespace);
        $this->assertSame('2.00', $p['soapheader']->data['versaoDados']);
        $this->assertSame($this->c14n($this->loteConsulta()), $this->c14n($tools->lastRequest));
    }

    public function testEnviaLoteConsultaComLayout100(): void
    {
        $tools = $this->tools(['versao' => '1.00']);

        $tools->sefazEnviaLoteConsulta($this->loteConsulta());

        $this->assertSame(1, $this->fake->getCalls());
        $this->assertSame('2.00', $this->fake->getSendParams()['soapheader']->data['versaoDados']);
    }

    public function testEnviaLoteConsultaInvalido(): void
    {
        $tools = $this->tools();
        $xml = str_replace('<tipoConsulta>C</tipoConsulta>', '', $this->loteConsulta());
        try {
            $this->expectException(ValidatorException::class);
            $tools->sefazEnviaLoteConsulta($xml);
        } finally {
            $this->assertSame(0, $this->fake->getCalls());
        }
    }

    public function testConsultaLoteConsulta(): void
    {
        $tools = $this->tools();

        $tools->sefazConsultaLoteConsulta('25123456789012');

        $p = $this->fake->getSendParams();
        $this->assertSame('https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLoteConsulta', $p['url']);
        $this->assertSame('consultar', $p['operation']);
        $this->assertSame('2.00', $p['soapheader']->data['versaoDados']);
        $this->assertSame(
            '<TConsLote_GNRE xmlns="http://www.gnre.pe.gov.br"><ambiente>2</ambiente>'
            . '<numeroRecibo>25123456789012</numeroRecibo></TConsLote_GNRE>',
            $tools->lastRequest
        );
    }

    public function testConsultaLoteConsultaComLayout100(): void
    {
        $tools = $this->tools(['versao' => '1.00']);
        $tools->sefazConsultaLoteConsulta('25123456789012');
        $this->assertSame('2.00', $this->fake->getSendParams()['soapheader']->data['versaoDados']);
    }

    public function testConsultaLoteConsultaReciboVazio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->tools()->sefazConsultaLoteConsulta('');
    }
}

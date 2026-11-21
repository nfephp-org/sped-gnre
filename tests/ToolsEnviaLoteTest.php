<?php

namespace NFePHP\Gnre\Tests;

use NFePHP\Common\Exception\ValidatorException;
use NFePHP\Gnre\Tests\Common\SoapFake;
use NFePHP\Gnre\Tools;

class ToolsEnviaLoteTest extends ToolsTestCase
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
        $this->fake->setReturnValue('<retorno/>');
        $tools->loadSoapClass($this->fake);
        return $tools;
    }

    public function testEnviaLote200(): void
    {
        $tools = $this->tools();
        $xml = $this->golden('lote-v2-valido-prod.xml', 'TLote_GNRE');

        $this->assertSame('<retorno/>', $tools->sefazEnviaLote($xml));

        $p = $this->fake->getSendParams();
        $ns = 'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao';
        $this->assertSame('https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao', $p['url']);
        $this->assertSame('processar', $p['operation']);
        $this->assertSame('"' . $ns . '"', $p['action']);
        $this->assertSame($ns, $p['soapheader']->namespace);
        $this->assertSame('gnreCabecMsg', $p['soapheader']->name);
        $this->assertSame('2.00', $p['soapheader']->data['versaoDados']);
        $this->assertStringStartsWith('<gnreDadosMsg xmlns="' . $ns . '"><TLote_GNRE', $p['request']);
        $this->assertSame($xml, $this->c14n($tools->lastRequest));
        $this->assertSame('<retorno/>', $tools->lastResponse);
    }

    public function testEnviaLote100(): void
    {
        $tools = $this->tools(['versao' => '1.00']);
        $xml = $this->golden('lote-v1-valido.xml', 'TLote_GNRE');

        $tools->sefazEnviaLote($xml);

        $this->assertSame('1.00', $this->fake->getSendParams()['soapheader']->data['versaoDados']);
        $this->assertSame($xml, $this->c14n($tools->lastRequest));
    }

    public function testRemoveDeclaracaoXml(): void
    {
        $tools = $this->tools();
        $xml = $this->golden('lote-v2-valido-prod.xml', 'TLote_GNRE');

        $tools->sefazEnviaLote('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml);

        $this->assertStringNotContainsString('<?xml', $tools->lastRequest);
        $this->assertSame(1, $this->fake->getCalls());
    }

    public function testXmlInvalidoNaoEnvia(): void
    {
        $tools = $this->tools();
        try {
            $this->expectException(ValidatorException::class);
            $tools->sefazEnviaLote($this->golden('lote-v2-completo-prod.xml', 'TLote_GNRE'));
        } finally {
            $this->assertSame(0, $this->fake->getCalls());
        }
    }

    public function testProducao(): void
    {
        $tools = $this->tools();
        $tools->setEnvironment(1);

        $tools->sefazEnviaLote($this->golden('lote-v2-valido-prod.xml', 'TLote_GNRE'));

        $p = $this->fake->getSendParams();
        $this->assertSame('https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao', $p['url']);
        $this->assertSame('http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao', $p['soapheader']->namespace);
    }
}

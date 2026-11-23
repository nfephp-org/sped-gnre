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

    public function testEnvelopeSerializado(): void
    {
        $tools = $this->tools();
        $tools->sefazEnviaLote($this->golden('lote-v2-valido-prod.xml', 'TLote_GNRE'));

        $envelope = $this->fake->envelope();

        $ns = 'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao';
        $this->assertStringContainsString(
            '<soap:Header><gnreCabecMsg xmlns="' . $ns . '">'
            . '<versaoDados>2.00</versaoDados></gnreCabecMsg></soap:Header>',
            $envelope
        );
        $this->assertStringContainsString('<soap:Body><gnreDadosMsg xmlns="' . $ns . '"><TLote_GNRE', $envelope);
        $this->assertStringContainsString('xmlns:soap="http://www.w3.org/2003/05/soap-envelope"', $envelope);
    }

    public function testLoteAcimaDe600KbNaoEnvia(): void
    {
        $tools = $this->tools();
        $make = new \NFePHP\Gnre\Make();
        for ($i = 0; $i < 1300; $i++) {
            $make->tagGuia((object) ['ufFavorecida' => 'SP', 'identificadorGuia' => (string) $i]);
            $make->tagEmitente((object) ['CNPJ' => '06157250000116', 'razaoSocial' => str_repeat('R', 60),
                'endereco' => str_repeat('E', 60), 'municipio' => '50308', 'uf' => 'SP', 'cep' => '01001000']);
            $make->tagItem((object) ['receita' => '100099', 'dataVencimento' => '2026-12-15']);
            $make->tagItemValor((object) ['tipo' => '11', 'valor' => 1]);
            $make->tagDestinatario((object) ['CNPJ' => '86268158000162', 'razaoSocial' => str_repeat('D', 60)]);
        }
        $xml = $make->getXML();
        $this->assertGreaterThan(600 * 1024, strlen($xml));
        try {
            $this->expectException(\NFePHP\Gnre\Exception\InvalidArgumentException::class);
            $this->expectExceptionMessageMatches('/600 KB/');
            $tools->sefazEnviaLote($xml);
        } finally {
            $this->assertSame(0, $this->fake->getCalls());
        }
    }
}

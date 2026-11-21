<?php

namespace NFePHP\Gnre\Tests;

use NFePHP\Gnre\Exception\RuntimeException;
use NFePHP\Gnre\Make;

class MakeTest extends MakeTestCase
{
    public function testGuiaMinima(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));

        $xml = $make->getXML();

        $this->assertSame(
            '<TLote_GNRE xmlns="http://www.gnre.pe.gov.br" versao="2.00"><guias><TDadosGNRE versao="2.00">'
            . '<ufFavorecida>SP</ufFavorecida><tipoGnre>0</tipoGnre></TDadosGNRE></guias></TLote_GNRE>',
            $xml
        );
        $this->valida($xml);
    }

    public function testEmitenteCnpj(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagEmitente($this->std([
            'CNPJ' => '06157250000116',
            'IE' => '123456',
            'razaoSocial' => 'ACME & FILHOS',
            'municipio' => '50308',
            'uf' => 'SP',
            'cep' => '01001000',
            'telefone' => '1133334444',
        ]));

        $xml = $make->getXML();

        $this->assertStringContainsString(
            '<identificacao><CNPJ>06157250000116</CNPJ><IE>123456</IE></identificacao>',
            $xml
        );
        $this->assertStringContainsString('<razaoSocial>ACME &amp; FILHOS</razaoSocial>', $xml);
        $this->valida($xml);
    }

    public function testEmitenteCpf(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagEmitente($this->std(['CPF' => '90483926086']));

        $this->assertStringContainsString(
            '<identificacao><CPF>90483926086</CPF></identificacao>',
            $make->getXML()
        );
    }

    public function testValorGnreFormatado(): void
    {
        $make = new Make();
        $make->tagGuia($this->std([
            'ufFavorecida' => 'SP',
            'valorGNRE' => 12.5,
            'dataPagamento' => '2026-12-01',
        ]));

        $xml = $make->getXML();

        $this->assertStringContainsString(
            '<tipoGnre>0</tipoGnre><valorGNRE>12.50</valorGNRE><dataPagamento>2026-12-01</dataPagamento>',
            $xml
        );
        $this->valida($xml);
    }

    public function testUfFavorecidaObrigatoria(): void
    {
        $make = new Make();
        $make->tagGuia($this->std([]));
        $this->assertNotEmpty($make->getErrors());
        $this->expectException(RuntimeException::class);
        $make->getXML();
    }

    public function testTipoGnreInvalido(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP', 'tipoGnre' => '9']));
        $this->assertNotEmpty($make->getErrors());
    }

    public function testSemGuias(): void
    {
        $this->expectException(RuntimeException::class);
        (new Make())->getXML();
    }

    public function testEmitenteSemGuia(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/tagGuia/');
        (new Make())->tagEmitente($this->std(['CNPJ' => '06157250000116']));
    }

    public function testDuasGuias(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagGuia($this->std(['ufFavorecida' => 'RJ']));

        $xml = $make->getXML();

        $this->assertSame(2, substr_count($xml, '<TDadosGNRE'));
        $this->valida($xml);
    }

    public function testGetXmlIdempotente(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $primeiro = $make->getXML();

        $this->assertSame($primeiro, $make->getXML());

        $make->tagGuia($this->std(['ufFavorecida' => 'MG']));
        $this->assertSame(2, substr_count($make->getXML(), '<TDadosGNRE'));
    }
}

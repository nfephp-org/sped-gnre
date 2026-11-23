<?php

namespace NFePHP\Gnre\Tests;

use NFePHP\Common\Exception\ValidatorException;
use NFePHP\Gnre\MakeV1;
use NFePHP\Gnre\Tests\Common\SoapFake;
use NFePHP\Gnre\Tools;

/**
 * Integração MakeV1 + Tools: os dois formatos do layout 1.00 (D-16).
 */
class ToolsEnviaLoteV1Test extends MakeTestCase
{
    /**
     * @var SoapFake
     */
    private $fake;

    private function tools(string $versao): Tools
    {
        $tools = new Tools($this->config(['versao' => $versao]), $this->certificado());
        $this->fake = new SoapFake();
        $tools->loadSoapClass($this->fake);
        return $tools;
    }

    private function lote(string $formato): string
    {
        $make = new MakeV1($formato);
        $make->tagGuia($this->std([
            'c01_UfFavorecida' => 'SP',
            'c02_receita' => '100099',
            'c27_tipoIdentificacaoEmitente' => '1',
            'c03_idContribuinteEmitente' => '06157250000116',
            'c06_valorPrincipal' => 10,
            'c14_dataVencimento' => '2026-12-15',
        ]));
        $make->tagReferencia($this->std(['periodo' => '0', 'mes' => '11', 'ano' => '2026']));
        return $make->getXML();
    }

    public function testEnviaGuia100NoLote200(): void
    {
        $tools = $this->tools('2.00');
        $xml = $this->lote(MakeV1::FORMATO_LOTE_200);

        $tools->sefazEnviaLote($xml);

        $this->assertSame(1, $this->fake->getCalls());
        $this->assertSame('2.00', $this->fake->getSendParams()['soapheader']->data['versaoDados']);
        $this->assertSame($this->c14n($xml), $this->c14n($tools->lastRequest));
    }

    public function testEnviaLote100Puro(): void
    {
        $tools = $this->tools('1.00');
        $xml = $this->lote(MakeV1::FORMATO_LOTE_100);

        $tools->sefazEnviaLote($xml);

        $this->assertSame(1, $this->fake->getCalls());
        $this->assertSame('1.00', $this->fake->getSendParams()['soapheader']->data['versaoDados']);
        $this->assertSame($this->c14n($xml), $this->c14n($tools->lastRequest));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public function formatoTrocadoProvider(): array
    {
        return [
            'lote 1.00 puro com config 2.00' => ['2.00', MakeV1::FORMATO_LOTE_100],
            'guia 1.00 embutida com config 1.00' => ['1.00', MakeV1::FORMATO_LOTE_200],
        ];
    }

    /**
     * @dataProvider formatoTrocadoProvider
     */
    public function testFormatoTrocado(string $versaoConfig, string $formato): void
    {
        $tools = $this->tools($versaoConfig);
        try {
            $this->expectException(ValidatorException::class);
            $tools->sefazEnviaLote($this->lote($formato));
        } finally {
            $this->assertSame(0, $this->fake->getCalls());
        }
    }
}

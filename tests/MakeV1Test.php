<?php

namespace NFePHP\Gnre\Tests;

use DOMDocument;
use DOMXPath;
use NFePHP\Gnre\Exception\InvalidArgumentException;
use NFePHP\Gnre\Exception\RuntimeException;
use NFePHP\Gnre\MakeV1;

class MakeV1Test extends MakeTestCase
{
    /**
     * Mesmos dados do golden lote-v1-valido.xml
     */
    private function guiaDoGolden(MakeV1 $make): MakeV1
    {
        $make->tagGuia($this->std([
            'c01_UfFavorecida' => 'SP',
            'c02_receita' => '100099',
            'c25_detalhamentoReceita' => '101010',
            'c26_produto' => '1',
            'c27_tipoIdentificacaoEmitente' => '1',
            'c03_idContribuinteEmitente' => '41819055000105',
            'c28_tipoDocOrigem' => '10',
            'c04_docOrigem' => '5656',
            'c06_valorPrincipal' => 10.99,
            'c10_valorTotal' => 12.52,
            'c14_dataVencimento' => '2015-05-01',
            'c15_convenio' => '546456',
            'c16_razaoSocialEmitente' => 'GNRE PHP EMITENTE',
            'c17_inscricaoEstadualEmitente' => '56756',
            'c18_enderecoEmitente' => 'Queens St',
            'c19_municipioEmitente' => '00108',
            'c20_ufEnderecoEmitente' => 'DF',
            'c21_cepEmitente' => '08215917',
            'c22_telefoneEmitente' => '1199999999',
            'c34_tipoIdentificacaoDestinatario' => '1',
            'c35_idContribuinteDestinatario' => '86268158000162',
            'c36_inscricaoEstadualDestinatario' => '10809181',
            'c37_razaoSocialDestinatario' => 'RAZAO SOCIAL GNRE PHP DESTINATARIO',
            'c38_municipioDestinatario' => '02306',
            'c33_dataPagamento' => '2015-11-30',
        ]));
        $make->tagReferencia($this->std(['mes' => '05', 'ano' => '2015', 'parcela' => '2']));
        $make->tagCampoExtra($this->std(['codigo' => '666', 'tipo' => 'T', 'valor' => 'GNRE']));
        $make->tagCampoExtra($this->std(['codigo' => '111', 'tipo' => 'N', 'valor' => '2']));
        return $make;
    }

    private function guiaMinima(string $formato = MakeV1::FORMATO_LOTE_200): MakeV1
    {
        $make = new MakeV1($formato);
        $make->tagGuia($this->std(['c01_UfFavorecida' => 'SP']));
        return $make;
    }

    public function testLote100IgualAoGolden(): void
    {
        $xml = $this->guiaDoGolden(new MakeV1(MakeV1::FORMATO_LOTE_100))->getXML();

        $this->assertSame($this->golden('lote-v1-valido.xml', 'TLote_GNRE'), $this->c14n($xml));
        $this->assertTrue(\NFePHP\Common\Validator::isValid(
            $xml,
            __DIR__ . '/../schemes/PL_GNRE_100/lote_gnre_v1.00.xsd'
        ));
    }

    public function testGuia100DentroDoLote200(): void
    {
        $xml = $this->guiaDoGolden(new MakeV1())->getXML();

        $this->assertStringStartsWith(
            '<TLote_GNRE xmlns="http://www.gnre.pe.gov.br" versao="2.00"><guias><TDadosGNRE versao="1.00">'
            . '<c01_UfFavorecida>SP',
            $xml
        );
        $this->valida($xml);
        $semVersao = str_replace([' versao="2.00"', ' versao="1.00"'], '', $xml);
        $this->assertSame($this->golden('lote-v1-valido.xml', 'TLote_GNRE'), $this->c14n($semVersao));
    }

    public function testPeriodoZeroEnviado(): void
    {
        $make = $this->guiaMinima();
        $make->tagReferencia($this->std(['periodo' => '0', 'mes' => '01', 'ano' => '2026']));

        $xml = $make->getXML();

        $this->assertStringContainsString('<c05_referencia><periodo>0</periodo>', $xml);
        $this->valida($xml);
    }

    public function testCpfEmitente(): void
    {
        $make = new MakeV1();
        $make->tagGuia($this->std([
            'c01_UfFavorecida' => 'SP',
            'c27_tipoIdentificacaoEmitente' => '2',
            'c03_idContribuinteEmitente' => '90483926086',
        ]));

        $xml = $make->getXML();

        $this->assertStringContainsString('<c03_idContribuinteEmitente><CPF>90483926086</CPF>', $xml);
        $this->valida($xml);
    }

    /**
     * @return array<string, array{0: array<string, string>}>
     */
    public function identificacaoInvalidaProvider(): array
    {
        return [
            'tipo 3' => [['c27_tipoIdentificacaoEmitente' => '3', 'c03_idContribuinteEmitente' => '41819055000105']],
            'documento sem tipo' => [['c03_idContribuinteEmitente' => '41819055000105']],
            'destinatário sem tipo' => [['c35_idContribuinteDestinatario' => '86268158000162']],
        ];
    }

    /**
     * @dataProvider identificacaoInvalidaProvider
     * @param array<string, string> $dados
     */
    public function testTipoIdentificacaoInvalido(array $dados): void
    {
        $make = new MakeV1();
        $make->tagGuia($this->std(['c01_UfFavorecida' => 'SP'] + $dados));
        $this->assertNotEmpty($make->getErrors());
    }

    public function testCampoExtraTipoInvalido(): void
    {
        $make = $this->guiaMinima();
        $make->tagCampoExtra($this->std(['codigo' => '666', 'tipo' => 'TXT', 'valor' => 'GNRE']));
        $this->assertNotEmpty($make->getErrors());
    }

    public function testQuartoCampoExtra(): void
    {
        $make = $this->guiaMinima();
        for ($i = 1; $i <= 4; $i++) {
            $make->tagCampoExtra($this->std(['codigo' => (string) $i, 'tipo' => 'T', 'valor' => 'V']));
        }
        $this->assertNotEmpty(array_filter($make->getErrors(), function (string $erro) {
            return strpos($erro, 'máximo de 3') !== false;
        }));
    }

    public function testFormatoInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MakeV1('3.00');
    }

    public function testReferenciaSemGuia(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/tagGuia/');
        (new MakeV1())->tagReferencia($this->std(['mes' => '01']));
    }

    public function testSemGuias(): void
    {
        $this->expectException(RuntimeException::class);
        (new MakeV1())->getXML();
    }

    public function testDuasGuias(): void
    {
        $make = $this->guiaMinima();
        $make->tagGuia($this->std(['c01_UfFavorecida' => 'RJ']));
        $make->tagReferencia($this->std(['mes' => '02', 'ano' => '2026']));

        $xml = $make->getXML();

        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('g', 'http://www.gnre.pe.gov.br');
        $this->assertSame(0.0, $xpath->evaluate('count(//g:TDadosGNRE[1]/g:c05_referencia)'));
        $this->assertSame('02', $xpath->evaluate('string(//g:TDadosGNRE[2]/g:c05_referencia/g:mes)'));
        $this->valida($xml);
    }
}

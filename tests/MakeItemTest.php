<?php

namespace NFePHP\Gnre\Tests;

use DOMDocument;
use DOMXPath;
use NFePHP\Gnre\Exception\RuntimeException;
use NFePHP\Gnre\Make;

class MakeItemTest extends MakeTestCase
{
    /**
     * Lote com os mesmos dados do golden lote-v2-valido-prod.xml
     */
    private function loteDoGolden(): Make
    {
        $make = new Make();
        $make->tagGuia($this->std([
            'ufFavorecida' => 'SP',
            'tipoGnre' => '0',
            'valorGNRE' => 12.52,
            'dataPagamento' => '2015-11-30',
            'identificadorGuia' => '1',
        ]));
        $make->tagEmitente($this->std([
            'CNPJ' => '41819055000105',
            'razaoSocial' => 'GNRE PHP EMITENTE',
            'endereco' => 'Queens St',
            'municipio' => '00108',
            'uf' => 'DF',
            'cep' => '08215917',
            'telefone' => '1199999999',
        ]));
        $make->tagItem($this->std([
            'receita' => '100099',
            'documentoOrigem' => '5656',
            'documentoOrigemTipo' => '10',
            'dataVencimento' => '2015-05-01',
        ]));
        $make->tagReferencia($this->std(['periodo' => '0', 'mes' => '05', 'ano' => '2015', 'parcela' => '2']));
        $make->tagItemValor($this->std(['tipo' => '11', 'valor' => 10.99]));
        $make->tagItemValor($this->std(['tipo' => '21', 'valor' => 10.99]));
        $make->tagDestinatario($this->std([
            'CNPJ' => '86268158000162',
            'IE' => '10809181',
            'razaoSocial' => 'RAZAO SOCIAL GNRE PHP DESTINATARIO',
            'municipio' => '02306',
        ]));
        $make->tagCampoExtra($this->std(['codigo' => '666', 'valor' => 'GNRE']));
        $make->tagCampoExtra($this->std(['codigo' => '111', 'valor' => '2']));
        return $make;
    }

    private function guiaComItem(): Make
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagItem($this->std(['receita' => '100099']));
        return $make;
    }

    public function testIgualAoGoldenDoLegado(): void
    {
        $this->assertSame(
            $this->golden('lote-v2-valido-prod.xml', 'TLote_GNRE'),
            $this->c14n($this->loteDoGolden()->getXML())
        );
    }

    public function testCamposQueOLegadoDescartava(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagEmitente($this->std(['CNPJ' => '41819055000105', 'IE' => '56756']));
        $make->tagItem($this->std([
            'receita' => '100099',
            'detalhamentoReceita' => '101010',
            'documentoOrigem' => '5656',
            'documentoOrigemTipo' => '10',
            'produto' => '1',
            'convenio' => '546456',
        ]));

        $xml = $make->getXML();

        $this->assertStringContainsString('<CNPJ>41819055000105</CNPJ><IE>56756</IE>', $xml);
        $this->assertStringContainsString(
            '<receita>100099</receita><detalhamentoReceita>101010</detalhamentoReceita>'
            . '<documentoOrigem tipo="10">5656</documentoOrigem><produto>1</produto>'
            . '<convenio>546456</convenio>',
            $xml
        );
        $this->valida($xml);
    }

    public function testOrdemDoItemIndependeDaOrdemDasChamadas(): void
    {
        $make = $this->guiaComItem();
        $make->tagCampoExtra($this->std(['codigo' => '1', 'valor' => 'X']));
        $make->tagDestinatario($this->std(['CNPJ' => '86268158000162']));
        $make->tagItemValor($this->std(['tipo' => '11', 'valor' => 1]));
        $make->tagReferencia($this->std(['mes' => '01', 'ano' => '2026']));

        $xml = $make->getXML();

        $posicoes = array_map(function (string $tag) use ($xml) {
            return strpos($xml, $tag);
        }, ['<referencia>', '<valor tipo', '<contribuinteDestinatario>', '<camposExtras>']);
        $ordenadas = $posicoes;
        sort($ordenadas);
        $this->assertSame($ordenadas, $posicoes);
        $this->valida($xml);
    }

    public function testDoisItensEDuasGuias(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagItem($this->std(['receita' => '100099']));
        $make->tagItem($this->std(['receita' => '100102']));
        $make->tagGuia($this->std(['ufFavorecida' => 'RJ']));
        $make->tagItem($this->std(['receita' => '100099']));

        $xml = $make->getXML();

        $this->assertSame(3, substr_count($xml, '<item>'));
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('g', 'http://www.gnre.pe.gov.br');
        $this->assertSame(2.0, $xpath->evaluate('count(//g:TDadosGNRE[1]//g:item)'));
        $this->assertSame(1.0, $xpath->evaluate('count(//g:TDadosGNRE[2]//g:item)'));
        $this->assertSame('100102', $xpath->evaluate('string(//g:TDadosGNRE[1]//g:item[2]/g:receita)'));
        $this->valida($xml);
    }

    public function testValorTipoInvalido(): void
    {
        $make = $this->guiaComItem();
        $make->tagItemValor($this->std(['tipo' => '99', 'valor' => 1]));
        $this->assertNotEmpty($make->getErrors());
    }

    public function testValorComVirgula(): void
    {
        $make = $this->guiaComItem();
        $make->tagItemValor($this->std(['tipo' => '11', 'valor' => '10,99']));
        $this->assertNotEmpty($make->getErrors());
        $this->expectException(RuntimeException::class);
        $make->getXML();
    }

    public function testValorFormatado(): void
    {
        $make = $this->guiaComItem();
        $make->tagItemValor($this->std(['tipo' => '11', 'valor' => 5]));
        $this->assertStringContainsString('<valor tipo="11">5.00</valor>', $make->getXML());
    }

    public function testQuartoCampoExtra(): void
    {
        $make = $this->guiaComItem();
        for ($i = 1; $i <= 4; $i++) {
            $make->tagCampoExtra($this->std(['codigo' => (string) $i, 'valor' => 'V']));
        }
        $this->assertNotEmpty(array_filter($make->getErrors(), function (string $erro) {
            return strpos($erro, 'máximo de 3') !== false;
        }));
    }

    public function testDocumentoOrigemSemTipo(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagItem($this->std(['receita' => '100099', 'documentoOrigem' => '5656']));
        $this->assertNotEmpty($make->getErrors());
    }

    public function testReceitaObrigatoria(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagItem($this->std([]));
        $this->assertNotEmpty($make->getErrors());
    }

    public function testTagsDeItemSemItem(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/tagItem/');
        $make->tagItemValor($this->std(['tipo' => '11', 'valor' => 1]));
    }

    public function testAcentosPreservados(): void
    {
        $make = $this->guiaComItem();
        $make->tagDestinatario($this->std(['CNPJ' => '86268158000162', 'razaoSocial' => 'JOÃO & CIA']));

        $xml = $make->getXML();

        $this->assertStringContainsString('JOÃO &amp; CIA', $this->c14n($xml));
        $this->valida($xml);
    }

    public function testLimiteDeTamanhoComEntidade(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagEmitente($this->std(['CNPJ' => '06157250000116', 'razaoSocial' => str_repeat('A', 57) . '&Cia']));
        $make->tagItem($this->std(['receita' => '100099']));
        $make->tagCampoExtra($this->std(['codigo' => '1', 'valor' => str_repeat('B', 97) . '&xyz']));

        $xml = $make->getXML();
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        $razao = (string) $dom->getElementsByTagName('razaoSocial')->item(0)->textContent;
        $valor = (string) $dom->getElementsByTagName('campoExtra')->item(0)->getElementsByTagName('valor')
            ->item(0)->textContent;

        $this->assertNotSame('', $razao);
        $this->assertLessThanOrEqual(60, mb_strlen($razao));
        $this->assertStringStartsWith(str_repeat('A', 57), $razao);
        $this->assertNotSame('', $valor);
        $this->assertLessThanOrEqual(100, mb_strlen($valor));
        $this->valida($xml);
    }

    public function testValorRepetidoOuNegativo(): void
    {
        $make = new Make();
        $make->tagGuia($this->std(['ufFavorecida' => 'SP']));
        $make->tagItem($this->std(['receita' => '100099']));
        $make->tagItemValor($this->std(['tipo' => '11', 'valor' => 10]));
        $make->tagItemValor($this->std(['tipo' => '11', 'valor' => 5]));
        $make->tagItemValor($this->std(['tipo' => '12', 'valor' => -1]));

        $erros = implode("\n", $make->getErrors());

        $this->assertStringContainsString('tipo 11 repetido', $erros);
        $this->assertStringContainsString('negativo', $erros);
    }
}

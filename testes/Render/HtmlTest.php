<?php

namespace Gnre\Render\Test;

use Gnre\Sefaz\Lote;
use Gnre\Render\Html;

/**
 * @covers \Gnre\Render\Html
 */
class HtmlTest extends \PHPUnit_Framework_TestCase {

    public function testDeveGerarOhtmlComOconteudoDoLote() {
        $this->markTestIncomplete();
        
        $domDocument = new \DOMDocument();
        $domDocument->preserveWhiteSpace = true;
        $domDocument->loadHTMLFile(__DIR__ . '/../../exemplos/guia.html');

        $guia = new \Gnre\Sefaz\Guia();
        $guia->c01_UfFavorecida = 26;
        $guia->c02_receita = 1000099;
        $guia->c25_detalhamentoReceita = 10101010;
        $guia->c26_produto = 'TESTE DE PROD';
        $guia->c27_tipoIdentificacaoEmitente = 1;
        $guia->c03_idContribuinteEmitente = '41819055000105';
        $guia->c28_tipoDocOrigem = 10;
        $guia->c04_docOrigem = 5656;
        $guia->c06_valorPrincipal = 10.99;
        $guia->c10_valorTotal = 12.52;
        $guia->c14_dataVencimento = '2015-05-01';
        $guia->c15_convenio = 546456;
        $guia->c16_razaoSocialEmitente = 'GNRE PHP EMITENTE';
        $guia->c17_inscricaoEstadualEmitente = 56756;
        $guia->c18_enderecoEmitente = 'Queens St';
        $guia->c19_municipioEmitente = 5300108;
        $guia->c20_ufEnderecoEmitente = 'DF';
        $guia->c21_cepEmitente = '08215917';
        $guia->c22_telefoneEmitente = 1199999999;
        $guia->c34_tipoIdentificacaoDestinatario = 1;
        $guia->c35_idContribuinteDestinatario = 86268158000162;
        $guia->c36_inscricaoEstadualDestinatario = 10809181;
        $guia->c37_razaoSocialDestinatario = 'RAZAO SOCIAL GNRE PHP DESTINATARIO';
        $guia->c38_municipioDestinatario = 2702306;
        $guia->c33_dataPagamento = '2015-11-30';
        $guia->retornoCodigoDeBarras = '91910919190191091090109109190109';
        $guia->mes = '05';
        $guia->ano = 2015;
        $guia->parcela = 2;
        $guia->periodo = 2014;

        $lote = new Lote();
        $lote->addGuia($guia);

        $html = new Html();
        $html->create($lote);

        $this->assertXmlStringEqualsXmlString($domDocument->saveXML(), $html->getHtml());
    }

    public function testDeveRetornarUmInstanciaDoBarCode() {
        $this->markTestIncomplete();
    }

}

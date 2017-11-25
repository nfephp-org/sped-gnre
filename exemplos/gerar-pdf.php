<?php

require '../vendor/autoload.php';

use Dompdf\Dompdf;

$guia = new Sped\Gnre\Sefaz\Guia();
$guia->c01_UfFavorecida = 'SP';
$guia->c02_receita = 1000099;
$guia->c25_detalhamentoReceita = 10101010;
$guia->c26_produto = 'TESTE DE PROD';
$guia->c27_tipoIdentificacaoEmitente = 1;
$guia->c03_idContribuinteEmitente = 41819055000105;
$guia->c28_tipoDocOrigem = 10;
$guia->c04_docOrigem = 5656;
$guia->c06_valorPrincipal = 10.99;
$guia->c10_valorTotal = 12.52;
$guia->c14_dataVencimento = '01/05/2015';
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
$guia->retornoInformacoesComplementares = 'teste teste teste';
$guia->retornoAtualizacaoMonetaria = 1.88;
$guia->retornoNumeroDeControle = '0000000000000000';
$guia->retornoCodigoDeBarras = '1118929812912011000000001818181000000001212';
$guia->retornoRepresentacaoNumerica = '11189298129120110000000018181810000000012121201';
$guia->retornoJuros = 2.78;
$guia->retornoMulta = 3.55;
$guia->mes = '05';
$guia->ano = 2015;
$guia->parcela = 2;
$guia->periodo = 2014;

$lote = new Sped\Gnre\Sefaz\Lote();
$lote->addGuia($guia);

$html = new Sped\Gnre\Render\Html();
$html->create($lote);

$pdf = new Sped\Gnre\Render\Pdf();
$pdf->create($html)->stream('gnre.pdf', array('Attachment' => 0));

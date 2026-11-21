<?php

/**
 * Gera os XMLs de referência (golden) a partir do código legado (0.x).
 *
 * Roda somente com o código antigo em lib/ (antes da T1.1). Depois da T1.1 este
 * script vive em legacy/gerar-golden.php apenas como registro de como os
 * arquivos foram produzidos.
 *
 * Uso: php tests/fixtures/legacy/gerar.php
 */

require __DIR__ . '/../../../vendor/autoload.php';

use Sped\Gnre\Sefaz\ConfigUf;
use Sped\Gnre\Sefaz\Consulta;
use Sped\Gnre\Sefaz\Guia;
use Sped\Gnre\Sefaz\Lote;
use Sped\Gnre\Sefaz\LoteV2;

/**
 * Guia com os mesmos valores de exemplos/gerar-xml.php
 */
function guiaExemplo(): Guia
{
    $guia = new Guia();
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
    $guia->mes = '05';
    $guia->ano = 2015;
    $guia->parcela = 2;
    $guia->periodo = 2014;
    $guia->c39_camposExtras = [
        ['campoExtra' => ['codigo' => 666, 'tipo' => 'TXT', 'valor' => 'GNRE']],
        ['campoExtra' => ['codigo' => 111, 'tipo' => 'INT', 'valor' => 'GNRE2']],
    ];
    return $guia;
}

function guiaCpfSemExtras(): Guia
{
    $guia = guiaExemplo();
    $guia->c27_tipoIdentificacaoEmitente = 2;
    $guia->c03_idContribuinteEmitente = '52162389004';
    $guia->c34_tipoIdentificacaoDestinatario = 2;
    $guia->c35_idContribuinteDestinatario = '12345678909';
    $guia->c39_camposExtras = null;
    return $guia;
}

/**
 * Mesma guia, com valores que respeitam os padrões dos XSDs (pacote do zip do manual):
 * receita e detalhamento com 6 dígitos, município IBGE sem a UF (5 dígitos),
 * datas ISO, produto numérico, período 0..5 e tipo de campo extra T/N/D.
 */
function guiaValida(): Guia
{
    $guia = guiaExemplo();
    $guia->c02_receita = '100099';
    $guia->c25_detalhamentoReceita = '101010';
    $guia->c26_produto = '1';
    $guia->c14_dataVencimento = '2015-05-01';
    $guia->c19_municipioEmitente = '00108';
    $guia->c38_municipioDestinatario = '02306';
    $guia->periodo = '0';
    $guia->c39_camposExtras = [
        ['campoExtra' => ['codigo' => 666, 'tipo' => 'T', 'valor' => 'GNRE']],
        ['campoExtra' => ['codigo' => 111, 'tipo' => 'N', 'valor' => '2']],
    ];
    return $guia;
}

function gravar(string $arquivo, string $xml): void
{
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    if (!$dom->loadXML($xml)) {
        fwrite(STDERR, "XML inválido gerado para $arquivo\n");
        exit(1);
    }
    file_put_contents(__DIR__ . '/' . $arquivo, $dom->saveXML());
    echo "gerado: $arquivo\n";
}

$lote = new Lote();
$lote->addGuia(guiaExemplo());
gravar('lote-v1-completo.xml', $lote->toXml());

$lote = new Lote();
$lote->addGuia(guiaCpfSemExtras());
gravar('lote-v1-cpf-sem-extras.xml', $lote->toXml());

$lote = (new LoteV2())->setAmbienteDeTesteV2(false);
$lote->addGuia(guiaExemplo());
gravar('lote-v2-completo-prod.xml', $lote->toXml());

$lote = (new LoteV2())->setAmbienteDeTesteV2(true);
$lote->addGuia(guiaExemplo());
gravar('lote-v2-completo-homolog.xml', $lote->toXml());

$lote = new Lote();
$lote->addGuia(guiaValida());
gravar('lote-v1-valido.xml', $lote->toXml());

$lote = (new LoteV2())->setAmbienteDeTesteV2(false);
$lote->addGuia(guiaValida());
gravar('lote-v2-valido-prod.xml', $lote->toXml());

$consulta = new Consulta();
$consulta->setRecibo('1234567890');
$consulta->setEnvironment(1);
$consulta->utilizarAmbienteDeTeste(false);
gravar('consulta-lote-prod.xml', $consulta->toXml());

$config = new ConfigUf();
$config->setEnvironment(1);
$config->setEstado('PR');
$config->setReceita(100099);
$config->utilizarAmbienteDeTeste(false);
gravar('config-uf-prod.xml', $config->toXml());

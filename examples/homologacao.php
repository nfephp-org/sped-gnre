<?php

/**
 * Roteiro de homologação (docs/HOMOLOGACAO.md): executa os 5 serviços do Portal GNRE no ambiente de
 * HOMOLOGAÇÃO e grava cada requisição e resposta para conferência.
 *
 * Pré-requisitos: examples/config.json (a partir de config.json.dist) e o certificado A1 indicado nele.
 * Nada disso vai para o git (ver .gitignore).
 *
 * php examples/homologacao.php            -> lote 2.00 (Make)
 * php examples/homologacao.php v1-200     -> guia 1.00 dentro do lote 2.00 (MakeV1, FORMATO_LOTE_200)
 * php examples/homologacao.php v1-100     -> lote 1.00 puro (MakeV1, FORMATO_LOTE_100; muda o config para 1.00)
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/dados-lote.php';

use NFePHP\Gnre\Common\Standardize;
use NFePHP\Gnre\MakeConsulta;
use NFePHP\Gnre\MakeV1;
use NFePHP\Gnre\Tools;

$modo = $argv[1] ?? '2.00';
$tools->setEnvironment(2); // sempre homologação
if ($modo === 'v1-100') {
    $tools->version('1.00');
}

$pasta = __DIR__ . '/homologacao-' . date('Ymd-His');
if (!mkdir($pasta) && !is_dir($pasta)) {
    exit("Não foi possível criar a pasta $pasta\n");
}
echo "Modo: $modo | versão do config: {$tools->version()} | saída: $pasta\n\n";

/**
 * Executa um serviço, grava request/response e devolve o stdClass do retorno (ou null)
 */
function executar(Tools $tools, string $pasta, string $nome, callable $chamada): ?stdClass
{
    static $ordem = 0;
    $ordem++;
    $prefixo = sprintf('%s/%02d-%s', $pasta, $ordem, $nome);
    try {
        $resposta = $chamada();
    } catch (\Exception $e) {
        file_put_contents("$prefixo-erro.txt", get_class($e) . ': ' . $e->getMessage());
        echo "[$nome] ERRO ANTES/DURANTE O ENVIO: {$e->getMessage()}\n";
        return null;
    } finally {
        file_put_contents("$prefixo-request.xml", $tools->lastRequest);
        file_put_contents("$prefixo-response.xml", $tools->lastResponse);
    }
    try {
        $std = (new Standardize())->toStd($resposta);
    } catch (\Exception $e) {
        echo "[$nome] resposta não reconhecida (veja $prefixo-response.xml): {$e->getMessage()}\n";
        return null;
    }
    $situacao = $std->situacaoRecepcao ?? $std->situacaoProcess ?? $std->situacaoConsulta ?? null;
    echo sprintf("[%s] %s - %s\n", $nome, $situacao->codigo ?? '?', $situacao->descricao ?? '(sem situação)');
    return $std;
}

// 1. Configuração da UF
executar($tools, $pasta, 'config-uf', function () use ($tools) {
    return $tools->sefazConsultaConfigUf($tools->config->siglaUF);
});

// 2. Envio do lote
$xmlLote = $modo === '2.00' ? montarLoteExemplo()->getXML() : montarLoteV1Exemplo($modo)->getXML();
file_put_contents("$pasta/lote.xml", $xmlLote);
$recibo = executar($tools, $pasta, 'envia-lote', function () use ($tools, $xmlLote) {
    return $tools->sefazEnviaLote($xmlLote);
});
$numeroRecibo = $recibo->recibo->numero ?? '';
if ($numeroRecibo === '') {
    exit("\nSem recibo: confira 02-envia-lote-response.xml e a tabela de códigos do docs/HOMOLOGACAO.md.\n");
}

// 3. Resultado do lote (aguarda o processamento: até 10 tentativas de 15 s)
$resultado = null;
for ($tentativa = 1; $tentativa <= 10; $tentativa++) {
    sleep(15);
    $resultado = executar($tools, $pasta, "resultado-lote-t$tentativa", function () use ($tools, $numeroRecibo) {
        return $tools->sefazConsultaLote($numeroRecibo);
    });
    $codigo = $resultado->situacaoProcess->codigo ?? '';
    if (!in_array($codigo, ['400', '401'], true)) {
        break;
    }
}

// 4 e 5. Consulta de guia pelo código de barras da primeira guia gerada
$guias = $resultado->resultado->guia ?? [];
$guia = is_array($guias) ? ($guias[0] ?? null) : $guias;
$codBarras = is_object($guia) ? (string) ($guia->codigoBarras ?? '') : '';
if ($codBarras === '') {
    exit("\nNenhuma guia com código de barras no resultado: a consulta de guia não foi testada.\n");
}
$consulta = new MakeConsulta();
$consulta->tagConsulta((object) [
    'uf' => $guia->ufFavorecida ?? $tools->config->siglaUF,
    'codBarras' => $codBarras,
    'idConsulta' => '1',
    'tipoConsulta' => 'C',
]);
$reciboConsulta = executar($tools, $pasta, 'envia-lote-consulta', function () use ($tools, $consulta) {
    return $tools->sefazEnviaLoteConsulta($consulta->getXML());
});
$numeroConsulta = $reciboConsulta->recibo->numero ?? '';
if ($numeroConsulta !== '') {
    sleep(15);
    executar($tools, $pasta, 'resultado-consulta', function () use ($tools, $numeroConsulta) {
        return $tools->sefazConsultaLoteConsulta($numeroConsulta);
    });
}
echo "\nConcluído. Anexe a pasta $pasta ao relatório (sem dados sensíveis).\n";

/**
 * Lote 1.00 de exemplo nos dois formatos do MakeV1
 */
function montarLoteV1Exemplo(string $modo): MakeV1
{
    $make = new MakeV1($modo === 'v1-100' ? MakeV1::FORMATO_LOTE_100 : MakeV1::FORMATO_LOTE_200);
    $make->tagGuia((object) [
        'c01_UfFavorecida' => 'SP',
        'c02_receita' => '100099',
        'c27_tipoIdentificacaoEmitente' => '1',
        'c03_idContribuinteEmitente' => '99999999000191',
        'c28_tipoDocOrigem' => '10',
        'c04_docOrigem' => '5656',
        'c06_valorPrincipal' => 12.52,
        'c14_dataVencimento' => date('Y-m-d', strtotime('+5 days')),
        'c16_razaoSocialEmitente' => 'EMPRESA EMITENTE LTDA',
        'c33_dataPagamento' => date('Y-m-d', strtotime('+5 days')),
    ]);
    return $make;
}

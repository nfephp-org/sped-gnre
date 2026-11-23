<?php

/**
 * Guia no layout 1.00 (campos c01_..c42_) nos dois formatos do MakeV1. Só monta e mostra o XML
 * (não envia, não precisa de certificado).
 *
 * - MakeV1::FORMATO_LOTE_200 (padrão): guia 1.00 DENTRO do lote 2.00, formato aceito pelo
 *   pacote oficial de schemas (Manual 2.17). Envie com o config "versao": "2.00".
 * - MakeV1::FORMATO_LOTE_100: lote 1.00 puro, como na 0.x. Envie com o config "versao": "1.00".
 *
 * php examples/make-lote-v1.php            (formato 2.00)
 * php examples/make-lote-v1.php 1.00       (formato 1.00 puro)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use NFePHP\Gnre\MakeV1;

$formato = ($argv[1] ?? '') === '1.00' ? MakeV1::FORMATO_LOTE_100 : MakeV1::FORMATO_LOTE_200;

try {
    $make = new MakeV1($formato);
    $make->tagGuia((object) [
        'c01_UfFavorecida' => 'SP',
        'c02_receita' => '100099',
        'c27_tipoIdentificacaoEmitente' => '1',       // 1 = CNPJ, 2 = CPF
        'c03_idContribuinteEmitente' => '99999999000191',
        'c28_tipoDocOrigem' => '10',
        'c04_docOrigem' => '5656',
        'c06_valorPrincipal' => 12.52,
        'c14_dataVencimento' => '2026-12-15',          // AAAA-MM-DD
        'c16_razaoSocialEmitente' => 'EMPRESA EMITENTE LTDA',
        'c19_municipioEmitente' => '50308',             // IBGE sem os 2 dígitos da UF
        'c20_ufEnderecoEmitente' => 'SP',
        'c33_dataPagamento' => '2026-12-15',
    ]);
    $make->tagReferencia((object) ['periodo' => '0', 'mes' => '11', 'ano' => '2026']);
    $make->tagCampoExtra((object) ['codigo' => '1', 'tipo' => 'T', 'valor' => 'EXEMPLO']); // tipo: T, N ou D

    echo $make->getXML();
} catch (\Exception $e) {
    echo $e->getMessage();
}

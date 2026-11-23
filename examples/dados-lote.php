<?php

/**
 * Monta um lote de exemplo com o Make (uma guia, um item). Usado por make-lote.php e envia-lote.php.
 *
 * Troque os dados pelos da sua operação. As regras de cada UF/receita (documento de origem,
 * campos extras obrigatórios etc.) podem ser consultadas com Tools::sefazConsultaConfigUf().
 */

use NFePHP\Gnre\Common\RegrasUf;
use NFePHP\Gnre\Make;

function montarLoteExemplo(): Make
{
    $make = new Make();

    $make->tagGuia((object) [
        'ufFavorecida' => 'SP',
        'tipoGnre' => '0',              // 0 simples, 1 múltiplos doc. origem, 2 múltiplas receitas
        'valorGNRE' => 12.52,
        'dataPagamento' => '2026-12-15', // AAAA-MM-DD
        'identificadorGuia' => '1',
    ]);

    $make->tagEmitente((object) [
        'CNPJ' => '99999999000191',
        'IE' => '123456789',
        'razaoSocial' => 'EMPRESA EMITENTE LTDA',
        'endereco' => 'RUA EXEMPLO, 100',
        'municipio' => '50308',          // código IBGE SEM os 2 dígitos da UF (5 dígitos)
        'uf' => 'SP',
        'cep' => '01001000',
        'telefone' => '1133334444',
    ]);

    $make->tagItem((object) [
        'receita' => '100099',
        'documentoOrigem' => '5656',
        'documentoOrigemTipo' => RegrasUf::tipoDocOrigem('SP'), // sugestão; confira no ConfigUF
        'dataVencimento' => '2026-12-15',
    ]);
    $make->tagReferencia((object) ['periodo' => '0', 'mes' => '11', 'ano' => '2026']);
    $make->tagItemValor((object) ['tipo' => '11', 'valor' => 12.52]); // 11 = valor principal ICMS
    $make->tagDestinatario((object) [
        'CNPJ' => '86268158000162',
        'IE' => '10809181',
        'razaoSocial' => 'EMPRESA DESTINATARIA LTDA',
        'municipio' => '02306',
    ]);

    return $make;
}

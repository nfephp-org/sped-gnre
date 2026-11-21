<?php

/**
 * Converte um retorno do Portal GNRE em stdClass/JSON (sem rede: usa um retorno de exemplo).
 *
 * Com a Tools, troque o arquivo pelo retorno real:
 *   $retorno = $tools->sefazConsultaLote($recibo);
 *
 * php examples/standardize-retorno.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use NFePHP\Gnre\Common\RetornoTxtV1;
use NFePHP\Gnre\Common\Standardize;

try {
    $retorno = (string) file_get_contents(__DIR__ . '/../tests/fixtures/xml/retorno-resultado-v2.xml');

    $standardize = new Standardize();
    $std = $standardize->toStd($retorno);

    echo "Raiz: {$standardize->key}\n";
    echo "Situação: {$std->situacaoProcess->codigo} - {$std->situacaoProcess->descricao}\n";
    $guias = is_array($std->resultado->guia) ? $std->resultado->guia : [$std->resultado->guia];
    foreach ($guias as $guia) {
        echo "Guia {$guia->identificadorGuia}: linha digitável {$guia->linhaDigitavel}\n";
        $valor = $guia->itensGNRE->item->valor;
        $valores = is_array($valor) ? $valor : [$valor];
        foreach ($valores as $valor) {
            echo "  valor tipo {$valor->attributes->tipo}: {$valor->value}\n";
        }
    }

    // Lote 1.00 puro: o <resultado> vem em TXT posicional
    // $txt = RetornoTxtV1::ler($std->resultado);
} catch (\Exception $e) {
    echo $e->getMessage();
}

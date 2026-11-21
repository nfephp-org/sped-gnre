<?php

/**
 * Monta a guia de DIFAL a partir do XML de uma NF-e autorizada (sem certificado: só monta o XML).
 *
 * A receita e o vencimento são definidos por você (confira as regras da UF com
 * Tools::sefazConsultaConfigUf()). Para enviar: $tools->sefazEnviaLote($make->getXML()).
 *
 * php examples/guia-difal-da-nfe.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use NFePHP\Gnre\Factories\FromNFe;

try {
    $nfe = new FromNFe((string) file_get_contents(__DIR__ . '/../tests/fixtures/xml/nfe-difal.xml'));

    $make = $nfe->guiaDifal((object) [
        'receita' => '100102',          // ex.: ICMS consumidor final não contribuinte outra UF por operação
        'dataVencimento' => '2026-12-15',
    ]);
    // o Make devolvido ainda aceita tags, ex. campos extras exigidos pela UF:
    // $make->tagCampoExtra((object) ['codigo' => '62', 'valor' => 'REFERENTE NF ' . $nfe->numero()]);

    echo $make->getXML();
} catch (\Exception $e) {
    echo $e->getMessage();
}

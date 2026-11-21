<?php

/**
 * Gera o PDF da GNRE (3 vias por guia) a partir do resultado do lote (sem rede: usa um retorno de exemplo).
 *
 * Com a Tools, use o retorno real:
 *   $retorno = $tools->sefazConsultaLote($recibo);
 *
 * php examples/dagnre.php > guia.pdf      (ou abra pelo navegador)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use NFePHP\Gnre\DA\Dagnre;

try {
    $retorno = (string) file_get_contents(__DIR__ . '/../tests/fixtures/xml/retorno-resultado-v2.xml');

    $dagnre = new Dagnre($retorno);
    $dagnre->creditsIntegratorFooter('Minha Empresa de Software');
    // $dagnre->logoParameters(__DIR__ . '/logo.png');   // logo opcional (jpg/png)

    $pdf = $dagnre->render();

    // guias rejeitadas não são impressas; os motivos ficam disponíveis:
    foreach ($dagnre->getGuiasNaoImpressas() as $guia) {
        error_log("Guia não impressa (situação {$guia->situacaoGuia})");
    }

    if (PHP_SAPI !== 'cli') {
        header('Content-Type: application/pdf');
    }
    echo $pdf;
} catch (\Exception $e) {
    echo $e->getMessage();
}

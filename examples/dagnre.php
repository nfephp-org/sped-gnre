<?php

/**
 * Impressão da guia: o PDF da GNRE é gerado pelo nfephp-org/sped-da (NFePHP\DA\GNRE\Dagnre)
 * a partir do retorno de sefazConsultaLote(). Este exemplo usa um retorno de exemplo (sem rede).
 *
 * Requer: composer require nfephp-org/sped-da
 *
 * php examples/dagnre.php > guia.pdf      (ou abra pelo navegador)
 */

require_once __DIR__ . '/../vendor/autoload.php';

if (!class_exists(\NFePHP\DA\GNRE\Dagnre::class)) {
    exit("A impressão da guia fica no sped-da. Instale com: composer require nfephp-org/sped-da\n"
        . "(a classe NFePHP\DA\GNRE\Dagnre está disponível a partir da versão do sped-da com suporte a GNRE)\n");
}

try {
    // com a Tools: $retorno = $tools->sefazConsultaLote($recibo);
    $retorno = (string) file_get_contents(__DIR__ . '/../tests/fixtures/xml/retorno-resultado-v2.xml');

    $dagnre = new \NFePHP\DA\GNRE\Dagnre($retorno);
    $dagnre->creditsIntegratorFooter('Minha Empresa de Software');
    $pdf = $dagnre->render();

    if (PHP_SAPI !== 'cli') {
        header('Content-Type: application/pdf');
    }
    echo $pdf;
} catch (\Exception $e) {
    echo $e->getMessage();
}

<?php

/**
 * Envia um lote de GNRE (serviço GnreLoteRecepcao). O retorno traz o número do recibo,
 * usado depois em consulta-lote.php.
 *
 * Até a fase F3 (classe Make) o XML do lote é montado à mão; aqui usamos o lote de
 * referência com dados válidos de tests/fixtures/legacy/lote-v2-valido-prod.xml.
 */

require_once __DIR__ . '/bootstrap.php';

try {
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->load(__DIR__ . '/../tests/fixtures/legacy/lote-v2-valido-prod.xml');
    $lote = $dom->getElementsByTagNameNS('http://www.gnre.pe.gov.br', 'TLote_GNRE')->item(0);

    mostrar($tools->sefazEnviaLote((string) $lote->C14N(true)));
} catch (\Exception $e) {
    echo $e->getMessage();
}

<?php

/**
 * Monta um lote com o Make e envia (serviço GnreLoteRecepcao).
 * O retorno traz o número do recibo, usado depois em consulta-lote.php.
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/dados-lote.php';

try {
    $xml = montarLoteExemplo()->getXML();
    mostrar($tools->sefazEnviaLote($xml));
} catch (\Exception $e) {
    echo $e->getMessage();
}

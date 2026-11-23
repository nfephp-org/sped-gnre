<?php

/**
 * Só monta e mostra o XML do lote (não envia, não precisa de certificado).
 *
 * php examples/make-lote.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/dados-lote.php';

try {
    echo montarLoteExemplo()->getXML();
} catch (\Exception $e) {
    echo $e->getMessage();
}

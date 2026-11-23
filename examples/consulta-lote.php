<?php

/**
 * Consulta o resultado de um lote enviado (serviço GnreResultadoLote).
 */

require_once __DIR__ . '/bootstrap.php';

try {
    $recibo = '0000000000'; // número do recibo retornado por envia-lote.php

    // opções do layout 2.00: incluir o PDF das guias (base64), o arquivo de pagamento e as notícias
    mostrar($tools->sefazConsultaLote($recibo, ['incluirPDFGuias' => 'S']));
} catch (\Exception $e) {
    echo $e->getMessage();
}

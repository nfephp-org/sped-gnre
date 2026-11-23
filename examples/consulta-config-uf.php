<?php

/**
 * Consulta as configurações de uma UF no Portal GNRE (serviço GnreConfigUF).
 */

require_once __DIR__ . '/bootstrap.php';

try {
    // UF favorecida e, opcionalmente, a receita para filtrar a regra
    mostrar($tools->sefazConsultaConfigUf('PR', '100099'));
} catch (\Exception $e) {
    echo $e->getMessage();
}

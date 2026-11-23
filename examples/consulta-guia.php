<?php

/**
 * Consulta de guias já emitidas (serviços GnreLoteRecepcaoConsulta e GnreResultadoLoteConsulta).
 *
 * 1º passo: monta o lote de consultas com o MakeConsulta, envia e recebe um recibo;
 * 2º passo: com o recibo, consulta o resultado (descomente abaixo).
 *
 * tipoConsulta: C código de barras, N número de controle, D documento de origem,
 * CD/ND código de barras ou número de controle + documento de origem,
 * CR/NR código de barras ou número de controle + receita.
 */

require_once __DIR__ . '/bootstrap.php';

use NFePHP\Gnre\MakeConsulta;

try {
    $consulta = new MakeConsulta();
    $consulta->tagConsulta((object) [
        'uf' => 'SP',
        'CNPJ' => '99999999000191',
        'codBarras' => '85800000000123456789012345678901234567890123',
        'idConsulta' => '1',
        'tipoConsulta' => 'C',
    ]);

    mostrar($tools->sefazEnviaLoteConsulta($consulta->getXML()));

    // $recibo = '0000000000';
    // mostrar($tools->sefazConsultaLoteConsulta($recibo));
} catch (\Exception $e) {
    echo $e->getMessage();
}

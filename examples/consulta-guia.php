<?php

/**
 * Consulta de guias já emitidas (serviços GnreLoteRecepcaoConsulta e GnreResultadoLoteConsulta).
 *
 * 1º passo: envia um lote de consultas e recebe um recibo;
 * 2º passo: com o recibo, consulta o resultado (descomente abaixo).
 *
 * tipoConsulta: C = por código de barras, N = por número de controle, D = por documento de origem.
 */

require_once __DIR__ . '/bootstrap.php';

try {
    $loteConsulta = '<TLote_ConsultaGNRE xmlns="http://www.gnre.pe.gov.br" versao="2.00">'
        . '<consulta>'
        . '<uf>SP</uf>'
        . '<emitenteId><CNPJ>99999999000191</CNPJ></emitenteId>'
        . '<codBarras>85800000000123456789012345678901234567890123</codBarras>'
        . '<idConsulta>1</idConsulta>'
        . '<tipoConsulta>C</tipoConsulta>'
        . '</consulta>'
        . '</TLote_ConsultaGNRE>';

    mostrar($tools->sefazEnviaLoteConsulta($loteConsulta));

    // $recibo = '0000000000';
    // mostrar($tools->sefazConsultaLoteConsulta($recibo));
} catch (\Exception $e) {
    echo $e->getMessage();
}

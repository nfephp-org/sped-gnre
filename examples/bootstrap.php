<?php

/**
 * Bootstrap dos exemplos.
 *
 * 1. copie config.json.dist para config.json e preencha os dados da empresa;
 * 2. coloque o certificado A1 (.pfx) nesta pasta com o nome indicado em "certificado";
 * 3. abra um dos exemplos pelo navegador ou rode: php consulta-config-uf.php
 *
 * config.json e *.pfx estão no .gitignore: nunca versione o seu certificado.
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../vendor/autoload.php';

use NFePHP\Common\Certificate;
use NFePHP\Gnre\Tools;

$arquivoConfig = __DIR__ . '/config.json';
if (!is_file($arquivoConfig)) {
    exit("Crie o arquivo examples/config.json a partir de examples/config.json.dist\n");
}
$config = json_decode((string) file_get_contents($arquivoConfig));

$certificado = Certificate::readPfx(
    (string) file_get_contents(__DIR__ . '/' . $config->certificado),
    $config->senha
);

$tools = new Tools((string) file_get_contents($arquivoConfig), $certificado);

/**
 * Mostra o XML de retorno (navegador) ou imprime no terminal
 */
function mostrar(string $xml): void
{
    if (PHP_SAPI !== 'cli') {
        header('Content-type: text/xml; charset=UTF-8');
    }
    echo $xml;
}

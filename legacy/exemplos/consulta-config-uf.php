<?php

namespace Exemplo;

require '../vendor/autoload.php';

class MySetup extends Sped\Gnre\Configuration\Setup
{

    public function getBaseUrl()
    {
    }

    public function getCertificateCnpj()
    {
    }

    public function getCertificateDirectory()
    {
    }

    public function getCertificateName()
    {
    }

    public function getCertificatePassword()
    {
    }

    public function getCertificatePemFile()
    {
    }

    public function getEnvironment()
    {
    }

    public function getPrivateKey()
    {
    }

    public function getProxyIp()
    {
    }

    public function getProxyPass()
    {
    }

    public function getProxyPort()
    {
    }

    public function getProxyUser()
    {
    }
}

$minhaConfiguracao = new MySetup();

$config = new \Sped\Gnre\Sefaz\ConfigUf;

/**
 * Qual ambiente sera realizada a consulta
 */
$config->setEnvironment(1);
$config->setReceita(100099);
$config->setEstado('PR');

$webService = new Sped\Gnre\Webservice\Connection($minhaConfiguracao, $config->getHeaderSoap(), $config->toXml());

$consulta = $webService->doRequest($config->soapAction());
echo '<pre>';
echo htmlspecialchars($consulta);

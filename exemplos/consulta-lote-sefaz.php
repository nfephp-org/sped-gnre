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

$guia = new Sped\Gnre\Sefaz\Guia();

$consulta = new Sped\Gnre\Sefaz\Consulta();
$consulta->setRecibo(12345123);

/**
 * O número que representa em qual ambiente sera realizada a consulta
 * 1 - produção 2 - homologação
 */
$consulta->setEnvironment(1);
//$consulta->utilizarAmbienteDeTeste(true); //Descomente essa linha para utilizar o ambiente de testes

//header('Content-Type: text/xml');
//print $consulta->toXml(); // exibe o XML da consulta

$webService = new Sped\Gnre\Webservice\Connection($minhaConfiguracao, $consulta->getHeaderSoap(), $consulta->toXml());
echo $webService->doRequest($consulta->soapAction());

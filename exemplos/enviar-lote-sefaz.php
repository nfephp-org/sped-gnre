<?php

require '../vendor/autoload.php';

class MySetup extends Gnre\Configuration\Setup {

    public function getBaseUrl() {
        
    }

    public function getCertificateCnpj() {
        
    }

    public function getCertificateDirectory() {
        
    }

    public function getCertificateName() {
        
    }

    public function getCertificatePassword() {
        
    }

    public function getCertificatePemFile() {
        
    }

    public function getEnvironment() {
        
    }

    public function getPrivateKey() {
        
    }

    public function getProxyIp() {
        
    }

    public function getProxyPass() {
        
    }

    public function getProxyPort() {
        
    }

    public function getProxyUser() {
        
    }

}

$xml = file_get_contents('estrutura-lote-completo-gnre.xml');


$minhaConfiguracao = new MySetup();

$guia = new \Gnre\Sefaz\Guia();

$lote = new \Gnre\Sefaz\Lote();
$lote->addGuia($guia);

$webService = new Gnre\Webservice\Connection($minhaConfiguracao, $lote->getHeaderSoap(), $lote->toXml());
echo $webService->doRequest($lote->soapAction());



<?php

/**
 * Este arquivo é parte do programa GNRE PHP
 * GNRE PHP é um software livre; você pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como 
 * publicada pela Fundação do Software Livre (FSF); na versão 2 da 
 * Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU
 * junto com este programa, se não, escreva para a Fundação do Software
 * Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Sped\Gnre\Webservice;

use Sped\Gnre\Configuration\Setup;
use Sped\Gnre\Sefaz\ObjetoSefaz;
use SoapClient, Log;

class SOAPConnection extends Connection implements ConnectionInterface {
    
    /**
     * Armazena todas as opções desejadas para serem incluídas no soapClient() 
     * @var array 
     */
    private $soapOptions = array();
    
    public function __construct(Setup $setup, ObjetoSefaz $data) {
        parent::__construct($setup, $data);
        $this->setConfig();
    }
    
    /**
     * Retorna as opções definidas para o SOAP
     * @return array
     */
    public function getSoapOptions()
    {
        return $this->soapOptions;
    }

    /**
     * Com esse método é possível adicionar novas opções ou alterar o valor das
     * opções exitentes antes de realizar a requisição para o web service
     * 
     * @param array $option
     * @return \Sped\Gnre\Webservice\Connection
     */
    public function addSoapOptions(array $option)
    {
        foreach ($option as $key => $value) {
            $this->soapOptions[$key] = $value;
        }

        return $this;
    }

    /**
     * Realiza a requisição ao webservice desejado através do soapClient() do php
     * @param  string  $url  String com a URL que será enviada a requisição
     * @since  1.0.0
     * @return string|boolean  Caso a requisição não seja feita com sucesso false caso contrário uma string com XML formatado
     */
    public function doRequest($url)
    {
        try {
            $soap = new SoapClient("{$url}?wsdl", $this->soapOptions);
            $xml = $soap->__doRequest($this->getData()->toXml(), $url, $this->getData()->getAction(), $this->soapOptions["soap_version"]);            
            Log::info($this->getData()->toXml());
            return str_replace("ns1:", "", $xml);
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    public function setConfig() {
        $certificado = $this->getSetup()->getPrivateKey() ? "{$this->getSetup()->getPrivateKey()}\n{$this->getSetup()->getCertificatePemFile()}" : $this->getSetup()->getCertificatePemFile();
        $this->soapOptions = array(
            "encoding" => "UTF-8",
            "verifypeer" => false,
            "verifyhost" => false,
            "soap_version" => SOAP_1_2,
            "style" => SOAP_DOCUMENT,
            "use" => SOAP_LITERAL,
            "local_cert" => $certificado,
            "trace" => true,
            "cache_wsdl" => WSDL_CACHE_NONE,
            "stream_context" => stream_context_create(array(
                "ssl" => array(
                    "verify_peer" => false,
                    "allow_self_signed" => true,
                    "verify_peer_name" => false
                ))
            )
        );
        $ip = $this->getSetup()->getProxyIp();
        $port = $this->getSetup()->getProxyPort();
        if (!empty($ip) && $port) {
            $this->soapOptions['proxy_host'] = $this->getSetup()->getProxyIp();
            $this->soapOptions['proxy_port'] = $this->getSetup()->getProxyPort();
        };
        
        if ($this->getSetup()->getCertificatePassword()) {
            $this->soapOptions['passphrase'] = $this->getSetup()->getCertificatePassword();
        }
    }
}
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

class CURLConnection extends Connection implements ConnectionInterface {
    
    /**
     * Armazena todas as opções desejadas para serem incluídas no curl() 
     * @var array 
     */
    private $curlOptions = array();
    
    public function __construct(Setup $setup, ObjetoSefaz $data) {
        parent::__construct($setup, $data);
        $this->setConfig();
    }
    
    /**
     * Retorna as opções definidas para o curl
     * @return array
     */
    public function getCurlOptions()
    {
        return $this->curlOptions;
    }

    /**
     * Com esse método é possível adicionar novas opções ou alterar o valor das
     * opções exitentes antes de realizar a requisição para o web service, 
     * exemplo de utilização com apenas uma opção:
     * <pre>
     * $connection->addCurlOption(
     * array(
     *       CURLOPT_PORT => 123
     *  )
     * );
     * </pre>
     * Exemplo de utilização com mais de uma opção :
     * <pre>
     * $connection->addCurlOption(
     * array(
     *       CURLOPT_SSLVERSION => 6,
     *       CURLOPT_SSL_VERIFYPEER => 1
     *  )
     * );
     * </pre>
     * 
     * @param array $option
     * @return \Sped\Gnre\Webservice\Connection
     */
    public function addCurlOption(array $option)
    {
        foreach ($option as $key => $value) {
            $this->curlOptions[$key] = $value;
        }

        return $this;
    }

    /**
     * Realiza a requisição ao webservice desejado através do curl() do php
     * @param  string  $url  String com a URL que será enviada a requisição
     * @since  1.0.0
     * @return string|boolean  Caso a requisição não seja feita com sucesso false caso contrário uma string com XML formatado
     */
    public function doRequest($url)
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, $this->curlOptions);
        $ret = curl_exec($curl);

        $n = strlen($ret);
        $x = stripos($ret, "<");
        $xml = substr($ret, $x, $n - $x);

        if ($this->getSetup()->getDebug()) {
            print_r(curl_getinfo($curl));
        }

        if ($xml === false) {
            $xml = curl_error($curl);
        }

        curl_close($curl);

        return $xml;
    }

    public function setConfig() {
        $this->curlOptions = array(
            CURLOPT_PORT => 443,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_SSLVERSION => 3,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSLCERT => $this->getSetup()->getCertificatePemFile(),
            CURLOPT_SSLKEY => $this->getSetup()->getPrivateKey(),
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $this->getData()->toXml(),
            CURLOPT_HTTPHEADER => $this->getData()->getHeaderSoap(),
            CURLOPT_VERBOSE => $this->getSetup()->getDebug(),
        );
        $ip = $this->getSetup()->getProxyIp();
        $port = $this->getSetup()->getProxyPort();
        if (!empty($ip) && $port) {
            $this->curlOptions[CURLOPT_HTTPPROXYTUNNEL] = 1;
            $this->curlOptions[CURLOPT_PROXYTYPE] = 'CURLPROXY_HTTP';
            $this->curlOptions[CURLOPT_PROXY] = $this->getSetup()->getProxyIp() . ':' . $this->getSetup()->getProxyPort();
        };
    }
}
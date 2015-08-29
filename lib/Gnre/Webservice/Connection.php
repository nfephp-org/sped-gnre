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

namespace Gnre\Webservice;

use Gnre\Configuration\Setup;

/**
 * Classe que realiza a conexão com o webservice da SEFAZ com a
 * configuração definida em alguma classe que implementa \Gnre\Configuration\Interfaces\Setup e
 * para o envido das informações é utilizado o curl
 * @package     gnre
 * @subpackage  webservice
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Connection
{

    /**
     * Armazena todas as opções desejadas para serem incluídas no curl() 
     * @var array 
     */
    private $curlOptions = array();

    /**
     * Inicia os parâmetros com o curl para se comunicar com o  webservice da SEFAZ.
     * São setadas a URL de acesso o certificado que será usado e uma série de parâmetros
     * para a header do curl e caso seja usado proxy esse método o adiciona
     * @param  \Gnre\Configuration\Interfaces\Setup $setup
     * @param  $headers  array
     * @param  $data  string
     * @since  1.0.0
     */
    public function __construct(Setup $setup, $headers, $data)
    {
        $this->curlOptions = array(
            CURLOPT_PORT => 443,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_SSLVERSION => 3,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSLCERT => $setup->getCertificatePemFile(),
            CURLOPT_SSLKEY => $setup->getPrivateKey(),
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers
        );

        $ip = $setup->getProxyIp();
        $port = $setup->getProxyPort();

        if (!empty($ip) && $port) {
            $this->curlOptions[CURLOPT_HTTPPROXYTUNNEL] = 1;
            $this->curlOptions[CURLOPT_PROXYTYPE] = 'CURLPROXY_HTTP';
            $this->curlOptions[CURLOPT_PROXY] = $setup->getProxyIp() . ':' . $setup->getProxyPort();
        }
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
     * @return \Gnre\Webservice\Connection
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

        if ($xml === false) {
            $xml = curl_error($curl);
        }

        curl_close($curl);

        return $xml;
    }

}

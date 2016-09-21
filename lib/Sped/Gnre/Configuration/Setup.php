<?php

/**
 * Este arquivo é parte do programa GNRE PHP
 * GNRE PHP é um software livre; você pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como 
 * abstractada pela Fundação do Software Livre (FSF); na versão 2 da 
 * Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU
 * junto com este programa, se não, escreva para a Fundação do Software
 * Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Sped\Gnre\Configuration;

/**
 * Classe abstrata para controlar as propriedades/métodos de uma classe que será
 * a base das configurações. Com isso temos certeza que será enviado as 
 * propriedades necessárias para a comunicação com a sefaz, independentemente da classe.
 * Basta usar essa classe abstrata que tudo deverá funcionar
 * 
 * @package     gnre
 * @subpackage  configuration
 * @abstract
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
abstract class Setup
{

    /**
     * Define o modo de debug, geralmente utilizado para ver dados da requisição e resposta
     * da comunicação com o webservice
     * @var bool
     */
    protected $debug = false;

    /**
     * Método utilizado para retornar o número do ambiente em que se deseja
     * realizar a conexão com o webservice da sefaz 1 - Produção 2 - Homologação
     * @abstract
     * @since  1.0.0
     * @return int
     */
    abstract function getEnvironment();

    /**
     * Método utilizado para retornar o diretório onde se encontram os certificados
     * que seram utilizados
     * @abstract
     * @since  1.0.0
     * @return string 
     */
    abstract function getCertificateDirectory();

    /**
     * Retorna o nome do certificado que será usado junto com sua extenção por exemplo
     * certificado_teste.pfx
     * @abstract
     * @since   1.0.0
     * @return  string 
     */
    abstract function getCertificateName();

    /**
     * Retorna a senha do certificado 
     * @abstract
     * @since   1.0.0
     * @return  string 
     */
    abstract function getCertificatePassword();

    /**
     * Retorna a URL base em que a api se encontra por exemplo http://gnre-api/
     * @abstract
     * @since   1.0.0
     * @return  string 
     */
    abstract function getBaseUrl();

    /**
     * Retorna o CNPJ da empresa em que que realizará a emissão da guia para a sefaz
     * @abstract
     * @since   1.0.0
     * @return  int
     */
    abstract function getCertificateCnpj();

    /**
     * Retorna o IP do proxy caso a API estaja atrás de um por exemplo 192.168.0.1
     * @abstract
     * @since   1.0.0
     * @return  string 
     */
    abstract function getProxyIp();

    /**
     * Retorna a porta do servidor de proxy por exemplo 3128 (squid)
     * @abstract
     * @since   1.0.0
     * @return   int
     */
    abstract function getProxyPort();

    /**
     * Retorna o usuário do servidor de proxy caso seja necessário a indentificação
     * @abstract
     * @since   1.0.0
     * @return  string
     */
    abstract function getProxyUser();

    /**
     * Retorna a senha do usuário do servidor de proxy caso seja necessário a indentificação
     * @abstract
     * @since   1.0.0
     * @return  string
     */
    abstract function getProxyPass();

    /**
     * Método que retorna o caminho e o nome do arquivo privado extraido do certificado por exemplo
     * /var/www/chave_privada.pem
     * @abstract
     * @since   1.0.0
     * @return  string
     */
    abstract function getPrivateKey();

    /**
     * Método que retorna o caminho e o nome do arquivo extraido do certificado por exemplo
     * /var/www/certificado_pem.pem
     * @abstract
     * @since   1.0.0
     * @return  string
     */
    abstract function getCertificatePemFile();

    /**
     * Método utilizado para retornar o modo de debug
     * @return bool
     */
    public function getDebug()
    {
        return $this->debug;
    }
}

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

namespace Sped\Gnre\Sefaz;

use Sped\Gnre\Configuration\Setup;
use Sped\Gnre\Exception\ConnectionFactoryUnavailable;
use Sped\Gnre\Sefaz\ObjetoSefaz;
use Sped\Gnre\Webservice\ConnectionFactory;

/**
 * Classe que realiza o intermediário entre a transformação dos dados(objetos) e a conexão
 * com o webservice da sefaz. Para isso é utilizado o objeto onde foi definido as configurações
 * e alguma classe que implementa a interface ObjectoSefaz (Sped\Gnre\Sefaz\ObjetoSefaz)
 * @package     gnre
 * @subpackage  sefaz
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Send
{

    /**
     * As configuraçoes definidas pelo usuarios que sera utilizada para a
     * transmissao dos dados
     * @var \Sped\Gnre\Configuration\Setup
     */
    private $setup;

    /**
     * Propriedade utilizada para armazenar o objecto de conexão com a SEFAZ
     * @var \Sped\Gnre\Webservice\ConnectionFactory
     */
    private $connectionFactory;

    /**
     * Armazena as configurações padrões em um atributo interno da classe para ser utilizado
     * posteriormente pela classe
     * @param  \Sped\Gnre\Configuration\Setup $setup Configuraçoes definidas pelo usuário
     * @since  1.0.0
     */
    public function __construct(Setup $setup)
    {
        $this->setup = $setup;
    }

    /**
     * Retorna o objeto de conexão com a SEFAZ
     * @return \Sped\Gnre\Webservice\ConnectionFactory
     * @throws \Sped\Gnre\Exception\ConnectionFactoryUnavailable
     */
    public function getConnectionFactory()
    {
        if (!$this->connectionFactory instanceof ConnectionFactory) {
            throw new ConnectionFactoryUnavailable();
        }

        return $this->connectionFactory;
    }

    /**
     * Define um objeto de comunicação com a SEFAZ
     * @param \Sped\Gnre\Webservice\ConnectionFactory $connectionFactory
     * @return \Sped\Gnre\Sefaz\Send
     */
    public function setConnectionFactory(ConnectionFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
        return $this;
    }

    /**
     * Obtém os dados necessários e realiza a conexão com o webservice da sefaz
     * @param  $objetoSefaz  Uma classe que implemente a interface ObjectoSefaz
     * @return string|boolean Caso a conexão seja feita com sucesso retorna um xml válido caso contrário retorna false
     * @since  1.0.0
     */
    public function sefaz(ObjetoSefaz $objetoSefaz)
    {
        $data = $objetoSefaz->toXml();
        $header = $objetoSefaz->getHeaderSoap();

        if ($this->setup->getDebug()) {
            print $data;
        }

        $connection = $this->getConnectionFactory()->createConnection($this->setup, $header, $data);

        return $connection->doRequest($objetoSefaz->soapAction());
    }
}

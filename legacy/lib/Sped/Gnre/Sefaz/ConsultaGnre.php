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

use Sped\Gnre\Sefaz\ObjetoSefaz;

/**
 * Classe que possui os métodos fundamentais para se realizar uma consulta
 * ao webservice da sefaz
 * @package     gnre
 * @subpackage  sefaz
 * @abstract
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
abstract class ConsultaGnre implements ObjetoSefaz
{

    /**
     * O número que representa em qual ambiente sera realizada a consulta
     * 1 - produção 2 - homologação
     * @var int
     */
    private $environment;

    /**
     * O número do recibo enviado apos um lote recebido com sucesso pelo webservice
     * da sefaz geralmente com 10 posições (1406670518)
     * @var int
     */
    private $recibo;

    /**
     * Retorna o número de recibo armazenado no atributo interno da classe
     * @since  1.0.0
     * @return int
     */
    public function getRecibo()
    {
        return $this->recibo;
    }

    /**
     * Define um número de recibo para ser utilizado na consulta ao
     * webservice da sefaz
     * @param  int  $recibo  Número retornado pelo webservice da sefaz após ter recebido um lote com sucesso
     * @since  1.0.0
     */
    public function setRecibo($recibo)
    {
        $this->recibo = $recibo;
    }

    /**
     * Retorna o valor do ambiente armazenado no atributo interno na classe
     * @return  int
     * @since   1.0.0
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Define o ambiente desejado para realizar a consulta no webservice da sefaz
     * @param  int  $environment O número do ambiente que se deseja consultar. 1 = produção e 2 = homologação
     * @since  1.0.0
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }
}

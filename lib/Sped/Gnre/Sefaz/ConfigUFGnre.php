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
abstract class ConfigUFGnre implements ObjetoSefaz
{

    /**
     * Atributo com o nome da ação
     * @var string
     */
    private $action = 'GnreConfigUF';
    
    /**
     * O número que representa em qual ambiente sera realizada a consulta
     * 1 - produção 2 - homologação
     * @var int
     */
    private $environment;

    /**
     * Sigal da UF para a qual devem ser retornadas as regras para envio do
     * lote
     * @var string 
     */
    private $uf;
    
    /**
     * Código da receita para a qual devem ser retornadas as regras para
     * envio do lote. Campo opcional.
     * @var int 
     */
    private $receita;

    /**
     * Retorna a sigla da UF
     * @since  1.0.0
     * @return string
     */
    public function getUF()
    {
        return $this->uf;
    }

    /**
     * Define a sigla da UF para ser utilizada na consulta das regras no 
     * webservice da sefaz
     * @param  string  $uf  Sigla da UF
     * @since  1.0.0
     */
    public function setUF($uf)
    {
        $this->uf = $uf;
    }
    
    /**
     * Retorna o código da receita
     * @since  1.0.0
     * @return int
     */
    public function getReceita()
    {
        return $this->uf;
    }

    /**
     * Define o código da receita para ser utilizada na consulta das regras no 
     * webservice da sefaz
     * @param  int  $receita  Código da receita
     * @since  1.0.0
     */
    public function setReceita($receita)
    {
        $this->receita = $receita;
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
     * @param  int  $environment  O número do ambiente que se deseja consultar número 1 representa produção e 2 representa homologação
     * @since  1.0.0
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }
    
    /**
     * Método utilizado para trocar a ação
     */
    public function setAction($action) {
        $this->action = $action;
    }
    
    /**
     * Método utilizado para retornar a ação
     */
    public function getAction() {
        return $this->action;
    }
}

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

/**
 * Classe que possui os métodos fundamentais para se realizar uma consulta
 * ao webservice da sefaz
 * @package     gnre
 * @subpackage  sefaz
 * @author      Renan Delmonico <renandelmonico@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
abstract class ConsultaConfigUf implements ObjetoSefaz
{
    
    /**
     * O número representa qual ambiente deve ser realizada a consulta
     * 1 - produção 2 - homologação
     * @var int
     */
    private $environment;

    /**
     * UF do estado
     * @var string 
     */
    private $estado;
    
    /**
     * Código da receita
     * @var int
     */
    private $receita;

    /**
     * Retorna a UF que deve ser consultada
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Define a UF que deve ser consultada
     * @param string $uf UF
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Retorna a receita que deve ser consultada
     * @return int
     */
    public function getReceita()
    {
        return $this->receita;
    }

    /**
     * Define a receita que deve ser consultada
     * @param int $receita Código da receita
     */
    public function setReceita($receita)
    {
        $this->receita = $receita;
    }

    /**
     * Retorna em qual ambiente deve ser consultado
     * @return  int
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Define em qual ambiente deve ser consultado
     * @param  int  $environment  O número do ambiente que se deseja consultar. 1 = produção - 2 = homologação
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }
    
}

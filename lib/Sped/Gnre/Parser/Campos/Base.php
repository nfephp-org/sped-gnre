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

namespace Sped\Gnre\Parser\Campos;

/**
 * <p>
 * Classe abstrata que utiliza o padrão de projeto Template Method para 
 * setar as regras de leitura do retorno da SEFAZ 
 * </p>
 * @package     gnre
 * @subpackage  parser
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @see         Sped\Gnre\Parser\Rules
 * @version     1.0.0
 */
class Base extends Campo
{
    private $exigeUfFavorecida = ["campo" => "c01_UfFavorecida", "obrigatorio" => false];
    private $exigeReceita = ["campo" => "c02_receita", "obrigatorio" => false];
    private $receitas = [];
    
    public function getExigeUfFavorecida() {
        return $this->exigeUfFavorecida;
    }

    public function getExigeReceita() {
        return $this->exigeReceita;
    }

    public function getReceitas() {
        return $this->receitas;
    }

    public function setExigeUfFavorecida($exigeUfFavorecida) {
        $this->exigeUfFavorecida = $exigeUfFavorecida;
    }
    
    public function setExigeUfFavorecidaObrigatorio($boolean) {
        $this->exigeUfFavorecida["obrigatorio"] = $boolean;
    }

    public function setExigeReceita($exigeReceita) {
        $this->exigeReceita = $exigeReceita;
    }
    
    public function setExigeReceitaObrigatorio($boolean) {
        $this->exigeReceita["obrigatorio"] = $boolean;
    }

    public function setReceitas($receitas) {
        $this->receitas = $receitas;
    }
    
    public function addReceita($receita) {
        $this->receitas[] = $receita;
    }
    
    public function getReceita($codigo) {
        if (count($this->receitas) > 0) {
            foreach ($this->receitas as $receita) {
                if ($receita->getCodigo() == $codigo) {
                    return $receita;
                }
            }
        }
        return false;
    }
}

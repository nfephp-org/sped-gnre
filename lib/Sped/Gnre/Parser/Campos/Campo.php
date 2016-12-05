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

use Sped\Gnre\Exception\UndefinedProperty;
use JsonSerializable;

/**
 * Classe abstrata que utiliza o padrão de projeto Template Method para 
 * setar as regras de leitura do retorno da SEFAZ
 * 
 * @package     gnre
 * @subpackage  configuration
 * @abstract
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @link        http://en.wikipedia.org/wiki/Template_method_pattern Template Method Design Pattern
 * @version     1.0.0
 */
abstract class Campo implements JsonSerializable{
    
    /**
     * Método mágico utilizado para retornar um valor de um
     * determinado atributo na classe
     * @param  string  $property  Uma propriedade válida dessa classe
     * @throws UndefinedProperty  Caso a propriedade desejada não exista
     * @return string  Caso a propriedade exista retorna o seu valor
     * @since  1.0.0
     */
    public function __get($property)
    {
        if ($this->verifyProperty($property)) {
            return $this->$property;
        }
    }

    /**
     * Método mágico utilizado para setar valores aos atributos 
     * existentes na classe
     * @param  string $property  O nome existente de um atributo existente na classe
     * @param  mixed  $value  O valor desejado para ser setado no atributo desejado (string, boolean, int, Object ou array)
     * @throws UndefinedProperty  Caso o atributo desejada não exista
     * @return boolean Retorna true caso seja setado o valor para o atributo desejado
     * @since  1.0.0
     */
    public function __set($property, $value)
    {
        if ($this->verifyProperty($property)) {
            $this->$property = $value;
            return true;
        }
    }

    /**
     * Método utilizado para verificar se o atributo desejado
     * exista na classe
     * @param  string $property  O nome existente de um atributo existente na classe
     * @return boolean  Retorna true caso o atributo desejado exista na classe
     * @throws UndefinedProperty  Caso o atributo desejada não exista na classe
     * @since  1.0.0
     */
    private function verifyProperty($property)
    {
        if (!property_exists($this, $property)) {
            throw new UndefinedProperty($property);
        }
        return true;
    }
    
    public function jsonSerialize() {
        $getter_names = get_class_methods(get_class($this));
        $gettable_attributes = array();
        foreach ($getter_names as $key => $value) {
            if(substr($value, 0, 3) === 'get') {
                $gettable_attributes[substr($value, 3, strlen($value))] = $this->$value();
            }
        }
        return $gettable_attributes;
    }
}
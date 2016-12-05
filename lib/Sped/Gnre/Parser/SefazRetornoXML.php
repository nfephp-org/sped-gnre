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

namespace Sped\Gnre\Parser;

use DOMDocument;

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
class SefazRetornoXML
{
    private $codigo;
    private $descricao;

    /**
     * Efetua o parse do XML para um array com o retorno
     * do envio de lote e outras consultas
     * @param array $xml
     * @return mixed Retorna um array com as validações e dados ou false caso
     * não consiga pracessar o XML
     */
    public function parse($xml) {
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        
        $situacaoConsulta = $doc->getElementsByTagName('situacaoRecepcao');
        if ($situacaoConsulta->length) {
            $childs = $situacaoConsulta->item(0)->childNodes;
            if ($childs->length > 0) {
                foreach ($childs as $child) {
                    if ($child->nodeName == "codigo") {
                        $this->codigo = $child->nodeValue;
                    }
                    if ($child->nodeName == "descricao") {
                        $this->descricao = $child->nodeValue;
                    }
                }
                return ["codigo" => $this->codigo, "descricao" => $this->descricao];
            }
        }
        return ["codigo" => 0, "descricao" => "Não foi possível processar o retorna da Sefaz."];
    }
}

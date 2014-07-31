<?php

/*
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

namespace Gnre\Sefaz;

use Gnre\Sefaz\Guia;
use Gnre\Sefaz\ObjetoSefaz;

/**
 * Classe que contém os métodos necessários para armazenar as guias em lotes
 * para serem transmitidas através do webservice da sefaz
 * @package     gnre
 * @subpackage  sefaz
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
abstract class LoteGnre implements ObjetoSefaz {

    /**
     * Atributo que armazenará todas as guias desejadas
     * @var array 
     */
    private $guias = array();

    /**
     * Método utilizado para armazenar a guia desejada na classe
     * @param \Gnre\Sefaz\Guia  $guia  Para armazenar uma guia com sucesso é necessário
     * enviar um objeto do tipo Guia
     * @since 1.0.0
     */
    public function addGuia(Guia $guia) {
        $this->guias[] = $guia;
    }

    /**
     * Método utilizado para retornar todas as guias existentes no lote
     * @return array
     * @since  1.0.0
     */
    public function getGuias() {
        return $this->guias;
    }

    /**
     * Método utilizado para retornar uma guia específica existente no lote
     * @return Guia
     * @since  1.0.0
     */
    public function getGuia($index) {
        return $this->guias[$index];
    }

}

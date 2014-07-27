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

namespace Gnre\Exception;

/**
 * Exceção lançada caso não seja possível criar um arquivo ou escrever em 
 * um arquivo existente com o file_put_contentes()
 * @package     gnre
 * @subpackage  exception
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0-0.0
 */
class UnableToWriteFile extends \Exception {

    /**
     * Define uma mensagem padrão caso a exceção seja lançada
     * @param  string  $file O nome do arquivo em que está tentando escrever/criar
     * @since  1.0-0.0
     */
    public function __construct($file) {
        parent::__construct('Não foi possível criar/escrever no arquivo ' . $file, NULL, NULL);
    }

}

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
 * Lança uma exceção caso o arquivo desejado não exista
 * @package     gnre
 * @subpackage  exception
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0-0.0
 */
class UnreachableFile extends \Exception {

    /**
     * Define uma mensagem padrão caso a exceção seja lançada
     * @param  string  $file O nome do arquivo que se deseja utilizar
     * @since  1.0-0.0
     */
    public function __construct($file) {
        parent::__construct('Não foi possível encontrar o arquivo ' . $file, NULL, NULL);
    }

}

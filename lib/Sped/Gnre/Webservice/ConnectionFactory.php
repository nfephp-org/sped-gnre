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

namespace Sped\Gnre\Webservice;

use Sped\Gnre\Configuration\Setup;
use Sped\Gnre\Sefaz\ObjetoSefaz;

/**
 * Factory utilizada para criar um objeto <b>\Sped\Gnre\Webservice\Connection</b>
 * @package     gnre
 * @subpackage  webservice
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class ConnectionFactory
{

    /**
     * Cria um objeto <b>\Sped\Gnre\Webservice\Connection</b>
     * @param \Sped\Gnre\Webservice\Setup $setup
     * @param \Sped\Gnre\Sefaz\ObjetoSefaz $data
     * @return \Sped\Gnre\Webservice\Connection
     */
    public function createConnection(Setup $setup, ObjetoSefaz $data)
    {
        $classe = sprintf(
            '\Sped\Gnre\Webservice\%sConnection',
            $setup->getTipoConexao()
        );

        if (!class_exists($classe)) {
            $classe = '\Sped\Gnre\Webservice\CURLConnection';
        }
        
        return new $classe($setup, $data);
    }

}

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
 * Interface criada para ser implementada pelas classes que desejam
 * enviar seus dados através do webservice da SEFAZ
 * @package     gnre
 * @subpackage  sefaz
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
interface ConnectionInterface
{
    /**
     * Seta a configuração da conexão
     * @since  1.0.0
     * @return null
     */
    public function setConfig();
    
    /**
     * Realiza a requisição ao webservice desejado através do curl() do php
     * @param  string  $url  String com a URL que será enviada a requisição
     * @since  1.0.0
     * @return string|boolean  Caso a requisição não seja feita com sucesso false caso contrário uma string com XML formatado
     */
    public function doRequest($url);
    
    public function getSetup();
    
    public function getData();
}

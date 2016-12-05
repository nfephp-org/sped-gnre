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
 * Classe que realiza a conexão com o webservice da SEFAZ com a
 * configuração definida em alguma classe que implementa \Sped\Gnre\Configuration\Interfaces\Setup e
 * para o envido das informações é utilizado o curl
 * @package     gnre
 * @subpackage  webservice
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Connection
{
    /**
     * @var \Sped\Gnre\Configuration\Setup
     */
    private $setup;
    
    /**
     * @var \Sped\Gnre\Sefaz\ObjetoSefaz
     */
    private $data;
    
    /**
     * Inicia os parâmetros com o curl para se comunicar com o  webservice da SEFAZ.
     * São setadas a URL de acesso o certificado que será usado e uma série de parâmetros
     * para a header do curl e caso seja usado proxy esse método o adiciona
     * @param  \Sped\Gnre\Configuration\Interfaces\Setup $setup
     * @param  \Sped\Gnre\Sefaz\ObjetoSefaz $data
     * @return mixed
     * @since  1.0.0
     */
    public function __construct(Setup $setup, ObjetoSefaz $data)
    {
        $this->setup = $setup;
        $this->data = $data;
    }
    
    public function getSetup() {
        return $this->setup;
    }
    
    public function getData() {
        return $this->data;
    }
}

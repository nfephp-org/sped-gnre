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
 * Interface criada para ser implementada pelas classes que desejam
 * enviar seus dados através do webservice da SEFAZ
 * @package     gnre
 * @subpackage  sefaz
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
interface ObjetoSefaz
{

    /**
     * Retorna em um formato de array os cabeçalhos necessários para a comunicação com o webservice da SEFAZ.
     * Esses cabeçalhos são diferentes para cada tipo de ação no webservice de destino 
     * @since  1.0.0
     * @return array
     */
    public function getHeaderSoap();

    /**
     * Retorna uma string com a ação SOAP que será enviada ao webservice para ser executada
     * @since 1.0.0
     * @return string Retorna uma string com o nome da ação que será executa pelo webservice
     */
    public function soapAction();

    /**
     * Método que transforma o objeto que sera enviado para o webservice em XML (O tipo de dado aceito pelo webservice)
     * @return string  Uma string contendo todo o XML gerado
     * @since  1.0.0
     * @return string Uma string XML contendo um documento XML formatado
     */
    public function toXml();

    /**
     * Método responsável por encapsular todo o XML gerado e encapsula-lo dentro
     * de um envelop SOAP válido para ser enviado
     * @return mixed
     */
    public function getSoapEnvelop($noRaiz, $conteudoEnvelope);

    /**
     * Define se a requisição será realizada no ambiente de testes ou não
     * @param boolen $ambiente Define se será utilizado o ambiente de teste ou não, o padrão é <b>false</b>(para
     * não usar o ambiente de testes)
     * @return mixed
     */
    public function utilizarAmbienteDeTeste($ambiente = false);
    
    /**
     * Método utilizado para trocar a ação
     * @return null
     */
    public function setAction($action);
    
    /**
     * Método utilizado para retornar a ação
     * @return string
     */
    public function getAction();
}

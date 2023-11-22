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
 * Classe utilzada para gerar o envelope SOAP para ser enviado ao web service
 * da SEFAZ para realizar a operação de consulta das configurações da UF.
 * @package     gnre
 * @subpackage  sefaz
 * @author      Renan Delmonico <renandelmonico@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class ConfigUf extends ConsultaConfigUf
{

    /**
     * @var int
     */
    private $ambienteDeTeste = false;

    /**
     * Retorna o header da requisição SOAP
     * @return array
     */
    public function getHeaderSoap()
    {
        $action = $this->ambienteDeTeste ?
            'https://www.testegnre.pe.gov.br/webservice/GnreConfigUF' :
            'https://www.gnre.pe.gov.br/webservice/GnreConfigUF';

        return array(
            'Content-Type: application/soap+xml;charset=utf-8;action="' . $action . '"',
            'SOAPAction: consultar'
        );
    }

    /**
     * Retorna a action da requisição SOAP
     * @return string
     */
    public function soapAction()
    {
        return $this->ambienteDeTeste ?
            'https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF' :
            'https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF';
    }

    /**
     * Retorna o XML que será enviado na requisição SOAP
     * @return string
     */
    public function toXml()
    {
        $gnre = new \DOMDocument('1.0', 'UTF-8');
        $gnre->formatOutput = false;
        $gnre->preserveWhiteSpace = false;

        $consulta = $gnre->createElement('TConsultaConfigUf');
        $consulta->setAttribute('xmlns', 'http://www.gnre.pe.gov.br');

        $ambiente = $gnre->createElement('ambiente', $this->getEnvironment());
        $estado   = $gnre->createElement('uf', $this->getEstado());
        $receita  = $gnre->createElement('receita', $this->getReceita());

        $consulta->appendChild($ambiente);
        $consulta->appendChild($estado);
        $consulta->appendChild($receita);

        $this->getSoapEnvelop($gnre, $consulta);

        return $gnre->saveXML();
    }

    /**
     * Retorna o envelope que sera enviado na requisicao SOAP
     * @return string
     */
    public function getSoapEnvelop($gnre, $consulta)
    {
        $soapEnv = $gnre->createElement('soap12:Envelope');
        $soapEnv->setAttribute('xmlns:soap12', 'http://www.w3.org/2003/05/soap-envelope');
        $soapEnv->setAttribute('xmlns:gnr', 'http://www.gnre.pe.gov.br/webservice/GnreConfigUF');

        $gnreCabecalhoSoap = $gnre->createElement('gnr:gnreCabecMsg');
        $gnreCabecalhoSoap->appendChild($gnre->createElement('gnr:versaoDados', '1.00'));

        $soapHeader = $gnre->createElement('soap12:Header');
        $soapHeader->appendChild($gnreCabecalhoSoap);

        $soapEnv->appendChild($soapHeader);
        $gnre->appendChild($soapEnv);

        $gnreDadosMsg = $gnre->createElement('gnr:gnreDadosMsg');
        $gnreDadosMsg->appendChild($consulta);

        $soapBody = $gnre->createElement('soap12:Body');
        $soapBody->appendChild($gnreDadosMsg);

        $soapEnv->appendChild($soapBody);
    }

    /**
     * Define se será utilizado o ambiente de testes ou não
     * @param boolean $ambiente Ambiente
     */
    public function utilizarAmbienteDeTeste($ambiente = false)
    {
        $this->ambienteDeTeste = $ambiente;
    }
}

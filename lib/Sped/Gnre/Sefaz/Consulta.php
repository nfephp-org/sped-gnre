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

use Sped\Gnre\Sefaz\ConsultaGnre;

/**
 * Classe utilzada para gerar o envelope SOAP para ser enviado ao web service
 * da SEFAZ para realizar a operação de consulta.
 * @package     gnre
 * @subpackage  sefaz
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Consulta extends ConsultaGnre
{

    /**
     * @var bool
     */
    private $ambienteDeTeste = false;

    /**
     * {@inheritdoc}
     */
    public function getHeaderSoap()
    {
        $action = $this->ambienteDeTeste ?
            'http://www.testegnre.pe.gov.br/webservice/GnreResultadoLote' :
            'http://www.gnre.pe.gov.br/webservice/GnreResultadoLote';

        return array(
            'Content-Type: application/soap+xml;charset=utf-8;action="' . $action . '"',
            'SOAPAction: consultar'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function soapAction()
    {
        return $this->ambienteDeTeste ?
            'http://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote' :
            'https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLote';
    }

    /**
     * {@inheritdoc}
     */
    public function toXml()
    {
        $gnre = new \DOMDocument('1.0', 'UTF-8');
        $gnre->formatOutput = false;
        $gnre->preserveWhiteSpace = false;

        $consulta = $gnre->createElement('TConsLote_GNRE');
        $consulta->setAttribute('xmlns', 'http://www.gnre.pe.gov.br');

        $ambiente = $gnre->createElement('ambiente', $this->getEnvironment());
        $numeroRecibo = $gnre->createElement('numeroRecibo', $this->getRecibo());

        $consulta->appendChild($ambiente);
        $consulta->appendChild($numeroRecibo);

        $this->getSoapEnvelop($gnre, $consulta);

        return $gnre->saveXML();
    }

    /**
     * {@inheritdoc}
     */
    public function getSoapEnvelop($gnre, $consulta)
    {
        $soapEnv = $gnre->createElement('soap12:Envelope');
        $soapEnv->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $soapEnv->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $soapEnv->setAttribute('xmlns:soap12', 'http://www.w3.org/2003/05/soap-envelope');

        $gnreCabecalhoSoap = $gnre->createElement('gnreCabecMsg');
        $gnreCabecalhoSoap->setAttribute('xmlns', 'http://www.gnre.pe.gov.br/wsdl/consultar');
        $gnreCabecalhoSoap->appendChild($gnre->createElement('versaoDados', '1.00'));

        $soapHeader = $gnre->createElement('soap12:Header');
        $soapHeader->appendChild($gnreCabecalhoSoap);

        $soapEnv->appendChild($soapHeader);
        $gnre->appendChild($soapEnv);

        $action = $this->ambienteDeTeste ?
            'http://www.testegnre.pe.gov.br/webservice/GnreResultadoLote' :
            'http://www.gnre.pe.gov.br/webservice/GnreResultadoLote';

        $gnreDadosMsg = $gnre->createElement('gnreDadosMsg');
        $gnreDadosMsg->setAttribute('xmlns', $action);

        $gnreDadosMsg->appendChild($consulta);

        $soapBody = $gnre->createElement('soap12:Body');
        $soapBody->appendChild($gnreDadosMsg);

        $soapEnv->appendChild($soapBody);
    }

    /**
     * {@inheritdoc}
     */
    public function utilizarAmbienteDeTeste($ambiente = false)
    {
        $this->ambienteDeTeste = $ambiente;
    }
}

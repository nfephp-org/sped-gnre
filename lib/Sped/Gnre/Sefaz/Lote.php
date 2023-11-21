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

use Sped\Gnre\Sefaz\LoteGnre;
use Sped\Gnre\Sefaz\EstadoFactory;

/**
 * Classe que armazena uma ou mais Guias (\Sped\Gnre\Sefaz\Guia) para serem
 * transmitidas. Não é possível transmitir uma simples guia em um formato unitário, para que seja transmitida
 * com sucesso a guia deve estar dentro de um lote (\Sped\Gnre\Sefaz\Lote).
 * @package     gnre
 * @subpackage  sefaz
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Lote extends LoteGnre
{

    /**
     * @var \Sped\Gnre\Sefaz\EstadoFactory
     */
    private $estadoFactory;

    /**
     * @var bool
     */
    private $ambienteDeTeste = false;

    /**
     * @return mixed
     */
    public function getEstadoFactory()
    {
        if (null === $this->estadoFactory) {
            $this->estadoFactory = new EstadoFactory();
        }

        return $this->estadoFactory;
    }

    /**
     * @param mixed $estadoFactory
     * @return Lote
     */
    public function setEstadoFactory(EstadoFactory $estadoFactory)
    {
        $this->estadoFactory = $estadoFactory;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderSoap()
    {
        $action = $this->ambienteDeTeste ?
            'http://www.testegnre.pe.gov.br/webservice/GnreLoteRecepcao' :
            'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao';

        return array(
            'Content-Type: application/soap+xml;charset=utf-8;action="' . $action . '"',
            'SOAPAction: processar'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function soapAction()
    {
        return $this->ambienteDeTeste ?
            'https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao' :
            'https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao';
    }

    /**
     * {@inheritdoc}
     */
    public function toXml()
    {
        $gnre = new \DOMDocument('1.0', 'UTF-8');
        $gnre->formatOutput = false;
        $gnre->preserveWhiteSpace = false;

        $loteGnre = $gnre->createElement('TLote_GNRE');
        $loteXmlns = $gnre->createAttribute('xmlns');
        $loteXmlns->value = 'http://www.gnre.pe.gov.br';
        $loteGnre->appendChild($loteXmlns);
        $guia = $gnre->createElement('guias');

        foreach ($this->getGuias() as $gnreGuia) {
            $estado = $gnreGuia->c01_UfFavorecida;

            $guiaEstado = $this->getEstadoFactory()->create($estado);

            $dados = $gnre->createElement('TDadosGNRE');
            $c1 = $gnre->createElement('c01_UfFavorecida', $estado);
            $c2 = $gnre->createElement('c02_receita', $gnreGuia->c02_receita);
            $c25 = $gnre->createElement('c25_detalhamentoReceita', $gnreGuia->c25_detalhamentoReceita);
            $c26 = $gnre->createElement('c26_produto', $gnreGuia->c26_produto);
            $c27 = $gnre->createElement('c27_tipoIdentificacaoEmitente', $gnreGuia->c27_tipoIdentificacaoEmitente);

            $c03 = $gnre->createElement('c03_idContribuinteEmitente');

            if ($gnreGuia->c27_tipoIdentificacaoEmitente == parent::EMITENTE_PESSOA_JURIDICA) {
                $emitenteContribuinteDocumento = $gnre->createElement('CNPJ', $gnreGuia->c03_idContribuinteEmitente);
            } else {
                $emitenteContribuinteDocumento = $gnre->createElement('CPF', $gnreGuia->c03_idContribuinteEmitente);
            }

            $c03->appendChild($emitenteContribuinteDocumento);

            $c28 = $gnre->createElement('c28_tipoDocOrigem', $gnreGuia->c28_tipoDocOrigem);
            $c04 = $gnre->createElement('c04_docOrigem', $gnreGuia->c04_docOrigem);
            if ($gnreGuia->c06_valorPrincipal) {
                $c06 = $gnre->createElement('c06_valorPrincipal', $gnreGuia->c06_valorPrincipal);
            }
            if ($gnreGuia->c10_valorTotal) {
                $c10 = $gnre->createElement('c10_valorTotal', $gnreGuia->c10_valorTotal);
            }
            $c14 = $gnre->createElement('c14_dataVencimento', $gnreGuia->c14_dataVencimento);
            $c15 = $gnre->createElement('c15_convenio', $gnreGuia->c15_convenio);
            $c16 = $gnre->createElement('c16_razaoSocialEmitente', $gnreGuia->c16_razaoSocialEmitente);
            if ($gnreGuia->c17_inscricaoEstadualEmitente) {
                $c17 = $gnre->createElement('c17_inscricaoEstadualEmitente', $gnreGuia->c17_inscricaoEstadualEmitente);
            }
            $c18 = $gnre->createElement('c18_enderecoEmitente', $gnreGuia->c18_enderecoEmitente);
            $c19 = $gnre->createElement('c19_municipioEmitente', $gnreGuia->c19_municipioEmitente);
            $c20 = $gnre->createElement('c20_ufEnderecoEmitente', $gnreGuia->c20_ufEnderecoEmitente);
            if ($gnreGuia->c21_cepEmitente) {
                $c21 = $gnre->createElement('c21_cepEmitente', $gnreGuia->c21_cepEmitente);
            }
            if ($gnreGuia->c22_telefoneEmitente) {
                $c22 = $gnre->createElement('c22_telefoneEmitente', $gnreGuia->c22_telefoneEmitente);
            }

            $c34_tipoIdentificacaoDestinatario = $gnreGuia->c34_tipoIdentificacaoDestinatario;
            $c34 = $gnre->createElement('c34_tipoIdentificacaoDestinatario', $c34_tipoIdentificacaoDestinatario);

            $c35 = $gnre->createElement('c35_idContribuinteDestinatario');

            $c35_idContribuinteDestinatario = $gnreGuia->c35_idContribuinteDestinatario;
            if ($gnreGuia->c34_tipoIdentificacaoDestinatario == parent::DESTINATARIO_PESSOA_JURIDICA) {
                $destinatarioContribuinteDocumento = $gnre->createElement('CNPJ', $c35_idContribuinteDestinatario);
            } else {
                $destinatarioContribuinteDocumento = $gnre->createElement('CPF', $c35_idContribuinteDestinatario);
            }

            $c35->appendChild($destinatarioContribuinteDocumento);

            $c36_inscricaoEstadualDestinatario = $gnreGuia->c36_inscricaoEstadualDestinatario;
            $c36 = $gnre->createElement('c36_inscricaoEstadualDestinatario', $c36_inscricaoEstadualDestinatario);
            $c37 = $gnre->createElement('c37_razaoSocialDestinatario', $gnreGuia->c37_razaoSocialDestinatario);
            $c38 = $gnre->createElement('c38_municipioDestinatario', $gnreGuia->c38_municipioDestinatario);
            $c33 = $gnre->createElement('c33_dataPagamento', $gnreGuia->c33_dataPagamento);

            $dados->appendChild($c1);
            $dados->appendChild($c2);
            if ($gnreGuia->c25_detalhamentoReceita) {
                $dados->appendChild($c25);
            }
            if ($gnreGuia->c26_produto) {
                $dados->appendChild($c26);
            }
            $dados->appendChild($c27);
            $dados->appendChild($c03);
            $dados->appendChild($c28);
            $dados->appendChild($c04);
            if (isset($c06)) {
                $dados->appendChild($c06);
            }
            if (isset($c10)) {
                $dados->appendChild($c10);
            }
            $dados->appendChild($c14);
            if ($gnreGuia->c15_convenio) {
                $dados->appendChild($c15);
            }
            $dados->appendChild($c16);
            if ($gnreGuia->c17_inscricaoEstadualEmitente) {
                $dados->appendChild($c17);
            }
            $dados->appendChild($c18);
            $dados->appendChild($c19);
            $dados->appendChild($c20);
            if (isset($c21)) {
                $dados->appendChild($c21);
            }
            if (isset($c22)) {
                $dados->appendChild($c22);
            }
            if ($c34_tipoIdentificacaoDestinatario) {
                $dados->appendChild($c34);
            }
            if ($c35_idContribuinteDestinatario) {
                $dados->appendChild($c35);
            }
            if ($gnreGuia->c36_inscricaoEstadualDestinatario) {
                $dados->appendChild($c36);
            }
            if ($gnreGuia->c37_razaoSocialDestinatario) {
                $dados->appendChild($c37);
            }
            if ($gnreGuia->c38_municipioDestinatario) {
                $dados->appendChild($c38);
            }
            $dados->appendChild($c33);

            $c05 = $guiaEstado->getNodeReferencia($gnre, $gnreGuia);
            if ($c05) {
                $dados->appendChild($c05);
            }

            $c39_camposExtras = $guiaEstado->getNodeCamposExtras($gnre, $gnreGuia);

            if ($c39_camposExtras != null) {
                $dados->appendChild($c39_camposExtras);
            }

            $guia->appendChild($dados);
            $gnre->appendChild($loteGnre);
            $loteGnre->appendChild($guia);
        }

        $this->getSoapEnvelop($gnre, $loteGnre);

        return $gnre->saveXML();
    }

    /**
     * {@inheritdoc}
     */
    public function getSoapEnvelop($gnre, $loteGnre)
    {
        $soapEnv = $gnre->createElement('soap12:Envelope');
        $soapEnv->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $soapEnv->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $soapEnv->setAttribute('xmlns:soap12', 'http://www.w3.org/2003/05/soap-envelope');

        $gnreCabecalhoSoap = $gnre->createElement('gnreCabecMsg');
        $gnreCabecalhoSoap->setAttribute('xmlns', 'http://www.gnre.pe.gov.br/wsdl/processar');
        $gnreCabecalhoSoap->appendChild($gnre->createElement('versaoDados', '1.00'));

        $soapHeader = $gnre->createElement('soap12:Header');
        $soapHeader->appendChild($gnreCabecalhoSoap);

        $soapEnv->appendChild($soapHeader);
        $gnre->appendChild($soapEnv);

        $action = $this->ambienteDeTeste ?
            'http://www.testegnre.pe.gov.br/webservice/GnreLoteRecepcao' :
            'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao';

        $gnreDadosMsg = $gnre->createElement('gnreDadosMsg');
        $gnreDadosMsg->setAttribute('xmlns', $action);

        $gnreDadosMsg->appendChild($loteGnre);

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

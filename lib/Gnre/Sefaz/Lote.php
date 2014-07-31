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

use Gnre\Sefaz\LoteGnre;

/**
 * Classe que armazena uma ou mais Guias (\Gnre\Sefaz\Guia) para serem 
 * transmitidas. Não é possível transmitir uma simples guia em um formato unitário, para que seja transmitida
 * com sucesso a guia deve estar dentro de um lote (\Gnre\Sefaz\Lote).
 * @package     gnre
 * @subpackage  sefaz
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Lote extends LoteGnre {

    /**
     * {@inheritdoc}
     */
    public function getHeaderSoap() {
        return array(
            'Content-Type: application/soap+xml;charset=utf-8;action="http://www.gnre.pe.gov.br/webservice/GnreRecepcaoLote"',
            'SOAPAction: processar'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function soapAction() {
        return 'https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao';
    }

    /**
     * {@inheritdoc}
     */
    public function toXml() {
        $gnre = new \DOMDocument('1.0', 'UTF-8');
        $gnre->formatOutput = false;
        $gnre->preserveWhiteSpace = false;

        $loteGnre = $gnre->createElement('TLote_GNRE');
        $loteXmlns = $gnre->createAttribute('xmlns');
        $loteXmlns->value = 'http://www.gnre.pe.gov.br';
        $loteGnre->appendChild($loteXmlns);
        $guia = $gnre->createElement('guias');

        foreach ($this->getGuias() as $gnreGuia) {
            $dados = $gnre->createElement('TDadosGNRE');
            $c1 = $gnre->createElement('c01_UfFavorecida', $gnreGuia->c01_UfFavorecida);
            $c2 = $gnre->createElement('c02_receita', $gnreGuia->c02_receita);
            $c25 = $gnre->createElement('c25_detalhamentoReceita', $gnreGuia->c25_detalhamentoReceita);
            $c26 = $gnre->createElement('c26_produto', $gnreGuia->c26_produto);
            $c27 = $gnre->createElement('c27_tipoIdentificacaoEmitente', $gnreGuia->c27_tipoIdentificacaoEmitente);

            $c03 = $gnre->createElement('c03_idContribuinteEmitente');
            if ($gnreGuia->c27_tipoIdentificacaoEmitente == 1) {
                $emitenteContribuinteDocumento = $gnre->createElement('CNPJ', $gnreGuia->c03_idContribuinteEmitente);
            } else {
                $emitenteContribuinteDocumento = $gnre->createElement('CPF', $gnreGuia->c03_idContribuinteEmitente);
            }
            $c03->appendChild($emitenteContribuinteDocumento);

            $c28 = $gnre->createElement('c28_tipoDocOrigem', $gnreGuia->c28_tipoDocOrigem);
            $c04 = $gnre->createElement('c04_docOrigem', $gnreGuia->c04_docOrigem);
            $c06 = $gnre->createElement('c06_valorPrincipal', $gnreGuia->c06_valorPrincipal);
            $c10 = $gnre->createElement('c10_valorTotal', $gnreGuia->c10_valorTotal);
            $c14 = $gnre->createElement('c14_dataVencimento', $gnreGuia->c14_dataVencimento);
            $c15 = $gnre->createElement('c15_convenio', $gnreGuia->c15_convenio);
            $c16 = $gnre->createElement('c16_razaoSocialEmitente', $gnreGuia->c16_razaoSocialEmitente);
            $c17 = $gnre->createElement('c17_inscricaoEstadualEmitente', $gnreGuia->c17_inscricaoEstadualEmitente);
            $c18 = $gnre->createElement('c18_enderecoEmitente', $gnreGuia->c18_enderecoEmitente);
            $c19 = $gnre->createElement('c19_municipioEmitente', $gnreGuia->c19_municipioEmitente);
            $c20 = $gnre->createElement('c20_ufEnderecoEmitente', $gnreGuia->c20_ufEnderecoEmitente);
            $c21 = $gnre->createElement('c21_cepEmitente', $gnreGuia->c21_cepEmitente);
            $c22 = $gnre->createElement('c22_telefoneEmitente', $gnreGuia->c22_telefoneEmitente);
            $c34 = $gnre->createElement('c34_tipoIdentificacaoDestinatario', $gnreGuia->c34_tipoIdentificacaoDestinatario);

            $c35 = $gnre->createElement('c35_idContribuinteDestinatario');
            if ($gnreGuia->c34_tipoIdentificacaoDestinatario == 1) {
                $destinatarioContribuinteDocumento = $gnre->createElement('CNPJ', $gnreGuia->c35_idContribuinteDestinatario);
            } else {
                $destinatarioContribuinteDocumento = $gnre->createElement('CPF', $gnreGuia->c35_idContribuinteDestinatario);
            }
            $c35->appendChild($destinatarioContribuinteDocumento);

            $c36 = $gnre->createElement('c36_inscricaoEstadualDestinatario', $gnreGuia->c36_inscricaoEstadualDestinatario);
            $c37 = $gnre->createElement('c37_razaoSocialDestinatario', $gnreGuia->c37_razaoSocialDestinatario);
            $c38 = $gnre->createElement('c38_municipioDestinatario', $gnreGuia->c38_municipioDestinatario);
            $c33 = $gnre->createElement('c33_dataPagamento', $gnreGuia->c33_dataPagamento);
            $c05 = $gnre->createElement('c05_referencia');

            $periodo = $gnre->createElement('periodo', $gnreGuia->periodo);
            $mes = $gnre->createElement('mes', $gnreGuia->mes);
            $ano = $gnre->createElement('ano', $gnreGuia->ano);
            $parcela = $gnre->createElement('parcela', $gnreGuia->parcela);

            $c05->appendChild($periodo);
            $c05->appendChild($mes);
            $c05->appendChild($ano);
            $c05->appendChild($parcela);


            if (is_array($gnreGuia->c39_camposExtras) && count($gnreGuia->c39_camposExtras) > 0) {
                $c39_camposExtras = $gnre->createElement('c39_camposExtras');

                foreach ($gnreGuia->c39_camposExtras as $key => $campos) {
                    $campoExtra = $gnre->createElement('campoExtra');
                    $codigo = $gnre->createElement('codigo', $campos['campoExtra']['codigo']);
                    $tipo = $gnre->createElement('tipo', $campos['campoExtra']['tipo']);
                    $valor = $gnre->createElement('valor', $campos['campoExtra']['valor']);

                    $campoExtra->appendChild($codigo);
                    $campoExtra->appendChild($tipo);
                    $campoExtra->appendChild($valor);

                    $c39_camposExtras->appendChild($campoExtra);
                }
            }

            $dados->appendChild($c1);
            $dados->appendChild($c2);
            $dados->appendChild($c25);
            $dados->appendChild($c26);
            $dados->appendChild($c27);
            $dados->appendChild($c03);
            $dados->appendChild($c28);
            $dados->appendChild($c04);
            $dados->appendChild($c06);
            $dados->appendChild($c10);
            $dados->appendChild($c14);
            $dados->appendChild($c15);
            $dados->appendChild($c16);
            $dados->appendChild($c17);
            $dados->appendChild($c18);
            $dados->appendChild($c19);
            $dados->appendChild($c20);
            $dados->appendChild($c21);
            $dados->appendChild($c22);
            $dados->appendChild($c34);
            $dados->appendChild($c35);
            $dados->appendChild($c36);
            $dados->appendChild($c37);
            $dados->appendChild($c38);
            $dados->appendChild($c33);
            $dados->appendChild($c05);

            if (isset($c39_camposExtras)) {
                $dados->appendChild($c39_camposExtras);
            }

            $guia->appendChild($dados);
            $gnre->appendChild($loteGnre);
            $loteGnre->appendChild($guia);
        }

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

        $gnreDadosMsg = $gnre->createElement('gnreDadosMsg');
        $gnreDadosMsg->setAttribute('xmlns', 'http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao');

        $gnreDadosMsg->appendChild($loteGnre);

        $soapBody = $gnre->createElement('soap12:Body');
        $soapBody->appendChild($gnreDadosMsg);

        $soapEnv->appendChild($soapBody);

        return $gnre->saveXML();
    }

}

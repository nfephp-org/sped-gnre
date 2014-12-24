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

namespace Gnre\Render;

use Gnre\Sefaz\Lote;

/**
 * Classe que contém a estrutura para gerar o pdf da guia de pagamento.
 * @package     gnre
 * @subpackage  pdf
 * @author      Leandro Pereira <llpereiras@gmail.com>
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Html {

    /**
     * @var string
     */
    private $html;

    /**
     * Utiliza o lote como parâmetro para transforma-lo em uma guia HTML
     * 
     * @param \Gnre\Sefaz\Lote $lote
     * @link https://github.com/marabesi/gnrephp/blob/dev-pdf/exemplos/guia.jpg <p>
     * Exemplo de como é transformado o objeto <b>\Gnre\Sefaz\Lote</b> após ser
     * utilizado por esse método</p>
     * @since 1.0.0
     */
    public function render(Lote $lote) {
        $guiaViaInfo = array(
            1 => '1ª via Banco',
            2 => '2ª via Contrinuinte',
            3 => '3ª via Contribuinte/Fisco'
        );

        $guias = $lote->getGuias();

        for ($index = 0; $index < count($guias); $index++) {
            $guia = $lote->getGuia($index);

            $html = <<<ABC
                <html>
                    <meta charset="UTF-8">
                <style type="text/css">
                    @page { 
                        margin: 5px;
                        padding:0px;
                    }
                    body{
                        margin:5px;
                        padding:0px;
                        font-size: 0.54rem;
                    }
                    table tr td{
                        border: 1px solid #000;
                    }
                    .columnone{
                        width: 500px;
                    }
                    .gnre{
                        font-size: 14px;
                        height:25px;
                        font-weight:bold;
                        text-align: center;
                    }
                    .noborder{
                        border-top: 0px;
                        border-bottom: 0px;
                        border-left: 0px;
                        border-right: 0px;
                    }
                    .center{
                        text-align: center;
                    }
                    .nobrdtb{
                        border-top: 0px;
                        border-bottom: 0px;
                    }
                    .noleft{
                        border-left: 0px;
                    }

                    .nobottom{
                        border-bottom: 0px;
                    }
                    .notop{
                        border-top: 0px;
                    }

                    .noright{
                        border-right: 0px;
                    }

                    .borderleft{
                        border-top: 0px;
                        border-bottom: 0px;
                        border-right: 0px;
                    }

                    .borderbottom{
                        border-top: 0px;
                        border-left: 0px;
                        border-right: 0px;
                    }

                    .borderright{
                        border-top: 0px;
                        border-bottom: 0px;
                        border-left: 0px;
                    }

                </style>
                    <body>
ABC;
            foreach ($guiaViaInfo as $key => $via) {
                $html.= <<<ABC
                        <table cellspacing="0" cellpadding="1" style="width:100%">
                            <tr>
                                <td style="width: 65%;" valign="top" class="noborder">
                                    <table cellspacing="0" cellpadding="1" style="width:100%">
                                        <tr>
                                            <td class="columnone gnre" colspan="2">
                                                Guia Nacional de Recolhimento de Tributos Estaduais - GNRE
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="center nobrdtb" colspan="2">
                                                Dados do emitente
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="borderleft">
                                                Razão Social
                                            </td>
                                            <td class="borderright" style="width: 50px">
                                                CNPJ/CPF/Insc. Est.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="borderleft">
                                                $guia->c16_razaoSocialEmitente
                                            </td>
                                            <td class="borderright">
                                                $guia->c03_idContribuinteEmitente
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="notop nobottom" colspan="2">
                                                Endereço: $guia->c18_enderecoEmitente
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="borderleft">
                                                Município: $guia->c19_municipioEmitente
                                            </td>
                                            <td class="borderright">
                                                UF: SP
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="noright notop">
                                                CEP: $guia->c21_cepEmitente
                                            </td>
                                            <td class="noleft notop">
                                                DDD/Telefone: $guia->c22_telefoneEmitente
                                            </td>
                                        </tr>
                                        <tr >
                                            <td class="center nobrdtb" colspan="2">
                                                Dados do Destinatário
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="notop nobottom" colspan="2">
                                                CNPJ/CPF/Insc. Est.: $guia->c35_idContribuinteDestinatario
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="notop" colspan="2">
                                                Município: $guia->c38_municipioDestinatario
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="center nobrdtb" colspan="2">
                                                Informações à Fiscalização
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="notop nobottom" colspan="2">
                                                Convênio/Protocolo: $guia->c15_convenio
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="notop" colspan="2">
                                                Produto: $guia->c26_produto
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="nobrdtb" colspan="2" style="height:64px" valign="top">
                                                Informações Complementares
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="notop" colspan="2">
                                                Documento válido para pagamento até $guia->c14_dataVencimento
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="noborder" valign="top">
                                    <table cellspacing="0" cellpadding="1" style="width:100%">
                                        <tr>
                                            <td class="nobottom">UF Favorecida</td>
                                            <td style="width: 120px" colspan="2" class="nobottom">Código da Receita</td>
                                        </tr>
                                        <tr>
                                            <td class="center notop">$guia->c01_UfFavorecida</td>
                                            <td class="center notop" colspan="2">$guia->c02_receita</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="nobottom">Nº de Controle</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="notop">$guia->c02_receita</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="nobottom">Data de Vencimento</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="notop">$guia->c14_dataVencimento</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="nobottom">Nº do Documento de Origem</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="notop">$guia->c04_docOrigem</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="nobottom">Período de Referência</td>
                                            <td class="center nobottom">Nº Parcela</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="notop">$guia->mes / $guia->ano</td>
                                            <td class="center notop">$guia->parcela</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="nobottom">Valor Principal</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="notop">R$ $guia->c06_valorPrincipal</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="nobottom">Atualização Monetária</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="center notop">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="nobottom">Juros</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="notop">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="nobottom">Multa</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="notop">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="nobottom">Total a Recolher</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="notop">$guia->c10_valorTotal</td>
                                        </tr>
                                        <tr>
                                            <td class="noborder">&nbsp;</td>
                                            <td class="noborder" colspan="2" style="text-align:right;">$via</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="noborder" style="padding-left:30px;height:5px">
                                    0000000000000 0 000000000000000 0 00000000000000 0 000000000000000 0
                                </td>
                            </tr>
                            <tr>
                                <td class="noborder" style="padding-left:10px;" >
                                    <p>Código de barras</p>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <hr style="margin-top:0px;border: 1px dotted #000; border-style: none none dotted;">
ABC;
            }
            $html .= "</body></html>";
            $this->html = $html;
        }
    }

    public function toPrint() {
        return $this->html;
    }

}

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
namespace Gnre\Pdf; 

use Gnre\Sefaz\Lote;

/**
 * Classe que contém a estrutura para gerar o pdf da guia de pagamento.
 * @package     gnre
 * @subpackage  pdf
 * @author      Leandro Pereira <llpereiras@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */

class Render{

public function render(Lote $lote)
{
	$guias = $lote->getGuias();
	for ($index = 0; $index < count($guias) -1 ; $index++) { 
		$guia = $lote->getGuias($index);
	}
	// Instancia objeto Gnre ou Guia com todos os itens do lote xml a ser impressao
	// Ex: gnre->c03_idContribuinteEmitente = 'Informe a identificação do contribuinte emitente!'
	// $gnre = new stdClass();
	// $gnre->c01_UfFavorecida = 'AL';
	// $gnre->c02_receita = '123456';
	// $gnre->c03_idContribuinteEmitente = '00.000.000/0000-00';
	// $gnre->c16_razaoSocialEmitente = 'Razão Teste da empresa GNRE';
	// $gnre->c16_razaoSocialEmitente = 'Razão Teste da empresa GNRE';
	$gnre = $lote;
	// disable DOMPDF's internal autoloader if you are using Composer
	define('DOMPDF_ENABLE_AUTOLOAD', true);
		
	// // include DOMPDF's default configuration
	require_once './vendor/dompdf/dompdf/dompdf_config.inc.php';
	require_once('./vendor/barcode/barcode.inc.php'); 
	// $barcode = new barCodeGenrator('125689365472365458',0,$barcode, 190, 130, false);
	$timestamp = microtime().'_'.time().'.gif';
	new \barCodeGenrator('9378182074207247247023742303223',true,$timestamp);

	$guiaViaInfo = array(1 => '1ª via Banco',
						 2 => '2ª via Contrinuinte',
						 3 => '3ª via Contribuinte/Fisco',);

	$guia = array(
		 0 => $guiaViaInfo, 
		 1 => $guiaViaInfo, 
		 2 => $guiaViaInfo, 
		 3 => $guiaViaInfo, 
		);

	$html = <<<ABC
		<html>
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
				// foreach ($guiaViaInfo as $key => $via) {
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
											$gnre->c16_razaoSocialEmitente
										</td>
										<td class="borderright">
											$gnre->c03_idContribuinteEmitente
										</td>
									</tr>
									<tr>
										<td class="notop nobottom" colspan="2">
											Endereço: Rua 1, teste
										</td>
									</tr>
									<tr>
										<td class="borderleft">
											Município: São Paulo
										</td>
										<td class="borderright">
											UF: SP
										</td>
									</tr>
									<tr>
										<td class="noright notop">
											CEP: 00000-000
										</td>
										<td class="noleft notop">
											DDD/Telefone: (11) 1234-1234
										</td>
									</tr>
									<tr >
										<td class="center nobrdtb" colspan="2">
											Dados do Destinatário
										</td>
									</tr>
									<tr>
										<td class="notop nobottom" colspan="2">
											CNPJ/CPF/Insc. Est.: 00.000.000/0000-00
										</td>
									</tr>
									<tr>
										<td class="notop" colspan="2">
											Município: São Paulo
										</td>
									</tr>
									<tr>
										<td class="center nobrdtb" colspan="2">
											Informações à Fiscalização
										</td>
									</tr>
									<tr>
										<td class="notop nobottom" colspan="2">
											Convênio/Protocolo: 8367846723
										</td>
									</tr>
									<tr>
										<td class="notop" colspan="2">
											Produto: Serviços XXX Produto YYY
										</td>
									</tr>
									<tr>
										<td class="nobrdtb" colspan="2" style="height:64px" valign="top">
											Informações Complementares
										</td>
									</tr>
									<tr>
										<td class="notop" colspan="2">
											Documento válido para pagamento até 00/00/0000
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
										<td class="center notop">$gnre->c01_UfFavorecida</td>
										<td class="center notop" colspan="2">$gnre->c02_receita</td>
									</tr>
									<tr>
										<td colspan="3" class="nobottom">Nº de Controle</td>
									</tr>
									<tr>
										<td colspan="3" class="notop">$gnre->c02_receita</td>
									</tr>
									<tr>
										<td colspan="3" class="nobottom">Data de Vencimento</td>
									</tr>
									<tr>
										<td colspan="3" class="notop">00/00/0000</td>
									</tr>
									<tr>
										<td colspan="3" class="nobottom">Nº do Documento de Origem</td>
									</tr>
									<tr>
										<td colspan="3" class="notop">123456</td>
									</tr>
									<tr>
										<td colspan="2" class="nobottom">Período de Referência</td>
										<td class="center nobottom">Nº Parcela</td>
									</tr>
									<tr>
										<td colspan="2" class="notop">11/2014</td>
										<td class="center notop">1</td>
									</tr>
									<tr>
										<td colspan="3" class="nobottom">Valor Principal</td>
									</tr>
									<tr>
										<td colspan="3" class="notop">R$ 19.292,00</td>
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
										<td colspan="3" class="notop">&nbsp;</td>
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
								<img src="$timestamp" style="height:40px;width:300px;" >
							</td>
						</tr>
					</table>
					<br>
					<hr style="margin-top:0px;border: 1px dotted #000; border-style: none none dotted;">
ABC;
				}
	$html .= <<<ABC
			</body>
		</html>
ABC;
	// echo $html;
	// die();
	$dompdf = new \DOMPDF();
	$dompdf->load_html($html);
	$dompdf->render();
	$dompdf->stream('gnre.pdf',array('Attachment' => 0));
	unlink($timestamp);

	}
}

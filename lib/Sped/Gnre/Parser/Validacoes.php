<?php

/**
 * Este arquivo é parte do programa GNRE PHP
 * GNRE PHP é um software livre; você pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como 
 * abstractada pela Fundação do Software Livre (FSF); na versão 2 da 
 * Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU
 * junto com este programa, se não, escreva para a Fundação do Software
 * Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Sped\Gnre\Parser;

use DOMDocument;

/**
 * Classe abstrata que utiliza o padrão de projeto Template Method para 
 * setar as regras de leitura do retorno da SEFAZ
 * 
 * @package     gnre
 * @subpackage  configuration
 * @abstract
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @link        http://en.wikipedia.org/wiki/Template_method_pattern Template Method Design Pattern
 * @version     1.0.0
 */
class Validacoes
{
    /**
     * Efetua o parse do XML para um array com as validações
     * de cada campo para o estado
     * @param array $xml
     * @return mixed Retorna um array com as validações e dados ou false caso
     * não consiga pracessar o XML
     */
    public function parse($xml) {
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        
        $situacaoConsulta = $doc->getElementsByTagName('situacaoConsulta');
        if ($situacaoConsulta->length) {
            $childs = $situacaoConsulta->item(0)->childNodes;
            if ($childs->length > 0) {
                foreach ($childs as $child) {
                    if ($child->nodeName == "codigo") {
                        if ($child->nodeValue == 450) {
                            $base = new \Sped\Gnre\Parser\Campos\Base();
                            $base->setExigeUfFavorecidaObrigatorio($this->converteTxtToBoolean($doc->getElementsByTagName("exigeUfFavorecida")->item(0)->nodeValue));
                            $base->setExigeReceitaObrigatorio($this->converteTxtToBoolean($doc->getElementsByTagName("exigeReceita")->item(0)->nodeValue));
                            
                            $receitas = $doc->getElementsByTagName("receitas");
                            foreach ($receitas->item(0)->childNodes as $receitaXML) {
                                $receita = new \Sped\Gnre\Parser\Campos\Receita();
                                $receita->setCodigo($receitaXML->getAttribute("codigo"));
                                
                                $receita->setDescricao($receitaXML->getAttribute("descricao"));
                                if ($receitaXML->getAttribute("codigo") == "100056" && $receitaXML->getAttribute("courier") == "S") {
                                    $receita->setDescricao($receitaXML->getAttribute("descricao") . " - Courier");
                                }
                                
                                $receita->setExigeContribuinteEmitente($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeContribuinteEmitente")->item(0)->nodeValue));
                                $receita->setExigeContribuinteDestinatario($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeContribuinteEmitente")->item(0)->nodeValue));
                                $receita->setExigeDetalhamentoReceitaObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeDetalhamentoReceita")->item(0)->nodeValue));
                                $receita->setExigeDocumentoOrigemObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeDocumentoOrigem")->item(0)->nodeValue));
                                $receita->setExigePeriodoReferenciaObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigePeriodoReferencia")->item(0)->nodeValue));
                                $receita->setValorExigidoObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("valorExigido")->item(0)->nodeValue));
                                $receita->setExigeDataVencimentoObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeDataVencimento")->item(0)->nodeValue));
                                $receita->setExigeConvenioObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeConvenio")->item(0)->nodeValue));
                                $receita->setExigeProdutoObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeProduto")->item(0)->nodeValue));
                                $receita->setExigeDataPagamentoObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeDataPagamento")->item(0)->nodeValue));
                                $receita->setExigeCamposAdicionaisObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeCamposAdicionais")->item(0)->nodeValue));
                                if ($receitaXML->getElementsByTagName("exigePeriodoApuracao")->length) {
                                    $receita->setExigePeriodoApuracaoObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigePeriodoApuracao")->item(0)->nodeValue));
                                }
                                $receita->setExigeParcelaObrigatorio($this->converteTxtToBoolean($receitaXML->getElementsByTagName("exigeParcela")->item(0)->nodeValue));
                                
                                if ($receitaXML->getElementsByTagName("detalhamentosReceita")->length) {
                                    foreach ($receitaXML->getElementsByTagName("detalhamentosReceita")->item(0)->childNodes as $detalhamentosReceita) {
                                        $detalhamentoReceita = new \Sped\Gnre\Parser\Campos\DetalhamentoReceita();
                                        $detalhamentoReceita->setCodigo($detalhamentosReceita->getElementsByTagName("codigo")->item(0)->nodeValue);
                                        $detalhamentoReceita->setDescricao($detalhamentosReceita->getElementsByTagName("descricao")->item(0)->nodeValue);
                                        $receita->addDetalhamentoReceita($detalhamentoReceita);
                                    }
                                }
                                
                                if ($receitaXML->getElementsByTagName("tiposDocumentosOrigem")->length) {
                                    foreach ($receitaXML->getElementsByTagName("tiposDocumentosOrigem")->item(0)->childNodes as $tiposDocumentoOrigem) {
                                        $tipoDocumentoOrigem = new \Sped\Gnre\Parser\Campos\TipoDocumentoOrigem();
                                        $tipoDocumentoOrigem->setCodigo($tiposDocumentoOrigem->getElementsByTagName("codigo")->item(0)->nodeValue);
                                        $tipoDocumentoOrigem->setDescricao($tiposDocumentoOrigem->getElementsByTagName("descricao")->item(0)->nodeValue);
                                        $receita->addTipoDocumentoOrigem($tipoDocumentoOrigem);
                                    }
                                }
                                
                                if ($receitaXML->getElementsByTagName("periodosApuracao")->length) {
                                    foreach ($receitaXML->getElementsByTagName("periodosApuracao")->item(0)->childNodes as $periodosApuracao) {
                                        $periodoApuracao = new \Sped\Gnre\Parser\Campos\PeriodoApuracao();
                                        $periodoApuracao->setCodigo($periodosApuracao->getElementsByTagName("codigo")->item(0)->nodeValue);
                                        $periodoApuracao->setDescricao($periodosApuracao->getElementsByTagName("descricao")->item(0)->nodeValue);
                                        $receita->addPeriodoApuracao($periodoApuracao);
                                    }
                                }
                                
                                if ($receitaXML->getElementsByTagName("produtos")->length) {
                                    foreach ($receitaXML->getElementsByTagName("produtos")->item(0)->childNodes as $produtos) {
                                        $produto = new \Sped\Gnre\Parser\Campos\Produto();
                                        $produto->setCodigo($produtos->getElementsByTagName("codigo")->item(0)->nodeValue);
                                        $produto->setDescricao($produtos->getElementsByTagName("descricao")->item(0)->nodeValue);
                                        $receita->addProduto($produto);
                                    }
                                }
                                
                                if ($receitaXML->getElementsByTagName("camposAdicionais")->length) {
                                    foreach ($receitaXML->getElementsByTagName("camposAdicionais")->item(0)->childNodes as $camposAdicionais) {
                                        $campoAdicional = new \Sped\Gnre\Parser\Campos\CampoAdicional();
                                        $campoAdicional->setObrigatorio($this->converteTxtToBoolean($camposAdicionais->getElementsByTagName("obrigatorio")->item(0)->nodeValue));
                                        $campoAdicional->setCodigo($camposAdicionais->getElementsByTagName("codigo")->item(0)->nodeValue);
                                        $campoAdicional->setTipo($camposAdicionais->getElementsByTagName("tipo")->item(0)->nodeValue);
                                        $campoAdicional->setTamanho($camposAdicionais->getElementsByTagName("tamanho")->item(0)->nodeValue);
                                        $campoAdicional->setTitulo($camposAdicionais->getElementsByTagName("titulo")->item(0)->nodeValue);
                                        $receita->addCampoAdicional($campoAdicional);
                                    }
                                }
                                $base->addReceita($receita);
                            }
                            return $base;
                        }
                    }
                }
            }
        }
        return false;
    }
    
    private function converteTxtToBoolean($string) {
        return $string === "S" ? 1 : 0;
    }
}

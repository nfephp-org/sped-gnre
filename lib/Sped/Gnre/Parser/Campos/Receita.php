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

namespace Sped\Gnre\Parser\Campos;

use Sped\Gnre\Parser\Campos\DetalhamentoReceita;
use Sped\Gnre\Parser\Campos\TipoDocumentoOrigem;
use Sped\Gnre\Parser\Campos\PeriodoApuracao;
use Sped\Gnre\Parser\Campos\Produto;
use Sped\Gnre\Parser\Campos\CampoAdicional;

/**
 * <p>
 * Classe abstrata que utiliza o padrão de projeto Template Method para 
 * setar as regras de leitura do retorno da SEFAZ 
 * </p>
 * @package     gnre
 * @subpackage  parser
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @see         Sped\Gnre\Parser\Rules
 * @version     1.0.0
 */
class Receita extends Campo
{
    private $codigo = 0;
    private $descricao = "";
    private $exigeContribuinteEmitente = false;
    private $exigeContribuinteDestinatario = false;
    private $exigeDocumentoOrigem = ["campo" => "c04_docOrigem", "obrigatorio" => false];
    private $exigePeriodoReferencia = ["campo" => "c05_referencia", "obrigatorio" => false];
    private $valorExigido = ["campo" => "c06_valorPrincipal", "obrigatorio" => false];
    private $exigeDataVencimento = ["campo" => "c14_dataVencimento", "obrigatorio" => false];
    private $exigeConvenio = ["campo" => "c15_convenio", "obrigatorio" => false];
    private $exigeDetalhamentoReceita = ["campo" => "c25_detalhamentoReceita", "obrigatorio" => false];
    private $exigeProduto = ["campo" => "c26_produto", "obrigatorio" => false];
    private $exigeDataPagamento = ["campo" => "c33_dataPagamento", "obrigatorio" => false];
    private $exigeCamposAdicionais = ["campo" => "c39_camposExtras", "obrigatorio" => false];
    private $exigePeriodoApuracao = ["campo" => "periodo", "obrigatorio" => false];
    private $exigeParcela = ["campo" => "parcela", "obrigatorio" => false];
    
    private $detalhamentosReceita = [];
    private $tiposDocumentosOrigem = [];
    private $periodosApuracao = [];
    private $produtos = [];
    private $camposAdicionais = [];
    
    public function getCodigo() {
        return $this->codigo;
    }

    public function getDescricao() {
        return $this->descricao;
    }
    
    public function getDetalhamentosReceita() {
        return $this->detalhamentosReceita;
    }

    public function getExigeContribuinteEmitente() {
        return $this->exigeContribuinteEmitente;
    }

    public function getExigeContribuinteDestinatario() {
        return $this->exigeContribuinteDestinatario;
    }

    public function getExigeDocumentoOrigem() {
        return $this->exigeDocumentoOrigem;
    }

    public function getExigePeriodoReferencia() {
        return $this->exigePeriodoReferencia;
    }

    public function getValorExigido() {
        return $this->valorExigido;
    }

    public function getExigeDataVencimento() {
        return $this->exigeDataVencimento;
    }

    public function getExigeConvenio() {
        return $this->exigeConvenio;
    }

    public function getExigeDetalhamentoReceita() {
        return $this->exigeDetalhamentoReceita;
    }

    public function getExigeProduto() {
        return $this->exigeProduto;
    }

    public function getExigeDataPagamento() {
        return $this->exigeDataPagamento;
    }

    public function getExigeCamposAdicionais() {
        return $this->exigeCamposAdicionais;
    }

    public function getExigePeriodoApuracao() {
        return $this->exigePeriodoApuracao;
    }

    public function getExigeParcela() {
        return $this->exigeParcela;
    }

    public function getPeriodosApuracao() {
        return $this->periodosApuracao;
    }

    public function getProdutos() {
        return $this->produtos;
    }

    public function getTiposDocumentosOrigem() {
        return $this->tiposDocumentosOrigem;
    }

    public function getCamposAdicionais() {
        return $this->camposAdicionais;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }
    
    public function setDetalhamentosReceita($detalhamentosReceita) {
        $this->detalhamentosReceita = $detalhamentosReceita;
    }

    public function setExigeContribuinteEmitente($exigeContribuinteEmitente) {
        $this->exigeContribuinteEmitente = $exigeContribuinteEmitente;
    }

    public function setExigeContribuinteDestinatario($exigeContribuinteDestinatario) {
        $this->exigeContribuinteDestinatario = $exigeContribuinteDestinatario;
    }

    public function setExigeDocumentoOrigem($exigeDocumentoOrigem) {
        $this->exigeDocumentoOrigem = $exigeDocumentoOrigem;
    }
    
    public function setExigeDocumentoOrigemObrigatorio($boolean) {
        $this->exigeDocumentoOrigem["obrigatorio"] = $boolean;
    }

    public function setExigePeriodoReferencia($exigePeriodoReferencia) {
        $this->exigePeriodoReferencia = $exigePeriodoReferencia;
    }
    
    public function setExigePeriodoReferenciaObrigatorio($boolean) {
        $this->exigePeriodoReferencia["obrigatorio"] = $boolean;
    }

    public function setValorExigido($valorExigido) {
        $this->valorExigido = $valorExigido;
    }
    
    public function setValorExigidoObrigatorio($boolean) {
        $this->valorExigido["obrigatorio"] = $boolean;
    }

    public function setExigeDataVencimento($exigeDataVencimento) {
        $this->exigeDataVencimento = $exigeDataVencimento;
    }
    
    public function setExigeDataVencimentoObrigatorio($boolean) {
        $this->exigeDataVencimento["obrigatorio"] = $boolean;
    }

    public function setExigeConvenio($exigeConvenio) {
        $this->exigeConvenio = $exigeConvenio;
    }
    
    public function setExigeConvenioObrigatorio($boolean) {
        $this->exigeConvenio["obrigatorio"] = $boolean;
    }

    public function setExigeDetalhamentoReceita($exigeDetalhamentoReceita) {
        $this->exigeDetalhamentoReceita = $exigeDetalhamentoReceita;
    }
    
    public function setExigeDetalhamentoReceitaObrigatorio($boolean) {
        $this->exigeDetalhamentoReceita["obrigatorio"] = $boolean;
    }

    public function setExigeProduto($exigeProduto) {
        $this->exigeProduto = $exigeProduto;
    }
    
    public function setExigeProdutoObrigatorio($boolean) {
        $this->exigeProduto["obrigatorio"] = $boolean;
    }

    public function setExigeDataPagamento($exigeDataPagamento) {
        $this->exigeDataPagamento = $exigeDataPagamento;
    }
    
    public function setExigeDataPagamentoObrigatorio($boolean) {
        $this->exigeDataPagamento["obrigatorio"] = $boolean;
    }

    public function setExigeCamposAdicionais($exigeCamposAdicionais) {
        $this->exigeCamposAdicionais = $exigeCamposAdicionais;
    }
    
    public function setExigeCamposAdicionaisObrigatorio($boolean) {
        $this->exigeCamposAdicionais["obrigatorio"] = $boolean;
    }

    public function setExigePeriodoApuracao($exigePeriodoApuracao) {
        $this->exigePeriodoApuracao = $exigePeriodoApuracao;
    }
    
    public function setExigePeriodoApuracaoObrigatorio($boolean) {
        $this->exigePeriodoApuracao["obrigatorio"] = $boolean;
    }

    public function setExigeParcela($exigeParcela) {
        $this->exigeParcela = $exigeParcela;
    }
    
    public function setExigeParcelaObrigatorio($boolean) {
        $this->exigeParcela["obrigatorio"] = $boolean;
    }

    public function setPeriodosApuracao($periodosApuracao) {
        $this->periodosApuracao = $periodosApuracao;
    }

    public function setProdutos($produtos) {
        $this->produtos = $produtos;
    }

    public function setTiposDocumentosOrigem($tiposDocumentosOrigem) {
        $this->tiposDocumentosOrigem = $tiposDocumentosOrigem;
    }

    public function setCamposAdicionais($camposAdicionais) {
        $this->camposAdicionais = $camposAdicionais;
    }
    
    public function addDetalhamentoReceita(DetalhamentoReceita $detalhamentoReceita) {
        $this->detalhamentosReceita[] = $detalhamentoReceita;
    }
    
    public function addTipoDocumentoOrigem(TipoDocumentoOrigem $tipoDocumentoOrigem) {
        $this->tiposDocumentosOrigem[] = $tipoDocumentoOrigem;
    }
    
    public function addPeriodoApuracao(PeriodoApuracao $periodoApuracao) {
        $this->periodosApuracao[] = $periodoApuracao;
    }
    
    public function addProduto(Produto $produto) {
        $this->produtos[] = $produto;
    }
    
    public function addCampoAdicional(CampoAdicional $campoAdicional) {
        $this->camposAdicionais[] = $campoAdicional;
    }
}

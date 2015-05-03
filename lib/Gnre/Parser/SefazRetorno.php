<?php

/*
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

namespace Gnre\Parser;

use Gnre\Parser\Rules;

/**
 * <p>
 * Classe utilizada para extrair os dados do web service da SEFAZ, como o retorno
 * é um conteúdo posicional utilizamos aqui o template method, ou seja,
 * essa classe transforma o arquivo posicional em um objeto manipulável pela
 * API
 * </p>
 * @package     gnre
 * @subpackage  parser
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @see         Gnre\Parser\Rules
 * @version     1.0.0
 */
class SefazRetorno extends Rules {

    /**
     * {@inheritdoc}
     */
    public function __construct($dadosArquivo) {
        parent::__construct($dadosArquivo);
    }

    protected function getIdentificador() {
        $this->getContent($this->dadosArquivo[$this->index], 0, 1);

        $this->identificador = $this->getContent($this->dadosArquivo[$this->index], 0, 1);
    }

    protected function getTipoIdentificadorDoSolicitante() {
        $this->lote['header']['tipoIdentificadorSolicitante'] = $this->getContent($this->dadosArquivo[$this->index], 1, 1);
    }

    protected function getIdentificadorDoSolicitante() {
        $this->lote['header']['identificadorSolicitante'] = $this->getContent($this->dadosArquivo[$this->index], 2, 14);
    }

    protected function getNumeroDoProtocoloDoLote() {
        $this->lote['header']['numeroProtocoloLote'] = $this->getContent($this->dadosArquivo[$this->index], 16, 10);
    }

    protected function getAmbiente() {
        $this->lote['header']['ambiente'] = $this->getContent($this->dadosArquivo[$this->index], 26, 1);
    }

    protected function getUfFavorecida() {
        $this->lote['lote'][$this->index]->c01_UfFavorecida = $this->getContent($this->dadosArquivo[$this->index], 6, 2);
    }

    protected function getCodigoReceita() {
        $this->lote['lote'][$this->index]->c02_receita = $this->getContent($this->dadosArquivo[$this->index], 8, 6);
    }

    protected function getTipoEmitente() {
        $this->lote['lote'][$this->index]->c27_tipoIdentificacaoEmitente = $this->getContent($this->dadosArquivo[$this->index], 14, 1);
    }

    protected function getDocumentoEmitente() {
        $this->lote['lote'][$this->index]->c03_idContribuinteEmitente = $this->getContent($this->dadosArquivo[$this->index], 15, 16);
    }

    protected function getRazaoSocialEmitente() {
        $this->lote['lote'][$this->index]->c16_razaoSocialEmitente = $this->getContent($this->dadosArquivo[$this->index], 31, 60);
    }

    protected function getEnderecoEmitente() {
        $this->lote['lote'][$this->index]->c18_enderecoEmitente = $this->getContent($this->dadosArquivo[$this->index], 91, 60);
    }

    protected function getMunicipioEmitente() {
        $this->lote['lote'][$this->index]->c19_municipioEmitente = $this->getContent($this->dadosArquivo[$this->index], 151, 50);
    }

    protected function getUFEmitente() {
        $this->lote['lote'][$this->index]->c20_ufEnderecoEmitente = $this->getContent($this->dadosArquivo[$this->index], 201, 2);
    }

    protected function getCEPEmitente() {
        $this->lote['lote'][$this->index]->c21_cepEmitente = $this->getContent($this->dadosArquivo[$this->index], 203, 8);
    }

    protected function getTelefoneEmitente() {
        $this->lote['lote'][$this->index]->c22_telefoneEmitente = $this->getContent($this->dadosArquivo[$this->index], 211, 11);
    }

    protected function getTipoDocDestinatario() {
        $this->lote['lote'][$this->index]->c34_tipoIdentificacaoDestinatario = $this->getContent($this->dadosArquivo[$this->index], 222, 1);
    }

    protected function getDocumentoDestinatario() {
        $this->lote['lote'][$this->index]->c35_idContribuinteDestinatario = $this->getContent($this->dadosArquivo[$this->index], 223, 16);
    }

    protected function getMunicipioDestinatario() {
        $this->lote['lote'][$this->index]->c38_municipioDestinatario = $this->getContent($this->dadosArquivo[$this->index], 239, 50);
    }

    protected function getProduto() {
        $this->lote['lote'][$this->index]->c26_produto = $this->getContent($this->dadosArquivo[$this->index], 289, 255);
    }

    protected function getNumeroDocumentoDeOrigem() {
        $this->lote['lote'][$this->index]->c04_docOrigem = $this->getContent($this->dadosArquivo[$this->index], 544, 18);
    }

    protected function getConvenio() {
        $this->lote['lote'][$this->index]->c15_convenio = $this->getContent($this->dadosArquivo[$this->index], 562, 30);
    }

    protected function getDataDeVencimento() {
        $this->lote['lote'][$this->index]->c14_dataVencimento = $this->getContent($this->dadosArquivo[$this->index], 892, 8);
    }

    protected function getDataLimitePagamento() {
        $this->lote['lote'][$this->index]->c33_dataPagamento = $this->getContent($this->dadosArquivo[$this->index], 900, 8);
    }

    protected function getPeriodoReferencia() {
        $this->lote['lote'][$this->index]->periodo = $this->getContent($this->dadosArquivo[$this->index], 909, 1);
    }

    protected function getMesAnoReferencia() {
        $this->lote['lote'][$this->index]->mes = $this->getContent($this->dadosArquivo[$this->index], 908, 30);
    }

    protected function getParcela() {
        $this->lote['lote'][$this->index]->parcela = $this->getContent($this->dadosArquivo[$this->index], 915, 3);
    }

    protected function getValorPrincipal() {
        $this->lote['lote'][$this->index]->c06_valorPrincipal = $this->getContent($this->dadosArquivo[$this->index], 918, 15);
    }

    protected function getSequencialGuia() {
        $this->lote['lote'][$this->index]->retornoSequencialGuia = $this->getContent($this->dadosArquivo[$this->index], 1, 4);
    }

    protected function getSituacaoGuia() {
        $this->lote['lote'][$this->index]->retornoSituacaoGuia = $this->getContent($this->dadosArquivo[$this->index], 5, 1);
    }

    protected function getInformacoesComplementares() {
        $this->lote['lote'][$this->index]->retornoInformacoesComplementares = $this->getContent($this->dadosArquivo[$this->index], 592, 300);
    }

    protected function getAtualizacaoMonetaria() {
        $this->lote['lote'][$this->index]->retornoAtualizacaoMonetaria = $this->getContent($this->dadosArquivo[$this->index], 933, 15);
    }

    protected function getJuros() {
        $this->lote['lote'][$this->index]->retornoJuros = $this->getContent($this->dadosArquivo[$this->index], 948, 15);
    }

    protected function getMulta() {
        $this->lote['lote'][$this->index]->retornoMulta = $this->getContent($this->dadosArquivo[$this->index], 963, 15);
    }

    protected function getRepresentacaoNumerica() {
        $this->lote['lote'][$this->index]->retornoRepresentacaoNumerica = $this->getContent($this->dadosArquivo[$this->index], 978, 48);
    }

    protected function getCodigoBarras() {
        $this->lote['lote'][$this->index]->retornoCodigoDeBarras = $this->getContent($this->dadosArquivo[$this->index], 1026, 44);
    }

    protected function getNumeroDeControle() {
        $this->lote['lote'][$this->index]->retornoNumeroDeControle = $this->getContent($this->dadosArquivo[$this->index], 1071, 16);
    }

    protected function getIdentificadorGuia() {
        $tratamento = array(
            'posicao' => 1087,
            'tamanho' => 10
        );

//        $this->lote['lote'][$this->index]['identificador_guia'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getSequencialGuiaErroValidacao() {
        $tratamento = array(
            'posicao' => 1,
            'tamanho' => 4
        );

        $this->sequencialGuiaErroValidacao = $this->getContent($this->dadosArquivo[$this->index], 1, 4);
    }

    protected function getNomeCampo() {
        foreach ($this->lote['lote'] as $index => $guia) {
            if ($guia->retornoSequencialGuia == $this->sequencialGuiaErroValidacao) {
                $this->lote['lote'][$index]->retornoErrosDeValidacaoCampo = $this->getContent($this->dadosArquivo[$this->index], 5, 30);
                break;
            }
        }
    }

    protected function getCodigoMotivoRejeicao() {
        foreach ($this->lote['lote'] as $index => $guia) {
            if ($guia->retornoSequencialGuia == $this->sequencialGuiaErroValidacao) {
                $this->lote['lote'][$index]->retornoErrosDeValidacaoCodigo = $this->getContent($this->dadosArquivo[$this->index], 35, 3);
                break;
            }
        }
    }

    protected function getDescricaoMotivoRejeicao() {
        foreach ($this->lote['lote'] as $index => $guia) {
            if ($guia->retornoSequencialGuia == $this->sequencialGuiaErroValidacao) {
                $this->lote['lote'][$index]->retornoErrosDeValidacaoDescricao = $this->getContent($this->dadosArquivo[$this->index], 38, 355);
                break;
            }
        }
    }

    protected function getNumeroProtocolo() {
        $tratamento = array(
            'posicao' => 1,
            'tamanho' => 10
        );

//        $this->lote['rodape']['numero_protocolo'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getTotalGuias() {
        $tratamento = array(
            'posicao' => 11,
            'tamanho' => 4
        );

//        $this->lote['rodape']['total_guias'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getHashDeValidacao() {
        $tratamento = array(
            'posicao' => 15,
            'tamanho' => 64
        );

//        $this->lote['rodape']['hash_validacao'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function aplicarParser() {
        return $this->lote;
    }

}

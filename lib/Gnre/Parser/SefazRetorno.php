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
 * Classe abstrata para controlar as propriedades/métodos de uma classe que será
 * a base das configurações. Com isso temos certeza que será enviado as 
 * propriedades necessárias para a comunicação com a sefaz, independentemente da classe.
 * Basta usar essa classe abstrata que tudo deverá funcionar
 * 
 * @package     gnre
 * @subpackage  configuration
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
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
        $tratamento = array(
            'posicao' => 0,
            'tamanho' => 1
        );

        $this->identificador = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getTipoIdentificadorDoSolicitante() {
        $tratamento = array(
            'posicao' => 1,
            'tamanho' => 1
        );

        $this->lote['header']['tipoIdentificadorSolicitante'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getIdentificadorDoSolicitante() {
        $tratamento = array(
            'posicao' => 2,
            'tamanho' => 14
        );

        $this->lote['header']['identificadorSolicitante'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getNumeroDoProtocoloDoLote() {
        $tratamento = array(
            'posicao' => 16,
            'tamanho' => 10
        );

        $this->lote['header']['numeroProtocoloLote'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getAmbiente() {
        $tratamento = array(
            'posicao' => 26,
            'tamanho' => 1
        );

        $this->lote['header']['ambiente'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getSequencialGuia() {
        $tratamento = array(
            'posicao' => 1,
            'tamanho' => 4
        );

//        $this->lote['lote'][$this->index]['sequencial_guia'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getSituacaoGuia() {
        $tratamento = array(
            'posicao' => 5,
            'tamanho' => 1
        );

//        $this->lote['lote'][$this->index]['situacao_guia'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getUfFavorecida() {
        $tratamento = array(
            'posicao' => 6,
            'tamanho' => 2
        );

        $this->lote['lote'][$this->index]->c01_UfFavorecida = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']); //['uf_favorecida'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getCodigoReceita() {
        $tratamento = array(
            'posicao' => 8,
            'tamanho' => 6
        );

        $this->lote['lote'][$this->index]->c02_receita = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getTipoEmitente() {
        $tratamento = array(
            'posicao' => 14,
            'tamanho' => 1
        );

        $this->lote['lote'][$this->index]->c27_tipoIdentificacaoEmitente = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getDocumentoEmitente() {
        $tratamento = array(
            'posicao' => 15,
            'tamanho' => 16
        );

        $this->lote['lote'][$this->index]->c03_idContribuinteEmitente = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getRazaoSocialEmitente() {
        $tratamento = array(
            'posicao' => 31,
            'tamanho' => 60
        );

        $this->lote['lote'][$this->index]->c16_razaoSocialEmitente = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getEnderecoEmitente() {
        $tratamento = array(
            'posicao' => 91,
            'tamanho' => 60
        );

        $this->lote['lote'][$this->index]->c18_enderecoEmitente = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getMunicipioEmitente() {
        $tratamento = array(
            'posicao' => 151,
            'tamanho' => 50
        );

        $this->lote['lote'][$this->index]->c19_municipioEmitente = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getUFEmitente() {
        $tratamento = array(
            'posicao' => 201,
            'tamanho' => 2
        );

        $this->lote['lote'][$this->index]->c20_ufEnderecoEmitente = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getCEPEmitente() {
        $tratamento = array(
            'posicao' => 203,
            'tamanho' => 8
        );

        $this->lote['lote'][$this->index]->c21_cepEmitente = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getTelefoneEmitente() {
        $tratamento = array(
            'posicao' => 211,
            'tamanho' => 11
        );

        $this->lote['lote'][$this->index]->c22_telefoneEmitente = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getTipoDocDestinatario() {
        $tratamento = array(
            'posicao' => 222,
            'tamanho' => 1
        );

        $this->lote['lote'][$this->index]->c34_tipoIdentificacaoDestinatario = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getDocumentoDestinatario() {
        $tratamento = array(
            'posicao' => 223,
            'tamanho' => 16
        );

        $this->lote['lote'][$this->index]->c35_idContribuinteDestinatario = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getMunicipioDestinatario() {
        $tratamento = array(
            'posicao' => 239,
            'tamanho' => 50
        );

        $this->lote['lote'][$this->index]->c38_municipioDestinatario = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getProduto() {
        $tratamento = array(
            'posicao' => 289,
            'tamanho' => 255
        );

        $this->lote['lote'][$this->index]->c26_produto = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getNumeroDocumentoDeOrigem() {
        $tratamento = array(
            'posicao' => 544,
            'tamanho' => 18
        );

        $this->lote['lote'][$this->index]->c04_docOrigem = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getConvenio() {
        $tratamento = array(
            'posicao' => 562,
            'tamanho' => 30
        );

        $this->lote['lote'][$this->index]->c15_convenio = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getInformacoesComplementares() {
        $tratamento = array(
            'posicao' => 592,
            'tamanho' => 300
        );

//        $this->lote['lote'][$this->index]['informacoes_complementares'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getDataDeVencimento() {
        $tratamento = array(
            'posicao' => 892,
            'tamanho' => 8
        );

        $this->lote['lote'][$this->index]->c14_dataVencimento = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getDataLimitePagamento() {
        $tratamento = array(
            'posicao' => 900,
            'tamanho' => 8
        );

        $this->lote['lote'][$this->index]->c33_dataPagamento = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getPeriodoReferencia() {
        $tratamento = array(
            'posicao' => 908,
            'tamanho' => 1
        );

        $this->lote['lote'][$this->index]->periodo = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getMesAnoReferencia() {
        $tratamento = array(
            'posicao' => 908,
            'tamanho' => 1
        );

        $this->lote['lote'][$this->index]->mes = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getParcela() {
        $tratamento = array(
            'posicao' => 915,
            'tamanho' => 3
        );

        $this->lote['lote'][$this->index]->parcela = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getValorPrincipal() {
        $tratamento = array(
            'posicao' => 918,
            'tamanho' => 15
        );

        $this->lote['lote'][$this->index]->c06_valorPrincipal = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getAtualizacaoMonetaria() {
        $tratamento = array(
            'posicao' => 933,
            'tamanho' => 15
        );

//        $this->lote['lote'][$this->index]['atualizacao_monetaria'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getJuros() {
        $tratamento = array(
            'posicao' => 948,
            'tamanho' => 15
        );

//        $this->lote['lote'][$this->index]['juros'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getMulta() {
        $tratamento = array(
            'posicao' => 963,
            'tamanho' => 15
        );

//        $this->lote['lote'][$this->index]['multa'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getRepresentacaoNumerica() {
        $tratamento = array(
            'posicao' => 978,
            'tamanho' => 48
        );

//        $this->lote['lote'][$this->index]['representacao_numerica'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getCodigoBarras() {
        $tratamento = array(
            'posicao' => 1026,
            'tamanho' => 44
        );

//        $this->lote['lote'][$this->index]['codigo_barras'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getQtdVias() {
        $tratamento = array(
            'posicao' => 1070,
            'tamanho' => 1
        );

//        $this->lote['lote'][$this->index]['qtd_vias'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getNumeroDeControle() {
        $tratamento = array(
            'posicao' => 1071,
            'tamanho' => 16
        );

//        $this->lote['lote'][$this->index]['numero_de_controle'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getIdentificadorGuia() {
        $tratamento = array(
            'posicao' => 1087,
            'tamanho' => 10
        );

//        $this->lote['lote'][$this->index]['identificador_guia'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getGuiaGeradaEmContingencia() {
        $tratamento = array(
            'posicao' => 1097,
            'tamanho' => 1
        );

//        $this->lote['lote'][$this->index]['guia_gerada_em_contingencia'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getReservado() {
        $tratamento = array(
            'posicao' => 1098,
            'tamanho' => 126
        );

//        $this->lote['lote'][$this->index]['reservado'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getSequencialGuiaErroValidacao() {
        $tratamento = array(
            'posicao' => 1,
            'tamanho' => 4
        );

        $this->sequencialGuiaErroValidacao = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getNomeCampo() {
        $tratamento = array(
            'posicao' => 5,
            'tamanho' => 30
        );

//        foreach ($this->lote['lote'] as $index => $guia) {
//            if ($guia['sequencial_guia'] == $this->sequencialGuiaErroValidacao) {
//                $this->lote['lote'][$index]['erros_validacao']['nome_campo'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
//                break;
//            }
//        }
    }

    protected function getCodigoMotivoRejeicao() {
        $tratamento = array(
            'posicao' => 35,
            'tamanho' => 3
        );

//        foreach ($this->lote['lote'] as $index => $guia) {
//            if ($guia['sequencial_guia'] == $this->sequencialGuiaErroValidacao) {
//                $this->lote['lote'][$index]['erros_validacao']['codigo_motivo_rejeicao'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
//                break;
//            }
//        }
    }

    protected function getDescricaoMotivoRejeicao() {
        $tratamento = array(
            'posicao' => 38,
            'tamanho' => 355
        );

//        foreach ($this->lote['lote'] as $index => $guia) {
//            if ($guia['sequencial_guia'] == $this->sequencialGuiaErroValidacao) {
//                $this->lote['lote'][$index]['erros_validacao']['descricao_motivo_rejeicao'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
//                break;
//            }
//        }
    }

    protected function getNumeroProtocolo() {
        $tratamento = array(
            'posicao' => 1,
            'tamanho' => 10
        );

        $this->lote['rodape']['numero_protocolo'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getTotalGuias() {
        $tratamento = array(
            'posicao' => 11,
            'tamanho' => 4
        );

        $this->lote['rodape']['total_guias'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function getHashDeValidacao() {
        $tratamento = array(
            'posicao' => 15,
            'tamanho' => 64
        );

        $this->lote['rodape']['hash_validacao'] = substr($this->dadosArquivo[$this->index], $tratamento['posicao'], $tratamento['tamanho']);
    }

    protected function aplicarParser() {
        return $this->lote;
    }

}

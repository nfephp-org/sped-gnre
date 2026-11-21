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

use Sped\Gnre\Parser\Rules;

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
 * @see         Sped\Gnre\Parser\Rules
 * @version     1.0.0
 */
class SefazRetorno extends Rules
{

    /**
     * {@inheritdoc}
     */
    public function __construct($dadosArquivo)
    {
        parent::__construct($dadosArquivo);
    }

    protected function getIdentificador()
    {
        $this->getContent($this->dadosArquivo[$this->index], 0, 1);

        $this->identificador = $this->getContent($this->dadosArquivo[$this->index], 0, 1);
    }

    protected function getTipoIdentificadorDoSolicitante()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 1, 1);
        $this->lote['header']['tipoIdentificadorSolicitante'] = $content;
    }

    protected function getIdentificadorDoSolicitante()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 2, 14);
        $this->lote['header']['identificadorSolicitante'] = $content;
    }

    protected function getNumeroDoProtocoloDoLote()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 16, 10);
        $this->lote['header']['numeroProtocoloLote'] = $content;
    }

    protected function getAmbiente()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 26, 1);
        $this->lote['header']['ambiente'] = $content;
    }

    protected function getUfFavorecida()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 6, 2);
        $this->lote['lote'][$this->index]->c01_UfFavorecida = $content;
    }

    protected function getCodigoReceita()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 8, 6);
        $this->lote['lote'][$this->index]->c02_receita = $content;
    }

    protected function getTipoEmitente()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 14, 1);
        $this->lote['lote'][$this->index]->c27_tipoIdentificacaoEmitente = $content;
    }

    protected function getDocumentoEmitente()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 15, 16);
        $this->lote['lote'][$this->index]->c03_idContribuinteEmitente = $content;
    }

    protected function getRazaoSocialEmitente()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 31, 60);
        $this->lote['lote'][$this->index]->c16_razaoSocialEmitente = $content;
    }

    protected function getEnderecoEmitente()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 91, 60);
        $this->lote['lote'][$this->index]->c18_enderecoEmitente = $content;
    }

    protected function getMunicipioEmitente()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 151, 50);
        $this->lote['lote'][$this->index]->c19_municipioEmitente = $content;
    }

    protected function getUFEmitente()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 201, 2);
        $this->lote['lote'][$this->index]->c20_ufEnderecoEmitente = $content;
    }

    protected function getCEPEmitente()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 203, 8);
        $this->lote['lote'][$this->index]->c21_cepEmitente = $content;
    }

    protected function getTelefoneEmitente()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 211, 11);
        $this->lote['lote'][$this->index]->c22_telefoneEmitente = $content;
    }

    protected function getTipoDocDestinatario()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 222, 1);
        $this->lote['lote'][$this->index]->c34_tipoIdentificacaoDestinatario = $content;
    }

    protected function getDocumentoDestinatario()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 223, 16);
        $this->lote['lote'][$this->index]->c35_idContribuinteDestinatario = $content;
    }

    protected function getMunicipioDestinatario()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 239, 50);
        $this->lote['lote'][$this->index]->c38_municipioDestinatario = $content;
    }

    protected function getProduto()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 289, 255);
        $this->lote['lote'][$this->index]->c26_produto = $content;
    }

    protected function getNumeroDocumentoDeOrigem()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 544, 18);
        $this->lote['lote'][$this->index]->c04_docOrigem = $content;
    }

    protected function getConvenio()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 562, 30);
        $this->lote['lote'][$this->index]->c15_convenio = $content;
    }

    protected function getDataDeVencimento()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 892, 8);
        $this->lote['lote'][$this->index]->c14_dataVencimento = $content;
    }

    protected function getDataLimitePagamento()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 900, 8);
        $this->lote['lote'][$this->index]->c33_dataPagamento = $content;
    }

    protected function getPeriodoReferencia()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 909, 1);
        $this->lote['lote'][$this->index]->periodo = $content;
    }

    protected function getMesAnoReferencia()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 908, 30);
        $this->lote['lote'][$this->index]->mes = $content;
    }

    protected function getParcela()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 915, 3);
        $this->lote['lote'][$this->index]->parcela = $content;
    }

    protected function getValorPrincipal()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 918, 15);
        $this->lote['lote'][$this->index]->c06_valorPrincipal = $content;
    }

    protected function getSequencialGuia()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 1, 4);
        $this->lote['lote'][$this->index]->retornoSequencialGuia = $content;
    }

    protected function getSituacaoGuia()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 5, 1);
        $this->lote['lote'][$this->index]->retornoSituacaoGuia = $content;
    }

    protected function getInformacoesComplementares()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 592, 300);
        $this->lote['lote'][$this->index]->retornoInformacoesComplementares = $content;
    }

    protected function getAtualizacaoMonetaria()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 933, 15);
        $this->lote['lote'][$this->index]->retornoAtualizacaoMonetaria = $content;
    }

    protected function getJuros()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 948, 15);
        $this->lote['lote'][$this->index]->retornoJuros = $content;
    }

    protected function getMulta()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 963, 15);
        $this->lote['lote'][$this->index]->retornoMulta = $content;
    }

    protected function getRepresentacaoNumerica()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 978, 48);
        $this->lote['lote'][$this->index]->retornoRepresentacaoNumerica = $content;
    }

    protected function getCodigoBarras()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 1026, 44);
        $this->lote['lote'][$this->index]->retornoCodigoDeBarras = $content;
    }

    protected function getNumeroDeControle()
    {
        $content = $this->getContent($this->dadosArquivo[$this->index], 1071, 16);
        $this->lote['lote'][$this->index]->retornoNumeroDeControle = $content;
    }

    protected function getIdentificadorGuia()
    {
        $tratamento = array(
            'posicao' => 1087,
            'tamanho' => 10
        );
    }

    protected function getSequencialGuiaErroValidacao()
    {
        $tratamento = array(
            'posicao' => 1,
            'tamanho' => 4
        );

        $this->sequencialGuiaErroValidacao = $this->getContent($this->dadosArquivo[$this->index], 1, 4);
    }

    protected function getNomeCampo()
    {
        foreach ($this->lote['lote'] as $index => $guia) {
            if ($guia->retornoSequencialGuia == $this->sequencialGuiaErroValidacao) {
                $content = $this->getContent($this->dadosArquivo[$this->index], 5, 30);
                $this->lote['lote'][$index]->retornoErrosDeValidacaoCampo = $content;
                break;
            }
        }
    }

    protected function getCodigoMotivoRejeicao()
    {
        foreach ($this->lote['lote'] as $index => $guia) {
            if ($guia->retornoSequencialGuia == $this->sequencialGuiaErroValidacao) {
                $content = $this->getContent($this->dadosArquivo[$this->index], 35, 3);
                $this->lote['lote'][$index]->retornoErrosDeValidacaoCodigo = $content;
                break;
            }
        }
    }

    protected function getDescricaoMotivoRejeicao()
    {
        foreach ($this->lote['lote'] as $index => $guia) {
            if ($guia->retornoSequencialGuia == $this->sequencialGuiaErroValidacao) {
                $content = $this->getContent($this->dadosArquivo[$this->index], 38, 355);
                $this->lote['lote'][$index]->retornoErrosDeValidacaoDescricao = $content;
                break;
            }
        }
    }

    protected function getNumeroProtocolo()
    {
        $tratamento = array(
            'posicao' => 1,
            'tamanho' => 10
        );
    }

    protected function getTotalGuias()
    {
        $tratamento = array(
            'posicao' => 11,
            'tamanho' => 4
        );
    }

    protected function getHashDeValidacao()
    {
        $tratamento = array(
            'posicao' => 15,
            'tamanho' => 64
        );
    }

    protected function aplicarParser()
    {
        return $this->lote;
    }
}

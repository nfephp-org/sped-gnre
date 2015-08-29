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

namespace Gnre\Parser;

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
abstract class Rules
{

    const ERRO_VALIDACAO = 2;
    const GUIA_EMITIDA_COM_SUCESSO = 9;

    /**
     * @var string 
     */
    protected $dadosArquivo;

    /**
     * @var int
     */
    protected $index;

    /**
     * @var int
     */
    protected $indentificador;

    /**
     * @var string
     */
    protected $sequencialGuiaErroValidacao;

    /**
     * @var array
     */
    protected $lote = array();

    /**
     * Utiliza o método construtor da classe para ser enviado um conteúdo de 
     * arquivo para ser extraido
     * 
     * @param string $dadosArquivo <p>String contendo o conteúdo de retorno do 
     * web service da SEFAZ</p>
     * @since 1.0.0
     */
    public function __construct($dadosArquivo)
    {
        $this->dadosArquivo = explode(PHP_EOL, $dadosArquivo);
    }

    protected abstract function getTipoIdentificadorDoSolicitante();

    protected abstract function getIdentificador();

    protected abstract function getSequencialGuia();

    protected abstract function getSituacaoGuia();

    protected abstract function getUfFavorecida();

    protected abstract function getCodigoReceita();

    protected abstract function getTipoEmitente();

    protected abstract function getDocumentoEmitente();

    protected abstract function getEnderecoEmitente();

    protected abstract function getMunicipioEmitente();

    protected abstract function getUFEmitente();

    protected abstract function getCEPEmitente();

    protected abstract function getTelefoneEmitente();

    protected abstract function getTipoDocDestinatario();

    protected abstract function getDocumentoDestinatario();

    protected abstract function getMunicipioDestinatario();

    protected abstract function getProduto();

    protected abstract function getNumeroDocumentoDeOrigem();

    protected abstract function getConvenio();

    protected abstract function getInformacoesComplementares();

    protected abstract function getDataDeVencimento();

    protected abstract function getDataLimitePagamento();

    protected abstract function getPeriodoReferencia();

    protected abstract function getParcela();

    protected abstract function getValorPrincipal();

    protected abstract function getAtualizacaoMonetaria();

    protected abstract function getJuros();

    protected abstract function getMulta();

    protected abstract function getRepresentacaoNumerica();

    protected abstract function getCodigoBarras();

    protected abstract function getNumeroDeControle();

    protected abstract function getIdentificadorGuia();

    protected abstract function getNumeroProtocolo();

    protected abstract function getTotalGuias();

    protected abstract function getHashDeValidacao();

    protected abstract function getIdentificadorDoSolicitante();

    protected abstract function getNumeroDoProtocoloDoLote();

    protected abstract function getAmbiente();

    protected abstract function getNomeCampo();

    protected abstract function getCodigoMotivoRejeicao();

    protected abstract function getDescricaoMotivoRejeicao();

    protected abstract function aplicarParser();

    /**
     * @return \Gnre\Sefaz\Lote
     */
    public function getLote()
    {
        $lote = new \Gnre\Sefaz\Lote();

        for ($i = 0; $i < sizeof($this->dadosArquivo); $i++) {
            $this->index = $i;
            $this->getIdentificador();
            $this->sequencialGuiaErroValidacao = null;

            if ($this->identificador == 0) {
                $this->getTipoIdentificadorDoSolicitante();
                $this->getIdentificadorDoSolicitante();
                $this->getNumeroDoProtocoloDoLote();
                $this->getAmbiente();
            } else if ($this->identificador == 1) {
                $this->lote['lote'][$i] = new \Gnre\Sefaz\Guia();

                $this->getSequencialGuia();
                $this->getSituacaoGuia();
                $this->getUfFavorecida();
                $this->getCodigoReceita();
                $this->getTipoEmitente();
                $this->getDocumentoEmitente();
                $this->getEnderecoEmitente();
                $this->getMunicipioEmitente();
                $this->getUFEmitente();
                $this->getCEPEmitente();
                $this->getTelefoneEmitente();
                $this->getTipoDocDestinatario();
                $this->getDocumentoDestinatario();
                $this->getMunicipioDestinatario();
                $this->getProduto();
                $this->getNumeroDocumentoDeOrigem();
                $this->getConvenio();
                $this->getInformacoesComplementares();
                $this->getDataDeVencimento();
                $this->getDataLimitePagamento();
                $this->getPeriodoReferencia();
                $this->getParcela();
                $this->getValorPrincipal();
                $this->getAtualizacaoMonetaria();
                $this->getJuros();
                $this->getMulta();
                $this->getRepresentacaoNumerica();
                $this->getCodigoBarras();
                $this->getNumeroDeControle();
                $this->getIdentificadorGuia();

                $lote->addGuia($this->lote['lote'][$i]);
            } else if ($this->identificador == 9) {
                $this->getNumeroProtocolo();
                $this->getTotalGuias();
                $this->getHashDeValidacao();
            } else if ($this->identificador == 2) {
                $this->getSequencialGuiaErroValidacao();
                $this->getNomeCampo();
                $this->getCodigoMotivoRejeicao();
                $this->getDescricaoMotivoRejeicao();
            }
        }

        $this->aplicarParser();

        return $lote;
    }

    /**
     * 
     * @param string $content
     * @param int $positionStart
     * @param int $length
     * @return string
     */
    public function getContent($content, $positionStart, $length)
    {
        return substr($content, $positionStart, $length);
    }

}

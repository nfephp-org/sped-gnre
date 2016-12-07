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

use Sped\Gnre\Exception\UndefinedProperty;

/**
 * Classe responsável por criar uma simples guia GNRE. Essa classe
 * armazena todos os atributos necessários para serem transformados no
 * XML aceito pela SEFAZ e posteriormente submetidos através do webservice
 * 
 * <b>
 * Os atributos com o prefixo "retorno" sao populados com os dados do retorno
 * do web service da SEFAZ, alguns deles podem ou nao possuir conteudo.
 * </b>
 * @package     gnre
 * @subpackage  sefaz
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Guia
{

    /**
     * Uma sigla representando um dos 27 estados brasileiros
     * por exemplo AC, BA, DF
     * @var    string
     */
    private $c01_UfFavorecida;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * para maiores informações visualizar a documentação oficial do GNRE
     * http://www.gnre.pe.gov.br/gnre/index.html
     * @var    int
     */
    private $c02_receita;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * para maiores informações visualizar a documentação oficial do GNRE
     * http://www.gnre.pe.gov.br/gnre/index.html
     * @var    int
     */
    private $c25_detalhamentoReceita;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * para maiores informações visualizar a documentação oficial do GNRE
     * http://www.gnre.pe.gov.br/gnre/index.html
     * @var    int
     */
    private $c26_produto;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * para maiores informações visualizar a documentação oficial do GNRE
     * http://www.gnre.pe.gov.br/gnre/index.html
     * @var    int
     */
    private $c27_tipoIdentificacaoEmitente;

    /**
     * Informar o CPF ou CNPJ sem nenhuma formatação
     * apenas os dígitos
     * @var    int 
     */
    private $c03_idContribuinteEmitente;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * para maiores informações visualizar a documentação oficial do GNRE
     * http://www.gnre.pe.gov.br/gnre/index.html
     * @var    int
     */
    private $c28_tipoDocOrigem;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * para maiores informações visualizar a documentação oficial do GNRE
     * http://www.gnre.pe.gov.br/gnre/index.html
     * @var    int
     */
    private $c04_docOrigem;

    /**
     * Para esse atributo é esperado um dado do tipo double com 
     * o valor total da guia sem juros e/ou acréscimos
     * @var    double
     */
    private $c06_valorPrincipal;

    /**
     * Para esse atributo é esperado um dado do tipo double com 
     * o valor total da guia porém com o juros e/ou acréscimo já 
     * somados ao valor principal. Ou seja se o valor total for 5.00 e o juros
     * por exemplo for 5.00 o valor total será 10.00
     * @var    double
     */
    private $c10_valorTotal;

    /**
     * Para esse atributo é esperado um dado do tipo string com 
     * a data de vencimento da guia no formato AAAA-MM-DD
     * @var    string
     */
    private $c14_dataVencimento;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * para maiores informações visualizar a documentação oficial do GNRE
     * http://www.gnre.pe.gov.br/gnre/index.html
     * @var    int
     */
    private $c15_convenio;

    /**
     * Para esse atributo é esperado um dado do tipo string com 
     * a razão social da empresa emitente
     * @var    int
     */
    private $c16_razaoSocialEmitente;

    /**
     * Para esse atributo é esperado um dado do tipo int com 
     * a inscrição estadual (I.E) da empresa emitente
     * @var    int
     */
    private $c17_inscricaoEstadualEmitente;

    /**
     * Para esse atributo é esperado um dado do tipo string com 
     * o endereço  da empresa emitente
     * @var    int
     */
    private $c18_enderecoEmitente;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * com o código do municipio de acordo com a tabela do IBGE removendo os 2 
     * primeiros digitos
     * @var    int
     */
    private $c19_municipioEmitente;

    /**
     * Para esse atributo é esperado um dado do tipo string
     * com a singla do estado da empresa emitente por exemplo SP, TO, AC
     * @var    string
     */
    private $c20_ufEnderecoEmitente;

    /**
     * Para esse atributo é esperado um dado do tipo int
     * com o CEP correspondente da empresa emitente 
     * @var    int
     */
    private $c21_cepEmitente;

    /**
     * Para esse atributo é esperado um dado do tipo int
     * com o telefone do emitente no formato DD99999999
     * @var    int
     */
    private $c22_telefoneEmitente;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * para maiores informações visualizar a documentação oficial do GNRE
     * http://www.gnre.pe.gov.br/gnre/index.html
     * @var    int
     */
    private $c34_tipoIdentificacaoDestinatario;

    /**
     * Informar o CPF ou CNPJ sem nenhuma formatação
     * apenas os dígitos
     * @var    int 
     */
    private $c35_idContribuinteDestinatario;

    /**
     * Para esse atributo é esperado um dado do tipo int com 
     * a inscrição estadual (I.E) da empresa a quem se destina a guia
     * @var    int
     */
    private $c36_inscricaoEstadualDestinatario;

    /**
     * Para esse atributo é esperado um dado do tipo string com 
     * a razão social da empresa a quem se destina a guia
     * @var    int
     */
    private $c37_razaoSocialDestinatario;

    /**
     * Para esse atributo é esperado um dado do tipo inteiro
     * com o código do municipio de acordo com a tabela do IBGE removendo os 2 
     * primeiros digitos
     * @var    int
     */
    private $c38_municipioDestinatario;

    /**
     * Para esse atributo é esperado um dado do tipo string com 
     * a data de pagamento da guia no formato AAAA-MM-DD
     * @var    string
     */
    private $c33_dataPagamento;

    /**
     * Para esse atributo é esperado um dado do tipo int
     * com o intervalo entre 0 e 5 (1, 2, 3, 4 ou 5)
     * @var    int
     */
    private $periodo;

    /**
     * Para esse atributo é esperado um dado do tipo int
     * com algum mês do ano (IMPORTANTE : é necessário informar o zero a esquerma caso o mês
     * desejado esteja entre 1 e 9)
     * @var    int
     */
    private $mes;

    /**
     * Para esse atributo é esperado um dado do tipo int com
     * algum ano válido como por exemplo 2014 (IMPORTANTE: o ano dever ser menor ou igual a 2000)
     * @var    int
     */
    private $ano;

    /**
     * Para esse atributo é esperado um dado do tipo int com
     * o número de parcelas desejadas entre 1 e 999 ( 1, 2, 3, 4 ... 999)
     * @var    int
     */
    private $parcela;

    /**
     * Para esse atributo é esperado um dado do tipo array
     * contendo os campos extras para a guia com os seguintes índices :
     * codigo, tipo e valor
     * @var    array
     */
    private $c39_camposExtras;

    /**
     * Para esse atributo é esperado um dado do tipo string
     * para maiores informações visualizar a documentação oficial do GNRE
     * http://www.gnre.pe.gov.br/gnre/index.html
     * @var    string
     */
    private $c42_identificadorGuia;

    /**
     * Dados retornados pelo web service da SEFAZ
     * com os dados complementares da guia
     * @var string 
     */
    private $retornoInformacoesComplementares;

    /**
     * Dados retornados pelo web service da SEFAZ
     * com o valor da atualização monetária, esse item pode
     * ser encontrado do lado direito da guia em 
     * https://github.com/marabesi/gnrephp/blob/dev-pdf/exemplos/guia.jpg
     * na sétima linha
     * @var double 
     */
    private $retornoAtualizacaoMonetaria;

    /**
     * Dados retornados pelo web service da SEFAZ
     * com o valor do juros, esse item pode ser encontrado do lado 
     * direito da guia em 
     * https://github.com/marabesi/gnrephp/blob/dev-pdf/exemplos/guia.jpg
     * na oitava linha
     * @var double 
     */
    private $retornoJuros;

    /**
     * Dados retornados pelo web service da SEFAZ
     * com o valor da multa, esse item pode ser encontrado do lado 
     * direito da guia em 
     * https://github.com/marabesi/gnrephp/blob/dev-pdf/exemplos/guia.jpg
     * na nona linha
     * @var double 
     */
    private $retornoMulta;

    /**
     * Dados retornados pelo web service da SEFAZ com a linha
     * digitável do código de barras possuindo 48 caracteres
     * @var int 
     */
    private $retornoRepresentacaoNumerica;

    /**
     * Dados retornados pelo web service da SEFAZ com o código
     * de barras, possuindo 44 caracteres (esse valor deve ser usado 
     * para gerar a imagem do  código de barras do boleto).
     * @var int 
     */
    private $retornoCodigoDeBarras;

    /**
     * Dados retornados pelo web service da SEFAZ com a situação
     * da guia, se foi processada com sucesso ou se houve erro.
     * Para maiores informações sobre esse item consulte
     * a documentação de retorno em http://www.gnre.pe.gov.br/gnre/portal/downloads.jsp
     * @var int 
     */
    private $retornoSituacaoGuia;

    /**
     * Dados retornados pelo web service da SEFAZ com o numero de sequencia
     * que a guia tem na SEFAZ.
     * Para maiores informações sobre esse item consulte
     * a documentação de retorno em http://www.gnre.pe.gov.br/gnre/portal/downloads.jsp
     * @var type 
     */
    private $retornoSequencialGuia;

    /**
     * Dados retornados pelo web service da SEFAZ com o nome dos campos do XML
     * que causaram o erro caso a guia não tenha sido processada com sucesso
     * @var string
     */
    private $retornoErrosDeValidacaoCampo;

    /**
     * Dados retornados pelo web service da SEFAZ com o código
     * do erro caso a guia não tenha sido processada com sucesso
     * @var string
     */
    private $retornoErrosDeValidacaoCodigo;

    /**
     * Dados retornados pelo web service da SEFAZ com a descrição
     * do erro caso a guia não tenha sido processada com sucesso
     * @var string
     */
    private $retornoErrosDeValidacaoDescricao;

    /**
     * Dados retornados pelo web service da SEFAZ com o número
     * de controle da guia, <b>o valor desse atributo é gerado pela SEFAZ</b>
     * @var int
     */
    private $retornoNumeroDeControle;
    
    /**
     * Dados retornados pelo web service da SEFAZ com o número
     * do protocolo da guia, <b>o valor desse atributo é gerado pela SEFAZ</b>
     * @var int
     */
    private $retornoNumeroProtocolo;

    /**
     * Método mágico utilizado para retornar um valor de um
     * determinado atributo na classe
     * @param  string  $property  Uma propriedade válida dessa classe
     * @throws UndefinedProperty  Caso a propriedade desejada não exista
     * @return string  Caso a propriedade exista retorna o seu valor
     * @since  1.0.0
     */
    public function __get($property)
    {
        if ($this->verifyProperty($property)) {
            return $this->$property;
        }
    }

    /**
     * Método mágico utilizado para setar valores aos atributos 
     * existentes na classe
     * @param  string $property  O nome existente de um atributo existente na classe
     * @param  mixed  $value  O valor desejado para ser setado no atributo desejado (string, boolean, int, Object ou array)
     * @throws UndefinedProperty  Caso o atributo desejada não exista
     * @return boolean Retorna true caso seja setado o valor para o atributo desejado
     * @since  1.0.0
     */
    public function __set($property, $value)
    {
        if ($this->verifyProperty($property)) {
            $this->$property = $value;
            return true;
        }
    }

    /**
     * Método utilizado para verificar se o atributo desejado
     * exista na classe
     * @param  string $property  O nome existente de um atributo existente na classe
     * @return boolean  Retorna true caso o atributo desejado exista na classe
     * @throws UndefinedProperty  Caso o atributo desejada não exista na classe
     * @since  1.0.0
     */
    private function verifyProperty($property)
    {
        if (!property_exists($this, $property)) {
            throw new UndefinedProperty($property);
        }

        return true;
    }

}

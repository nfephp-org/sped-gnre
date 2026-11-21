<?php

namespace NFePHP\Gnre;

use NFePHP\Common\Strings;
use NFePHP\Gnre\Common\Tools as ToolsCommon;
use NFePHP\Gnre\Exception\InvalidArgumentException;

/**
 * Serviços do Portal GNRE (SEFAZ-PE).
 *
 * Todos os métodos sefaz* devolvem o retorno SOAP bruto; a mensagem enviada
 * fica em $lastRequest e a resposta em $lastResponse.
 *
 * @package    NFePHP\Gnre
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class Tools extends ToolsCommon
{
    /**
     * Envia um lote de GNRE (serviço GnreLoteRecepcao, assíncrono: retorna o recibo)
     *
     * @param string $xmlLote TLote_GNRE no layout da versão configurada
     * @return string retorno SOAP bruto
     */
    public function sefazEnviaLote(string $xmlLote): string
    {
        $request = $this->limparXml($xmlLote);
        $this->isValid($request, "lote_gnre_v{$this->versao}.xsd");
        $this->servico('GnreLoteRecepcao');
        return $this->sendRequest($request);
    }

    /**
     * Consulta o resultado do processamento de um lote (serviço GnreResultadoLote)
     *
     * @param string $recibo número do recibo devolvido por sefazEnviaLote (10 ou 14 dígitos)
     * @param array<string, string> $opcoes incluirPDFGuias|incluirArquivoPagamento|incluirNoticias => S|N
     *                                      (somente no layout 2.00)
     * @return string retorno SOAP bruto
     */
    public function sefazConsultaLote(string $recibo, array $opcoes = []): string
    {
        $request = '<TConsLote_GNRE xmlns="' . self::PORTAL . '">'
            . '<ambiente>' . $this->tpAmb . '</ambiente>'
            . '<numeroRecibo>' . $this->recibo($recibo) . '</numeroRecibo>'
            . $this->opcoesConsulta($opcoes)
            . '</TConsLote_GNRE>';
        $this->isValid($request, 'lote_gnre_consulta_v1.00.xsd');
        $this->servico('GnreResultadoLote');
        return $this->sendRequest($request);
    }

    /**
     * Consulta as configurações de uma UF no Portal GNRE (serviço GnreConfigUF)
     *
     * @param string $uf sigla da UF favorecida
     * @param string $receita código da receita (opcional) para filtrar a regra
     * @param bool $courier indica o atributo courier="S" na receita
     * @return string retorno SOAP bruto
     */
    public function sefazConsultaConfigUf(string $uf, string $receita = '', bool $courier = false): string
    {
        $uf = strtoupper(trim($uf));
        if (!in_array($uf, self::UFS, true)) {
            throw new InvalidArgumentException("UF inválida [$uf].");
        }
        $receita = trim($receita);
        if ($courier && $receita === '') {
            throw new InvalidArgumentException('O atributo courier exige a informação da receita.');
        }
        $request = '<TConsultaConfigUf xmlns="' . self::PORTAL . '">'
            . '<ambiente>' . $this->tpAmb . '</ambiente>'
            . '<uf>' . $uf . '</uf>';
        if ($receita !== '') {
            $request .= '<receita' . ($courier ? ' courier="S"' : '') . '>' . $this->escapar($receita) . '</receita>';
        }
        $request .= '</TConsultaConfigUf>';
        $this->isValid($request, 'consulta_config_uf_v1.00.xsd');
        $this->servico('GnreConfigUF');
        return $this->sendRequest($request);
    }

    /**
     * Valida e normaliza o número do recibo (o formato fica a cargo do XSD)
     */
    protected function recibo(string $recibo): string
    {
        $recibo = trim($recibo);
        if ($recibo === '') {
            throw new InvalidArgumentException('Informe o número do recibo.');
        }
        return $this->escapar($recibo);
    }

    /**
     * Monta as opções de retorno da consulta do lote na ordem do XSD
     *
     * @param array<string, string> $opcoes
     */
    protected function opcoesConsulta(array $opcoes): string
    {
        if (empty($opcoes)) {
            return '';
        }
        if ($this->versao !== '2.00') {
            throw new InvalidArgumentException('As opções de retorno da consulta exigem o layout 2.00.');
        }
        $permitidas = ['incluirPDFGuias', 'incluirArquivoPagamento', 'incluirNoticias'];
        $desconhecidas = array_diff(array_keys($opcoes), $permitidas);
        if (!empty($desconhecidas)) {
            throw new InvalidArgumentException('Opção de consulta desconhecida: ' . implode(', ', $desconhecidas));
        }
        $xml = '';
        foreach ($permitidas as $opcao) {
            if (!isset($opcoes[$opcao])) {
                continue;
            }
            if (!in_array($opcoes[$opcao], ['S', 'N'], true)) {
                throw new InvalidArgumentException("A opção $opcao aceita apenas S ou N.");
            }
            $xml .= "<$opcao>{$opcoes[$opcao]}</$opcao>";
        }
        return $xml;
    }

    /**
     * Escapa um valor para uso como conteúdo XML
     */
    protected function escapar(string $valor): string
    {
        return htmlspecialchars($valor, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Remove a declaração XML, quebras de linha e espaços entre tags
     */
    protected function limparXml(string $xml): string
    {
        return trim((string) Strings::clearXmlString($xml, true));
    }
}

<?php

namespace NFePHP\Gnre;

use DOMElement;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Strings;
use NFePHP\Gnre\Common\Tools as ToolsCommon;
use NFePHP\Gnre\Exception\RuntimeException;
use stdClass;

/**
 * Monta o lote de consultas de guias GNRE já emitidas (TLote_ConsultaGNRE versao="2.00"),
 * enviado por Tools::sefazEnviaLoteConsulta().
 *
 * tipoConsulta: C código de barras, N número de controle, D documento de origem,
 * CD/ND código de barras ou número de controle + documento de origem,
 * CR/NR código de barras ou número de controle + receita.
 *
 * @package    NFePHP\Gnre
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class MakeConsulta
{
    protected const VERSAO = '2.00';
    protected const TIPOS_CONSULTA = ['C', 'N', 'D', 'CD', 'ND', 'CR', 'NR'];
    protected const MAX_CONSULTAS = 50;

    /**
     * @var array<int, string>
     */
    public $errors = [];
    /**
     * @var Dom
     */
    protected $dom;
    /**
     * @var array<int, DOMElement>
     */
    protected $consultas = [];

    public function __construct()
    {
        $this->dom = new Dom('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = false;
    }

    /**
     * Acrescenta uma consulta ao lote (máximo 50)
     *
     * @param stdClass $std uf, CNPJ|CPF|IE, codBarras, numControle, docOrigem, docOrigemTipo,
     *                      idConsulta, tipoConsulta
     */
    public function tagConsulta(stdClass $std): DOMElement
    {
        $std = Strings::equilizeParameters(clone $std, [
            'uf', 'CNPJ', 'CPF', 'IE', 'codBarras', 'numControle', 'docOrigem', 'docOrigemTipo',
            'idConsulta', 'tipoConsulta',
        ]);
        $id = '[consulta ' . (count($this->consultas) + 1) . '] ';
        if (count($this->consultas) >= self::MAX_CONSULTAS) {
            $this->errors[] = $id . 'máximo de ' . self::MAX_CONSULTAS . ' consultas por lote.';
        }
        if (!empty($std->tipoConsulta) && !in_array((string) $std->tipoConsulta, self::TIPOS_CONSULTA, true)) {
            $this->errors[] = $id . "tipoConsulta inválido [{$std->tipoConsulta}]: use "
                . implode(', ', self::TIPOS_CONSULTA) . '.';
        }
        $consulta = $this->dom->createElement('consulta');
        $this->dom->addChild($consulta, 'uf', $std->uf, true, $id . 'UF');
        if (!empty($std->CNPJ) || !empty($std->CPF) || !empty($std->IE)) {
            $emitente = $this->dom->createElement('emitenteId');
            if (!empty($std->CNPJ)) {
                $this->dom->addChild($emitente, 'CNPJ', $std->CNPJ, true, $id . 'CNPJ');
            } elseif (!empty($std->CPF)) {
                $this->dom->addChild($emitente, 'CPF', $std->CPF, true, $id . 'CPF');
            }
            $this->dom->addChild($emitente, 'IE', $std->IE, false, $id . 'IE');
            $consulta->appendChild($emitente);
        }
        $this->dom->addChild($consulta, 'codBarras', $std->codBarras, false, $id . 'código de barras');
        $this->dom->addChild($consulta, 'numControle', $std->numControle, false, $id . 'número de controle');
        if (!empty($std->docOrigem)) {
            if (empty($std->docOrigemTipo)) {
                $this->errors[] = $id . 'docOrigemTipo é obrigatório quando docOrigem é informado.';
            }
            $doc = $this->dom->createElement('docOrigem', (string) $std->docOrigem);
            $doc->setAttribute('tipo', (string) $std->docOrigemTipo);
            $consulta->appendChild($doc);
        }
        $this->dom->addChild($consulta, 'idConsulta', $std->idConsulta, false, $id . 'identificador');
        $this->dom->addChild($consulta, 'tipoConsulta', $std->tipoConsulta, true, $id . 'tipo da consulta');
        $this->consultas[] = $consulta;
        return $consulta;
    }

    /**
     * XML do lote de consultas (TLote_ConsultaGNRE), sem declaração XML
     *
     * @throws RuntimeException se não houver consultas ou houver erros de preenchimento
     */
    public function getXML(): string
    {
        if (empty($this->consultas)) {
            throw new RuntimeException('O lote de consultas está vazio: chame tagConsulta() ao menos uma vez.');
        }
        if (!empty($this->getErrors())) {
            throw new RuntimeException(
                "O lote de consultas tem erros de preenchimento:\n" . implode("\n", $this->getErrors())
            );
        }
        $dom = new Dom('1.0', 'UTF-8');
        $lote = $dom->createElementNS(ToolsCommon::PORTAL, 'TLote_ConsultaGNRE');
        $lote->setAttribute('versao', self::VERSAO);
        $dom->appendChild($lote);
        foreach ($this->consultas as $consulta) {
            $lote->appendChild($dom->importNode($consulta, true));
        }
        return Strings::clearXmlString((string) $dom->saveXML($lote), true);
    }

    /**
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return array_values(array_unique(array_merge($this->errors, $this->dom->errors)));
    }
}

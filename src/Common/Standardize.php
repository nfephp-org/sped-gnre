<?php

namespace NFePHP\Gnre\Common;

use DOMDocument;
use DOMElement;
use DOMNode;
use NFePHP\Common\Validator;
use NFePHP\Gnre\Exception\DocumentsException;
use stdClass;

/**
 * Converte mensagens e retornos do Portal GNRE em stdClass, array ou JSON.
 *
 * Aceita a resposta SOAP completa: o nó de dados é localizado pelo nome local,
 * com ou sem prefixo de namespace (os retornos reais usam "ns1:").
 *
 * Regras de conversão (D-17), pensadas para não perder informação:
 * - elemento só com texto vira string;
 * - elemento com atributos ganha a propriedade "attributes" e, se tiver texto, "value"
 *   (ex.: <valor tipo="12">1.82</valor> vira {attributes: {tipo: "12"}, value: "1.82"});
 * - elementos irmãos com o mesmo nome viram array; um único elemento vira objeto/string;
 * - os nomes saem sem prefixo de namespace.
 *
 * @package    NFePHP\Gnre\Common
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class Standardize
{
    /**
     * XML do nó de dados, sem prefixos de namespace
     * @var string
     */
    public $node = '';
    /**
     * @var string
     */
    public $json = '';
    /**
     * Raiz identificada por whichIs()
     * @var string
     */
    public $key = '';
    /**
     * Raízes reconhecidas, em ordem de prioridade (retornos primeiro)
     * @var array<int, string>
     */
    public $rootTagList = [
        'TRetLote_GNRE',
        'TResultLote_GNRE',
        'TConfigUf',
        'TRetorno_GNRE',
        'TLote_GNRE',
        'TLote_ConsultaGNRE',
        'TConsLote_GNRE',
        'TConsultaConfigUf',
    ];

    public function __construct(?string $xml = null)
    {
        if (!empty($xml)) {
            $this->toStd($xml);
        }
    }

    /**
     * Identifica a raiz GNRE do documento e guarda o nó (sem prefixos) em $node
     */
    public function whichIs(string $xml): string
    {
        if (!Validator::isXML($xml)) {
            throw DocumentsException::wrongDocument(6);
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        $this->verificarFault($dom);
        foreach ($this->rootTagList as $key) {
            $no = $dom->getElementsByTagNameNS('*', $key)->item(0);
            if (!$no instanceof DOMElement) {
                $no = $dom->getElementsByTagName($key)->item(0);
            }
            if ($no instanceof DOMElement) {
                $this->node = $this->semPrefixo($no);
                return $key;
            }
        }
        throw DocumentsException::wrongDocument(7);
    }

    public function __toString(): string
    {
        return $this->node;
    }

    public function toStd(?string $xml = null): stdClass
    {
        if (!empty($xml)) {
            $this->key = $this->whichIs($xml);
        }
        if ($this->node === '') {
            throw DocumentsException::wrongDocument(6);
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($this->node);
        $raiz = $dom->documentElement;
        $dados = $raiz instanceof DOMElement ? $this->converter($raiz) : new stdClass();
        if (!$dados instanceof stdClass) {
            $dados = (object) ['value' => $dados];
        }
        $opcoes = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;
        $this->json = (string) json_encode($dados, $opcoes);
        return json_decode($this->json);
    }

    public function toJson(?string $xml = null): string
    {
        if (!empty($xml)) {
            $this->toStd($xml);
        }
        return $this->json;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(?string $xml = null): array
    {
        if (!empty($xml)) {
            $this->toStd($xml);
        }
        return (array) json_decode($this->json, true);
    }

    /**
     * SOAP Fault devolvido com HTTP 200: lança a exceção com o motivo informado pelo servidor
     */
    protected function verificarFault(DOMDocument $dom): void
    {
        $fault = $dom->getElementsByTagNameNS('*', 'Fault')->item(0);
        if (!$fault instanceof DOMElement) {
            return;
        }
        $motivo = '';
        foreach (['Text', 'faultstring'] as $nome) {
            $no = $fault->getElementsByTagNameNS('*', $nome)->item(0) ?? $fault->getElementsByTagName($nome)->item(0);
            if ($no !== null && trim((string) $no->textContent) !== '') {
                $motivo = trim((string) $no->textContent);
                break;
            }
        }
        if ($motivo === '') {
            $motivo = trim((string) $fault->textContent);
        }
        throw DocumentsException::wrongDocument(4, 'SOAP Fault: ' . $motivo);
    }

    /**
     * Converte um elemento segundo as regras D-17
     *
     * @return stdClass|string
     */
    protected function converter(DOMElement $no)
    {
        $filhos = [];
        $texto = '';
        foreach ($no->childNodes as $filho) {
            if ($filho instanceof DOMElement) {
                $filhos[$filho->localName][] = $this->converter($filho);
            } elseif ($filho->nodeType === XML_TEXT_NODE || $filho->nodeType === XML_CDATA_SECTION_NODE) {
                $texto .= $filho->nodeValue;
            }
        }
        $atributos = new stdClass();
        $temAtributos = false;
        if ($no->attributes !== null) {
            foreach ($no->attributes as $atributo) {
                $atributos->{$atributo->localName} = $atributo->nodeValue;
                $temAtributos = true;
            }
        }
        if (empty($filhos) && !$temAtributos) {
            return trim($texto);
        }
        $obj = new stdClass();
        if ($temAtributos) {
            $obj->attributes = $atributos;
        }
        foreach ($filhos as $nome => $valores) {
            $obj->$nome = count($valores) === 1 ? $valores[0] : $valores;
        }
        if (empty($filhos) && trim($texto) !== '') {
            $obj->value = trim($texto);
        }
        return $obj;
    }

    /**
     * Copia o elemento para um documento novo, mantendo o namespace mas sem prefixos
     */
    protected function semPrefixo(DOMElement $no): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($this->copiar($dom, $no));
        return (string) $dom->saveXML($dom->documentElement);
    }

    protected function copiar(DOMDocument $dom, DOMNode $origem): DOMNode
    {
        if (!$origem instanceof DOMElement) {
            return $dom->importNode($origem, false);
        }
        $copia = $origem->namespaceURI
            ? $dom->createElementNS($origem->namespaceURI, (string) $origem->localName)
            : $dom->createElement((string) $origem->localName);
        if ($origem->attributes !== null) {
            foreach ($origem->attributes as $atributo) {
                $copia->setAttribute((string) $atributo->localName, (string) $atributo->nodeValue);
            }
        }
        foreach ($origem->childNodes as $filho) {
            $copia->appendChild($this->copiar($dom, $filho));
        }
        return $copia;
    }
}

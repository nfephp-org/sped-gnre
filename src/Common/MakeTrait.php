<?php

namespace NFePHP\Gnre\Common;

use DOMElement;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Strings;
use stdClass;

/**
 * Utilitários comuns aos montadores de XML (Make e MakeV1).
 * A classe que usa este trait precisa ter a propriedade pública array $errors.
 *
 * @package    NFePHP\Gnre\Common
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
trait MakeTrait
{
    /**
     * Acrescenta um filho só se o valor não for nulo nem vazio
     *
     * @param mixed $valor
     */
    protected function filho(Dom $dom, DOMElement $pai, string $nome, $valor): void
    {
        if ($valor === null || $valor === '') {
            return;
        }
        $pai->appendChild($dom->createElement($nome, (string) $valor));
    }

    /**
     * Formata um valor monetário com 2 casas; valor não numérico vira erro
     *
     * @param mixed $valor
     */
    protected function decimal($valor, string $descricao, bool $obrigatorio = false): ?string
    {
        if ($valor === null || $valor === '') {
            if ($obrigatorio) {
                $this->errors[] = "Preenchimento Obrigatório! $descricao";
            }
            return null;
        }
        if (!is_numeric($valor)) {
            $this->errors[] = "$descricao: valor numérico inválido [$valor] (use ponto como separador decimal).";
            return null;
        }
        return number_format((float) $valor, 2, '.', '');
    }

    /**
     * @param mixed $valor
     */
    protected function limitar($valor, int $tamanho): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }
        return mb_substr((string) $valor, 0, $tamanho);
    }

    /**
     * @param array<int, string> $possible
     */
    protected function equilizar(stdClass $std, array $possible): stdClass
    {
        return Strings::equilizeParameters(clone $std, $possible);
    }

    /**
     * Documento de trabalho sem formatação
     */
    protected function novoDom(): Dom
    {
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        return $dom;
    }
}

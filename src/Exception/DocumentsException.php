<?php

namespace NFePHP\Gnre\Exception;

/**
 * Erros relacionados aos documentos GNRE (lote, consultas e retornos)
 *
 * @package    NFePHP\Gnre\Exception
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class DocumentsException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @var array<int, string>
     */
    public static $list = [
        0 => "Este documento [{{msg}}] não é suportado. Confira a ordem dos parâmetros.",
        1 => "O arquivo indicado não é um documento GNRE válido.",
        2 => "O documento não contém a tag obrigatória {{msg}}.",
        3 => "O documento de resposta não contém o NODE {{msg}}.",
        4 => "O documento de resposta relata um erro {{msg}}.",
        5 => "Os documentos se referem a diferentes objetos. {{msg}}.",
        6 => "O argumento passado não é um XML válido.",
        7 => "Este xml não pertence ao projeto SPED-GNRE.",
        8 => "A configuração (config.json) não é válida {{msg}}.",
    ];

    /**
     * @param int $code índice de self::$list
     * @param string $msg texto que substitui {{msg}}
     * @return self
     */
    public static function wrongDocument(int $code, string $msg = ''): self
    {
        return new self(str_replace('{{msg}}', $msg, self::$list[$code] ?? self::$list[0]));
    }
}

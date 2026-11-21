<?php

namespace NFePHP\Gnre\Common;

use NFePHP\Gnre\Exception\InvalidArgumentException;
use NFePHP\Gnre\Exception\RuntimeException;
use stdClass;

/**
 * Sugestões por UF para o documento de origem da guia (tabela herdada da 0.x).
 *
 * ATENÇÃO: são apenas valores padrão. A regra oficial e vigente de cada UF e receita
 * (tipos de documento de origem aceitos, campos extras, exigências) deve ser obtida
 * com Tools::sefazConsultaConfigUf().
 *
 * @package    NFePHP\Gnre\Common
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class RegrasUf
{
    /**
     * @var stdClass|null
     */
    private static $regras;

    /**
     * Código sugerido do tipo de documento de origem (atributo tipo de documentoOrigem)
     *
     * @param string $uf UF favorecida
     * @param bool $difal guia de diferencial de alíquota (altera a sugestão em PE)
     */
    public static function tipoDocOrigem(string $uf, bool $difal = false): string
    {
        $regra = self::regra($uf);
        if ($difal && isset($regra->tipoDocOrigemDifal)) {
            return (string) $regra->tipoDocOrigemDifal;
        }
        return (string) ($regra->tipoDocOrigem ?? self::regras()->padrao->tipoDocOrigem);
    }

    /**
     * Se a UF espera o número ('numero') ou a chave ('chave') do documento fiscal
     */
    public static function tipoChaveDoc(string $uf): string
    {
        $regra = self::regra($uf);
        return (string) ($regra->tipoChaveDoc ?? self::regras()->padrao->tipoChaveDoc);
    }

    private static function regra(string $uf): stdClass
    {
        $uf = strtoupper(trim($uf));
        if (!in_array($uf, Tools::UFS, true)) {
            throw new InvalidArgumentException("UF inválida [$uf].");
        }
        return self::regras()->uf->$uf ?? new stdClass();
    }

    private static function regras(): stdClass
    {
        if (self::$regras === null) {
            $regras = json_decode((string) file_get_contents(__DIR__ . '/../../storage/regras_uf.json'));
            if (!$regras instanceof stdClass || !isset($regras->padrao, $regras->uf)) {
                throw new RuntimeException('Arquivo storage/regras_uf.json inválido.');
            }
            self::$regras = $regras;
        }
        return self::$regras;
    }
}

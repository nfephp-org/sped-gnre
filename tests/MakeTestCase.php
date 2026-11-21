<?php

namespace NFePHP\Gnre\Tests;

use NFePHP\Common\Validator;
use stdClass;

/**
 * Base dos testes do Make: atalho para stdClass e validação no XSD 2.00.
 */
abstract class MakeTestCase extends ToolsTestCase
{
    /**
     * @param array<string, mixed> $dados
     */
    protected function std(array $dados): stdClass
    {
        return (object) $dados;
    }

    protected function valida(string $xml, string $xsd = 'lote_gnre_v2.00.xsd'): void
    {
        $this->assertTrue(Validator::isValid($xml, __DIR__ . '/../schemes/PL_GNRE_200/' . $xsd));
    }
}

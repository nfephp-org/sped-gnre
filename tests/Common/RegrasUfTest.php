<?php

namespace NFePHP\Gnre\Tests\Common;

use NFePHP\Gnre\Common\RegrasUf;
use NFePHP\Gnre\Common\Tools;
use NFePHP\Gnre\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RegrasUfTest extends TestCase
{
    /**
     * Tabela da 0.x (LoteV2::getCodigoDoc / getNumDoc)
     */
    private const LEGADO = [
        'AM' => ['22', 'chave'],
        'PE' => ['22', 'chave'],
        'RJ' => ['24', 'chave'],
        'RS' => ['22', 'chave'],
        'SC' => ['24', 'chave'],
    ];

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public function ufsProvider(): array
    {
        $lista = [];
        foreach (Tools::UFS as $uf) {
            [$doc, $chave] = self::LEGADO[$uf] ?? ['10', 'numero'];
            $lista[$uf] = [$uf, $doc, $chave];
        }
        return $lista;
    }

    /**
     * @dataProvider ufsProvider
     */
    public function testIgualAoLegado(string $uf, string $tipoDoc, string $tipoChave): void
    {
        $this->assertSame($tipoDoc, RegrasUf::tipoDocOrigem($uf));
        $this->assertSame($tipoChave, RegrasUf::tipoChaveDoc($uf));
    }

    public function testDifalPernambuco(): void
    {
        $this->assertSame('24', RegrasUf::tipoDocOrigem('PE', true));
        $this->assertSame('24', RegrasUf::tipoDocOrigem('RJ', true));
        $this->assertSame('10', RegrasUf::tipoDocOrigem('SP', true));
    }

    public function testUfMinuscula(): void
    {
        $this->assertSame('24', RegrasUf::tipoDocOrigem('sc'));
    }

    public function testUfInvalida(): void
    {
        $this->expectException(InvalidArgumentException::class);
        RegrasUf::tipoDocOrigem('XX');
    }
}

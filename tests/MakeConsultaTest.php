<?php

namespace NFePHP\Gnre\Tests;

use NFePHP\Gnre\Exception\RuntimeException;
use NFePHP\Gnre\MakeConsulta;

class MakeConsultaTest extends MakeTestCase
{
    private const XSD = 'lote_consulta_guia_v2.00.xsd';

    public function testIgualAFixtureDaF2(): void
    {
        $make = new MakeConsulta();
        $make->tagConsulta($this->std([
            'uf' => 'SP',
            'CNPJ' => '06157250000116',
            'codBarras' => '85800000000123456789012345678901234567890123',
            'idConsulta' => '1',
            'tipoConsulta' => 'C',
        ]));

        $xml = $make->getXML();

        $this->assertSame(
            $this->c14n((string) file_get_contents(self::FIXTURES . '/xml/lote-consulta-guia.xml')),
            $this->c14n($xml)
        );
        $this->valida($xml, self::XSD);
    }

    public function testConsultaPorDocumentoOrigem(): void
    {
        $make = new MakeConsulta();
        $make->tagConsulta($this->std([
            'uf' => 'RJ',
            'docOrigem' => '5656',
            'docOrigemTipo' => '10',
            'tipoConsulta' => 'D',
        ]));

        $xml = $make->getXML();

        $this->assertStringContainsString('<docOrigem tipo="10">5656</docOrigem><tipoConsulta>D</tipoConsulta>', $xml);
        $this->assertStringNotContainsString('<emitenteId>', $xml);
        $this->valida($xml, self::XSD);
    }

    public function testDuasConsultas(): void
    {
        $make = new MakeConsulta();
        $make->tagConsulta($this->std(['uf' => 'SP', 'numControle' => '1234567890', 'tipoConsulta' => 'N']));
        $make->tagConsulta($this->std([
            'uf' => 'MG',
            'IE' => '0623079040081',
            'numControle' => '1234567891',
            'tipoConsulta' => 'N',
        ]));

        $xml = $make->getXML();

        $this->assertSame(2, substr_count($xml, '<consulta>'));
        $this->assertStringContainsString('<emitenteId><IE>0623079040081</IE></emitenteId>', $xml);
        $this->valida($xml, self::XSD);
    }

    /**
     * @return array<string, array{0: array<string, string>}>
     */
    public function consultaInvalidaProvider(): array
    {
        return [
            'tipoConsulta inválido' => [['uf' => 'SP', 'codBarras' => str_repeat('1', 44), 'tipoConsulta' => 'X']],
            'sem uf' => [['codBarras' => str_repeat('1', 44), 'tipoConsulta' => 'C']],
            'sem tipoConsulta' => [['uf' => 'SP', 'codBarras' => str_repeat('1', 44)]],
            'docOrigem sem tipo' => [['uf' => 'SP', 'docOrigem' => '5656', 'tipoConsulta' => 'D']],
        ];
    }

    /**
     * @dataProvider consultaInvalidaProvider
     * @param array<string, string> $dados
     */
    public function testConsultaInvalida(array $dados): void
    {
        $make = new MakeConsulta();
        $make->tagConsulta($this->std($dados));
        $this->assertNotEmpty($make->getErrors());
        $this->expectException(RuntimeException::class);
        $make->getXML();
    }

    public function test51Consultas(): void
    {
        $make = new MakeConsulta();
        for ($i = 1; $i <= 51; $i++) {
            $make->tagConsulta($this->std([
                'uf' => 'SP',
                'numControle' => (string) (1000000000 + $i),
                'tipoConsulta' => 'N',
            ]));
        }
        $this->assertNotEmpty($make->getErrors());
        $this->expectException(RuntimeException::class);
        $make->getXML();
    }

    public function testSemConsultas(): void
    {
        $this->expectException(RuntimeException::class);
        (new MakeConsulta())->getXML();
    }
}

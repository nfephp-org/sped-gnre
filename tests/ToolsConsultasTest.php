<?php

namespace NFePHP\Gnre\Tests;

use NFePHP\Common\Exception\ValidatorException;
use NFePHP\Gnre\Exception\InvalidArgumentException;
use NFePHP\Gnre\Tests\Common\SoapFake;
use NFePHP\Gnre\Tools;

class ToolsConsultasTest extends ToolsTestCase
{
    /**
     * @var SoapFake
     */
    private $fake;

    /**
     * @param array<string, mixed> $config
     */
    private function tools(array $config = []): Tools
    {
        $tools = new Tools($this->config($config), $this->certificado());
        $this->fake = new SoapFake();
        $tools->loadSoapClass($this->fake);
        return $tools;
    }

    public function testConsultaLoteIgualAoGolden(): void
    {
        $tools = $this->tools(['tpAmb' => 1]);

        $tools->sefazConsultaLote('1234567890');

        $p = $this->fake->getSendParams();
        $this->assertSame($this->golden('consulta-lote-prod.xml', 'TConsLote_GNRE'), $this->c14n($tools->lastRequest));
        $this->assertSame('https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLote', $p['url']);
        $this->assertSame('consultar', $p['operation']);
        $this->assertSame('http://www.gnre.pe.gov.br/webservice/GnreResultadoLote', $p['soapheader']->namespace);
        $this->assertSame('2.00', $p['soapheader']->data['versaoDados']);
    }

    public function testConsultaLoteComPdf(): void
    {
        $tools = $this->tools();

        $tools->sefazConsultaLote('2512345678', ['incluirPDFGuias' => 'S']);

        $this->assertStringContainsString(
            '</numeroRecibo><incluirPDFGuias>S</incluirPDFGuias>',
            $tools->lastRequest
        );
    }

    public function testConsultaLoteOpcoesNaOrdemDoXsd(): void
    {
        $tools = $this->tools();

        $tools->sefazConsultaLote('2512345678', ['incluirNoticias' => 'N', 'incluirPDFGuias' => 'S']);

        $this->assertStringContainsString(
            '<incluirPDFGuias>S</incluirPDFGuias><incluirNoticias>N</incluirNoticias>',
            $tools->lastRequest
        );
    }

    /**
     * @return array<string, array{0: string, 1: array<string, string>, 2: array<string, mixed>}>
     */
    public function consultaLoteInvalidaProvider(): array
    {
        return [
            'recibo vazio' => ['  ', [], []],
            'opção desconhecida' => ['2512345678', ['incluirPDF' => 'S'], []],
            'valor de opção inválido' => ['2512345678', ['incluirNoticias' => 'X'], []],
            'opção no layout 1.00' => ['2512345678', ['incluirPDFGuias' => 'N'], ['versao' => '1.00']],
        ];
    }

    /**
     * @dataProvider consultaLoteInvalidaProvider
     * @param array<string, string> $opcoes
     * @param array<string, mixed> $config
     */
    public function testConsultaLoteInvalida(string $recibo, array $opcoes, array $config): void
    {
        $tools = $this->tools($config);
        try {
            $this->expectException(InvalidArgumentException::class);
            $tools->sefazConsultaLote($recibo, $opcoes);
        } finally {
            $this->assertSame(0, $this->fake->getCalls());
        }
    }

    public function testReciboFormatoInvalido(): void
    {
        $this->expectException(ValidatorException::class);
        $this->tools()->sefazConsultaLote('12345');
    }

    public function testConfigUfIgualAoGolden(): void
    {
        $tools = $this->tools(['tpAmb' => 1]);

        $tools->sefazConsultaConfigUf('PR', '100099');

        $p = $this->fake->getSendParams();
        $this->assertSame(
            $this->golden('config-uf-prod.xml', 'TConsultaConfigUf'),
            $this->c14n($tools->lastRequest)
        );
        $this->assertSame('https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF', $p['url']);
        $this->assertSame('http://www.gnre.pe.gov.br/webservice/GnreConfigUF', $p['soapheader']->namespace);
        $this->assertSame('2.00', $p['soapheader']->data['versaoDados']);
    }

    public function testConfigUfCourier(): void
    {
        $tools = $this->tools();
        $tools->sefazConsultaConfigUf('SP', '100099', true);
        $this->assertStringContainsString('<receita courier="S">100099</receita>', $tools->lastRequest);
    }

    public function testConfigUfSemReceita(): void
    {
        $tools = $this->tools();
        $tools->sefazConsultaConfigUf('BA');
        $this->assertStringNotContainsString('<receita', $tools->lastRequest);
        $this->assertSame(1, $this->fake->getCalls());
    }

    public function testUfInvalida(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->tools()->sefazConsultaConfigUf('XX');
    }

    public function testCourierSemReceita(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->tools()->sefazConsultaConfigUf('SP', '', true);
    }
}

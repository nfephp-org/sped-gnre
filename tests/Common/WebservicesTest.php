<?php

namespace NFePHP\Gnre\Tests\Common;

use NFePHP\Gnre\Common\Webservices;
use NFePHP\Gnre\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class WebservicesTest extends TestCase
{
    private function webservices(): Webservices
    {
        return new Webservices((string) file_get_contents(__DIR__ . '/../../storage/wsgnre.json'));
    }

    public function testLoteRecepcaoProducao(): void
    {
        $ws = $this->webservices()->get('GnreLoteRecepcao', 1);
        $this->assertSame('https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao', $ws->url);
        $this->assertSame('http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao', $ws->namespace);
        $this->assertSame('"http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao"', $ws->action);
        $this->assertSame('processar', $ws->metodo);
        $this->assertNull($ws->versaoDados);
    }

    public function testHomologacaoMudaSoUrl(): void
    {
        $ws = $this->webservices()->get('GnreResultadoLote', 2);
        $this->assertSame('https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote', $ws->url);
        $this->assertSame('http://www.gnre.pe.gov.br/webservice/GnreResultadoLote', $ws->namespace);
    }

    public function testConsultaGuiaTemVersaoFixa(): void
    {
        $this->assertSame('2.00', $this->webservices()->get('GnreLoteRecepcaoConsulta', 2)->versaoDados);
        $this->assertSame('consultar', $this->webservices()->get('GnreResultadoLoteConsulta', 1)->metodo);
    }

    public function testServicoDesconhecido(): void
    {
        $this->expectException(RuntimeException::class);
        $this->webservices()->get('Xpto', 1);
    }

    public function testAmbienteInvalido(): void
    {
        $this->expectException(RuntimeException::class);
        $this->webservices()->get('GnreConfigUF', 3);
    }

    public function testJsonInvalido(): void
    {
        $this->expectException(RuntimeException::class);
        new Webservices('{');
    }
}

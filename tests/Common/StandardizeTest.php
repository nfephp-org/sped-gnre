<?php

namespace NFePHP\Gnre\Tests\Common;

use DOMDocument;
use DOMElement;
use NFePHP\Common\Validator;
use NFePHP\Gnre\Common\Standardize;
use NFePHP\Gnre\Exception\DocumentsException;
use NFePHP\Gnre\Make;
use PHPUnit\Framework\TestCase;
use stdClass;

class StandardizeTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/../fixtures/xml/';

    private function fixture(string $arquivo): string
    {
        return (string) file_get_contents(self::FIXTURES . $arquivo);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public function fixturesProvider(): array
    {
        return [
            'recibo' => ['retorno-recibo.xml', 'TRetLote_GNRE', 'lote_gnre_recibo_v1.00.xsd'],
            'resultado v2' => ['retorno-resultado-v2.xml', 'TResultLote_GNRE', 'lote_gnre_result_v2.00.xsd'],
            'resultado v1' => ['retorno-resultado-v1.xml', 'TResultLote_GNRE', 'lote_gnre_result_v1.00.xsd'],
            'config uf' => ['retorno-config-uf.xml', 'TConfigUf', 'config_uf_v1.00.xsd'],
        ];
    }

    /**
     * @dataProvider fixturesProvider
     */
    public function testFixturesValidas(string $arquivo, string $raiz, string $xsd): void
    {
        $origem = new DOMDocument();
        $origem->load(self::FIXTURES . $arquivo);
        $no = $origem->getElementsByTagNameNS('*', $raiz)->item(0);
        $this->assertInstanceOf(DOMElement::class, $no);
        $destino = new DOMDocument();
        $destino->appendChild($destino->importNode($no, true));
        $this->assertTrue(Validator::isValid(
            (string) $destino->saveXML(),
            __DIR__ . '/../../schemes/PL_GNRE_200/' . $xsd
        ));
    }

    public function testWhichIs(): void
    {
        $std = new Standardize();
        $this->assertSame('TRetLote_GNRE', $std->whichIs($this->fixture('retorno-recibo.xml')));
        $this->assertSame('TResultLote_GNRE', $std->whichIs($this->fixture('retorno-resultado-v2.xml')));
        $this->assertSame('TConfigUf', $std->whichIs($this->fixture('retorno-config-uf.xml')));
        $make = new Make();
        $make->tagGuia((object) ['ufFavorecida' => 'SP']);
        $this->assertSame('TLote_GNRE', $std->whichIs($make->getXML()));
    }

    public function testRecibo(): void
    {
        $std = (new Standardize())->toStd($this->fixture('retorno-recibo.xml'));
        $this->assertSame('2512345678', $std->recibo->numero);
        $this->assertSame('100', $std->situacaoRecepcao->codigo);
        $this->assertSame('Lote recebido com sucesso', $std->situacaoRecepcao->descricao);
    }

    public function testResultadoDuasGuias(): void
    {
        $std = (new Standardize())->toStd($this->fixture('retorno-resultado-v2.xml'));
        $this->assertIsArray($std->resultado->guia);
        $this->assertCount(2, $std->resultado->guia);
        $this->assertSame('2.00', $std->resultado->guia[0]->attributes->versao);
        $this->assertSame(
            '858100000013822599992000280100562200000000000000',
            $std->resultado->guia[1]->linhaDigitavel
        );
    }

    public function testAtributoComTexto(): void
    {
        $std = (new Standardize())->toStd($this->fixture('retorno-resultado-v2.xml'));
        $item = $std->resultado->guia[1]->itensGNRE->item;
        $this->assertIsArray($item->valor);
        $this->assertCount(7, $item->valor);
        $this->assertSame('12', $item->valor[1]->attributes->tipo);
        $this->assertSame('1.82', $item->valor[1]->value);
        $this->assertSame('22', $item->documentoOrigem->attributes->tipo);
        $this->assertSame('35200111111111111111550400000096131957613855', $item->documentoOrigem->value);
    }

    public function testUmaGuiaViraObjeto(): void
    {
        $dom = new DOMDocument();
        $dom->loadXML($this->fixture('retorno-resultado-v2.xml'));
        $guias = $dom->getElementsByTagNameNS('*', 'guia');
        $segunda = $guias->item(1);
        $this->assertInstanceOf(DOMElement::class, $segunda);
        $pai = $segunda->parentNode;
        $this->assertNotNull($pai);
        $pai->removeChild($segunda);

        $std = (new Standardize())->toStd((string) $dom->saveXML());

        $this->assertInstanceOf(stdClass::class, $std->resultado->guia);
        $this->assertSame('1', $std->resultado->guia->identificadorGuia);
    }

    public function testToArrayEToJson(): void
    {
        $standardize = new Standardize();
        $xml = $this->fixture('retorno-recibo.xml');
        $this->assertSame('2512345678', $standardize->toArray($xml)['recibo']['numero']);
        $this->assertEquals($standardize->toStd($xml), json_decode($standardize->toJson($xml)));
    }

    public function testToStringSemPrefixo(): void
    {
        $standardize = new Standardize($this->fixture('retorno-resultado-v2.xml'));
        $xml = (string) $standardize;
        $this->assertStringStartsWith('<TResultLote_GNRE', $xml);
        $this->assertStringNotContainsString('ns1:', $xml);
    }

    public function testNaoXml(): void
    {
        $this->expectException(DocumentsException::class);
        (new Standardize())->toStd('abc');
    }

    public function testRaizDesconhecida(): void
    {
        $this->expectException(DocumentsException::class);
        (new Standardize())->toStd('<foo/>');
    }

    public function testToStdSemDocumento(): void
    {
        $this->expectException(DocumentsException::class);
        (new Standardize())->toStd();
    }

    public function testSoapFaultRepassaMotivo(): void
    {
        $fault = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"><soap:Body><soap:Fault>'
            . '<soap:Code><soap:Value>soap:Receiver</soap:Value></soap:Code>'
            . '<soap:Reason><soap:Text xml:lang="pt">Erro interno no Portal</soap:Text></soap:Reason>'
            . '</soap:Fault></soap:Body></soap:Envelope>';

        $this->expectException(DocumentsException::class);
        $this->expectExceptionMessageMatches('/Erro interno no Portal/');
        (new Standardize())->toStd($fault);
    }
}

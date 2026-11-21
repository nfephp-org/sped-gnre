<?php

namespace NFePHP\Gnre\Tests;

use DOMDocument;
use DOMElement;
use DOMXPath;
use NFePHP\Common\Validator;
use NFePHP\Gnre\Exception\DocumentsException;
use NFePHP\Gnre\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

class SchemesTest extends TestCase
{
    private const RAIZ = __DIR__ . '/..';

    /**
     * Raízes de dados que podem aparecer nos golden XMLs (fora do envelope SOAP)
     */
    private const NOS_DE_DADOS = ['TLote_GNRE', 'TConsLote_GNRE', 'TConsultaConfigUf'];

    /**
     * @return array<string, array{0: string}>
     */
    public function xsdProvider(): array
    {
        $lista = [];
        foreach (glob(self::RAIZ . '/schemes/*/*.xsd') ?: [] as $xsd) {
            $lista[basename(dirname($xsd)) . '/' . basename($xsd)] = [$xsd];
        }
        return $lista;
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public function goldenProvider(): array
    {
        $arquivo = self::RAIZ . '/tests/fixtures/legacy/validos.txt';
        $lista = [];
        $linhas = is_file($arquivo) ? file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        foreach ($linhas ?: [] as $linha) {
            [$golden, $xsd] = explode(';', trim($linha));
            $pasta = strpos($xsd, 'v2.00') !== false ? 'PL_GNRE_200' : 'PL_GNRE_100';
            $lista[$golden] = [
                self::RAIZ . '/tests/fixtures/legacy/' . $golden,
                self::RAIZ . '/schemes/' . $pasta . '/' . $xsd,
            ];
        }
        return $lista;
    }

    /**
     * @dataProvider xsdProvider
     */
    public function testTodosOsXsdCarregam(string $xsd): void
    {
        $this->assertTrue((new DOMDocument())->load($xsd), "Falha ao carregar $xsd");
    }

    /**
     * @dataProvider xsdProvider
     */
    public function testIncludesExistem(string $xsd): void
    {
        $dom = new DOMDocument();
        $dom->load($xsd);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');
        $nos = $xpath->query('//xs:include/@schemaLocation | //xs:import/@schemaLocation');
        $this->assertNotFalse($nos);
        foreach ($nos as $no) {
            $this->assertFileExists(dirname($xsd) . '/' . $no->nodeValue);
        }
    }

    public function testExistemGoldenValidos(): void
    {
        $this->assertCount(4, $this->goldenProvider());
    }

    /**
     * @dataProvider goldenProvider
     */
    public function testGoldenValidos(string $golden, string $xsd): void
    {
        $origem = new DOMDocument();
        $origem->load($golden);
        $no = null;
        foreach (self::NOS_DE_DADOS as $nome) {
            $no = $origem->getElementsByTagNameNS('*', $nome)->item(0);
            if ($no instanceof DOMElement) {
                break;
            }
        }
        $this->assertInstanceOf(DOMElement::class, $no, "Nenhum nó de dados em $golden");
        $destino = new DOMDocument('1.0', 'UTF-8');
        $destino->appendChild($destino->importNode($no, true));
        $this->assertTrue(Validator::isValid((string) $destino->saveXML(), $xsd));
    }

    public function testDocumentsExceptionImplementaInterface(): void
    {
        $this->assertInstanceOf(ExceptionInterface::class, DocumentsException::wrongDocument(1));
    }

    public function testDocumentsExceptionSubstituiMensagem(): void
    {
        $e = DocumentsException::wrongDocument(3, 'numeroRecibo');
        $this->assertStringContainsString('numeroRecibo', $e->getMessage());
    }
}

<?php

namespace NFePHP\Gnre\Tests;

use DOMDocument;
use DOMElement;
use NFePHP\Common\Certificate;
use PHPUnit\Framework\TestCase;

/**
 * Base dos testes de Tools: config, certificado de teste e comparação de XML.
 */
abstract class ToolsTestCase extends TestCase
{
    protected const FIXTURES = __DIR__ . '/fixtures';

    /**
     * @param array<string, mixed> $sobrescreve chaves a trocar; valor null remove a chave
     */
    protected function config(array $sobrescreve = []): string
    {
        $config = [
            'tpAmb' => 2,
            'razaosocial' => 'EMPRESA TESTE',
            'cnpj' => '06157250000116',
            'siglaUF' => 'SP',
            'versao' => '2.00',
        ];
        foreach ($sobrescreve as $chave => $valor) {
            if ($valor === null) {
                unset($config[$chave]);
                continue;
            }
            $config[$chave] = $valor;
        }
        return (string) json_encode($config);
    }

    protected function certificado(): Certificate
    {
        return Certificate::readPfx(
            (string) file_get_contents(self::FIXTURES . '/certs/cert_cnpj_06157250000116_senha_minhasenha.pfx'),
            'minhasenha'
        );
    }

    /**
     * C14N do nó $raiz do golden (os golden foram gravados com indentação).
     */
    protected function golden(string $arquivo, string $raiz): string
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->load(self::FIXTURES . '/legacy/' . $arquivo);
        $no = $dom->getElementsByTagNameNS('*', $raiz)->item(0);
        $this->assertInstanceOf(DOMElement::class, $no, "Nó $raiz não encontrado em $arquivo");
        return (string) $no->C14N();
    }

    /**
     * Forma canônica de um XML, ignorando espaços entre as tags.
     */
    protected function c14n(string $xml): string
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        $this->assertInstanceOf(DOMElement::class, $dom->documentElement);
        return (string) $dom->documentElement->C14N();
    }
}

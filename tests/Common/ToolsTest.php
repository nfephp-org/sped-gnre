<?php

namespace NFePHP\Gnre\Tests\Common;

use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Gnre\Common\Tools;
use NFePHP\Gnre\Exception\DocumentsException;
use NFePHP\Gnre\Exception\InvalidArgumentException;
use NFePHP\Gnre\Tests\ToolsTestCase;

class ToolsTest extends ToolsTestCase
{
    public function testConfigValido(): void
    {
        $tools = new Tools($this->config(), $this->certificado());
        $this->assertSame(2, $tools->tpAmb);
        $this->assertSame('2.00', $tools->versao);
        $this->assertSame('SP', $tools->config->siglaUF);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public function configInvalidoProvider(): array
    {
        return [
            'json quebrado' => ['{'],
            'tpAmb 3' => [$this->config(['tpAmb' => 3])],
            'versao 3.00' => [$this->config(['versao' => '3.00'])],
            'siglaUF AN' => [$this->config(['siglaUF' => 'AN'])],
            'sem cnpj e cpf' => [$this->config(['cnpj' => null])],
        ];
    }

    /**
     * @dataProvider configInvalidoProvider
     */
    public function testConfigInvalido(string $config): void
    {
        $this->expectException(DocumentsException::class);
        new Tools($config, $this->certificado());
    }

    public function testCpfNoLugarDoCnpj(): void
    {
        $tools = new Tools($this->config(['cnpj' => null, 'cpf' => '90483926086']), $this->certificado());
        $this->assertSame('90483926086', $tools->config->cpf);
    }

    public function testSetEnvironment(): void
    {
        $tools = new Tools($this->config(), $this->certificado());
        $tools->setEnvironment(1);
        $this->assertSame(1, $tools->tpAmb);
        $this->expectException(InvalidArgumentException::class);
        $tools->setEnvironment(5);
    }

    public function testVersion(): void
    {
        $tools = new Tools($this->config(), $this->certificado());
        $this->assertSame('2.00', $tools->version());
        $this->assertSame('1.00', $tools->version('1.00'));
        $this->assertSame('1.00', $tools->versao);
        $this->expectException(InvalidArgumentException::class);
        $tools->version('9.99');
    }

    public function testLoadSoapClass(): void
    {
        $tools = new Tools($this->config(), $this->certificado());
        $fake = new SoapFake();
        $tools->loadSoapClass($fake);
        $this->assertSame($fake, $tools->soap);
    }

    public function testCheckSoapCriaSoapCurl(): void
    {
        $tools = new class ($this->config(), $this->certificado()) extends Tools {
            public function verificar(): void
            {
                $this->checkSoap();
            }
        };
        $this->assertNull($tools->soap);
        $tools->verificar();
        $this->assertInstanceOf(SoapCurl::class, $tools->soap);
    }
}

<?php

namespace NFePHP\Gnre\Tests\Common;

use NFePHP\Common\Soap\SoapBase;
use NFePHP\Common\Soap\SoapInterface;

/**
 * Substitui o SoapCurl nos testes: não faz rede e guarda os parâmetros de send().
 */
class SoapFake extends SoapBase implements SoapInterface
{
    /**
     * @var string
     */
    protected $returnValue = '';

    /**
     * @var array<string, mixed>
     */
    protected $sendParams = [];

    /**
     * @var int
     */
    protected $calls = 0;

    /**
     * @param string $url
     * @param string $operation
     * @param string $action
     * @param int $soapver
     * @param array<mixed> $parameters
     * @param array<string, string> $namespaces
     * @param string $request
     * @param \SoapHeader|null $soapheader
     * @return string
     */
    public function send(
        $url,
        $operation = '',
        $action = '',
        $soapver = SOAP_1_2,
        $parameters = [],
        $namespaces = [],
        $request = '',
        $soapheader = null
    ) {
        $this->calls++;
        $this->sendParams = [
            'url' => $url,
            'operation' => $operation,
            'action' => $action,
            'soapver' => $soapver,
            'parameters' => $parameters,
            'namespaces' => $namespaces,
            'request' => $request,
            'soapheader' => $soapheader,
        ];
        return $this->returnValue;
    }

    public function setReturnValue(string $returnValue): void
    {
        $this->returnValue = $returnValue;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSendParams(): array
    {
        return $this->sendParams;
    }

    public function getCalls(): int
    {
        return $this->calls;
    }

    /**
     * Envelope SOAP que o SoapCurl montaria com os parâmetros do último send()
     */
    public function envelope(): string
    {
        return $this->makeEnvelopeSoap(
            $this->sendParams['request'],
            $this->sendParams['namespaces'],
            $this->sendParams['soapver'],
            $this->sendParams['soapheader']
        );
    }
}

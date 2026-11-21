<?php

namespace Sped\Gnre\Test\Sefaz;

use PHPUnit\Framework\TestCase;
use Sped\Gnre\Exception\ConnectionFactoryUnavailable;
use Sped\Gnre\Sefaz\Send;

/**
 * @covers Sped\Gnre\Sefaz\Send
 * @covers Sped\Gnre\Exception\ConnectionFactoryUnavailable
 */
class SendTest extends TestCase
{

    private $setup;
    private $objetoSefaz;

    public function setUp():void
    {
        $this->setup = $this->createMock('\Sped\Gnre\Configuration\Setup');
        $this->objetoSefaz = $this->createMock('\Sped\Gnre\Sefaz\ObjetoSefaz');
    }

    public function testDeveLancarExcecaoAoNaoSetarUmaConnectionFactoryParaSerUsada()
    {
        $this->expectException(ConnectionFactoryUnavailable::class);

        $send = new Send($this->setup);
        $send->sefaz($this->objetoSefaz);
    }

    public function testDeveSetarUmaConnectionFactoryParaSerUsada()
    {
        $connectionFactory = $this->createMock('\Sped\Gnre\Webservice\ConnectionFactory');

        $send = new Send($this->setup);
        $this->assertInstanceOf('\Sped\Gnre\Sefaz\Send', $send->setConnectionFactory($connectionFactory));
    }

    public function testDeveRetornarUmaConnectionFactory()
    {
        $connectionFactory = $this->createMock('\Sped\Gnre\Webservice\ConnectionFactory');

        $send = new Send($this->setup);
        $send->setConnectionFactory($connectionFactory);

        $this->assertInstanceOf('\Sped\Gnre\Webservice\ConnectionFactory', $send->getConnectionFactory());
    }

    public function testDeveRealizarAconexaoComAsefaz()
    {
        $connection = $this->getMockBuilder('\Sped\Gnre\Webservice\Connection')
                ->disableOriginalConstructor()
                ->getMock();
        $connection->expects($this->once())
                ->method('doRequest')
                ->will($this->returnValue(true));

        $connectionFactory = $this->createMock('\Sped\Gnre\Webservice\ConnectionFactory');
        $connectionFactory->expects($this->once())
                ->method('createConnection')
                ->will($this->returnValue($connection));

        $send = new Send($this->setup);
        $send->setConnectionFactory($connectionFactory);
        $send->sefaz($this->objetoSefaz);
    }

    public function testDeveExibirDebug()
    {
        $connection = $this->getMockBuilder('\Sped\Gnre\Webservice\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('doRequest')
            ->will($this->returnValue(true));

        $connectionFactory = $this->createMock('\Sped\Gnre\Webservice\ConnectionFactory');
        $connectionFactory->expects($this->once())
            ->method('createConnection')
            ->will($this->returnValue($connection));

        $this->setup->expects($this->once())
            ->method('getDebug')
            ->will($this->returnValue(true));

        $send = new Send($this->setup);
        $send->setConnectionFactory($connectionFactory);
        $send->sefaz($this->objetoSefaz);
    }
}

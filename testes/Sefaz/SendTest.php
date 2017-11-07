<?php

namespace Sped\Gnre\Test\Sefaz;

use Sped\Gnre\Sefaz\Send;
use PHPUnit\Framework\TestCase;

/**
 * @covers Sped\Gnre\Sefaz\Send
 * @covers Sped\Gnre\Exception\ConnectionFactoryUnavailable
 */
class SendTest extends TestCase
{

    private $setup;
    private $objetoSefaz;

    public function setUp()
    {
        $this->setup = $this->createMock('\Sped\Gnre\Configuration\Setup');
        $this->objetoSefaz = $this->createMock('\Sped\Gnre\Sefaz\ObjetoSefaz');
    }

    /**
     * @expectedException \Sped\Gnre\Exception\ConnectionFactoryUnavailable
     */
    public function testDeveLancarExcecaoAoNaoSetarUmaConnectionFactoryParaSerUsada()
    {
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

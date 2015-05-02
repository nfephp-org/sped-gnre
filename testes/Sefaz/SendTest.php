<?php

namespace Gnre\Sefaz\Test;

use Gnre\Sefaz\Send;

/**
 * @covers Gnre\Sefaz\Send
 * @covers Gnre\Exception\ConnectionFactoryUnavailable
 */
class SendTest extends \PHPUnit_Framework_TestCase {

    private $setup;
    private $objetoSefaz;

    public function setUp() {
        $this->setup = $this->getMock('\Gnre\Configuration\Setup');
        $this->objetoSefaz = $this->getMock('\Gnre\Sefaz\ObjetoSefaz');
    }

    /**
     * @expectedException Gnre\Exception\ConnectionFactoryUnavailable
     */
    public function testDeveLancarExcecaoAoNaoSetarUmaConnectionFactoryParaSerUsada() {
        $send = new Send($this->setup);
        $send->sefaz($this->objetoSefaz);
    }

    public function testDeveSetarUmaConnectionFactoryParaSerUsada() {
        $connectionFactory = $this->getMock('\Gnre\Webservice\ConnectionFactory');

        $send = new Send($this->setup);
        $this->assertInstanceOf('\Gnre\Sefaz\Send', $send->setConnectionFactory($connectionFactory));
    }

    public function testDeveRetornarUmaConnectionFactory() {
        $connectionFactory = $this->getMock('\Gnre\Webservice\ConnectionFactory');

        $send = new Send($this->setup);
        $send->setConnectionFactory($connectionFactory);

        $this->assertInstanceOf('\Gnre\Webservice\ConnectionFactory', $send->getConnectionFactory());
    }

    public function testDeveRealizarAconexaoComAsefaz() {
        $connection = $this->getMockBuilder('\Gnre\Webservice\Connection')
                ->disableOriginalConstructor()
                ->getMock();
        $connection->expects($this->once())
                ->method('doRequest')
                ->will($this->returnValue(true));

        $connectionFactory = $this->getMock('\Gnre\Webservice\ConnectionFactory');
        $connectionFactory->expects($this->once())
                ->method('createConnection')
                ->will($this->returnValue($connection));

        $send = new Send($this->setup);
        $send->setConnectionFactory($connectionFactory);
        $send->sefaz($this->objetoSefaz);
    }

}

<?php

namespace Sped\Gnre\Sefaz\Test;

use Sped\Gnre\Sefaz\Send;

/**
 * @covers Sped\Gnre\Sefaz\Send
 * @covers Sped\Gnre\Exception\ConnectionFactoryUnavailable
 */
class SendTest extends \PHPUnit_Framework_TestCase {

    private $setup;
    private $objetoSefaz;

    public function setUp() {
        $this->setup = $this->getMock('\Sped\Gnre\Configuration\Setup');
        $this->objetoSefaz = $this->getMock('\Sped\Gnre\Sefaz\ObjetoSefaz');
    }

    /**
     * @expectedException \Sped\Gnre\Exception\ConnectionFactoryUnavailable
     */
    public function testDeveLancarExcecaoAoNaoSetarUmaConnectionFactoryParaSerUsada() {
        $send = new Send($this->setup);
        $send->sefaz($this->objetoSefaz);
    }

    public function testDeveSetarUmaConnectionFactoryParaSerUsada() {
        $connectionFactory = $this->getMock('\Sped\Gnre\Webservice\ConnectionFactory');

        $send = new Send($this->setup);
        $this->assertInstanceOf('\Sped\Gnre\Sefaz\Send', $send->setConnectionFactory($connectionFactory));
    }

    public function testDeveRetornarUmaConnectionFactory() {
        $connectionFactory = $this->getMock('\Sped\Gnre\Webservice\ConnectionFactory');

        $send = new Send($this->setup);
        $send->setConnectionFactory($connectionFactory);

        $this->assertInstanceOf('\Sped\Gnre\Webservice\ConnectionFactory', $send->getConnectionFactory());
    }

    public function testDeveRealizarAconexaoComAsefaz() {
        $connection = $this->getMockBuilder('\Sped\Gnre\Webservice\Connection')
                ->disableOriginalConstructor()
                ->getMock();
        $connection->expects($this->once())
                ->method('doRequest')
                ->will($this->returnValue(true));

        $connectionFactory = $this->getMock('\Sped\Gnre\Webservice\ConnectionFactory');
        $connectionFactory->expects($this->once())
                ->method('createConnection')
                ->will($this->returnValue($connection));

        $send = new Send($this->setup);
        $send->setConnectionFactory($connectionFactory);
        $send->sefaz($this->objetoSefaz);
    }

}

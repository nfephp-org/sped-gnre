<?php

namespace Gnre\Webservice\Test;

use Gnre\Webservice\ConnectionFactory;

/**
 * @covers \Gnre\Webservice\ConnectionFactory
 */
class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testDeveRetornarUmaNovaInstanciaDeConnection() {
        $setup = $this->getMockForAbstractClass('\Gnre\Configuration\Setup');

        $factory = new ConnectionFactory();
        $connection = $factory->createConnection($setup, array(), '<env:soap>my data</env:soap>');

        $this->assertInstanceOf('\Gnre\Webservice\Connection', $connection);
    }

}

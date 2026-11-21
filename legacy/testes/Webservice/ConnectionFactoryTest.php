<?php

namespace Sped\Gnre\Test\Sefaz;

use PHPUnit\Framework\TestCase;
use Sped\Gnre\Webservice\ConnectionFactory;

/**
 * @covers \Sped\Gnre\Webservice\ConnectionFactory
 */
class ConnectionFactoryTest extends TestCase
{

    public function testDeveRetornarUmaNovaInstanciaDeConnection()
    {
        $setup = $this->getMockForAbstractClass('\Sped\Gnre\Configuration\Setup');

        $factory = new ConnectionFactory();
        $connection = $factory->createConnection($setup, array(), '<env:soap>my data</env:soap>');

        $this->assertInstanceOf('\Sped\Gnre\Webservice\Connection', $connection);
    }
}

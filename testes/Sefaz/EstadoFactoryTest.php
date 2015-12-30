<?php

use Sped\Gnre\Sefaz\EstadoFactory;

/**
 * @covers Sped\Gnre\Sefaz\EstadoFactory
 */
class EstadoFactoryTest extends PHPUnit_Framework_TestCase
{

    public function testShouldReturnAnObjectWhenIsGivenAexistingClass()
    {
        $estado = new EstadoFactory();

        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\Padrao', $estado->create('BA'));
        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\BA', $estado->create('BA'));
    }

    public function testShouldReturnACwhenAclassDoesExists()
    {
        $estado = new EstadoFactory();

        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\Padrao', $estado->create('AC'));
        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\AC', $estado->create('AC'));
    }
}

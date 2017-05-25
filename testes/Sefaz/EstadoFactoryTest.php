<?php

namespace Sped\Gnre\Test\Sefaz;

use Sped\Gnre\Sefaz\EstadoFactory;

/**
 * @covers Sped\Gnre\Sefaz\EstadoFactory
 */
class EstadoFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldReturnAnObjectWhenIsGivenAexistingClass()
    {
        $estado = new EstadoFactory();

        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\Padrao', $estado->create('BA'));
        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\BA', $estado->create('BA'));
    }

    public function testShouldReturnACObjectwhenAclassDoesExists()
    {
        $estado = new EstadoFactory();

        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\Padrao', $estado->create('AC'));
        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\AC', $estado->create('AC'));
    }

    public function testReturnAdefaultObject()
    {
        $estado = new EstadoFactory();

        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\BA', $estado->create('EstadoNaoExistente'));
    }

    public function testShouldCreateACObjectFromFactory()
    {
        $factory = new EstadoFactory();
        $estado = $factory->create('AC');

        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\Padrao', $estado);
        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\AC', $estado);
    }

    public function testShouldCreateALObjectFromFactory()
    {
        $factory = new EstadoFactory();
        $estado = $factory->create('AL');

        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\Padrao', $estado);
        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\AL', $estado);
    }

    public function testShouldCreateAMObjectFromFactory()
    {
        $factory = new EstadoFactory();
        $estado = $factory->create('AM');

        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\Padrao', $estado);
        $this->assertInstanceOf('Sped\Gnre\Sefaz\Estados\AM', $estado);
    }
}

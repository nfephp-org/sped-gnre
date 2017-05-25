<?php

namespace Sped\Gnre\Test\Sefaz;

use Sped\Gnre\Sefaz\boolen;

/**
 * @covers Sped\Gnre\Sefaz\ConsultaGnre
 */
class ConsultaGnreTest extends \PHPUnit_Framework_TestCase
{

    public function testDeveDefinirOreciboParaSerUtilizado()
    {
        $gnreConsulta = new MinhaConsultaGnre();
        $this->assertNull($gnreConsulta->setRecibo(12345));
    }

    public function testDeveRetornarOreciboDefinido()
    {
        $gnreConsulta = new MinhaConsultaGnre();
        $gnreConsulta->setRecibo(123456);

        $this->assertEquals(123456, $gnreConsulta->getRecibo());
    }

    public function testDeveDefinirOambienteParaSerUtilizado()
    {
        $gnreConsulta = new MinhaConsultaGnre();
        $this->assertNull($gnreConsulta->setEnvironment(1));
    }

    public function testDeveRetornarOambienteDefinido()
    {
        $gnreConsulta = new MinhaConsultaGnre();
        $gnreConsulta->setEnvironment(1);

        $this->assertEquals(1, $gnreConsulta->getEnvironment());
    }
}

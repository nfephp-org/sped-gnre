<?php

/**
 * @covers Sped\Gnre\Sefaz\Guia
 * @covers Sped\Gnre\Exception\UndefinedProperty
 */
class GuiaTest extends \PHPUnit_Framework_TestCase {

    public function testDeveSetarOvalorAumaPropriedadeExistenteDaClasse() {
        $gnreGuia = new Sped\Gnre\Sefaz\Guia();
        $gnreGuia->c01_UfFavorecida = 'SP';

        $this->assertEquals('SP', $gnreGuia->c01_UfFavorecida);
    }

    /**
     * @expectedException Sped\Gnre\Exception\UndefinedProperty
     * @expectedExceptionMessage Não foi possível encontrar o atributo desejado na classe
     * @expectedExceptionCode 100
     */
    public function testAcessarUmaPropriedadeQueNaoExisteNaClasse() {
        $gnreGuia = new Sped\Gnre\Sefaz\Guia();
        $gnreGuia->teste = 'SP';
    }

}

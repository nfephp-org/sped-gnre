<?php

/*
* Este arquivo é parte do programa GNRE PHP
* GNRE PHP é um software livre; você pode redistribuí-lo e/ou
* modificá-lo dentro dos termos da Licença Pública Geral GNU como
* publicada pela Fundação do Software Livre (FSF); na versão 2 da
* Licença, ou (na sua opinião) qualquer versão.
* Este programa é distribuído na esperança de que possa ser útil,
* mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
* MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
* Licença Pública Geral GNU para maiores detalhes.
* Você deve ter recebido uma cópia da Licença Pública Geral GNU
* junto com este programa, se não, escreva para a Fundação do Software
* Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

/**
 * @covers Gnre\Sefaz\LoteGnre
 */
class LoteTest extends PHPUnit_Framework_TestCase {

    private $lote;

    public function setUp() {
        $this->lote = new Gnre\Sefaz\Lote();
    }

    public function tearDown() {
        $this->lote = null;
    }

    public function testAdicionarUmaGuiaAoLote() {
        $this->lote->addGuia(new Gnre\Sefaz\Guia());
        $this->assertEquals(1, count($this->lote->getGuias()));
    }

    public function testBuscarUmaGuiaEmEspecifico() {
        $this->lote->addGuia(new Gnre\Sefaz\Guia());
        $this->lote->addGuia(new Gnre\Sefaz\Guia());

        $this->assertInstanceOf('Gnre\Sefaz\Guia', $this->lote->getGuia(0));
        $this->assertInstanceOf('Gnre\Sefaz\Guia', $this->lote->getGuia(1));
    }

}

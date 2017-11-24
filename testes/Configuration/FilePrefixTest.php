<?php

/*
 * Este arquivo é parte do programa GNRE PHP
 * GNRE PHP é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como
 * publicada pela Fundação do Software Livre (FSF); na versão 2 da
 * Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil,
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU
 * junto com este programa, se não, escreva para a Fundação do Software
 * Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Sped\Gnre\Test\Configuration;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Sped\Gnre\Configuration\FilePrefix
 */
class FilePrefixTest extends TestCase
{

    public function testPassarAoAplicarUmPrefixoEmUmArquivo()
    {
        $prefix = new \Sped\Gnre\Configuration\FilePrefix();
        $prefix->setPrefix('meuPref');
        $this->assertEquals('/var/www/filemeuPref.doc', $prefix->apply('/var/www/file.doc'));
    }

    public function testPassarSemAdicionarPrefixoEmUmArquivo()
    {
        $prefix = new \Sped\Gnre\Configuration\FilePrefix();
        $this->assertEquals('/path/to/foo.doc', $prefix->apply('/path/to/foo.doc'));
    }

    public function testPassarAoEnviarUmCaminhoDeArquivoVazio()
    {
        $prefix = new \Sped\Gnre\Configuration\FilePrefix();
        $this->assertEmpty($prefix->apply(''));
    }
}

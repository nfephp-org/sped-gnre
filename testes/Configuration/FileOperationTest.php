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

namespace Gnre\Configuration\Test;

use Gnre\Configuration\FileOperation;

/**
 * @covers Gnre\Configuration\FileOperation
 * @covers Gnre\Exception\UnreachableFile
 */
class FileOperationTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException Gnre\Exception\UnreachableFile
     */
    public function testArquivoInformadoNaoExiste() {
        $myFile = new MyFile('/foo/bar.txt');
    }

    public function testArquivoInformadoExistente() {
        $file = __DIR__ . '/../../exemplos/estrutura-lote-completo-gnre.xml';
        $myFile = new MyFile($file);
    }

}

class MyFile extends FileOperation {

    public function writeFile($content, \Gnre\Configuration\FilePrefix $filePrefix) {
        return null;
    }

}

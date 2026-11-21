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
 * @covers Sped\Gnre\Configuration\CertificatePfx
 */
class TestCertificatePfx extends TestCase
{

    public function testPassarAoCriarChavePrivadaApartirDoCertificado()
    {
        $stubFileOperation = $this->getMockBuilder('\Sped\Gnre\Configuration\CertificatePfxFileOperation')
                ->disableOriginalConstructor()
                ->getMock();

        $stubFileOperation->expects($this->once())
                ->method('writeFile')
                ->will($this->returnValue('vfs://certificadoDir/metadata/certificado_Private.pem'));

        $certificatePfx = new \Sped\Gnre\Configuration\CertificatePfx($stubFileOperation, 'senha');
        $caminhoDoArquivoCriado = $certificatePfx->getPrivateKey();

        $this->assertEquals('vfs://certificadoDir/metadata/certificado_Private.pem', $caminhoDoArquivoCriado);
    }

    public function testPassarAoCriarCertificadoPemApartirDoCertificado()
    {
        $mockFileOperation = $this->getMockBuilder('\Sped\Gnre\Configuration\CertificatePfxFileOperation')
                ->disableOriginalConstructor()
                ->getMock();

        $mockFileOperation->expects($this->once())
                ->method('writeFile')
                ->will($this->returnValue('vfs://certificadoDir/metadata/certificado_pemKEY.pem'));

        $certificatePfx = new \Sped\Gnre\Configuration\CertificatePfx($mockFileOperation, 'senha');
        $caminhoDoArquivoCriado = $certificatePfx->getCertificatePem();

        $this->assertEquals('vfs://certificadoDir/metadata/certificado_pemKEY.pem', $caminhoDoArquivoCriado);
    }
}

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

namespace Gnre\Configuration;

use Gnre\Configuration\Setup;
use Gnre\Configuration\CertificatePfx;

/**
 * Classe de exemplo com a configuraçao necessaria para que uma guia seja
 * emitida com sucesso para o webservice da SEFAZ.
 * @package     gnre
 * @subpackage  configuration
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0-0.0
 */
class Development extends Setup {

    /**
     * Objeto necessário para extrair os dados do certificado
     * @var \Gnre\Configuration\CertificatePfx 
     */
    private $certificatePfx;

    public function __construct() {
        $this->certificatePfx = new CertificatePfx($this->getCertificateDirectory(), $this->getCertificateName(), $this->getCertificatePassword());
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl() {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificateCnpj() {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificateDirectory() {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificateName() {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificatePassword() {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificatePemFile() {
        return $this->certificatePfx->getCertificatePem();
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment() {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getPrivateKey() {
        return $this->certificatePfx->getPrivateKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getProxyIp() {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getProxyPass() {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getProxyPort() {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getProxyUser() {
        return '';
    }

}

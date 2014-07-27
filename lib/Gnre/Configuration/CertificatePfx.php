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

use Gnre\Exception\CannotOpenCertificate;
use Gnre\Exception\UnreachableFile;
use Gnre\Exception\UnableToWriteFile;

/**
 * Classe responsável por extrair os dados de um certificado baseado 
 * nos parâmetros passados para enviar uma consulta para a sefaz com sucesso
 * @package     gnre
 * @subpackage  configuration
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0-0.0
 */
class CertificatePfx {

    /**
     * Atributo que armazena os dados extraidos do certificado com a função openssl_pkcs12_read
     * @var array 
     */
    private $dataCertificate = array();

    /**
     * Caminho em que o certificado físico está alocado
     * @var string 
     */
    private $pathCertificate;

    /**
     * O nome do certificado para ser utilizado com a extensão .pfx
     * por exemplo: meu_certificado.px
     * @var string 
     */
    private $nameCertificate;

    /**
     * A senha correspondente ao certificado para ser aberto
     * @var string 
     */
    private $password;

    /**
     * O nome da pasta em que os meta dados dos certificados são armazenados essa pasta 
     * ficará abaixo da pasta /certs ficando então /certs/metadata
     * @var string 
     */
    private $metadataFolder = 'metadata';

    /**
     * Inicializa os dados necessários para que a classe trabalhe 
     * corretamente com seus métodos
     * @param  string $pathCertificate  O caminho em que o certificado se encontra poe exemplo /var/tmp
     * @param  string $nameCertificate  O nome do certificado que será utilizado para extrair os dados
     * @param  string $password  A senha do certificado enviado
     * @throws Gnre\Exception\UnreachableFile  Caso não seja encontrado o arquivo informado
     * @since  1.0-0.0
     */
    public function __construct($pathCertificate, $nameCertificate, $password) {
        if (!file_exists($pathCertificate . $nameCertificate)) {
            throw new UnreachableFile($pathCertificate . $nameCertificate);
        }

        $this->pathCertificate = $pathCertificate;
        $this->nameCertificate = $nameCertificate;
        $this->password = $password;

        $key = file_get_contents($this->pathCertificate . $this->nameCertificate);

        if (!openssl_pkcs12_read($key, $this->dataCertificate, $this->password)) {
            throw new CannotOpenCertificate($this->pathCertificate . $this->nameCertificate);
        }
    }

    /**
     * Cria um arquivo na pasta definida nas configurações padrões (/certs/metadata) com a 
     * chave privada do certificado. Para salvar o novo arquivo é utilizado
     * o mesmo nome do certificado com a extensão .pem por exemplo: certificado1.pfx_priKEY.pem
     * @throws Gnre\Exception\UnableToWriteFile Se a pasta de destino não tiver permissão para escrita
     * @return string  Retorna uma string com o caminho e o nome do arquivo que foi criado
     * @since  1.0-0.0
     */
    public function getPrivateKey() {
        $privatekey = $this->nameCertificate . '_priKEY.pem';
        if (!file_put_contents($this->pathCertificate . $this->metadataFolder . '/' . $privatekey, $this->dataCertificate['pkey'])) {
            throw new UnableToWriteFile($this->pathCertificate . $privatekey);
        }

        return $this->pathCertificate . $this->metadataFolder . '/' . $privatekey;
    }

    /**
     * Cria um arquivo na pasta definida nas configurações padrões (/certs/metadata) com a 
     * chave privada do certificado. Para salvar o novo arquivo é utilizado
     * o mesmo nome do certificado com a extensão .pem por exemplo: certificado1.pfx.pem
     * @throws Gnre\Exception\UnableToWriteFile Se a pasta de destino não tiver permissão para escrita
     * @return string Retorna uma string com o caminho e o nome do arquivo que foi criado
     * @since  1.0-0.0
     */
    public function getCertificatePem() {
        $certificatePem = $this->nameCertificate . '.pem';
        if (!file_put_contents($this->pathCertificate . $this->metadataFolder . '/' . $certificatePem, $this->dataCertificate['cert'])) {
            throw new UnableToWriteFile($this->pathCertificate . $certificatePem);
        }

        return $this->pathCertificate . $this->metadataFolder . '/' . $certificatePem;
    }

}

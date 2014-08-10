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

use Gnre\Configuration\CertificatePfxFileOperation;

/**
 * Classe responsável por extrair os dados de um certificado baseado 
 * nos parâmetros passados para enviar uma consulta para a sefaz com sucesso
 * @package     gnre
 * @subpackage  configuration
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class CertificatePfx {

    /**
     * Atributo que armazena os dados extraidos do certificado com a função openssl_pkcs12_read
     * @var array 
     */
    private $dataCertificate = array();

    /**
     * Objecto necessário para realizar operações de criação de arquivos
     * a partir dos dados do certificado
     * @var \Gnre\Configuration\CertificatePfxFileOperation 
     */
    private $cerficationFileOperation;

    /**
     * Dependências utilizadas para efetuar operação no certificado desejado
     * @param \Gnre\Configuration\CertificatePfxFileOperation $cerficationFileOperation
     * @param string $password  senha utilizada para realizar operações com o certificado
     * @since  1.0.0
     */
    public function __construct(CertificatePfxFileOperation $cerficationFileOperation, $password) {
        $this->cerficationFileOperation = $cerficationFileOperation;
        $this->dataCertificate = $this->cerficationFileOperation->open($password);
    }

    /**
     * Cria um arquivo na pasta definida nas configurações padrões (/certs/metadata) com a 
     * chave privada do certificado. Para salvar o novo arquivo é utilizado
     * o mesmo nome do certificado e com prefixo definido no método
     * @throws Gnre\Exception\UnableToWriteFile Se a pasta de destino não tiver permissão para escrita
     * @return string  Retorna uma string com o caminho e o nome do arquivo que foi criado
     * @since  1.0.0
     */
    public function getPrivateKey() {
        $filePrefix = new FilePrefix();
        $filePrefix->setPrefix('_privKEY');
        return $this->cerficationFileOperation->writeFile($this->dataCertificate['pkey'], $filePrefix);
    }

    /**
     * Cria um arquivo na pasta definida nas configurações padrões (/certs/metadata) com a 
     * chave privada do certificado. Para salvar o novo arquivo é utilizado
     * o mesmo nome do certificado e com prefixo definido no método
     * @throws Gnre\Exception\UnableToWriteFile Se a pasta de destino não tiver permissão para escrita
     * @return string Retorna uma string com o caminho e o nome do arquivo que foi criado
     * @since  1.0.0
     */
    public function getCertificatePem() {
        $filePrefix = new FilePrefix();
        $filePrefix->setPrefix('_certKEY');
        return $this->cerficationFileOperation->writeFile($this->dataCertificate['cert'], $filePrefix);
    }

}

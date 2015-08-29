<?php

/**
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

use Gnre\Exception\UnreachableFile;

/**
 * Classe abstrata que contém os métodos necessários para realizar ações em um arquivo
 * @package     gnre
 * @subpackage  configuration
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
abstract class FileOperation
{

    /**
     * Caminho em que o certificado físico está alocado
     * @var string 
     */
    protected $filePath;

    /**
     * Define o caminho absoluto de um arquivo para que a classe trabalhe 
     * corretamente com seus métodos
     * @param string $filePath caminho do arquivo a ser utilizado
     * @throws Gnre\Exception\UnreachableFile  Caso não seja encontrado o arquivo informado
     * @since  1.0.0
     */
    public function __construct($filePath)
    {
        if (!file_exists($filePath)) {
            throw new UnreachableFile($filePath);
        }

        $this->filePath = $filePath;
    }

    /**
     * Método utilizado para escrever em um arquivo
     * @abstract
     * @param string $content Conteúdo desejado para ser escrito em um arquivo
     * @param FilePrefix Utilizado para aplicar algum prefixo ou regras em um determinado arquivo
     * @since  1.0.0
     */
    abstract function writeFile($content, FilePrefix $filePrefix);
}

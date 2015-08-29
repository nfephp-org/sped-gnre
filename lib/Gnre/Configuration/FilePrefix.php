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

/**
 * Classe que realiza a adição de prefixos no nome do arquivo desejado 
 * IMPORTANTE: A classe não realiza escrita em disco ou manipulação de arquivos
 * @package     gnre
 * @subpackage  configuration
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class FilePrefix
{

    /**
     * Armazena o prefixo desejado para ser aplicado no nome do arquivo
     * @var string 
     */
    private $prefix;

    /**
     * Define o prefixo desejado para ser aplicado no nome do arquivo
     * @param  string  $prefix  Nome do prefixo por exemplo _private, _public etc
     * @since  1.0.0
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Aplica o prefixo desejado no arquivo caso ele exista
     * @param  string  $path  O caminho completo junto com o nome do arquivo por exemplo /var/foo/arquivo.tmp
     * @return string  O novo nome do arquivo e seu caminho completo
     * @since  1.0.0
     */
    public function apply($path)
    {
        if (empty($this->prefix)) {
            return $path;
        }

        $arrayPath = explode('/', $path);
        $nameFilePosition = count($arrayPath) - 1;

        $fileName = $arrayPath[count($arrayPath) - 1];
        $arrayFileName = explode('.', $fileName);

        $extension = $arrayFileName[1];
        $singleFileName = $arrayFileName[0];

        $arrayPath[$nameFilePosition] = $singleFileName . $this->prefix . '.' . $extension;

        $finalPath = implode('/', $arrayPath);

        return $finalPath;
    }

}

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

namespace Gnre\Render;

/**
 * Factory utilizada para criar um objeto <b>\Smarty</b> e definir
 * algumas configurações padrões para o objeto
 * @package     gnre
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class SmartyFactory {

    /**
     * Cria um objeto smarty com o diretório padrão <b>/root/templates</b> para
     * os templates e utiliza o diretório temporário padrão
     * do sistema operacional para definir o diretório que os arquivos
     * compilados pelo smarty serão salvos
     * @return \Smarty
     */
    public function create() {
        $documentRoot = getenv('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR;

        $smarty = new \Smarty();
        $smarty->caching = false;
        $smarty->setTemplateDir($documentRoot . 'templates');
        $smarty->setCompileDir(sys_get_temp_dir());

        return $smarty;
    }

}

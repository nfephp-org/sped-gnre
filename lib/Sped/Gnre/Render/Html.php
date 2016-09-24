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

namespace Sped\Gnre\Render;

use Sped\Gnre\Sefaz\Lote;
use Sped\Gnre\Render\Barcode128;
use Sped\Gnre\Render\SmartyFactory;

/**
 * Classe que contém a estrutura para gerar o pdf da guia de pagamento.
 * @package     gnre
 * @subpackage  pdf
 * @author      Leandro Pereira <llpereiras@gmail.com>
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Html
{

    /**
     * Conteúdo HTML gerado pela classe
     * @var string
     */
    private $html;

    /**
     * Objeto utilizado para gerar o código de barras
     * @var \Sped\Gnre\Render\Barcode128
     */
    private $barCode;

    /**
     * @var type 
     */
    private $smartyFactory;

    /**
     * Retorna a instância do objeto atual ou cria uma caso não exista
     * @return \Sped\Gnre\Render\Barcode128
     */
    public function getBarCode()
    {
        if (!$this->barCode instanceof Barcode128) {
            $this->barCode = new Barcode128();
        }

        return $this->barCode;
    }

    /**
     * Define um objeto <b>\Sped\Gnre\Render\Barcode128</b> para ser utilizado
     * internamente pela classe
     * @param \Sped\Gnre\Render\Barcode128 $barCode
     * @return \Sped\Gnre\Render\Html
     */
    public function setBarCode(Barcode128 $barCode)
    {
        $this->barCode = $barCode;
        return $this;
    }

    public function setSmartyFactory(\Sped\Gnre\Render\SmartyFactory $smartyFactory)
    {
        $this->smartyFactory = $smartyFactory;
        return $this;
    }

    /**
     * Retorna uma factory para ser possível utilizar o Smarty
     * @return Sped\Gnre\Render\SmartyFactory
     */
    public function getSmartyFactory()
    {
        if ($this->smartyFactory === null) {
            $this->smartyFactory = new SmartyFactory();
        }

        return $this->smartyFactory;
    }

    /**
     * Utiliza o lote como parâmetro para transforma-lo em uma guia HTML
     * @param \Sped\Gnre\Sefaz\Lote $lote
     * @link https://github.com/marabesi/gnrephp/blob/dev-pdf/exemplos/guia.jpg <p>
     * Exemplo de como é transformado o objeto <b>\Sped\Gnre\Sefaz\Lote</b> após ser
     * utilizado por esse método</p>
     * @since 1.0.0
     */
    public function create(Lote $lote)
    {
        $guiaViaInfo = array(
            1 => '1ª via Banco',
            2 => '2ª via Contrinuinte',
            3 => '3ª via Contribuinte/Fisco'
        );

        $guias = $lote->getGuias();
        $html = '';

        for ($index = 0; $index < count($guias); $index++) {
            $guia = $lote->getGuia($index);

            $barcode = $this->getBarCode()
                    ->setNumeroCodigoBarras($guia->retornoCodigoDeBarras);

            $smarty = $this->getSmartyFactory()
                    ->create();
            $smarty->assign('guiaViaInfo', $guiaViaInfo);
            $smarty->assign('barcode', $barcode);
            $smarty->assign('guia', $guia);

            $documentRoot = dirname(dirname(dirname(dirname(dirname(__FILE__))))) .  DIRECTORY_SEPARATOR ;

            $html .= $smarty->fetch($documentRoot . 'templates' . DIRECTORY_SEPARATOR . 'gnre.tpl');
        }

        $this->html = $html;
    }

    /**
     * Retorna o conteúdo HTML gerado pela classe
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

}

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

namespace Gnre\Render;

/**
 * @package     gnre
 * @subpackage  render
 * @author      Matheus Marabesi <matheus.marabesi@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-howto.html GPL
 * @version     1.0.0
 */
class Barcode128 {

    /**
     * @var int 
     */
    private $numeroCodigoBarras;

    /**
     * @return int
     */
    public function getNumeroCodigoBarras() {
        return $this->numeroCodigoBarras;
    }

    /**
     * @param int $numeroCodigoBarras
     * @return \Gnre\Render\Barcode128
     */
    public function setNumeroCodigoBarras($numeroCodigoBarras) {
        $this->numeroCodigoBarras = $numeroCodigoBarras;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodigoBarrasBase64() {
        ob_start();

        $text = $this->getNumeroCodigoBarras();
        $options = array(
            'text' => (string) $text,
            'imageType' => 'jpeg',
            'drawText' => false
        );

        $barcode = new \Zend\Barcode\Object\Code128();
        $barcode->setOptions($options);

        $barcodeOBj = \Zend\Barcode\Barcode::factory($barcode);

        $imageResource = $barcodeOBj->draw();

        imagejpeg($imageResource);

        $contents = ob_get_contents();
        ob_end_clean();

        return base64_encode($contents);
    }

}

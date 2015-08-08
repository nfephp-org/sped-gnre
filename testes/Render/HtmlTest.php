<?php

namespace Gnre\Render\Test;

use Gnre\Render\Html;

/**
 * @covers \Gnre\Render\Html
 */
class HtmlTest extends \PHPUnit_Framework_TestCase {

    public function testDeveRetornarUmInstanciaDoBarCode() {
        $html = new Html();
        $this->assertInstanceOf('\Gnre\Render\Barcode128', $html->getBarCode());
    }

    public function testDeveRetornarUmaInstanciaDoSmartyFactory() {
        $html = new Html();
        $this->assertInstanceOf('\Gnre\Render\SmartyFactory', $html->getSmartyFactory());
    }

}

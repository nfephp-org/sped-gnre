<?php

namespace Sped\Gnre\Sefaz\Estados;

use Sped\Gnre\Sefaz\Guia;

class AC extends Padrao
{

    public function getNodeCamposExtras(\DOMDocument $gnre, Guia $gnreGuia)
    {
        return null;
    }
}
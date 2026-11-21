<?php

namespace Sped\Gnre\Test\Configuration;

use Sped\Gnre\Configuration\FileOperation;

class MyFile extends FileOperation
{

    public function writeFile($content, \Sped\Gnre\Configuration\FilePrefix $filePrefix)
    {
        return null;
    }
}

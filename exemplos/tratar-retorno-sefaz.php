<?php

require '../vendor/autoload.php';

use Gnre\Parser\SefazRetorno;

$resultado = '020560726600011014111866530
100011DF12345630000100931575117TESTE TESTE                                                 AQAA A AAAAAAAAA                                                                                              DF053390000000533900030000100931575117                                                                                                                                                                                                                                                                                                                 0000012345678911111111111                                                                                                                                                                                                                                                                                                                                   01122014000000001122014001000000000001001000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000003000000000000000000000000000                                                                                                                              
20001c02_receita                   205A UF favorecida nao gera GNRE para a Receita informada.                                                                                                                                                                                                                                                                                                            
100021DF12345630000100931575117TESTE TESTE                                                 AQAA A AAAAAAAAA                                                                                              DF053390000000533900030000100931575117                                                                                                                                                                                                                                                                                                                 0000012345678911111111111                                                                                                                                                                                                                                                                                                                                   01122014000000001122014001000000000001001000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000003000000000000000000000000000                                                                                                                              
20002c02_receita                   205A UF favorecida nao gera GNRE para a Receita informada.                                                                                                                                                                                                                                                                                                            
914111866530002a6ddd0b7dd53be0080e7991ccab1cd38bfa4f6a1285412f5a3aaf7d91c9e6850';

$parser = new SefazRetorno($resultado);
$lote = $parser->getLote();

$consulta = new Gnre\Sefaz\Consulta();

header('Content-Type: text/xml');
echo $lote->toXml();


<?php
require '../vendor/autoload.php';

ob_start();

$text = '91910919190191091090109109190109';

$options = array('text' => (string) $text, 'barHeight' => 40, 'barWidth' => 100, 'imageType' => 'jpeg');
$barcode = new \Laminas\Barcode\Object\Code128();
$barcode->setOptions($options);

$barcodeOBj = \Laminas\Barcode\Barcode::factory($barcode);

$imageResource = $barcodeOBj->draw();

imagejpeg($imageResource);

$contents = ob_get_contents();
ob_end_clean();


$barcodeGnre = new Sped\Gnre\Render\Barcode128();
$barcodeGnre->setNumeroCodigoBarras('91910919190191091090109109190109');

?>

<img src="<?php echo 'data:image/jpeg;base64,' . $barcodeGnre->getCodigoBarrasBase64(); ?>"/>
<img src="<?php echo 'data:image/jpeg;base64,' . base64_encode($contents); ?>"/>


<?php
chdir('../../../../../');
require_once 'app/Mage.php';
umask(0);
Mage::app('default');


//$points = Mage::getHelper('madetoorder');
//6.12 meters
//by
//8.79 meters

$mto_width = 6100;
$mto_width_frac = 20;

$mto_height = 8700;
$mto_height_frac = 90;

$customSizeLine = Metrof_MadeToOrder_Helper_Event::createCustomSizeLine($mto_width, $mto_width_frac, $mto_height, $mto_height_frac);
var_dump($customSizeLine);
?>

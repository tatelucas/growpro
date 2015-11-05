<?php
$installer = $this;

$installer->startSetup();

$installer->setConfigData('easy_lightbox/general/enabled', 1);
$installer->setConfigData('easy_lightbox/general/mainImageSize', '265_265');
$installer->setConfigData('easy_lightbox/general/additionalImageSize', '60_60');
$installer->setConfigData('easy_lightbox/general/animate', 1);
$installer->setConfigData('easy_lightbox/general/overlayOpacity', '0.8');
$installer->setConfigData('easy_lightbox/general/resizeSpeed', '7');
$installer->setConfigData('easy_lightbox/general/borderSize', '10');
$installer->setConfigData('easy_lightbox/general/labelImage', 'Image');
$installer->setConfigData('easy_lightbox/general/labelOf', 'of');

$installer->endSetup();
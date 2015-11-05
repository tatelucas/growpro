<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE {$this->getTable('imageslider')}
    
    ADD COLUMN `title1` varchar(255) NOT NULL AFTER `title`,
    ADD COLUMN `link1` varchar(255) NOT NULL AFTER `link`,
    ADD COLUMN `link2` varchar(255) NOT NULL AFTER `link1`;
    ");
$installer->endSetup();
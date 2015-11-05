<?php
class Themevast_Fbfanbox_Helper_Data extends Mage_Core_Helper_Abstract
{

    const SECTIONS      = 'fbfanbox';   // module name
    const GROUPS        = 'general';       // setup general
    public function getGeneralCfg($cfg=null) 
    {
        $config = Mage::getStoreConfig(self::SECTIONS.'/'.self::GROUPS);
        if(isset($config[$cfg])) return $config[$cfg];
        return $config;
    }

}

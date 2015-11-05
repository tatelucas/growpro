<?php

class Themevast_Onsaleslider_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getConfig($cfg=null) 
	{
		$config = Mage::getStoreConfig('onsaleslider');
		if (isset($config['general'][$cfg]) ) return $config['general'][$cfg];
		return $config;
	}
}


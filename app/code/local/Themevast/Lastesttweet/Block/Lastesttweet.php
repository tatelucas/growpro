<?php
class Themevast_Lastesttweet_Block_Lastesttweet extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
	public function getConfig($cfg) 
	{
		$config = Mage::getStoreConfig('lastesttweet');
		if (isset($config['general'][$cfg]) ) {
			$value = $config['general'][$cfg];
			return $value;
		}
	}
}
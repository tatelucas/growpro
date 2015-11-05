<?php

class Metrof_MadeToOrder_Model_Product_Addon {

	static protected $_addonCodes  = array();
	static protected $_addonNames  = array();
	static protected $_addonPrices = array();

	public function __construct() {
	}

	public function init() {
		if (count (self::$_addonCodes) > 0 ) { return true; }
		$codes = array(
			'SPL' => array('name'=>'Sample Addon', 'price'=>1.23),
		);
		self::$_addonCodes = array_keys($codes);
		foreach ($codes as $_code => $addon) {
			self::$_addonNames[$_code] = $addon['name'];
			self::$_addonPrices[$_code] = $addon['price'];
		}
		return true;
	}

	public function getAddonNames() {
		return self::$_addonNames;
	}

	public function getAddonCodes() {
		return self::$_addonCodes;
	}

	public function getAddonPrices() {
		return self::$_addonPrices;
	}


}
?>

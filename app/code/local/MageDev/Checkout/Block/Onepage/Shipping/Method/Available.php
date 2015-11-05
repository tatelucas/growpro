<?php
class MageDev_Checkout_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
	public function getShippingRates()
	{
		$rates = parent::getShippingRates();
		if (array_key_exists('freeshipping', $rates)) {
			$rates = array('freeshipping' => $rates['freeshipping']);
		}
 
		return $rates;
	}
}
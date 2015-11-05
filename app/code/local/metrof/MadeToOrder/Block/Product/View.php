<?php

/**
 * Product View block
 *
 * @category   Metrof
 * @package    Metrof_MadeToOrder
 * @module     MadeToOrder
 */
class Metrof_MadeToOrder_Block_Product_View extends Mage_Catalog_Block_Product_View
{

    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }


    /**
     * Decides which attribute are available by reading the system attribute "optavail0X"
     * @return Array key value pairs suitable for building checkboxes
     * 
     */
    public function getAddons($product) {
	//try optavail02 and 03 as well
	$options = $product->getData('available_opt');
	if (strlen( trim($options)) < 1 ) {
		return false;
	}

	$optArray = explode(',',$options); 
	$ret = array();
	$prices = array();
	$addonModel = Mage::getSingleton('madetoorder/product_addon');
	$addonModel->init();
	$names = $addonModel->getAddonNames();
	$codes = $addonModel->getAddonCodes();
	foreach($optArray as $optName) {
		$optName = trim($optName);
		if (in_array($optName, $codes)) {
			$ret[$optName] = $names[ $optName ];
		}
	}
	return $ret;
    }

    public function getAddonPrice($code, $product) {
	$addonModel = Mage::getSingleton('madetoorder/product_addon');
	$addonModel->init();
	$prices = $addonModel->getAddonPrices();
        $_priceModel = Mage::getSingleton('catalog/product_price');

        return Mage::app()->getStore()->formatPrice($prices[$code]);
    }

    /**
     * decide if a product is availble custom sizes
     */
    public function hasCustomSizes($product) {
		$allFields = Mage::getStoreConfig('mto');
		foreach ($allFields as $key => $fieldSet) {
			if (substr($key,0,9) == 'mto_field') {
				if ($fieldSet['use_as_dimension'] !== 'none') {
					$attribute = $product->getResource()->getAttribute($fieldSet['field_name'].'_max');
					if ( is_object($attribute) ) {  
						if ($attribute->getFrontend()->getValue($product) !== NULL) {
							return true;
						}
					}
				}
			}
		}
		return false;
    }

    /**
     * decide if a product can take custom input
     */
    public function hasCustomInput($product) {
		$allFields = Mage::getStoreConfig('mto');
		foreach ($allFields as $key => $fieldSet) {
			if (substr($key,0,9) == 'mto_field') {
				if ($fieldSet['use_as_dimension'] === 'none' && $fieldSet['field_type'] !== 'disabled') {
					$attribute = $product->getResource()->getAttribute($fieldSet['field_name']);
					if ( is_object($attribute) ) {  
						if ($attribute->getFrontend()->getValue($product) !== NULL) {
							return true;
						}
					}
				}
			}
		}
		return false;
    }

    /**
	 * Return an array of custom input variables
     */
    public function getCustomInputs($product) {
		$customInput = array();
		$allFields = Mage::getStoreConfig('mto');
		foreach ($allFields as $key => $fieldSet) {
			if (substr($key,0,9) == 'mto_field') {
				if ($fieldSet['use_as_dimension'] === 'none' && $fieldSet['field_type'] !== 'disabled') {
					$attribute = $product->getResource()->getAttribute($fieldSet['field_name']);
					if ( is_object($attribute) ) { 
						$customInput[] = $attribute;
					}
				}
			}
		}
		return $customInput;
    }


	/**
	 * Return a list of "<option" tags
	 *
	 */
    public function getOptions($start, $end, $stepping=0) {
		$useMetric = Mage::getStoreConfig('mto/measurement_class/metric');
		if ($useMetric === '1' || $userMetric === 'yes') {
			return $this->getOptionsMetric($start, $end, $stepping);
		}
        $html = '';
        for ($x=$start; $x<=$end; $x++) {
            $html .= '
                <option value="'.$x.'">'.$x.' &quot;</option>';
        }
        return $html;
    }


	/**
	 * Return a list of "<option" tags with meters and centimeters
	 *
	 * The base increment will be one metric deci-unit greater
	 * than the stepping. 
	 *
	 * 10   for stepping=1
	 * 100  for stepping=10
	 * 10   for stepping=2
	 * 100  for stepping=14
	 */
    public function getOptionsMetric($start, $end, $stepping) {
		if ($stepping == 0) { $stepping = 10; }
		//get metric deci-unit
		$humble  = $stepping / 10;
		$rounded = floor($humble);
		$deci    = $rounded * 100;
		if ($deci == 0 ) { $deci = 10; }

        $html = '';
        for ($x=$start; $x<=$end; $x+=$deci) {
			if ($x >= 100) {
				$show = $x/100;
				$unit = ' m';
			} else {
				$show = $x;
				$unit = ' cm';
			}
            $html .= '
                <option value="'.$x.'">'.$show.$unit.'</option>';
        }
        return $html;
    }

	/**
	 * Return a list of "<option" tags with meters and centimeters
	 */
    public function getOptionsFracMetric($stepping=10) {
        $html = '';
		$stepping = (int) $stepping;
		if ($stepping < 1) { $stepping = 10; }
		$start = 00;
		$end  =  (10 * $stepping) - $stepping;
        for ($x=$start; $x<=$end; $x+=$stepping) {
            $html .= '
                <option value="'.$x.'">'.$x.' cm</option>';
        }
        return $html;
    }


    public function getOptionsFrac($stepping='1/4') {
		$useMetric = Mage::getStoreConfig('mto/measurement_class/metric');
		if ($useMetric === '1') {
			return $this->getOptionsFracMetric($stepping);
		}


        $parts = explode('/',$stepping);
        $numerator = $parts[0];
        $denominator = $parts[1];
        $html = '
                <option value="0">even</option>';

        for ($x=$numerator; $x<$denominator; $x+=$numerator) {
            $top = $x;
            $bot = $denominator;
            //reduce fraction
            if ( ($top%2 == 0) && ($bot%2==0)) {
                $top = $top/2;
                $bot = $bot/2;
            }
            //doing this twice is probably enough, we're only going to 1/8"
            if ( ($top%2 == 0) && ($bot%2==0)) {
                $top = $top/2;
                $bot = $bot/2;
            }

            $frac = $top.'/'.$bot;
            $html .= '
                <option value="'.$frac.'">'.$frac.' &quot;</option>';
        }
        return $html;
    }

	/**
	 * Shortcut method
	 */
	public function getAttributeValue($product, $code) {
        $attribute = $product->getResource()->getAttribute($code);
		if (! is_object($attribute) ) { return NULL; }
		return $attribute->getFrontend()->getValue($product);
	}

}


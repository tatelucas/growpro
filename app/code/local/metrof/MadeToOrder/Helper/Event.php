<?php
function bt_die() {
    $bt = debug_backtrace();
    $x =0;
    foreach ($bt as $b) {
    echo '#'.$x++.' '. @$b['file'] .':'.@$b['line'];
    echo "<br/>\n";
    echo $b['function'].'()';
    echo "<br/>\n";
}
exit();
}


class Metrof_MadeToOrder_Helper_Event extends Mage_Core_Helper_Abstract
{

    function Metof_MadeToOrder_Helper_Event() {
    }


    static function attachSpecialQuoteAttribs($observer) {
        $event = $observer->getEvent();
        $product = $event->getProduct();
	    $mto_width = 0;
	    $mto_height = 0;

		$allFields = Mage::getStoreConfig('mto');
		$textValues = array();
		foreach ($allFields as $key => $fieldSet) {
			if (substr($key,0,9) == 'mto_field') {
				if ($fieldSet['use_as_dimension'] == 'width') {
					$mto_width = $event->getRequest()->getParam($fieldSet['field_name']);
					$mto_width_frac = $event->getRequest()->getParam($fieldSet['field_name'].'_frac');
				}
				if ($fieldSet['use_as_dimension'] == 'height') {
					$mto_height = $event->getRequest()->getParam($fieldSet['field_name']);
					$mto_height_frac = $event->getRequest()->getParam($fieldSet['field_name'].'_frac');
				}
				if ($fieldSet['use_as_dimension'] == 'depth') {
					$mto_depth = $event->getRequest()->getParam($fieldSet['field_name']);
					$mto_depth_frac = $event->getRequest()->getParam($fieldSet['field_name'].'_frac');
				}

				if ($fieldSet['use_as_dimension'] == 'none' && $fieldSet['field_type'] !== 'disabled') {
					$textValues[$fieldSet['field_name']] = $event->getRequest()->getParam($fieldSet['field_name']);
				}
			}
		}

		if ( (int)$mto_width > 0 || $mto_height > 0) {
			$lineDetails = $product->getData('line_item_details');
			$customSizeLine = self::createCustomSizeLine($mto_width, $mto_width_frac, $mto_height, $mto_height_frac);
			$lineDetails .= $customSizeLine;
			$product->setData('line_item_details', $lineDetails);
			$product->setData('config_id', $product->getSku());
		}

		$addons = $event->getRequest()->getParam('addons');

		if(is_array($addons)) {
			$product->setData('config_id', $product->getSku().'-OPT');
			$lineDetails = $product->getData('line_item_details');
			$lineDetails .= 'Options: '.implode(',',$addons)."\n";
			$product->setData('line_item_details', $lineDetails);
		}

		if(is_array($textValues)) {
			$lineDetails = $product->getData('line_item_details');
			foreach ($textValues as $attrCode => $value) {
				$lineDetails .= $product->getResource()
					->getAttribute($attrCode)
					->getFrontend()->getLabel() . ': '.$value . "\n";
			}
			$product->setData('line_item_details', $lineDetails);
		}
		/*
		var_dump($product->getData('line_item_details'));
		exit();
		 */
/*
        foreach ($items as $item) {
            var_dump($item->getLineItemDetails());
        }
*/
    }

	static function createCustomSizeLine($width, $wfrac, $height, $hfrac, $depth=-1, $dfrac=-1) {
		$useMetric = Mage::getStoreConfig('mto/measurement_class/metric');
		if ($useMetric === '1') {
			$symbol = ' m';
			$glueSymbol = ',';
			//meters and centimeters
			$width = ($width + $wfrac) / 1000;
			$height = ($height + $hfrac) / 1000;
			if ($depth !== -1) {
				$depth = ($depth + $dfrac) / 1000;
			}

			$l = new Zend_Locale_Format();
			$width = $l->toNumber($width);
			$height = $l->toNumber($height);
			if ($depth !== -1) {
				$depth = $l->toNumber($depth);
			}

		} else {
			$symbol = '"';
			$glueSymbol = ' & ';
			//inches and factional inches
			if ( (int)$wfrac > 0 ) {
				$width = $width.$glueSymbol.$wfrac;
			} else {
				$wfrac = '';
			}
			if ( (int)$hfrac > 0 ) {
				$height = $height.$glueSymbol.$hfrac;
			} else {
				$hfrac = '';
			}
		}

		$customSize = 'Custom Size: '.$width.$symbol.' x '.$height.$symbol;
		if ($depth !== -1) {
			$customSize .= $depth.$symbol;
		}

		return $customSize."\n";
	}

    function cartBeforeSave($observer) {
        $event = $observer->getEvent();
        $items = $event->getQuote()->getItemsCollection();
        foreach ($items as $item) {
            $product = $item->getProduct();
            if (strlen($product->getLineItemDetails()) ) {
                $item->setLineItemDetails($product->getLineItemDetails());
                $this->updateAddonsPrice($item);
            }
            if (strlen($product->getConfigId()) ) {
                $item->setConfigId($product->getConfigId());
            }
        }
    }

    /**
     * Set the custom_price based on the "options: " line in the lineitem details
     */
    function updateAddonsPrice($item) {
        $details = explode("\n", $item->getLineItemDetails());
        $adjustPrice = 0;
        foreach ($details as $_line) {
            if  (substr($_line, 0, 9) === 'Options: ') {
                $addonModel = Mage::getSingleton('madetoorder/product_addon');
                $addonModel->init();
                $prices = $addonModel->getAddonPrices();
                $options = explode(',', substr($_line,9));
                foreach ($options as $_opt) {
                    $adjustPrice += $prices[$_opt];
                }
            }
        }
        if ($adjustPrice > 0 ) {
            $item->setCustomPrice( $item->getPrice()+ $adjustPrice);
        }
    }


    static function cartAfterSave($observer) {
/*
        $event = $observer->getEvent();
        $items = $event->getQuote()->getItemsCollection();
        foreach ($items as $item) {
//            var_dump($item->getProduct()->getLineItemDetails());
        }
/*
    die('adksjlf');
//*/
    }


    /**
     * hook this up to event "sales_convert_quote_item_to_order_item"
     */
    static function attachSpecialOrderAttribs($observer) {

        $event = $observer->getEvent();
        $orderItem = $event->getOrderItem();
        $quoteItem = $event->getItem();

        $orderItem->setLineItemDetails( $quoteItem->getLineItemDetails() );
        $orderItem->setConfigId( $quoteItem->getConfigId() );

    }

}
?>

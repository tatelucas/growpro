<?php
/**
 * MadeToOrder default helper
 *
 */
class Metrof_MadeToOrder_Helper_Data extends Mage_Core_Helper_Abstract
{
     /**
     * @@CUSTMIZED MTO
     */
    public function getQuoteItemDetails($item) {
        $details = $item->getLineItemDetails();
        if (!strlen($details)) {
            return '';
        }
        $detailLines = explode("\n",$details);
        $optionList = array();
        $newDetails = '';
        foreach ($detailLines as $l) {
            if (substr($l,0,7) == 'Options') {
                $optionList = explode(',', substr($l, 9));
            } else {
                //we're cutting off the option list if 
                // we found it
                $newDetails .= $l."\n";
            }
        }
        //this should only remove the "Options" line and html escape all the other lines.
        $details =  $this->htmlEscape($newDetails);
		//add br tags between "Custom Size" and plain text fields, but not at the end
		$details = nl2br(trim($details));

        //get the full option name instead of just the codes
        if (count($optionList)) {
            $addonModel = Mage::getSingleton('madetoorder/product_addon');
            $addonModel->init();
            $names = $addonModel->getAddonNames();
            $codes = $addonModel->getAddonCodes();
            $ret = array();
            foreach($optionList as $optName) {
                $optName = trim($optName);
                if (in_array($optName, $codes)) {
                    $ret[$optName] = $names[ $optName ];
                }
            }
            $details .= "\nOptions: ". implode(', ',$ret);
        }
        return $details.'<br/>';
    }

     /**
     * @@CUSTMIZED MTO
     */
    public function getProductPartNumber($item) {
//      $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $product = $item->getProduct();
        if (!is_object($product)) {
            return '';
        }
        $details = $product->getPartNum();
        if (strlen($details)) {
                return 'Part Number: '.$details;
        }
        return '';
    }

    public function getFullReferenceName($item) {
        return $this->getQuoteItemProductName($item). '<br/>'.
               // $this->getQuoteItemDetails($item). 
//                $this->getQuoteItemProductAttribs($item);
                $this->getProductPartNumber($item);
    }

    function getQuoteItemProductAttribs($item) {
        $name = '';
        if ($product = $this->getQuoteItemProduct($item)) {
            $attr = $product->getAttributes();
            foreach ($attr as $attrCode => $attrObj) {
              if ($attrCode == 'part_num') { continue; }
              if ($attrCode == 'line_item_details') { continue; }

              if ($attrObj->getIsVisibleOnFront() && $attrObj->getIsUserDefined()) {
                  $value = $attrObj->getFrontend()->getValue($product);
                  $label = $attrObj->getFrontend()->getLabel($product);
                  if (!empty($value) && !empty($label)) {
                      $name .= $label.':&nbsp;'.$value. ' ';
                    }
                }
            }
        }
        return $name;
    }

    /**
     * Retrieve quote item product name
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  string
     */
    public function getQuoteItemProductName($item)
    {
        if ($product = $this->getQuoteItemProduct($item)) {
            return $product->getName();
        }
        return $item->getName();
    }

    public function getQuoteItemProduct($item)
    {
        $superProduct = $item->getSuperProduct();
        if ($superProduct) {
            $product = $superProduct;
        } else {
            $product = $item->getProduct();
        }

        return $product;
    }

	public function getDynPriceUrl($id=0) {
		return $this->_getUrl('mto/mto/getDynPrice/').'?isAjax=1';
	}


    function getProductPartNumberForOrder($item) {
        if (!$product = $item->getProduct()) {
        	$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$item->setProduct($product);
	}
        if (!is_object($product)) {
            return '';
        }
        $details = $product->getPartNum();
        if (strlen($details)) {
                return 'Part Number: '.$this->htmlEscape($details);
        }
        return '';
    }

    function getOrderItemProductAttribs($item) {
        $name = '';
        if (!$product = $item->getProduct()) {
        	$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$item->setProduct($product);
	}

        if ($product) {
            $attr = $product->getAttributes();
            foreach ($attr as $attrCode => $attrObj) {
              if ($attrCode == 'part_num') { continue; }
              if ($attrCode == 'line_item_details') { continue; }

              if ($attrObj->getIsVisibleOnFront() && $attrObj->getIsUserDefined()) {
                  $value = $attrObj->getFrontend()->getValue($product);
                  $label = $attrObj->getFrontend()->getLabel($product);
                  if (!empty($value) && !empty($label)) {
                      $name .= $label.':&nbsp;'.$this->htmlEscape($value). ' ';
                    }
                }
            }
        }
        return $name;
    }

    function htmlEscape($txt) {
        return htmlspecialchars($txt);
    }
}
?>

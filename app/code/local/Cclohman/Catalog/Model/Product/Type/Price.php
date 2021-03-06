<?php

class Cclohman_Catalog_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Get product final price
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty=null, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $product->getPrice();
        $finalPrice = $this->_applyTierPrice($product, $qty, $finalPrice);
	 	
		// Call our custom function to apply category Tier Pricing, if applicable.
	    $finalPrice = $this->_applyCategoryTierPrice($qty, $finalPrice, $product);
        $finalPrice = $this->_applySpecialPrice($product, $finalPrice);
        $product->setFinalPrice($finalPrice);

        Mage::dispatchEvent('catalog_product_get_final_price', array('product'=>$product));

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);

        return max(0, $finalPrice);
    }

 protected function _applyTierPrice($product, $qty, $finalPrice)

    {	
        $tierPrice  = $product->getTierPrice($qty);
        if (is_numeric($tierPrice)) {
			if ($finalPrice > 0) { //in case the product doesnt have normal price value
            $finalPrice = min($finalPrice, $tierPrice);
		} else {
			$finalPrice = $tierPrice;
		}
        }
        return $finalPrice;
    }

   protected function _applyCategoryTierPrice($qty, $finalPrice, $theProduct)
	    {
	        if (is_null($qty)) { // or doesnt have a categortytierprice
	            return $finalPrice;
	        }
	
			//Reload the product
			 $currentProduct = $this->getProductModel($theProduct->getEntityID());

			// Get the data needed to match the current product to others like it in the cart
				$priceCat = $currentProduct->getCategoryTierPrice();
				if (!($currentProduct->getCategoryTierPrice())) {return $finalPrice;}
				
				// $priceSKU = substr($theProduct->getSku(), -1);
				if (substr($theProduct->getSku(), -2, 1) == '-') {$priceSKU = substr($theProduct->getSku(), -1);}
				else {
					$priceSKU = NULL;}
				if ($priceSKU == 'C') { $isColor = true; } else {$isColor = false;}
				
	        // Get items in the cart
	        	$cartItems = Mage::getModel('checkout/session')->getQuote()->getAllItems();
			
			// Start counting the quantity of matching items
				$cartQty = 0;

			// If category tiered pricing is being used, load the master product so we can use its pricing
			// Load the model for the Master product for this item's ID + SKU
				if ($isColor) {$priceSKU = 'B';} // If imprint is color, use black pricing
				if ($priceSKU){
				$priceMasterList = Mage::getModel('catalog/product')
						->getCollection()
						->addAttributeToFilter('category_tier_price', $priceCat)
						->addAttributeToFilter('category_tier_price_master', 1)
						->addAttributeToFilter('category_tier_price_sku', $priceSKU)
						->addAttributeToSelect('entity_id')
						->load();
						} else {
				$priceMasterList = Mage::getModel('catalog/product')
						->getCollection()
						->addAttributeToFilter('category_tier_price', $priceCat)
						->addAttributeToFilter('category_tier_price_master', 1)
						->addAttributeToSelect('entity_id')
						->load();}
									
				foreach($priceMasterList as $priceMasterModel) {continue;}
				$priceMasterId = $priceMasterModel->getId();
				$priceMasterItem = $this->getProductModel($priceMasterId);

	        // Loop through items and count how many are in the item's pricing category + sku
				
				if ($priceMasterItem->getCategoryPricing() == 1) {
					foreach($cartItems as $cartItem){	
						if ($isColor) {$priceSKU = 'C';};
						$cartItemSKU = substr($cartItem->getSku(), -1);
						// May be needed if single-option category priced masters have sku of 'N'
						// if (substr($cartItem->getSku(), -2, 1) == '-') {$cartItemSKU = substr($cartItem->getSku(), -1);}
						// 					else {	$priceSKU = "N";}
						// $cartItemSKU = substr($cartItem->getSku(), -1);
	      	        	$cartItemProductId = $cartItem->getProduct()->getId();
	      	        	$cartItemModel = $this->getProductModel($cartItemProductId);
						if (($cartItemModel->getCategoryTierPrice() == $priceCat) & (($cartItemSKU == $priceSKU) || $priceSKU == NULL)) 
							{$cartQty += $cartItem->getQty();}
			        	}
				} else {
					$cartQty = $qty;
				} 

				$tierPrice = $priceMasterItem->getTierPrice($cartQty);
				
	        if (is_numeric($tierPrice)) {
				$finalPrice = $tierPrice;
				if ($isColor) {$finalPrice += (40 / $cartQty);} // add $40 for color imprint
	        }
	        return $finalPrice;
	    }
	
	function getProductModel($EntityID){
			$Product = Mage::getModel('catalog/product')->load($EntityID);
			return $Product;
		}
	
	}

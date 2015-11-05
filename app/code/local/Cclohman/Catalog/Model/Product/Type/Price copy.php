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



   protected function _applyCategoryTierPrice($qty, $finalPrice, $theProduct)
	    {
	        if (is_null($qty)) {
	            return $finalPrice;
	        }
			
			//Reload the product, since the $theProduct object passed to this function doesn't include all attributes (we need them)
			 $fullProduct = Mage::getModel('catalog/product')->load($theProduct->getEntityID());

			// Now that we have it, get the Product's Tier Price Category & sku suffix
				$CategoryID = $fullProduct->getCategoryTierPrice();
				$SkuSuffix = substr($theProduct->getSku(), -1);
				// echo($SkuSuffix);
			
	        // Get array of items in cart
	        	$items = Mage::getModel('checkout/session')->getQuote()->getAllItems();
	        	$catTierCount = 0;
			
	        // Loop through items and count how many are category tier pricing items
	        	foreach($items as $item)
	        	{
				
					$itemSkuSuffix = substr($item->getSku(), -1);
					if ($itemSkuSuffix == 'C') { // Color imprint, use black imprint table & add $40 (I know, I know)
						$isColor = true;
						$itemSkuSuffix = 'B';
					} else {$isColor = false;}
	            	$productId = $item->getProduct()->getId();

	            	$productList = Mage::getModel('catalog/product')->getCollection()
	                	->addAttributeToSelect('category_tier_price')
						->addAttributeToSelect('category_tier_price')
	                	->addIdFilter($productId);

	            // Because we filtered by productId in the Collection above, and productId is unique, there will only be one item in the collection
	            foreach($productList as $product)		{continue;}
					
					// See if the product we're looping on($product) uses the same category for tiered pricing as the added Product ($theProduct)
	            if(($product->getCategoryTierPrice() == $CategoryID) & ($itemSkuSuffix == $SkuSuffix))	{$catTierCount += $item->getQty();}

	        }
	        $tierPrice = '';
	
			// Find the ID of the master product of this category (that has the tiered pricing that is to be used for all in the category)
						
					$masterProductList = Mage::getModel('catalog/product')
						->getCollection()
						->addAttributeToFilter('category_tier_price', $CategoryID)
						->addAttributeToFilter('category_tier_price_master', 1)
						->addAttributeToFilter('category_tier_price_sku', $SkuSuffix)
						->addAttributeToSelect('entity_id')
						->load();
									
					foreach($masterProductList as $masterProduct) {continue;}
					$masterProductID = $masterProduct->getId();
											
			// If category tiered pricing is being used, load the master product so we can use its pricing
	        if($catTierCount) {
  				$dummyProduct = Mage::getModel('catalog/product')->load($masterProductID);  
  				   	            $tierPrice = $dummyProduct->getTierPrice($catTierCount);
			}
	        if (is_numeric($tierPrice)) {
				$finalPrice = $tierPrice;
				if ($isColor) {$finalPrice = ($finalPrice + 40);}
	        }
	        return $finalPrice;
	    }
	}

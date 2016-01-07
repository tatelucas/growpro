<?php
/*
 * Copyright (c) 2015 Schogini Systems P Ltd http://schogini.biz/magento
 * Community:
 *     Follow us on LinkedIn: https://www.linkedin.com/company/schogini-systems
 *     Follow us on Twitter: @schogini
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class Schogini_Producttextlogo_Model_Observer 
{
	public function add_product_text_logo($observer) 
	{
		$isActive 		= Mage::getStoreConfig('schogini/settings/active');
		$fileTypes 		= Mage::getStoreConfig('schogini/settings/filetypes');
		$maxWidth 		= Mage::getStoreConfig('schogini/settings/maxwidth');
		$maxHeight 		= Mage::getStoreConfig('schogini/settings/maxheight');
		$addTextOption 	= true;
		$addLogoOption 	= true;
		$newoptions 	= array();


		if (!$isActive) return;

		if ($actionInstance = Mage::app()->getFrontController()->getAction()) {
	        $action = $actionInstance->getFullActionName();
	        if ($action == 'adminhtml_catalog_product_save') { //if on admin save action
	            $product 		= $observer->getProduct();
	            $flagEnabled 	= $product->getSchProductTextImage();

	            // If the product doesn't have this field enabled then, do not add options
			    if (!$flagEnabled) {
			    	$addTextOption = false;
			    	$addLogoOption = false;
			    }

	            $options = $product->getProductOptions();
			    foreach ($options as $key => $option) {
			    	// echo $option['title'] . '---' . $option['type'] . '<br />';
			        if ($option['title'] == 'Custom Text' && $option['type'] == 'field' && !$option['is_delete']) {
			            // we already added the option
			            $addTextOption = false;
			            if (!$flagEnabled) {
			            	$option['is_delete'] = 1;
			            	$newoptions[$key] 	 = $option;

			            }

			        }

			        if ($option['title'] == 'Customer Logo' && $option['type'] == 'file' && !$option['is_delete']) {
			        	// we already added the option
			        	$addLogoOption = false;
			        	if (!$flagEnabled) {
			            	$option['is_delete'] = 1;
			            	$newoptions[$key] 	 = $option;

			            }

			        }

			    }
				
			    if ($addTextOption == true) {
			    	// echo 'Add Text';
			    	$newoptions[] = array(
					    'title' => 'Custom Text',
					    'type' => 'field',
					    'is_require' => 0,
					    'sort_order' => 1,
					    'is_delete' => '',
					    'previous_type' => '',
					    'previous_group' => '',
					    'price' => '0.00',
					    'price_type' => 'fixed',
					    'sku' => ''
					);
			    }
			    if ($addLogoOption == true) {
			    	// echo 'Add Logo';
			    	$newoptions[] = array(
				        'title' => 'Customer Logo',
				        'type' => 'file',
				        'is_require' => 0,
				        'sort_order' => 2,
				        'is_delete' => '',
					    'previous_type' => '',
					    'previous_group' => '',
				        'price' => '0.00',
					    'price_type' => 'fixed',
					    'sku' => '',
				        'file_extension' => $fileTypes,
				        'image_size_x' => $maxwidth,
				        'image_size_y' => $maxheight
					);
			    }
			    
			    if (is_array($newoptions) && !empty($newoptions) && sizeof($newoptions) > 0) {
			    	$product->setCanSaveCustomOptions(true);
			    	foreach ($newoptions as $option) {
			    		$product->getOptionInstance()->addOption($option);
			    	}
					$product->setHasOptions(true);
					// echo "Added Options";exit;

			    } else {
			    	// echo "Why?";exit;

			    }

	        }

	    }

	}

}
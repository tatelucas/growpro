<?php
/**
 * Shoping cart model
 *
 * @category   Metrof
 * @package    Maetrof_BugSidebar
 */
class Metrof_MadeToOrder_Model_Cart extends Mage_Checkout_Model_Cart
{

    public function getCartInfo($quoteId=null)
    {
        $store = Mage::app()->getStore();
        if (is_null($quoteId)) {
            $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        }

        $cacheKey = 'CHECKOUT_QUOTE'.$quoteId.'_STORE'.$store->getId();
        if (Mage::app()->useCache('checkout_quote') && $cache = Mage::app()->loadCache($cacheKey)) {
            return unserialize($cache);
        }

        $cart = array('items'=>array(), 'subtotal'=>0);
        $cacheTags = array('checkout_quote', 'catalogrule_product_price', 'checkout_quote_'.$quoteId);

        if ($this->getSummaryQty($quoteId)>0) {

            $itemsArr = $this->_getResource()->fetchItems($quoteId);
            $productIds = array();
            foreach ($itemsArr as $item) {
                $productIds[] = $item['product_id'];
                if (!empty($item['super_product_id'])) {
                    $productIds[] = $item['super_product_id'];
                }
            }

            $productIds = array_unique($productIds);
            foreach ($productIds as $id) {
                $cacheTags[] = 'catalog_product_'.$id;
            }

            $quoteItems = Mage::getModel('sales/quote_item')
                ->getCollection()
                ->setQuote( Mage::getSingleton('checkout/session')->getQuote())
                ->addAttributeToSelect('*');


            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addMinimalPrice()
                ->addStoreFilter()
                ->addIdFilter($productIds);


            foreach ($itemsArr as $it) {
                $product = $products->getItemById($it['product_id']);
                if (!$product) {
                    continue;
                }

                $item = $quoteItems->getItemById($it['id']);

		$item->_resource = NULL;
		$product->_resource = NULL;

                $item->setProduct($product);
                $superProduct = null;
                if (!empty($it['super_product_id'])) {
                    $superProduct = $products->getItemById($it['super_product_id']);
                    $item->setSuperProduct($superProduct);
                    $product->setProduct($product);
                    $product->setSuperProduct($superProduct);
                }
                if ($item->getCalculationPrice()) {
                    $item->setPrice($item->getCalculationPrice());
                }
                $item->setProductName(!empty($superProduct) ? $superProduct->getName() : $product->getName());
                $item->setProductUrl(!empty($superProduct) ? $superProduct->getProductUrl() : $product->getProductUrl());
//                $item->setPrice($product->getFinalPrice($it['qty']));

                $thumbnailObjOrig = Mage::helper('checkout')->getQuoteItemProductThumbnail($item);
                $thumbnailObj = Mage::getModel('catalog/product');
                foreach ($thumbnailObjOrig->getData() as $k=>$v) {
                    if (is_scalar($v)) {
                        $thumbnailObj->setData($k, $v);
                    }
                }
                $item->setThumbnailObject($thumbnailObj);

                $item->setProductDescription(Mage::helper('catalog/product')->getProductDescription($product));

                $item->unsProduct()->unsSuperProduct();

                $cart['items'][] = $item;

                $cart['subtotal'] += $item->getCalculationPrice()*$item->getQty();
            }
        }

        $cartObj = new Varien_Object($cart);
        if (Mage::app()->useCache('checkout_quote')) {
            Mage::app()->saveCache(serialize($cartObj), $cacheKey, $cacheTags);
        }

        return $cartObj;
    }
}

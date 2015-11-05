<?php
/**
 * Product controller
 *
 * @category   Metrof
 * @package    Metrof_MadeToOrder
 * @module     MadeToOrder
 */
class Metrof_MadeToOrder_MtoController extends Mage_Core_Controller_Front_Action
{
    protected function _initProduct()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');

        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);

        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            Mage::register('current_category', $category);
        }

        Mage::register('current_product', $product);
        Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());

        Mage::register('product', $product); // this need remove after all replace

        Mage::getModel('catalog/design')->applyDesign($product, 1);
    }

    public function indexAction() {
		echo "mto installed properly.";
	}

	//@@MTO CUSTOMIZED
    public function getDynPriceAction() {
        $this->_initProduct();
        $price = Mage::registry('current_product')->getPrice();
        echo  Mage::app()->getStore()->formatPrice($price);
    }

}

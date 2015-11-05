<?php
class Metrof_MadeToOrder_Block_Checkout_Cart
  extends Mage_Checkout_Block_Cart 
{

    /**
     * Return the value of "case_count" or 0
     *
     * return int quantity of product per case
     */
    function getCaseCount($item) {
        $product = $item->getProduct();
        return intval($product->getCase());
    }

    /**
     * Return true if this product is sold "by-the-case".
     *
     * return boolean
     */
    function hasCaseCount($item) {
        $product = $item->getProduct();
        return $product->getCase() > 1 ;
    }

    /**
     * Use Magento's stock level classes to determine the
     * maximum allowable quantity per order.
     * 
     * just return 12 times the case count;
     *
     * return int
     */
    function getMaximumQty($item) {
        $product = $item->getProduct();
        $case = $product->getCase();
        if ($case > 1) {
            return $case * 24;
        } else {
            return 1000;
        }
    }
}

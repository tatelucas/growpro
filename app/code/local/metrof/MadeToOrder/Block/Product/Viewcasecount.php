<?php

class Metrof_MadeToOrder_Block_Product_Viewcasecount
    extends Mage_Catalog_Block_Product_View_Type_Simple {

    /**
     * Return the value of "case_count" or 0
     *
     * return int quantity of product per case
     */
    function getCaseCount() {
        $product = $this->getProduct();
        return intval($product->getCase());
    }

    /**
     * Return true if this product is sold "by-the-case".
     *
     * return boolean
     */
    function hasCaseCount() {
        $product = $this->getProduct();
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
    function getMaximumQty() {
        $product = $this->getProduct();
        $case = $product->getCase();
        if ($case > 1) {
            return $case * 24;
        } else {
            return 1000;
        }
/*
        $product = $this->getProduct();
        $stock = $product->getStockItem();
        return $stock->getMaxSaleQty();
*/
    }
}

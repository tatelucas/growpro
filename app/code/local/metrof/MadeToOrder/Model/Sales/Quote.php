<?php


class Metrof_MadeToOrder_Model_Sales_Quote extends  Mage_Sales_Model_Quote {

    /**
     * Retrieve quote item by product id
     * Overridden to compare line_item_details of a quote item and a product 
     *  for storing MTO variables.
     *
     * @param   int $productId
     * @return  Mage_Sales_Model_Quote_Item || false
     */
    public function getItemByProduct($product, $superProductId = null)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId      = $product->getId();
            $superProductId = $product->getSuperProduct() ? $product->getSuperProduct()->getId() : null;
        }
        else {
            $productId = $product;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getSuperProductId()) {
                if ($superProductId && $item->getSuperProductId() == $superProductId) {
                    if ($item->getProductId() == $productId) {
                        return $item;
                    }
                }
            }
            else {
                if ($item->getProductId() == $productId && $item->getLineItemDetails() == $product->getLineItemDetails()) {
                    return $item;
                }
            }
        }
        return false;
    }
}

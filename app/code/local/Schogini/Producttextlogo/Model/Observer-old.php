<?php
/**
 * PayPal Express Checkout validates the cart total with the final order total
 * Since, our discount is added above all other Magento discounts we need to 
 * let PayPal know that explicitly.
 */
class Schogini_Checkoutdiscount_Model_Observer 
{
	protected $_remark              = '';
    protected $_discountcode        = '';
    protected $_checkout_discount   = 0;
    protected $_percentage          = 0;
    
    function apply_checkout_discount($observer) 
	{
		if (Mage::getSingleton('checkout/session')->getSchAddDiscount() != 1) {
            return;

        }

        $this->_getCheckoutDetails();
		$paypal_cart 	= $observer->getPaypalCart();
		// $paypal_cart->addItem($this->_remark, 1, $this->_checkout_discount);
        $paypal_cart->appendDiscount($this->_checkout_discount); // Quickfix Solution: Modified core class Mage_Paypal_Model_Cart and added this new function.
		return;
	}

	protected function _schGetBaseCheckoutDiscount($quoteid)
    {
        $resource 		= Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table 			= $resource->getTableName('sales_flat_quote');
        $query 			= 'SELECT base_checkoutdiscount_amount FROM ' . $table . ' WHERE entity_id = ' . (int)$quoteid . ' LIMIT 1';
        $checkout_discount = $readConnection->fetchOne($query);

        return $checkout_discount;
    }

    /**
     * Get the discount percentage from the table
     * Since, we are already fetching data from the table lets fetch other information too.
     */
    protected function _getCheckoutDetails()
    {
        $resource       = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $table 		= $resource->getTableName('schogini_checkoutdiscount');
        $query      = "SELECT * FROM $table ORDER BY 1 DESC LIMIT 1";
        $results    = $readConnection->fetchAll($query);
        if (isset($results[0])) {
            $this->_discountcode        = $results[0]['checkoutdiscount_code'];
            $this->_percentage          = $results[0]['checkoutdiscount_percent'];
            $this->_remark              = $results[0]['checkoutdiscount_remark'];
            $this->_checkout_discount   = $this->_schGetBaseCheckoutDiscount(Mage::getSingleton('checkout/session')->getQuoteId());

        } else {
            $this->_discountcode    = '';
            $this->_percentage      = 0;
            $this->_remark          = '';
            $this->_checkout_discount = 0;

        }
        return ($this->_percentage * -1);
    }
}
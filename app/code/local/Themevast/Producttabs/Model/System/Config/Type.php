<?php

class Themevast_Producttabs_Model_System_Config_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'random', 'label'=>Mage::helper('adminhtml')->__('Random Products')),
            array('value' => 'featured', 'label'=>Mage::helper('adminhtml')->__('Featured')),
            array('value' => 'saleproduct', 'label'=>Mage::helper('adminhtml')->__('Hot Sale')),
            array('value' => 'newproduct', 'label'=>Mage::helper('adminhtml')->__('New Arrivals')),
            array('value' => 'bestseller', 'label'=>Mage::helper('adminhtml')->__('BEST OFFERS')),
            array('value' => 'mostviewed', 'label'=>Mage::helper('adminhtml')->__('Most Viewed')),
            
        );
    }
}
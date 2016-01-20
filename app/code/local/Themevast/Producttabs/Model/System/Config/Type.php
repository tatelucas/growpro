<?php

class Themevast_Producttabs_Model_System_Config_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'random', 'label'=>Mage::helper('adminhtml')->__('Random Products')),
            array('value' => 'featured', 'label'=>Mage::helper('adminhtml')->__('Featured Products')),
            array('value' => 'saleproduct', 'label'=>Mage::helper('adminhtml')->__('Hot Sales')),
            array('value' => 'newproduct', 'label'=>Mage::helper('adminhtml')->__('New Arrivals')),
            array('value' => 'bestseller', 'label'=>Mage::helper('adminhtml')->__('Best Sellers')),
            array('value' => 'mostviewed', 'label'=>Mage::helper('adminhtml')->__('Most Viewed')),
			array('value' => 'moreleads', 'label'=>Mage::helper('adminhtml')->__('Get More Leads')),
			array('value' => 'moresales', 'label'=>Mage::helper('adminhtml')->__('Close More Sales')),
			array('value' => 'communicatebetter', 'label'=>Mage::helper('adminhtml')->__('Communicate Better')),

        );
    }
}

<?php

/**
 * MadeToOrder dimension of variables for backend config
 *
 * @category   Metrof
 * @package    Metrof_MadeToOrder
 */
class Metrof_MadeToOrder_Model_System_Config_Source_Dimensions extends Varien_Object
{

    public function toOptionArray()
    {
		$options = array();
		$options['none']   = 'No Dimension';
		$options['width']  = 'Width';
		$options['height'] = 'Height';
		$options['depth']  = 'Depth';

        return $options;
    }
}

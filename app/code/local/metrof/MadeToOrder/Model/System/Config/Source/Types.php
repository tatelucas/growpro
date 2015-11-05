<?php

/**
 * MadeToOrder types of variables for backend config
 *
 * @category   Metrof
 * @package    Metrof_MadeToOrder
 */
class Metrof_MadeToOrder_Model_System_Config_Source_Types extends Varien_Object
{

    public function toOptionArray()
    {
		$options = array();
		$options['disabled']  = 'Do Not Use';
		$options['range']     = 'Range (*_min, *_max, *_step vars required)';
		$options['text']      = 'Plain Text';

        return $options;
    }
}

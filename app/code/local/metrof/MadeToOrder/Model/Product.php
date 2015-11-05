<?php
/**
 * FGI
 */

/**
 * Catalog product
 *
 * @category   Metrof
 * @package    Metrof_MadeToOrder
 */
class Metrof_MadeToOrder_Model_Product extends Mage_Catalog_Model_Product
{
    /**
     * Product Types
     */
    const STATUS_ENABLED            = 1;
    const STATUS_DISABLED           = 2;

    const TYPE_SIMPLE         = 'simple';
    const TYPE_BUNDLE         = 'bundle';
    const TYPE_CONFIGURABLE   = 'configurable';
    const TYPE_GROUPED        = 'grouped';
    const TYPE_VIRTUAL        = 'virtual';
    const TYPE_DYNAMIC_PRICE  = 'simple';

    const DEFAULT_TYPE      = 'simple';



    protected $productWidth  = 0;
    protected $productHeight = 0;
    protected $productDepth  = 0;
    protected $productSqft   = 0;
    protected $productLine   = '';
    protected $dynPrice = -1;

    public function getPrice() {
        if ($this->hasDynamicPrice()) {
            if ($this->dynPrice > 0 ) { return $this->dynPrice;}
            $dyn = $this->getDynamicPrice();
//var_dump($dyn);exit();
//exit();
            $this->dynPrice = $dyn;
            return $dyn;
        } else {
            return $this->getData('price');
        }
    }

    public function hasDynamicPrice() {
        switch ($this->getTypeId()) {
            case Metrof_MadeToOrder_Model_Product::TYPE_SIMPLE:
            case Metrof_MadeToOrder_Model_Product::TYPE_DYNAMIC_PRICE:
                return true;
                break;

        }
        return false;
    }

	/**
	 * Return true if we found some user submitted sizes in the POST
	 * This should trigger other processing if the user submitted sizes
	 */
    public function setupDynamicAttribs() {
		$foundAttribs = false;
        //look in request
        //FIXME try to grab the global Zend Request object somehow
        if(isset($_POST['mto_width']) ) {
            //if not, use some defaults
			$foundAttribs = true;
            $this->productWidth = @$_POST['mto_width'];
        } else {
            //if not, use some defaults
            $this->productWidth = @$this->_data['mto_width_min'];
        }

        if(isset($_POST['mto_height']) ) {
			$foundAttribs = true;
            $this->productHeight = @$_POST['mto_height'];
		} else {
            //if not, use some defaults
            $this->productHeight = @$this->_data['mto_height_min'];
		}

        if(isset($_POST['mto_depth']) ) {
			$foundAttribs = true;
            $this->productDepth = @$_POST['mto_depth'];
		} else {
            //if not, use some defaults
            $this->productDepth = @$this->_data['mto_height_min'];
		}

        //product line is sku minus '-CS'
        $this->productLine = substr($this->getData('sku'),0, -3);
        //calc sqft, round down
		//TODO add in fractional inches
        $this->productSqft = floor($this->productWidth * $this->productHeight);
//var_dump($this->productWidth);exit();
		return $foundAttribs;
    }

    public function getCalculatedPrice(array $options)
    {
    	$price = $this->getPrice();
    	foreach ($this->getSuperAttributes() as $attribute) {
    		if(isset($options[$attribute['attribute_id']])) {
	    		if($value = $this->_getValueByIndex($attribute['values'], $options[$attribute['attribute_id']])) {
	    			if($value['pricing_value'] != 0) {
	    				$price += $this->getPricingValue($value);
	    			}
	    		}
    		}
    	}
    	return $price;
    }


	/**
	 * This method is mainly to be run from the product view page, after a user
	 *  enters some config values.  Otherwise it should simply shortcut and 
	 *  return the default price.
	 */
    public function getDynamicPrice() {
		if (!$this->setupDynamicAttribs()) {
			return $this->getData('price');
		}

		if (! function_exists('simplexml_load_string') ) {
			return $this->getData('price');
		}
		$xmlString = $this->getPriceXml();
		try {
        	$xml = @simplexml_load_string($this->getPriceXml());
		} catch (Exception $e) { 
			return $this->getData('price');
		}

		if (! is_object($xml) ) {
			return $this->getData('price');
		}

        $moreConditions = true;
        //limit to 3 nested conditions
        foreach ($xml->productLine[0]->product->children() as $subTag) {
            if ( $subTag->getName() == 'condition' ) {
                $result = $this->evalConditions($subTag);
                if ( is_object($result) && $result->getName() == 'calculation' ) {
                    $price = (float)$result * $this->productSqft;
                    break;
                } else if ($result !== false) {
                    $price = (string)$result;
                    break;
                }
            }

            if ( $subTag->getName() == 'calculation' ) {
                $price = $this->productSqft * (float)$subTag;
                break;
            }
        }
        if (!isset($price) || $result === false) {
		return 'sqft = '.$this->productSqft .' w = '.$this->productWidth;
             throw new Exception ('error in pricing logic');
        }

        //chop off dollar sign if one exists.
        if (substr($price,0,1) === '$') {
            $price = substr($price,1);
        }

        return $price;
    }


    /**
     * Recursive function, returns price, calculation tag, or false
     */
    protected function evalConditions($condition) {
        if ($condition->getName() === 'condition') {
            //eval
            $rslt = true;
            switch ($condition['type']) {
                case 'range':
                    list($min,$max) = explode('-',$condition['value']);
                    if ($condition['attrib'] == 'sqft') { $test = (float)$this->productSqft;}
                    if ($condition['attrib'] == 'width') { $test = (float)$this->productWidth;}

/*
var_dump($test);
var_dump($max);
var_dump('$test <= $max');
var_dump($test <= $max);
//var_dump($condition);
// */
                    $rslt = $test >= (int)$min;
                    $rslt = ($test <= (int)$max) && $rslt;
                    break;
            }
            if (!$rslt) { return false; }
            //eval went okay
            //do children
            $hasChildren = false;
            foreach($condition->children() as $subTag) {
                $hasChildren = true;
                $rslt = $this->evalConditions($subTag);
                //we found a price
                if (is_string($rslt)) { return $rslt; }
            }
            if (!$hasChildren) { return (string)$condition; }
            
        }
        //we're not even a condition, return ourselves as a condition
        //TODO: should we just return the price right here?
        if ($condition->getName() === 'calculation' ) {
             return (float)$condition * $this->productSqft;
        }
        return $rslt;
    }

	/**
	 * This should return the string content of a file based on the product line.
	 */
    public function getPriceXml() {
		//app/code/local is in the include path
		//file get contents does not use include path
		if (! file_exists(BP.'/app/code/local/Metrof/MadeToOrder/pricingsheets/samplexml.txt') ) {
			return '';
		}
		return @file_get_contents(BP.'/app/code/local/Metrof/MadeToOrder/pricingsheets/samplexml.txt');
	}


	public function getTypeId() {
		return isset($this->_data['type_id'])? $this->_data['type_id']:NULL;
	}

	public function getName() {
		return isset($this->_data['name'])? $this->_data['name']:NULL;
	}

    public function getSampleXml() {
return <<<EOF
<?xml version="1.0"?>
<fgipricingrules>
        <productLine line="50050">
                <product sku="VL-OR1">
                        <condition type="eq" attrib="thickness" value="1">
                                <calculation attrib="sqft">0.86</calculation>
                        </condition>
                        <condition type="eq" attrib="thickness" value="2">
                                <calculation attrib="sqft">0.96</calculation>
                        </condition>
                </product>
        </productLine>
        <productLine line="20000">
                <product sku="20000">
                        <condition type="range" attrib="width" value="0-33">
                                <condition type="eq" attrib="depth" value="1/2">
                                        <condition type="range" attrib="sqft" value="0-99">$3.20</condition>
                                        <condition type="range" attrib="sqft" value="100-199">$3.28</condition>
                                        <condition type="range" attrib="sqft" value="200-249">$3.42</condition>
                                </condition>
                                <condition type="eq" attrib="depth" value="1">
                                        <condition type="range" attrib="sqft" value="0-99">$3.30</condition>
                                        <condition type="range" attrib="sqft" value="100-199">$3.28</condition>
                                        <condition type="range" attrib="sqft" value="200-249">$3.42</condition>
                                </condition>
                                <condition type="eq" attrib="depth" value="2">
                                        <condition type="range" attrib="sqft" value="0-99">$3.40</condition>
                                        <condition type="range" attrib="sqft" value="100-199">$3.28</condition>
                                        <condition type="range" attrib="sqft" value="200-249">$3.99</condition>
                                </condition>
                        </condition>
                        <condition type="range" attrib="width" value="34-66">
                                <condition type="eq" attrib="depth" value="1/2">
                                        <condition type="range" attrib="sqft" value="0-99">$9.10</condition>
                                        <condition type="range" attrib="sqft" value="100-199">$9.28</condition>
                                        <condition type="range" attrib="sqft" value="200-249">$9.42</condition>
                                </condition>
                                <condition type="eq" attrib="depth" value="1">
                                        <condition type="range" attrib="sqft" value="0-99">$9.10</condition>
                                        <condition type="range" attrib="sqft" value="100-199">$9.28</condition>
                                        <condition type="range" attrib="sqft" value="200-249">$9.42</condition>
                                </condition>
                                <condition type="eq" attrib="depth" value="2">
                                        <condition type="range" attrib="sqft" value="0-99">$9.10</condition>
                                        <condition type="range" attrib="sqft" value="100-199">$9.28</condition>
                                        <condition type="range" attrib="sqft" value="200-249">$9.99</condition>
                                </condition>
                        </condition>
                </product>
        </productLine>
</fgipricingrules>
EOF;
    }
}

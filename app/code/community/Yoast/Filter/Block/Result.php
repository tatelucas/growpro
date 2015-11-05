<?php
class Yoast_Filter_Block_Result extends Mage_Catalog_Block_Product_List
{
  protected function _getProductCollection()
  {
    if (is_null($this->_productCollection)) {
      $collection = Mage::getResourceModel('catalog/product_collection');
      Mage::getModel('catalog/layer')->prepareProductCollection($collection);
		
	if ($this->getValue()){
		$value = $this->getValue();		
	} else{
		$value = $this->getRequest()->getParam('filterValue', 0);
	}

	if ($this->getCategory())
	{
		$categoryId = $this->getCategory();	
	} 
	else
	{
		 $categoryId = $this->getRequest()->getParam('filterCategory', 0);
	}


	if ($this->getAttribute()){
		$attribute = $this->getAttribute();		
	} else {
		 $attribute = 'color' ;
	}
 
	$collection->addAttributeToFilter($attribute, $value);

      
	if ($categoryId) {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $collection->addCategoryFilter($category, true);
      }
 
      $this->_productCollection = $collection;
    }
 
    return $this->_productCollection;
  }
}
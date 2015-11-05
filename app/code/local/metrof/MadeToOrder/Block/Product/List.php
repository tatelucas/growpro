<?php
/**
 * Product list
 *
 * @category   Metrof
 * @package    Metrof_MadeToOrder
 */
class Metrof_MadeToOrder_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = Mage::getSingleton('catalog/layer');
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::getStoreConfig('catalog/category/root_id'));
            }
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                $collection->setCurrentCategory($category);
            }
            $collection->getProductCollection()->addAttributeToSelect('*');
            $this->_productCollection = $collection->getProductCollection();
        }
        return $this->_productCollection;
    }


    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }
}

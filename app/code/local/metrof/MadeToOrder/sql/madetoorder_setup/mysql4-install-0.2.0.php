<?php

/**
 * Catalog install
 *
 * @category   Metrof
 * @package    Metrof_MadeToOrder
 */
$installer = $this;

$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$read->query('select `entity_id` from `catalog_product_entity');

$select = $read->select();
$select->from('eav_entity_type', 'entity_type_id');
$dumb = 'catalog_product';
$select->where('entity_type_code = "quote_item"');
$stmt = $select->query();
$result = $stmt->fetchAll();
$quote_type_id = $result[0]['entity_type_id'];


$select = $read->select();
$select->from('eav_entity_type', 'entity_type_id');
$dumb = 'catalog_product';
$select->where('entity_type_code = "order_item"');
$stmt = $select->query();
$result = $stmt->fetchAll();
$order_type_id = $result[0]['entity_type_id'];



$attributes = array();
$attributes[] =
    array(
    'entity_type_id' =>  $quote_type_id ,
    'attribute_code' => 'line_item_details',
    'attribute_model' => NULL,
    'backend_model' => NULL,
    'backend_type' => 'text',
    'backend_table' => NULL,
    'frontend_model' => NULL,
    'frontend_input' => 'text',
    'frontend_label' => 'Line Item Details',
    'frontend_class' => '',
    'source_model' => 'eav/entity_attribute_source_table',
    'is_global' => '1',
    'is_visible' => '0',
    'is_required' => '0',
    'is_user_defined' => '1',
    'default_value' => ''
    );

$attributes[] =
    array(
    'entity_type_id' =>  $order_type_id ,
    'attribute_code' => 'line_item_details',
    'attribute_model' => NULL,
    'backend_model' => NULL,
    'backend_type' => 'text',
    'backend_table' => NULL,
    'frontend_model' => NULL,
    'frontend_input' => 'text',
    'frontend_label' => 'Line Item Details',
    'frontend_class' => '',
    'source_model' => 'eav/entity_attribute_source_table',
    'is_global' => '1',
    'is_visible' => '0',
    'is_required' => '0',
    'is_user_defined' => '1',
    'default_value' => ''
    );


$attributes[] =
    array(
    'entity_type_id' =>  $quote_type_id ,
    'attribute_code' => 'config_id',
    'attribute_model' => NULL,
    'backend_model' => NULL,
    'backend_type' => 'varchar',
    'backend_table' => NULL,
    'frontend_model' => NULL,
    'frontend_input' => 'text',
    'frontend_label' => 'Config ID',
    'frontend_class' => '',
    'source_model' => 'eav/entity_attribute_source_table',
    'is_global' => '1',
    'is_visible' => '0',
    'is_required' => '0',
    'is_user_defined' => '1',
    'default_value' => ''
    );


$attributes[] =
    array(
    'entity_type_id' =>  $order_type_id ,
    'attribute_code' => 'config_id',
    'attribute_model' => NULL,
    'backend_model' => NULL,
    'backend_type' => 'varchar',
    'backend_table' => NULL,
    'frontend_model' => NULL,
    'frontend_input' => 'text',
    'frontend_label' => 'Config ID',
    'frontend_class' => '',
    'source_model' => 'eav/entity_attribute_source_table',
    'is_global' => '1',
    'is_visible' => '0',
    'is_required' => '0',
    'is_user_defined' => '1',
    'default_value' => ''
    );

function mto_createAttribute($c)
{
    $attribute = new Mage_Eav_Model_Entity_Attribute();

    $attribute->loadByCode($c['entity_type_id'],$c['attribute_code'])
    ->setStoreId(0)
    ->load(null)
    ->addData($c);

    $attribute->save();
    return $attribute->getId();
}



$installer->startSetup();

foreach ($attributes as $_attrib) {
    $parent_id = mto_createAttribute($_attrib);
}


$installer->endSetup();


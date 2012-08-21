<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Booking
 * @version    1.2.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


$installer = $this;

/* $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/* Delete attrs */
$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('booking/order')}; 

	CREATE TABLE {$this->getTable('booking/order')} (
		`id` int(11) NOT NULL auto_increment,
		`order_id` int(11) NOT NULL,
		`product_id` int(11) NOT NULL,
		`sku` VARCHAR( 64 ) NOT NULL,
		`product_name` varchar(255) NOT NULL,
		`bind_start` datetime NOT NULL,
		`bind_end` datetime NOT NULL,
		`bind_type` varchar(64) NOT NULL,
		`created_time` datetime NOT NULL,
		
		PRIMARY KEY  (`id`),
		KEY `created_time` (`created_time`),
		KEY `product_name` (`product_name`),
		KEY `product_id` (`product_id`),
		KEY `order_id` (`order_id`),
		KEY `bind_start` (`bind_start`),
		KEY `bind_end` (`bind_end`),
		KEY `sku` ( `sku` )
	) DEFAULT CHARSET utf8 ENGINE = InnoDB; 
	");




$setup = new Mage_Eav_Model_Entity_Setup('core_setup');


try {
    $setup->removeAttribute('catalog_product', 'aw_booking_enabled');
    $setup->removeAttribute('catalog_product', 'aw_booking_quantity');
    $setup->removeAttribute('catalog_product', 'aw_booking_range_type');
    $setup->removeAttribute('catalog_product', 'aw_booking_date_from');
    $setup->removeAttribute('catalog_product', 'aw_booking_date_to');
    $setup->removeAttribute('catalog_product', 'aw_booking_time_from');
    $setup->removeAttribute('catalog_product', 'aw_booking_time_to');
    $setup->removeAttribute('catalog_product', 'aw_booking_qratio');
    $setup->removeAttribute('catalog_product', 'aw_booking_qratio_multiplier');

}catch(Exception $E) {

}

$setup = $this;







$setup->addAttribute('catalog_product', 'aw_booking_quantity', array(
	'backend'       => '',
	'source'        => '',
	'group'		=> 'Booking',
	'label'         => 'Quantity',
	'input'         => 'text',
	'class'         => 'validate-digit',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'default_value' => 1,
	'visible'       => true,
	'required'      => true,
	'user_defined'  => false,
	'default'       => '1',
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  1,
));

$setup->addAttribute('catalog_product', 'aw_booking_range_type', array(
	'backend'       => 'booking/entity_attribute_backend_rangetype',
	'source'        => 'booking/entity_attribute_source_rangetype',
	'group'		=> 'Booking',
	'label'         => 'Period type',
	'input'         => 'select',
	'class'         => 'validate-digit',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => 'date_fromto',
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  2,
));	


$setup->addAttribute('catalog_product', 'aw_booking_date_from', array(
	'backend'       => 'eav/entity_attribute_backend_datetime',
	'source'        => '',
	'group'		=> 'Booking',
	'label'         => 'Date from',
	'input'         => 'date',
	'type'          => 'datetime',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  3,
));		
$setup->addAttribute('catalog_product', 'aw_booking_date_to', array(
	'backend'       => 'eav/entity_attribute_backend_datetime',
	'source'        => '',
	'type'          => 'datetime',
	'group'		=> 'Booking',
	'label'         => 'Date to',
	'input'         => 'date',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  4,
));		

$setup->addAttribute('catalog_product', 'aw_booking_time_from', array(
	'backend'       => 'eav/entity_attribute_backend_array',

	'source'        => '',
	'group'		=> 'Booking',
	'label'         => 'Time from',
	'input'         => 'time',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  5,
));	
$setup->addAttribute('catalog_product', 'aw_booking_time_to', array(
	'backend'       => 'eav/entity_attribute_backend_array',
	'source'        => '',
	'group'		=> 'Booking',
	'label'         => 'Time to',
	'input'         => 'time',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  6,
));		

$setup->addAttribute('catalog_product', 'aw_booking_qratio', array(
	'backend'       => '',
	'source'        => '',
	'group'		=> 'Booking',
	'label'         => 'Ratio',
	'input'         => 'text',
	'class'         => 'validate-digit',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => 0,
	'default_value' => 1,
	'required'      => 0,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  7,
));			

$setup->addAttribute('catalog_product', 'aw_booking_qratio_multiplier', array(
	'backend'       => '',
	'source'        => 'booking/entity_attribute_source_qratiomultipliertype',
	'group'		=> 'Booking',
	'label'         => 'Billable period',
	'input'         => 'select',
	'class'         => 'validate-digit',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'default'       => '0',
	'visible_on_front' => false,
    'position'      =>  8,
));		


$fieldList = array('price','special_price','special_from_date','special_to_date',
	'minimal_price','cost','tier_price','tax_class_id');
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    if (!in_array('bookable', $applyTo)) {
	$applyTo[] = 'bookable';
	$installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
    }
}

$installer->endSetup();

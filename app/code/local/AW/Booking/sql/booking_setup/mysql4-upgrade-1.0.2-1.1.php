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
$setup = $this;
$setup->addAttribute('catalog_product', 'aw_booking_shipping_enabled', array(
	'backend'       => '',
	'source'        => 'booking/product_shipping',
	'group'			=> 'Booking',
	'label'         => 'Shipping',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 1,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  9,
));	

$setup->addAttribute('catalog_product', 'aw_booking_multiply_options', array(
	'backend'       => '',
	'source'        => 'booking/product_multiplyoptions',
	'group'			=> 'Booking',
	'label'         => 'Multiply options',
	'input'         => 'select',
	'type'          => 'int',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  10,
));	

$setup->addAttribute('catalog_product', 'aw_booking_exclude_days', array(
	'backend'       => 'booking/product_backend_excludedays',
	'source'        => '',
	'group'			=> 'Booking',
	'label'         => 'Exclude days',
	'input'         => 'text',
	'type'          => 'text',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  11,
));	

$setup->addAttribute('catalog_product', 'aw_booking_prices', array(
	'backend'       => 'booking/product_backend_prices',
	'source'        => '',
	'group'			=> 'Booking',
	'label'         => 'Price rules',
	'input'         => 'text',
	'type'          => 'text',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'default' 		=> 0,
	'required'      => false,
	'user_defined'  => false,
	'apply_to'      => 'bookable',
	'visible_on_front' => false,
    'position'      =>  12,
));	


$installer->startSetup();
/* Create table for excluded days*/
$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('booking/excluded_days')}; 

	CREATE TABLE {$this->getTable('booking/excluded_days')} (
		`id` INT( 11 ) NOT NULL auto_increment,
		`entity_id` INT( 11 ) NOT NULL ,
		`store_id` INT( 11 ) NOT NULL ,
		`period_type` ENUM( 'single', 'recurrent_day', 'recurrent_date', 'period', 'recurrent_period' ) NOT NULL ,
		`period_recurrence_type` ENUM( 'monthly', 'yearly' ) NOT NULL,
		`period_from` DATE NOT NULL ,
		`period_to` DATE NOT NULL ,
		PRIMARY KEY ( `id` ) ,
		KEY `store_id` (`store_id`),
		KEY `period_recurrence_type` (`period_recurrence_type`),
		INDEX ( `entity_id` , `period_type` , `period_from` , `period_to` )
		
	) DEFAULT CHARSET utf8 ENGINE = InnoDB; 
	
	
	ALTER TABLE {$this->getTable('booking/order')} ADD `is_canceled` INT( 1 ) NOT NULL DEFAULT '0';
	ALTER TABLE {$this->getTable('booking/order')} ADD INDEX ( `is_canceled` ) ;
	");

$installer->run("
 CREATE TABLE {$this->getTable('booking/price')} (
	`id` INT( 11 ) NOT NULL auto_increment,
	`entity_id` INT( 11 ) NOT NULL ,
	`store_id` INT( 11 ) NOT NULL DEFAULT '0',
	`date_from` DATE NOT NULL ,
	`date_to` DATE NOT NULL ,
	`price_from` FLOAT NOT NULL ,
	`price_to` FLOAT NOT NULL ,
	`is_progressive` TINYINT NOT NULL DEFAULT '0',
	PRIMARY KEY ( `id` ) ,
	INDEX ( `entity_id` , `store_id` , `date_from` , `date_to` , `price_from` , `price_to` , `is_progressive` )
) DEFAULT CHARSET utf8 ENGINE = InnoDB 

	");

$installer->endSetup();

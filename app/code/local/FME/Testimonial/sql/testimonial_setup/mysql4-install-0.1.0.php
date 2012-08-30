<?php
/**
 * Advance Testimonial extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Advance Testimonial
 * @author     Kamran Rafiq Malik <support@fmeextensions.com>
 *             1- Created - 10-10-2010
 *             
 * 	       Asif Hussain <support@fmeextensions.com>
 * 	       1 - Testimonial Table - 23-03-2012
 * 	       
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('testimonial')};
CREATE TABLE {$this->getTable('testimonial')} (                                
               `testimonial_id` int(11) unsigned NOT NULL auto_increment,  
               `company_name` varchar(255) NOT NULL default '',            
               `contact_name` varchar(255) NOT NULL default '',            
               `contact_photo` varchar(255) default NULL,                  
               `email` varchar(255) NOT NULL default '',                   
               `website` varchar(255) default '',                          
               `order_num` int(11) NOT NULL default '0',                   
               `designation` varchar(255) default '',                      
               `testimonial` text NOT NULL,                                
               `short_description` varchar(255) default '',                
               `url_identifier` varchar(255) default NULL,                   
               `featured_testimonial` tinyint(6) default '1',              
               `status` smallint(6) NOT NULL default '0',                  
               `created_time` datetime default NULL,                       
               `update_time` datetime default NULL,                        
               `meta_title` varchar(255) default NULL,                     
               `meta_keywords` varchar(255) default NULL,                  
               `meta_description` varchar(255) default NULL,               
               `custom_field1_value` varchar(255) default NULL,            
               `custom_field2_value` varchar(255) default NULL,            
               `custom_field3_value` varchar(255) default NULL,            
               `custom_field4_value` varchar(255) default NULL,            
               PRIMARY KEY  (`testimonial_id`),                            
               UNIQUE KEY `url_identifier` (`url_identifier`)                            
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('testimonial_store')};
CREATE TABLE {$this->getTable('testimonial_store')} (  
	`testimonial_id` int(11) NOT NULL,                               
	`store_id` smallint(5) unsigned NOT NULL,                        
	PRIMARY KEY  (`testimonial_id`,`store_id`),                      
	KEY `FK_TESTIMONIAL_STORE_STORE` (`store_id`)                    
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Testimonial Stores';

");

//Set Values for Default Configuration

$installer->setConfigData('testimonial/list/page_title','Testimonials');
$installer->setConfigData('testimonial/list/identifier','testimonial');
$installer->setConfigData('testimonial/list/items_per_page','6');
$installer->setConfigData('testimonial/list/meta_keywords','Testimonials');
$installer->setConfigData('testimonial/list/meta_description','Testimonials');
$installer->setConfigData('testimonial/list/allow_read_more','1');
$installer->setConfigData('testimonial/list/show_contact_photo','1');

$installer->setConfigData('testimonial/add_testimonial_settings/customer_allowed','guests');
$installer->setConfigData('testimonial/add_testimonial_settings/admin_approval','1');
$installer->setConfigData('testimonial/add_testimonial_settings/open_form','popup');

$installer->setConfigData('testimonial/formfields/company_name','1');
$installer->setConfigData('testimonial/formfields/contact_name','1');
$installer->setConfigData('testimonial/formfields/email','1');
$installer->setConfigData('testimonial/formfields/website','1');
$installer->setConfigData('testimonial/formfields/contact_photo','1');
$installer->setConfigData('testimonial/formfields/short_desc','1');

$installer->setConfigData('testimonial/featuredtestimonials/enable','1');
$installer->setConfigData('testimonial/featuredtestimonials/block_title','Testimonials');
$installer->setConfigData('testimonial/featuredtestimonials/no_of_testimonials','5');
$installer->setConfigData('testimonial/featuredtestimonials/block_type','featured');
$installer->setConfigData('testimonial/featuredtestimonials/allow_read_more','1');
$installer->setConfigData('testimonial/featuredtestimonials/Js_effect','fade');
$installer->setConfigData('testimonial/featuredtestimonials/allow_pagination','1');
$installer->setConfigData('testimonial/featuredtestimonials/effect_duration','1');

$installer->setConfigData('testimonial/themes/select_theme','theme1');

$installer->setConfigData('testimonial/email_settings/email_sender','general');
$installer->setConfigData('testimonial/email_settings/enable_moderator_notification','1');
$installer->setConfigData('testimonial/email_settings/moderator_email','support@fmeextensions.com');
$installer->setConfigData('testimonial/email_settings/moderator_email_subject','New Testimonial Added');
$installer->setConfigData('testimonial/email_settings/moderator_email_template','testimonial_email_settings_moderator_email_template');
$installer->setConfigData('testimonial/email_settings/enable_client_notification','1');
$installer->setConfigData('testimonial/email_settings/client_email_subject','Your Testimonial Added');
$installer->setConfigData('testimonial/email_settings/client_email_template','testimonial_email_settings_client_email_template');

$installer->setConfigData('testimonial/seo/url_suffix','.html');

$installer->endSetup(); 
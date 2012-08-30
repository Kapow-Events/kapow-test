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
 * 	       Asif Hussain <support@fmeextensions.com>
 * 	       1 - Random Testimonials Block - 23-03-2012
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Block_Block extends Mage_Core_Block_Template
{

    public function getItems($limit = 3)     
	{ 
		$store = Mage::app()->getStore()->getStoreId();
		
		
		//Collection For Featured Testimonials
		if(Mage::getStoreConfig('testimonial/featuredtestimonials/block_type') == 'featured'):    
		    
			if(Mage::getStoreConfig('testimonial/list/sort_by') == 'order'):
			
				$collection = Mage::getModel('testimonial/testimonial')->getCollection()
							->addStoreFilter($store)
							->addFieldToFilter('main_table.status', 1)
							->addFieldToFilter('main_table.featured_testimonial', 1)
							->addOrder('main_table.order_num', 'asc')
							->setPageSize($limit)
							->getData();
			else:
				
				$collection = Mage::getModel('testimonial/testimonial')->getCollection()
							->addStoreFilter($store)
							->addFieldToFilter('main_table.status', 1)
							->addFieldToFilter('main_table.featured_testimonial', 1)
							->addOrder('main_table.created_time', 'desc')
							->setPageSize($limit)
							->getData();
			
			endif;
		
		//Collection For Random Testimonials
		elseif(Mage::getStoreConfig('testimonial/featuredtestimonials/block_type') == 'random'):  
		    $collection = Mage::getModel('testimonial/testimonial')->getCollection()
							->addStoreFilter($store)
							->addFieldToFilter('main_table.status', 1)
							->addOrder(new Zend_Db_Expr('RAND()'))
							->setPageSize($limit)
							->getData();
							
							
		   
		endif;
		
		
		
		
		return $collection;
        
	}
    
}
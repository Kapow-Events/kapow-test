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
 *             Asif Hussain <support@fmeextensions.com>
 * 	      
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Block_Pagination extends Mage_Core_Block_Template
{
    
	public function getPaginator()     
	{ 

		if ( !$this->hasData('items') ) {
			$this->setData('items', Mage::registry('items'));
		}
		return $this->getData('items')->getPages('Sliding');
        
	}
	
}
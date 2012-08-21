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

/**
 * @deprecated
 */
class AW_Booking_Block_Order extends Mage_Core_Block_Template{
    /**
     * Now we can push green button and our changes will be applied to local server
     *
     */
    protected $_product;
    
	public function getProduct(){
		if(!$this->_product){
			$this->_product = Mage::getSingleton('catalog/product')->load($this->getRequest()->getParam('id'));
		}
		return $this->_product;
	}
	
	public function getBindedDates(){
		/* Returns booked days as string */
	//	if($this->getProduct()->getAwBookingRangeType() == 'date_fromto'){
			$days = Mage::helper('booking/dates')->getUnavailDays(
				$this->getProduct()
			);
			return Mage::helper('booking/yui')->formatDayArray($days);
	//	}else return '';	
	}
}

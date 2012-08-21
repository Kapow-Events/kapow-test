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

class AW_Booking_Block_Adminhtml_Sales_Order_Info
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface{
    
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getTabLabel()
    {
        return Mage::helper('booking')->__('Booking');
    }

    public function getTabTitle()
    {
        return Mage::helper('booking')->__('Booking Information');
    }

    public function canShowTab()
    {
        return true;
    }

	public function isHidden(){
		// Show only if there are reserved items in order
		return !$this->getItemsCollection()->getSize();
	}
	
	public function getItemsCollection(){
		if(!$this->_collection){
			$id = $this->getRequest()->getParam('order_id');
			$order = $this->getOrder();
			$this->_collection = Mage::getModel('booking/order')->getCollection()->addOrderIdFilter($order->getIncrementId())->load();
		}
		return $this->_collection;
	}
	
	public function getCollection(){
		return $this->getItemsCollection();
	}
}

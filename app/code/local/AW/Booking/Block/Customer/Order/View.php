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

class AW_Booking_Block_Customer_Order_View extends Mage_Core_Block_Template {
    public function __construct() {
	parent::__construct();
	$this->setTemplate('booking/customer/order/view.phtml');
    }


    public function getAction() {
	return Mage::getUrl('helpdeskultimate/customer/new');
    }

    /**
     * Return collection of reserved items.
     * @return <type>
     */
    public function getCollection() {
	$id = $this->getRequest()->getParam('order_id');
	$order = Mage::getModel('sales/order')->load($id);
	$coll = Mage::getModel('booking/order')
		->getCollection()
		->addOrderIdFilter($order->getIncrementId())
		->load();
	return $coll;
    }

}

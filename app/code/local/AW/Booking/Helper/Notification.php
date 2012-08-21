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


class AW_Booking_Helper_Notification extends Mage_Core_Helper_Abstract{
	
	public function getNotifSender(){
		if($email = Mage::getStoreConfig('booking/notification/notification_email',Mage::app()->getStore()->getId()))
			return array(
				'mail' => $email,
				'name' => $this->__('AW_Booking'),
			);
		else
			return array(
				'mail' => Mage::getStoreConfig('trans_email/ident_general/email',Mage::app()->getStore()->getId()),
				'name' => Mage::getStoreConfig('trans_email/ident_general/name',Mage::app()->getStore()->getId()),
			);
	}
	
	public function getNotifTemplate(){
		return Mage::getStoreConfig('booking/notification/email_template',Mage::app()->getStore()->getId());
	}
	
	public function getTemplate(){
		$mailModel = Mage::getModel('core/email_template');
		$template = $mailModel->load($this->getNotifTemplate());
		if($template->getId())
			return $template;
		else
			return $mailModel->loadDefault('booking_notify_to_admin');
	}
}


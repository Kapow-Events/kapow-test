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

class AW_Booking_Model_Notification{
	
	public function sendQtyNotAvailableAlert($order,$product,$qtyAvalible){

		$variables = array(
            'product_sku'  				=> $product->getSku(),
            'product_name'   			=> $product->getName(),
			'product_link'   			=> Mage::getModel('adminhtml/url')->getUrl('adminhtml/catalog_product/edit', array('id'=>$product->getProductId())),
			'current_reservations'		=> (int)$product->getQtyOrdered(),
			'reservations_available'	=> $qtyAvalible,
			'order_date'				=> $order->getCreatedAt(),
			'order_id'					=> $order->getIncrementId(),
			'order_link'				=> Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/view', array('order_id'=>$order->getId())),
			'customer_name'				=> $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
			'customer_link'				=> ($order->getCustomerId())?Mage::getModel('adminhtml/url')->getUrl('adminhtml/customer/edit', array('id'=>$order->getCustomerId())):null,
        );
        
        $subject = Mage::helper('booking')->__('Notification about unsuccessful ') . $product->getName(). Mage::helper('booking')->__(' reservation');
        $storeId = Mage::app()->getStore()->getId();
        $sender = Mage::helper('booking/notification')->getNotifSender();
        $emailTemplate = Mage::helper('booking/notification')->getTemplate();
        
		$this->sendNotify($sender['mail'],$sender['name'],$sender['mail'],$sender['name'],$subject,$emailTemplate,$variables);

    }

    public function sendNotify($toEmail,$toName,$fromEmail,$fromName,$subject,$emailTemplate,$variables){

        $emailTemplate->getProcessedTemplate($variables);
        $emailTemplate->setSenderName($fromName)
                      ->setSenderEmail($fromEmail)
                      ->setTemplateSubject($subject)
                      ->send($toEmail,$toName, $variables);
    }
}

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


class AW_Booking_Model_Observer {

    protected $_session;

    public function __construct() {
        $this->_session = Mage::getSingleton('customer/session');
    }

    public function getSession() {
        return $this->_session;
    }

    /**
     * Update product's name at booking/order table
     * @param object $event
     */
    public function updateBookingOrdersProductName($observer) {
        $Product = $observer->getEvent()->getProduct();

        if (!$storeId = $Product->getStore()->getId()) {
            $storeId = null;
        }

        Mage::getResourceModel('booking/order')
        ->setProductNameById($Product->getId(), $Product->getName(), $storeId);
    }

    /**
     * Set binds for products added to cart
     * @param Object $event
     */
    public function bindCartItems($event) {
        if (Mage::registry("booking_order_created")) {
            // Already converted to order
            return;
        }

        $quoteItem = $event->getItem();
        if ($quoteItem->getProductType() != AW_Booking_Helper_Config::PRODUCT_TYPE_CODE) {
            // Not bookable product type
            return;
        }

        /** If item is deleted, don't process later */
        if ($quoteItem->isDeleted()) {
            return;
        }

        /** Test if quote item is already converted to order item */
        if (Mage::getModel('sales/order_item')->load($quoteItem->getId(), 'quote_item_id')->getId()) {
            return;
        }

        // Get From and To values
        $Product = $quoteItem->getProduct();
        if(!is_object($Product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME))){
            $source = unserialize($Product->getCustomOption('info_buyRequest')->getValue());
            $from_date = $source['aw_booking_from'];
            $to_date = $source['aw_booking_to'];
        }
        else{
            $from_date = $Product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME)->getValue();
            $to_date = $Product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_DATE_OPTION_NAME)->getValue();
            $from_time = $Product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_TIME_OPTION_NAME)->getValue();
            $to_time = $Product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_TIME_OPTION_NAME)->getValue();

            $from_date .= " $from_time";
            $to_date .= " $to_time";
        }

        //$From = new Zend_Date($from_date, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        $From = new Zend_Date($from_date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        $To = new Zend_Date($to_date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);


        // Check if specified quantity is available
        $originalQty = Mage::getModel('booking/order')->getCollection()->addQuoteItemIdFilter($quoteItem->getId())->count();
        $qtyToAdd = $quoteItem->getQty() - $originalQty;

        //if (($qtyToAdd > 0) && ! Mage::getModel('booking/checker_bind')->isQtyAvailableForPeriod($Product, $From, $To, $qtyToAdd)) {
        if (($qtyToAdd > 0) && ! Mage::getModel('booking/checker_bind')->isQtyAvailableForPeriodCheck($Product, $From, $To, $quoteItem->getQty())) {
            if ($quoteItem->getQuote()->getItemById($quoteItem->getId())) $quoteItem->getQuote()->removeItem($quoteItem->getId())->save();
            throw new Mage_Core_Exception(Mage::helper("checkout")->__("Some of the products you requested are not available in the desired quantity"));
        }

        if ($qtyToAdd == 0) {
            return;
        }

        Mage::getResourceModel('booking/order')->deleteByQuoteItem($quoteItem);


        /* Create as many records as quoteItem quantity is */
        $i = 1;
        $qty = $quoteItem->getQty();
        while ($i <= $qty) {
            $BQuoteItem = Mage::getModel('booking/order');
            $BQuoteItem
            ->setProductId($quoteItem->getProductId())
            ->setProductName($quoteItem->getName())
            ->setSku($quoteItem->getSku())
            ->setBindStart($From->get(AW_Core_Model_Abstract::DB_DATETIME_FORMAT))
            ->setBindEnd($To->get(AW_Core_Model_Abstract::DB_DATETIME_FORMAT))
            ->setBindType(AW_Booking_Model_Order::BIND_TYPE_CART)
            ->setOrderId($quoteItem->getId())
            ->setQuoteId($quoteItem->getQuote()->getId())
            ->setCreatedTime(now())
            ->save();
            $i += 1;
        }
    }

    /**
     * Remove all cart binds for specified quote item
     * @param Varien_Object $event
     * @todo emplement
     */
    public function removeCartBinds($event) {

        $quoteItem = $event->getQuoteItem();
        if (!$quoteItem) {
            $quoteItem = $event->getItem();
        }

        if ($quoteItem->getProductType() != AW_Booking_Helper_Config::PRODUCT_TYPE_CODE) {
            // Not bookable product type
            return;
        }
        Mage::getResourceModel('booking/order')->deleteByQuoteItem($quoteItem);
    }

    /**
     * Clear old quote info
     * @param Varien_Object $event
     */
    public function quoteMergeAfter($event) {
        $Source = $event->getSource();
        $Quote = $event->getQuote();
        Mage::getResourceModel('booking/order')->deleteByQuoteId($Source->getId());
    }

    /**
     * Binds product as ordered
     * @param <type> $observer
     * @return <type>
     */
    public function bindOrderItems($observer) {
        $items = $observer->getInvoice()->getOrder()->getItemsCollection();

        foreach ($items as $item) {
            // Fetch for products
            $Product = Mage::getModel('catalog/product')->load($item->getProductId());
            // Affect only bookable broducts
            if ($Product->getTypeId() != AW_Booking_Helper_Config::PRODUCT_TYPE_CODE) {
                continue;
            }

            $data = $item->getProductOptionByCode('info_buyRequest');

            $_date_from = $data['aw_booking_from'];
            $_date_to = @$data['aw_booking_to'] ? $data['aw_booking_to'] : $data['aw_booking_from'];
            $_time_from = @$data['aw_booking_time_from'];
            $_time_to = @$data['aw_booking_time_to'];


            // Parse date to set to db

            $From = new Zend_Date($_date_from, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
            $To = new Zend_Date($_date_to, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));

            $from = $From->toString(AW_Core_Model_Abstract::DB_DATE_FORMAT);
            $to = $To->toString(AW_Core_Model_Abstract::DB_DATE_FORMAT);
            // If time is set, we should also remember it
            /**
                     * @todo Test with time options
                     */
            if ($_time_from) {
                $from .= ( " " . AW_Booking_Model_Product_Type_Bookable::convertTime($_time_from, AW_Core_Model_Abstract::RETURN_STRING));
            }
            if ($_time_to) {
                $to .= ( " " . AW_Booking_Model_Product_Type_Bookable::convertTime($_time_to, AW_Core_Model_Abstract::RETURN_STRING));
            }
            // If interval is free, check if interval is available

            $qty = ($item->getQtyInvoiced());

            if (Mage::getModel('booking/checker_bind')->isQtyAvailableForPeriod($Product, new Zend_Date($from, AW_Core_Model_Abstract::DB_DATETIME_FORMAT), new Zend_Date($to, AW_Core_Model_Abstract::DB_DATETIME_FORMAT), $qty, false)) {

                $model = Mage::getModel('booking/order')
                    ->setProductId($item->getProductId())
                    ->setProductName($Product->getName())
                    ->setSku($Product->getSku())
                    ->setBindStart($from)
                    ->setBindEnd($to)
                    ->setOrderId($observer->getInvoice()->getOrder()->getIncrementId())
                    ->setOrderItemId($item->getId())
                    ->setCreatedTime(now())
                ;

                while ($qty--) {
                    $model->setId(null)->save();
                }
                Mage::getResourceModel('booking/order')->deleteByQuoteItemId($item->getQuoteItemId());
            } else {
                Mage::getSingleton('customer/session')->addError('Sorry, the period you specified is inaccessible');
                return false;
            }
        }

        // Write flag to session to skip checking quantity for bookable products set
        Mage::register('booking_order_created', 1);

        return true;
    }

    public function attachExcludeEditor($observer) {
        $form = $observer->getForm();
        if ($excludedDays = $form->getElement('aw_booking_exclude_days')) {
            $excludedDays->setRenderer(
                Mage::getSingleton('core/layout')->createBlock('booking/adminhtml_catalog_product_edit_tab_booking_excludeddays')
            );
        }
    }

    public function attachPricesEditor($observer) {
        $form = $observer->getForm();
        if ($excludedDays = $form->getElement('aw_booking_prices')) {

            $excludedDays->setRenderer(
                Mage::getSingleton('core/layout')->createBlock('booking/adminhtml_catalog_product_edit_tab_booking_prices')
            );
        }
    }

    /**
     * Cancels booking "Order" record
     * TODO: Check if this function needed or cancelOrderItem function can replace it
     * @param  $observer
     * @return void
     */
    public function cancelOrder($observer) {
        $order = $observer->getOrder();
        if ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED || $order->getState() == Mage_Sales_Model_Order::STATE_CLOSED) {
            Mage::getResourceSingleton('booking/order')->cancelByOrderId($order->getIncrementId());
        }
    }

    /**
     * Cancels booking "Order" record by order item
     * @param  $observer
     * @return void
     */
    public function cancelOrderItem($observer) {
        $orderItem = $observer->getEvent()->getDataObject();
        if (!($orderItem instanceof Mage_Sales_Model_Order_Item)) return;
        if ($orderItem->getStatusId() == Mage_Sales_Model_Order_Item::STATUS_REFUNDED)
            Mage::getResourceSingleton('booking/order')->cancelByOrderId($orderItem->getOrder()->getIncrementId());
    }
    
    
    public function checkQtyOrder($observer){
    	
        $order = $observer->getEvent()->getOrder();
        if($order->getStatus() == Mage_Sales_Model_Order::STATE_COMPLETE){
	        foreach($order->getAllItems() as $item){
	            
	            $product = Mage::getModel('catalog/product')->load($item->getProductId());
	            $placed = Mage::getModel('booking/order')->load($order->getIncrementId(),'order_id');
	            if($item->getProductType() == AW_Booking_Helper_Config::PRODUCT_TYPE_CODE && $item->getQtyInvoiced() == $item->getQtyOrdered() && !$placed->getId()){
	
	                $options = $item->getProductOptions();
	                $from = (isset($options['info_buyRequest'][AW_Booking_Model_Product_Type_Bookable::FROM_TIME_OPTION_NAME]))?$options['info_buyRequest'][AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME] . $options['info_buyRequest'][AW_Booking_Model_Product_Type_Bookable::FROM_TIME_OPTION_NAME]:$options['info_buyRequest'][AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME];
	                $to = (isset($options['info_buyRequest'][AW_Booking_Model_Product_Type_Bookable::TO_TIME_OPTION_NAME]))?$options['info_buyRequest'][AW_Booking_Model_Product_Type_Bookable::TO_DATE_OPTION_NAME] . $options['info_buyRequest'][AW_Booking_Model_Product_Type_Bookable::TO_TIME_OPTION_NAME]:$options['info_buyRequest'][AW_Booking_Model_Product_Type_Bookable::TO_DATE_OPTION_NAME];
	
	                $from  = new Zend_Date($from,Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
	                $to  = new Zend_Date($to,Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
	
	                if(!Mage::getModel('booking/checker_bind')->isQtyAvailableForPeriod($product, $from, $to, $item->getQtyOrdered(),false)){
	                	$orderedQty = Mage::getModel('booking/order')->getOrderedQty($product,$from,$to);
	                	$avalible = $product->getAwBookingQuantity() - $orderedQty;  
	                	Mage::getModel('booking/notification')->sendQtyNotAvailableAlert($order,$item,$avalible);
	                	//throw new Exception('Qty is not avalible more');
	                }
	            }
	        }
	    }
    }
}
?>

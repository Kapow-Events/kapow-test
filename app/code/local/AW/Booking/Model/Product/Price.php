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

class AW_Booking_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price {

    /**
     * Returns complex price (booking price rules + common price * multiplier)
     * @param Mage_Catalog_Model_Product $product
     * @param Zend_Date $From
     * @param mixed $To
     * @param $basePrice base price
     * @return string
     */
    public function getBookingPrice($product, Zend_Date $From, $To=null, $basePrice = null, $ret = null) {
        if(is_null($basePrice)) {
            $basePrice = $product->getData('price');
        }

        if($To instanceof Zend_Date) {
            // Period is used
            $price = Mage::getModel('booking/checker_price')->getPriceForPeriod($product, $From, $To, $basePrice, Mage::app()->getStore()->getId(), null, $ret);


        }else {
            // Price for single day
            $price = Mage::getModel('booking/checker_price')->getPriceForDate($product, $From, Mage::app()->getStore()->getId());
            if($price == AW_Booking_Model_Checker::NO_PRICE) {
                $price = $basePrice;
            }
        }

        return $price;
    }

    /**
     * Applies "Special" price. This is built-in magento.
     * By the moment this is just stub, please set special price via provided with booking price rules selector
     * @param Mage_Catalog_Model_Product $product
     * @param float $finalPrice
     * @return float
     */
    protected function _applySpecialPrice($product, $finalPrice) {
        return $finalPrice;
    }

    public function getStoredFirstAvailableDate($product){

        $cProduct_id = Mage::registry('booking-time-product-id');
        $product_id = $product->getId();
        if($product_id != Mage::registry('booking-time-product-id')){
            Mage::unregister('booking-time-product-id');
            Mage::register('booking-time-product-id',$product_id);
        }
        if($cProduct_id != Mage::registry('booking-time-product-id')){
            Mage::unregister('booking-time-firstAvailableDate');
            Mage::register('booking-time-firstAvailableDate',$product->getTypeInstance()->getFirstAvailableDate());
        }
        return Mage::registry('booking-time-firstAvailableDate');
    }

    /**
     * Default action to get price of product
     *
     * @return decimal
     */
    public function getPrice($product) {
//        $now = new Zend_Date;
        //$Date = $product->getTypeInstance()->getFirstAvailableDate();
        $Date = ($this->getStoredFirstAvailableDate($product))?$this->getStoredFirstAvailableDate($product):new Zend_Date();

        $from_date = $Date->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        $Date->addHour(1);
        $to_date = $Date->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT);

        if($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME)) {
            $from_date = $product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME)->getValue();
        }
        if($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_DATE_OPTION_NAME)) {
            $to_date = $product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_DATE_OPTION_NAME)->getValue();
        }
        if($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_TIME_OPTION_NAME)) {
            $from_time = $product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_TIME_OPTION_NAME)->getValue();
            $from_date .= " $from_time";
        }
        if($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_TIME_OPTION_NAME)) {
            $to_time = $product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_TIME_OPTION_NAME)->getValue();
            $to_date .= " $to_time";
        }

        $From = new Zend_Date($from_date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        if($to_date) {
            $To = new Zend_Date($to_date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        }else {
            $To = clone $From;
        }

        $price =  $this->getBookingPrice($product, $From, $To, $product->getData('price'));
        $product->setData('final_price', $price);

        return $price;
    }

    /**
     * Return final price
     * @param int  $qty
     * @param  Mage_Catalog_Model_Product $product
     * @todo Optimize that with previous method to be more spartan
     * @return int|mixed
     */
    public function getFinalPrice($qty=null, $product) {

        $bookingPrice = $this->getPrice($product);
        // Calculate how many reservations used


        //$Date = $product->getTypeInstance()->getFirstAvailableDate();
        $Date = ($this->getStoredFirstAvailableDate($product))?$this->getStoredFirstAvailableDate($product):new Zend_Date();

        $from_date = $Date->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        $Date->addHour(1);
        $to_date = $Date->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT);

        if($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME)) {
            $from_date = $product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME)->getValue();
        }
        if($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_DATE_OPTION_NAME)) {
            $to_date = $product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_DATE_OPTION_NAME)->getValue();
        }
        //check product Billable period and if it equal days don't use time in price calculating
        if($product->getAwBookingQratioMultiplier() != AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype::DAYS){
            if($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_TIME_OPTION_NAME)) {
                $from_time = $product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_TIME_OPTION_NAME)->getValue();
                $from_date .= " $from_time";
            }
            if($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_TIME_OPTION_NAME)) {
                $to_time = $product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::TO_TIME_OPTION_NAME)->getValue();
                $to_date .= " $to_time";
            }
        }

        $From = new Zend_Date($from_date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        if($to_date) {
            $To = new Zend_Date($to_date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        }else {
            $To = clone $From;
        }

        $price = $product->getData('price');
        $finalPrice = $this->_applyTierPrice($product, $qty, $price);
        $finalPrice = $this->_applySpecialPrice($product, $finalPrice);

        list($finalPrice, $occurs) = $this->getBookingPrice($product, $From, $To, $finalPrice , AW_Core_Model_Abstract::RETURN_ARRAY);
        $bookingRatio = $occurs;

        return $finalPrice;
    }

    public function getOptionsPrice($product, $price)
    {
        $optprice = 0;
        if ($optionIds = $product->getCustomOption('option_ids')) {
            $basePrice = $price;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {

                    $quoteItemOption = $product->getCustomOption('option_'.$option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setQuoteItemOption($quoteItemOption);

                    $optprice += $group->getOptionPrice($quoteItemOption->getValue(), $basePrice);
                }
            }
        }

        return $optprice;
    }
}

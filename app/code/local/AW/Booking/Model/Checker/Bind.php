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

class AW_Booking_Model_Checker_Bind extends AW_Core_Object {

    protected $skipTimeCheck = false;

    public function _construct() {
        Zend_Date::setOptions(array('extend_month' => true));
        return parent::_construct();
    }

    /**
     * Check if date can be binded
     * @param Zend_Date $Date
     * @param object    $product_id
     * @param object    $qty [optional]
     * @return bool
     */
    public function isQtyAvailable($product_id, Zend_Date $Date, $qty = 1, $includeCart = true) {
        return $this->isQtyAvailableForDate($product_id, $Date, $qty, $includeCart);
    }

    /**
     * Alias for $this->isQtyAvailable
     * @param <type> $product_id
     * @param Zend_Date $Date
     * @param <type> $qty
     * @param bool $includeCart
     */
    public function isQtyAvailableForDate($product_id, Zend_Date $Date, $qty = 1, $includeCart = true) {

        $_date = clone $Date;
        $this->skipTimeCheck = true;

        if ($this->getProduct())
        {
            $Product = $this->getProduct();
        }
        else
        {
            $model = Mage::getModel('catalog/product');
            if ($product_id instanceof $model) {
                $Product = $product_id;
            } else {
                $Product = $model->load($product_id);
            }
        }

        if (
            !$this->skipTimeCheck
            && $Product->getAwBookingQratioMultiplier() == AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype::HOURS
            && $Date->Compare($Product->getData('aw_booking_time_to'), Zend_Date::TIME_SHORT) > -1
            )
        {
            return false;
        }

        if ($includeCart && $Quote = Mage::helper('checkout')->getQuote()) {
            $quoteId = $Quote->getId();
        } else {
            $quoteId = 0;
        }

        $Orders = Mage::getModel('booking/order')
                    ->getCollection()
                    ->addQuoteIdFilter($quoteId)
                    ->addProductIdFilter($Product->getId());

        if ($Product->getAwBookingQratioMultiplier() == AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype::HOURS) {
            $Orders->addBindDateTimeFilter($_date);
        } else {
            $Orders->addBindDateFilter($_date);
        }

        $total_binds = $Orders->count();

        if (!($bookingQty = $Product->getAwBookingQuantity())) {
            $Product = $Product->load($Product->getId());
            $bookingQty = $Product->getAwBookingQuantity();
        }

        return $total_binds <= ($bookingQty - $qty);
    }


    /**
     * Check if period if specified quantity is available for period
     * @param object    $product_id
     * @param Zend_Date $From
     * @param Zend_Date $To
     * @param object    $qty [optional]
     * @return bool
     */
    public function isQtyAvailableForPeriod($product_id, Zend_Date $_From, Zend_Date $_To, $qty = 1, $includeCart = true) {

        $From = clone $_From;
        $To = clone $_To;

        $model = Mage::getModel('catalog/product');
        if ($product_id instanceof $model) {
            $Product = $product_id;
        } else {
            $Product = $model->load($product_id);
        }

        if ($Product->getAwBookingQratioMultiplier() == AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype::HOURS) {
            $method = 'addHour';
        } else {
            $method = 'addDayOfYear';
        }

        while ($this->compareDateOrTime($Product->getAwBookingRangeType(), $From, $To))
        {
            if (!$this->isQtyAvailable($Product, $From, $qty, $includeCart)) 
                return false;
            $From = call_user_func(array($From, $method), 1);
        }
        return true;
    }

    public function isQtyAvailableForPeriodCheck($product_id, Zend_Date $from, Zend_Date $to, $qty = 1) {

        $model = Mage::getModel('catalog/product');
        if ($product_id instanceof $model) {
            $Product = $product_id;
        } else {
            $Product = $model->load($product_id);
        }

        list($h,$m,$s) = explode(',',$Product->getAwBookingTimeFrom());
        $productFrom = ($Product->getAwBookingDateFrom())? new Zend_Date($Product->getAwBookingDateFrom()): new Zend_Date();
        $productFrom->setTime('00:00:00');
        if($Product->getAwBookingRangeType() != 'date_fromto'){
            $productFrom->addHour((int)$h);
            $productFrom->addMinute((int)$m);
            $productFrom->addSecond((int)$s);
        }

        list($h,$m,$s) = explode(',',$Product->getAwBookingTimeTo());
        if($Product->getAwBookingDateTo())
                $productTo = new Zend_Date($Product->getAwBookingDateTo());
        else{
            $productTo = new Zend_Date();
            $productTo->addYear(1);
        }
        
        $productTo->setTime('00:00:00');
        if($Product->getAwBookingRangeType() != 'date_fromto'){
            $productTo->addHour((int)$h);
            $productTo->addMinute((int)$m);
            $productTo->addSecond((int)$s);
        }

        $orderedQty = Mage::getModel('booking/order')->getOrderedQty($Product,$from,$to);

        if($from >= $productFrom && $from<$productTo && $to >= $productFrom && $to <= $productTo && $qty <= ($Product->getAwBookingQuantity() - $orderedQty))
            return true;
        else
            return false;
    }

    /**
     * Compares if $compareType is date, then use <= else (datetime and time) use <
     * @param string $compareType
     * @param Zend_Date $From
     * @param Zend_Date $To
     * @return bool 
     */

    private function compareDateOrTime($compareType, $From, $To)
    {
        if ($compareType == AW_Booking_Model_Entity_Attribute_Source_Rangetype::DATE)
            $compareResult = $From->compare($To) <= 0;
        else
            $compareResult = $From->compare($To) < 0;
        return $compareResult;
    }

    /**
     * Return unavailable dates as array
     * @param <type> $product_id
     * @param Zend_Date $_From
     * @param Zend_Date $_To
     * @param <type> $qty
     * @return array
     */
    public function getUnavailDays($Product, Zend_Date $_From, Zend_Date $_To, $qty = 1, $includeCart = true) {
        $dates = array();
        // Clone from and to to not affect original values
        $From = clone $_From;
        $To = clone $_To;
        $this->setProduct($Product->load($Product->getId()));
        while ($From->compare($To) <= 0) {
            $this->skipTimeCheck = true;
            $dates[$From->toString(AW_Core_Model_Abstract::DB_DATE_FORMAT)] = $this->isQtyAvailable(null, $From, $qty, $includeCart);
            $From = $From->addDayOfYear(1);
        }

        return $dates;
    }
}
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
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   AW
 * @package    AW_Booking
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 *
 */



class AW_Booking_Model_Product_Type_Bookable extends Mage_Catalog_Model_Product_Type_Abstract {

    /** Flag constant indicationg no period from is set for product */
    const HAS_NO_PERIOD_FROM = 2; // Indicates
    /** Flag constant indicationg no period to is set for product */
    const HAS_NO_PERIOD_TO = 4;
    /** "From" date field name */
    const FROM_DATE_OPTION_NAME = 'aw_booking_from';
    /** "To" date field name */
    const TO_DATE_OPTION_NAME = 'aw_booking_to';
    /** "From" time field name */
    const FROM_TIME_OPTION_NAME = 'aw_booking_time_from';
    /** "To" time field name */
    const TO_TIME_OPTION_NAME = 'aw_booking_time_to';

    protected $_isDuplicable = false;
    protected $_product;

    /**
     * Retrive product instance in any cost
     * @return
     */
    public function getProduct($product = null) {
        if(!$product) 
        {
            if ($this->_product) return $this->_product;
            $product = $this->_product;
            if(!$product)
            {
                $product = Mage::registry('product');
                if(!$product) {
                    throw new AW_Core_Exception("Can't get product instance");
                }
                $this->_product = Mage::getModel('catalog/product')->load($product->getId());
            }
        }
        $this->setProduct($product);
        return $product;
    }
    public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null){
        if(!$product) $product = $this->getProduct();

        /* We should add custom options that doesnt exist */
        if(!$product) $product = $this->getProduct();

        if($buyRequest->getAwBookingFrom()) {
            // Set "from" equal to "To" if no "to" specified
            if(!$buyRequest->getAwBookingTo()) {
                $buyRequest->setAwBookingTo($buyRequest->getAwBookingFrom());
            }

            $From = new Zend_Date($buyRequest->getAwBookingFrom(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
            $To = new Zend_Date($buyRequest->getAwBookingTo(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));


            // Add time and date
            list($fH, $fM) = self::_convertTimeTo24hArray($buyRequest->getAwBookingTimeFrom());
            list($tH, $tM) = self::_convertTimeTo24hArray($buyRequest->getAwBookingTimeTo());

            $From->setHour((int)$fH)->setMinute((int)$fM);
            $To->setHour((int)$tH)->setMinute((int)$tM);

            $product->addCustomOption(self::FROM_DATE_OPTION_NAME, $From->toString('Y-m-d', 'php'), $product);
            $product->addCustomOption(self::TO_DATE_OPTION_NAME,  $To->toString('Y-m-d', 'php'), $product);
            $product->addCustomOption(self::FROM_TIME_OPTION_NAME,  $From->toString('H:i', 'php'), $product);
            $product->addCustomOption(self::TO_TIME_OPTION_NAME, $To->toString('H:i', 'php'), $product);

            /* Check if product can be added to cart */
            //list($isAvail, $itemsLeft) = $this->isAvailable($product, $buyRequest->getQty(), true);
            $isAvail = $this->isAvailable($product, $buyRequest->getQty(), true);
            if(!$isAvail) {
                Mage::throwException(
                    //Mage::helper('booking')->__("Chosen quantity is not available. Only %s reservations left.", $itemsLeft)
                    Mage::helper('booking')->__("Chosen quantity is not available. Only reservations left.")
                );
            }
            return parent::prepareForCartAdvanced($buyRequest, $product);
        }
        return Mage::helper('booking')->__('Please specify reservation information');
    }
    public function prepareForCart(Varien_Object $buyRequest, $product = null) {
        if(!$product) $product = $this->getProduct();

        /* We should add custom options that doesnt exist */
        if(!$product) $product = $this->getProduct();

        if($buyRequest->getAwBookingFrom()) {
            // Set "from" equal to "To" if no "to" specified
            if(!$buyRequest->getAwBookingTo()) {
                $buyRequest->setAwBookingTo($buyRequest->getAwBookingFrom());
            }

            $From = new Zend_Date($buyRequest->getAwBookingFrom(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
            $To = new Zend_Date($buyRequest->getAwBookingTo(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));


            // Add time and date
            list($fH, $fM) = self::_convertTimeTo24hArray($buyRequest->getAwBookingTimeFrom());
            list($tH, $tM) = self::_convertTimeTo24hArray($buyRequest->getAwBookingTimeTo());

            $From->setHour((int)$fH)->setMinute((int)$fM);
            $To->setHour((int)$tH)->setMinute((int)$tM);

            $product->addCustomOption(self::FROM_DATE_OPTION_NAME, $From->toString('Y-m-d', 'php'), $product);
            $product->addCustomOption(self::TO_DATE_OPTION_NAME,  $To->toString('Y-m-d', 'php'), $product);
            $product->addCustomOption(self::FROM_TIME_OPTION_NAME,  $From->toString('H:i', 'php'), $product);
            $product->addCustomOption(self::TO_TIME_OPTION_NAME, $To->toString('H:i', 'php'), $product);

            /* Check if product can be added to cart */
            //list($isAvail, $itemsLeft) = $this->isAvailable($product, $buyRequest->getQty(), true);
            $isAvail = $this->isAvailable($product, $buyRequest->getQty(), true);
            if(!$isAvail) {
                Mage::throwException(
                    //Mage::helper('booking')->__("Chosen quantity is not available. Only %s reservations left.", $itemsLeft)
                    Mage::helper('booking')->__("Chosen quantity is not available. Only reservations left.")
                );
            }
            return parent::prepareForCart($buyRequest, $product);
        }
        return Mage::helper('booking')->__('Please specify reservation information');



    }

    /**
     * Detects if specified date is first salable day
     * @param Zend_Date $Date
     * @return
     */
    public function isFirstDay(Zend_Date $Date) {
        $product = ($this->_product) ? $this->_product : $this->getProduct();
        $From = new Zend_Date($product->getAwBookingDateFrom(), AW_Core_Model_Abstract::DB_DATE_FORMAT);
        return $Date->compare($From, Zend_Date::DATE_SHORT) == 0;
    }

    /**
     * Detects if specified date is last salable day
     * @param Zend_Date $Date
     * @return
     * @untested
     */
    public function isLastDay(Zend_Date $Date) {
        $product = ($this->_product) ? $this->_product : $this->getProduct();
        $To = new Zend_Date($product->getAwBookingDateTo(), AW_Core_Model_Abstract::DB_DATE_FORMAT);
        return $Date->compare($To, Zend_Date::DATE_SHORT) == 0;
    }

    /**
     * Check if product can be really bought
     * @param object $product [optional]
     * @param object $qty [optional]
     * @param object $includeAvail [optional]
     * @return
     */
    public function isAvailable($product = null, $qty=1, $includeAvail = false) {
        if(!$qty) {
            $qty = 1;
        }
        if(is_null($product)) {
            $product = ($this->_product) ? $this->_product : $this->getProduct();
        }

        $from_date = $product->getCustomOption(self::FROM_DATE_OPTION_NAME)->getValue();
        $to_date = $product->getCustomOption(self::TO_DATE_OPTION_NAME)->getValue();

        $from_time = $product->getCustomOption(self::FROM_TIME_OPTION_NAME)->getValue();
        $to_time = $product->getCustomOption(self::TO_TIME_OPTION_NAME)->getValue();

        $from_date .= " $from_time";
        $to_date .= " $to_time";

        if($product->getAwBookingRangeType()) {
            $_product = $product;
        }else {
            $_product = Mage::getModel('catalog/product')->load($product->getId());
        }

        /**
         * @todo Attach time to "from" and "to"
         */
        $From = new Zend_Date($from_date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        if($to_date) {
            $To = new Zend_Date($to_date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        }else {
            $To = clone $From;
        }


        if(!Mage::getModel('booking/checker')->getDateChecker()->isPeriodAvail($product, $From, $To)) {
            // Time/date out of bounds
            return false;
        }

        if(!Mage::getModel('booking/checker_bind')->isQtyAvailableForPeriod($product, $From, $To)) {
            // Check already booked
            return false;
        }
        return true;
    }

    /**
     * Returns first available day for product
     * This method checks for ranges, binded days and excluded days
     * @return Zend_Date
     */
    public function getFirstAvailableDate() {
        /* @var $date Zend_Date */
        $Today = new Zend_Date;
        if(($From = $this->getDateFrom()) !== self::HAS_NO_PERIOD_FROM){
           if($From->compare($Today) > 0){
               $MinDate = $From;
           }else{
               $MinDate = $Today;
           }
        }else{
            $MinDate = $Today;
        }
        $MinDate->setHour(0)->setMinute(0)->setSecond(0)->setMilliSecond(0); // Reset date

       
        if($this->getProduct()->getAwBookingQratioMultiplier() == AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype::HOURS) {
            // Hours multiplier
            $method = 'addHour';
        }else {
            // Days multiplier
            $method = 'addDayOfYear';
            // To switch month forward
            Zend_Date::setOptions(array('extend_month' => true));
        }

        if(($MaxDate = $this->getDateTo()) === self::HAS_NO_PERIOD_TO) {
            // Assert that $MaxDate is year later than $MinDate
            $MaxDate = clone $MinDate;
            $MaxDate = $MaxDate->addYear(1);
        }

        $checkerBind = Mage::getModel('booking/checker_bind')->setProduct($this->getProduct());
        $checker = Mage::getModel('booking/checker')->getDateChecker();
        while($MinDate->compare($MaxDate) <= 0) {
            // Check if day is available as for excluded days
            if($checker->isDateAvail($this->getProduct(), $MinDate, Mage::app()->getStore()->getId())) {
                // iterate
                if($checkerBind->isQtyAvailableForDate(null, $MinDate)) {
                    return $MinDate;
                }
            }
            // Iterate
            $MinDate = call_user_func(array($MinDate, $method), 1);
        }

        return $MinDate;
    }


    /**
     * Returns bookable period start as Zend_Date object
     *
     * @return Zend_Date | int
     */
    public function getDateFrom() {
        /**
         * @var $product Mage_Catalog_Model_Product
         */
        $Product = $this->getProduct();

        $date_from = $Product->getAwBookingDateFrom();
        if(is_null($date_from)) {
            // Not set.
            return self::HAS_NO_PERIOD_FROM;
        }
        $From = new Zend_Date($date_from, AW_Core_Model_Abstract::DB_DATE_FORMAT);
        return $From;
    }


    /**
     * Returns bookable period end as Zend_Date object
     *
     * @return Zend_Date
     */
    public function getDateTo() {
        /**
         * @var $product Mage_Catalog_Model_Product
         */
        $Product = $this->getProduct();

        $date_from = $Product->getAwBookingDateTo();
        if(is_null($date_from)) {
            // Not set. Return
            return self::HAS_NO_PERIOD_TO;
        }
        $From = new Zend_Date($date_from, AW_Core_Model_Abstract::DB_DATE_FORMAT);
        return $From;
    }

    /**
     * Converts array to other array convtaining 24h data
     * @param array $Time
     * @return array
     */
    protected static function _convertTimeTo24hArray($time) {
        if(!is_array($time)) {
            $time = array(0,0);
        }
        list($hours, $minutes) = array(@$time[AW_Booking_Block_Catalog_Product_Options_Date::TYPE_HOURS], @$time[AW_Booking_Block_Catalog_Product_Options_Date::TYPE_MINUTES]);
        if(isset($time[AW_Booking_Block_Catalog_Product_Options_Date::TYPE_DAYPART])) {
            // Am/Pm
            if($hours == 12) {
                $hours = 0;
            }
            if($time[AW_Booking_Block_Catalog_Product_Options_Date::TYPE_DAYPART] == 'pm') {
                $hours += 12;
            }
        }
        return array($hours, $minutes);
    }

    /**
     * Converts array to time string
     * @param array $Time
     * @return string
     */
    protected function _convertTimeToStringFromArray($time) {
        list($hours, $minutes) = self::_convertTimeTo24hArray($time);
        return $hours.":".$minutes.":00";
    }

    /**
     * Return time formatted in specified type
     * @param mixed $time
     * @param string $format
     * @return mixed
     */
    public static function convertTime($time, $format = false) {
        list($hours, $minutes) = self::_convertTimeTo24hArray($time);
        if($format == AW_Core_Model_Abstract::RETURN_STRING) {
            return $hours.":".$minutes.":00";
        }else {
            return array(sprintf("%02s", $hours), sprintf("%02s",$minutes));
        }
    }

    /**
     * Stub to render price checker
     * This can be potential source of troubles
     * @todo check if it intersects with magento logic
     * @param Mage_Core_Model_Abstract $product
     * @return boolean
     */
    public function hasOptions($product = null){
        return true;
    }

    public function hasRequiredOptions($product = null)
    {
        return true;
    }




    /**
     * Prepares product for cart according to buyRequest
     *
     * @param Varien_Object $buyRequest
     * @param object        $product [optional]
     * @return
     */
    public function d_prepareForCart(Varien_Object $buyRequest, $product = null) {
        /* We should add custom options that doesnt exist */
        if(!$product) $product = $this->getProduct();

        if($buyRequest->getAwBookingFrom()) {

            if(!$buyRequest->getAwBookingTo()) {
                $buyRequest->setAwBookingTo($buyRequest->getAwBookingFrom());
            }

            // Parsing from and to
            $From = $this->_toDate(
                $buyRequest->getAwBookingFrom(),
                $this->_glueTime($buyRequest->getAwBookingTimeFrom())
            );

            $To = $this->_toDate(
                $buyRequest->getAwBookingTo(),
                $this->_glueTime($buyRequest->getAwBookingTimeTo())
            );

            $product->addCustomOption('aw_booking_from', $From->toString('Y-m-d', 'php'), $product);
            $product->addCustomOption('aw_booking_to',  $To->toString('Y-m-d', 'php'), $product);
            $product->addCustomOption('aw_booking_time_from',  $From->toString('H:i', 'php'), $product);
            $product->addCustomOption('aw_booking_time_to', $To->toString('H:i', 'php'), $product);

            /* Check if product can be added to cart */
            list($isAvail, $itemsLeft) = $this->isAvailable($product, $buyRequest->getQty(), true);

            if(!$isAvail) {
                Mage::throwException(
                    Mage::helper('booking')->__("Chosen quantity is not available. Only %s reservations left.", $itemsLeft)
                );
            }
            return parent::prepareForCart($buyRequest, $product);
        }
        return Mage::helper('booking')->__('Please specify reservation information');
    }

    protected function _toDate($date, $time = null) {
        $str = $date . ($time ? " $time" : "");
        $ts = strtotime($str);
        // Add offset

        list($Y, $m, $d, $H, $i ) = explode("-",date('Y-m-d-H-i', $ts));

        $date = Mage::app()->getLocale()->storeDate(
            Mage::app()->getStore()->getId()
            )
            //->setTimestamp($ts);
            ->setYear($Y)
            ->setMonth($m)
            ->setDay($d)
            ->setHour($H)
            ->setMinute($i);

        return $date;
    }

    /**
     * @deprecated
     * @param object $time
     * @return
     */
    protected function _glueTime($time) {
        if($time) {
            return $time[0].":".$time[1].(isset($time[2]) ? " {$time[2]}" : '');
        }else {
            return "00:00";
        }
    }

    /**
     * Checks if product is salable
     *
     * @param Mage_Catalog_Model_Product $product [optional]
     * @return
     */
    public function isSalable($product = null) {
        /* New 'instant' way to update manage stock */
        if(is_null($product)) {
            $product = Mage::registry('product');
        }

        if(!$product->getAwBookingQuantity() && !is_null($product->getAwBookingQuantity())){
            return false;
        }

        if($product->getStockItem() && $product->getStockItem()->getManageStock()) {
            $product->getStockItem()
                ->setManageStock(0)
                ->setUseConfigManageStock(0)
                ->setQty(1)
                ->save();
        }

        $Today = new Zend_Date();
        $salable = true;

        /*
        if(!is_null($product->getAwBookingDateFrom())) {

            $From = new Zend_Date($product->getAwBookingDateFrom()." ".str_replace(",",":",$product->getAwBookingTimeFrom()), AW_Core_Model_Abstract::DB_DATE_FORMAT);
            if($Today->compare($From) < 0) {
                $salable = false;
            }
        } */
        if(!is_null($product->getAwBookingDateTo())) {
            $To = new Zend_Date($product->getAwBookingDateTo()." ".str_replace(",",":",$product->getAwBookingTimeTo()), AW_Core_Model_Abstract::DB_DATE_FORMAT);
            if($Today->compare($To) > 0) {
                $salable = false;
            }
        }

        if(!$salable) {
            return $salable;
        }


        return parent::isSalable($product);
    }

    /**
     * Returns if product is "virtual", e.g. requires no shipping
     *
     * @param object $product [optional]
     * @return bool
     */
    public function isVirtual($product = null) {
        if(is_null($product)) {
            $product = Mage::registry('product');
        }
        $product->load($product->getId());
        return !$product->getAwBookingShippingEnabled();
    }

    /**
     * Check if product is available for specified date
     * @param object $product [optional]
     * @param int $qty [optional]
     * @return bool
     */
    public function ___isAvailable($product = null, $qty=1, $includeAvail = false) {
        if(!$qty) {
            $qty = 1;
        }
        if(is_null($product)) {
            $product = Mage::registry('product');
        }

        $from_date = $product->getCustomOption('aw_booking_from')->getValue();
        $to_date = $product->getCustomOption('aw_booking_to')->getValue();

        $from_time = $product->getCustomOption('aw_booking_time_from')->getValue();
        $to_time = $product->getCustomOption('aw_booking_time_to')->getValue();

        if($product->getAwBookingRangeType()) {
            $_product = $product;
        }else {
            $_product = Mage::getModel('catalog/product')->load($product->getId());
        }
        $q = $_product->getAwBookingQuantity();
        $r = $_product->getAwBookingRangeType();

        switch($r) {
            case 'time_fromto':
                $reserved = Mage::getModel('booking/order')
                    ->getCollection()
                    ->addProductIdFilter( $product->getId() )
                    ->addBindedDateFilter($from_date, $from_time, $from_date, $to_time, false)
                    ->count();


                $delta = (($q - ($reserved + $qty)) );
            case 'date_fromto':
                $from_time = $to_time = null;
            default:
                $reserved = Mage::getModel('booking/order')
                    ->getCollection()
                    ->addProductIdFilter( $product->getId() )
                    ->addBindedDateFilter($from_date, $from_time, $to_date, $to_time)
                    ->count();
                $delta = (($q - ($reserved + $qty)));

        }
        if($includeAvail) {
            return array($delta >= 0, ($q - $reserved));
        }

        return $delta >= 0;
    }
}

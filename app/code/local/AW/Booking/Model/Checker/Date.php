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

class AW_Booking_Model_Checker_Date extends AW_Core_Object {


    /**
     * Return unavail day
     * @param object $product_id
     * @return
     */
    public function getUnavailDays($product_id, $store_id = 0) {
        if (!$this->getData('unavail_days')) {
            $model = Mage::getSingleton('catalog/product');
            if ($product_id instanceof $model) {
                $product_id = $product_id->getId();
            }

            $rules = $this->_getCollection($product_id, $store_id);

            $data = array(
                AW_Booking_Model_Excludeddays::TYPE_SINGLE => array(),
                AW_Booking_Model_Excludeddays::TYPE_RECURRENT_DAY => array(),
                AW_Booking_Model_Excludeddays::TYPE_RECURRENT_DATE => array(),
                AW_Booking_Model_Excludeddays::TYPE_PERIOD => array(),
                AW_Booking_Model_Excludeddays::TYPE_RECURRENT_PERIOD => array()
            );

            foreach ($rules as $rule) {
                if ($format = $this->getOutputFormat()) {
                    $rule->setOutputFormat($format);
                }
                $arr = $rule->toArray();
                $type = $rule->getType();

                $data[$type][] = $arr;
            }
            $this->setData('unavail_days', $data);
        }
        return $this->getData('unavail_days');
    }

    /**
     * Detectes if product is salable for date
     * @param object $product_id
     * @param Zend_Date $Date
     * @return true
     */
    public function isDateAvail($product_id, Zend_Date $Date, $store_id = 0) {
        $model = Mage::getSingleton('catalog/product');
        if ($product_id instanceof $model) {
            $product_id = $product_id->getId();
        }
        //$coll = $this->_getCollection($product_id, $store_id = 0);
        $coll = $this->getTimeCollection($product_id, $store_id);
        foreach ($coll as $rule) {
            if (!$rule->isDateAvail($Date)) {
                return false;
            }
        }
        return true;
    }

    public function getTimeCollection($product_id,$store_id = 0){
        $cProductId = (Mage::registry('booking-time-product-id'))?Mage::registry('booking-time-product-id'):null;
        $cStoreId   = (Mage::registry('booking-time-store-id'))?Mage::registry('booking-time-store-id'):null;
        
        if($product_id != Mage::registry('booking-time-product-id')){
            Mage::unregister('booking-time-product-id');
            Mage::register('booking-time-product-id',$product_id);
        }
        if($store_id != Mage::registry('booking-time-store-id')){
            Mage::unregister('booking-time-store-id');
            Mage::register('booking-time-store-id',$store_id);
        }
        if($cProductId != Mage::registry('booking-time-product-id') || $cStoreId != Mage::registry('booking-time-store-id')){
            Mage::unregister('booking-time-collection');
            Mage::register('booking-time-collection',$this->_getCollection($product_id, $store_id = 0));
        }
        return Mage::registry('booking-time-collection');
    }

    /**
     * Check if period is available
     * @param object    $product_id
     * @param Zend_Date $From
     * @param Zend_Date $To
     * @param object    $store_id [optional]
     * @param object    $failOnNoMultiplier [optional]
     * @return
     */
    public function isPeriodAvail($product_id, Zend_Date $From, Zend_Date $To, $store_id = 0, $failOnNoMultiplier = 0) {
        $model = Mage::getSingleton('catalog/product');
        if (!($product_id instanceof $model)) {
            $Product = $model->load($product_id);
        } else {
            $Product = $product_id;
            if (!$Product->getAwBookingQratioMultiplier()) {
                if (!$failOnNoMultiplier) {
                    // Reload product if no multiplier is load
                    return $this->isPeriodAvail($Product->getId(), $From, $To, $store_id, true);
                } else {
                    throw new AW_Core_Exception("Can't load product #{$Product->getId()} as bookable. Is it really bookable?");
                }
            }
        }

        $_dates_cache = array();

        $rules = $this->_getCollection($product_id, $store_id = 0);

        if ($From->compare($To) > 0) {
            $_From = clone $To;
            $_To = clone $From;
        } else {
            $_From = clone $From;
            $_To = clone $To;
        }

        if ($Product->getAwBookingQratioMultiplier() != AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype::HOURS) {
            $_From->setHour(0)->setMinute(0)->setSecond(0);
            $_To->setHour(0)->setMinute(0)->setSecond(0);
        }

        $Date = $_From;
        // Check if to is not out of bounds
        if ($Product->getTypeInstance()->getDateTo() !== AW_Booking_Model_Product_Type_Bookable::HAS_NO_PERIOD_TO) {
            if ($_To->compare($Product->getTypeInstance()->getDateTo()) > 0) {
                return false;
            }
        }

        while ($_To->compare($Date, Zend_Date::DATE_MEDIUM) >= 0) {
            if (!isset($_dates_cache[$Date->toString(AW_Core_Model_Abstract::DB_DATE_FORMAT)])) {
                foreach ($this->_getCollection($product_id, $store_id) as $rule) {
                    if (!$rule->isDateAvail($Date)) {
                        return false;
                    }
                }
                $_dates_cache[$Date->toString(AW_Core_Model_Abstract::DB_DATE_FORMAT)] = true;
            }

            if ($Product->getAwBookingQratioMultiplier() == AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype::HOURS) {
                $Date = $Date->addHour(1);
            } else {
                $Date = $Date->addDayOfYear(1);
            }
        }
        return true;
    }

    /**
     * Get collection of excluded days rules
     * @param mixed $product_id
     * @param object $store_id [optional]
     * @return
     */
    protected function _getCollection($product_id, $store_id = 0) {
        if (($product_id instanceof Mage_Catalog_Model_Product)) {
            $product_id = $product_id->getId();
        }
        if ($this->getData('product_id') != $product_id) {
            $this
                ->setData('product_id', $product_id)
                ->setData('collection', null);
        }
        if (!$this->getData('collection')) {
            $collection = array();
            $coll = Mage::getModel('booking/excludeddays')
                ->getCollection()
                ->addEntityIdFilter($product_id)
                ->addStoreIdFilter($store_id);

            foreach ($coll as $rule) {
                $collection[] = $rule;
            }
            $this->setData('collection', $collection);
        }
        $arr = $this->getData('collection');

        reset($arr);
        return $this->getData('collection');
    }

    /**
     * Sets output format. If no format specified, Zend_Date objects are returned
     * @return string
     */
    public function getOutputFormat() {
        if (!$this->getData('output_format')) {
            return false;
        }
        return $this->getData('output_format');
    }
}
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

class AW_Booking_Model_Checker_Price extends AW_Core_Object {

    /**
     * Get price for date
     * @param Zend_Date $Date
     * @param object    $product_id
     * @param object    $store_id [optional]
     * @return
     */
    public function getPriceForDate($product_id, Zend_Date $Date, $store_id = 0) {

        $model = Mage::getSingleton('catalog/product');
        
        if ($product_id instanceof $model) {
            $product_id = $product_id->getEntityId();
        }

        $coll = Mage::getModel('booking/prices')
        ->getCollection()
        ->addEntityIdFilter($product_id)
        ->addStoreIdFilter($store_id)
        ->addDateFilter($Date);

        // Get the first matching price
        $item = false;
        foreach ($coll as $item) {
            break;
        }

        if (!$item) {
            return AW_Booking_Model_Checker::NO_PRICE; // No price rules found
        }

        // Not progressive price
        if (!$item->getIsProgressive()) {
            return $item->getPriceFrom();
        }


        // Progressive price
        $From = new Zend_Date($item->getDateFrom(), AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        $To = new Zend_Date($item->getDateTo(), AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
        $_To = clone($To);

        $delta = $_To->sub($From);
        /* Zend > 1.9 check */
        if ($delta instanceof Zend_Date) $delta = $delta->toValue();
        
        $days_delta = ($delta / AW_Booking_Model_Checker::ONE_DAY);

        $prices_delta = ($item->getPriceTo() - $item->getPriceFrom());

        $day_price = $prices_delta / $days_delta;

        // Calculate how much days spent till date
        $_Date = clone $Date;
        $_Date->setHour(0)->setMinute(0)->setSecond(0);
        // Days spent
        $dateSub = $_Date->sub($From);
        /* Zend > 1.9 check */
        if ($dateSub instanceof Zend_Date) $dateSub = $dateSub->toValue();

        $days_spent = $dateSub / AW_Booking_Model_Checker::ONE_DAY;
        return $item->getPriceFrom() + $days_spent * $day_price;
    }

    /**
     * Get price for period
     * @param mixed    $entityId
     * @param Zend_Date $From
     * @param Zend_Date $To
     * @param float    $basePrice
     * @param int    $store_id [optional]
     * @param boolean    $failOnNoMultiplier [optional]
     * @return
     */
    public function getPriceForPeriod($entityId, Zend_Date $From, Zend_Date $To, $basePrice = false, $store_id = 0, $failOnNoMultiplier = 0, $return = null) {
        Zend_Date::setOptions(array('extend_month' => true));

        
        $model = Mage::getModel('catalog/product');
        if (!($entityId instanceof $model)) {
            $Product = $model->load($entityId);
        } else {
            $Product = $entityId;
            if (!$Product->getAwBookingQratioMultiplier()) {
                if (!$failOnNoMultiplier) {
                    // Reload product if no multiplier is load
                    return $this->getPriceForPeriod($Product->getId(), $From, $To, $basePrice, $store_id, true, $return);
                } else {
                    throw new AW_Core_Exception("Can't load product #{$Product->getId()} as bookable. Is it really bookable?");
                }
            }
        }

        // Try to retrieve base price if not specified
        if ($basePrice === false) {
            $basePrice = $Product->getData('price');
        }

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
        $price = 0;

        $comparision =
                $Product->getAwBookingRangeType() != AW_Booking_Model_Entity_Attribute_Source_Rangetype::DATE
                        ?
                        (null) :
                        Zend_Date::DATE_MEDIUM;


        if ($_To->compare($Date, $comparision) == 0) {
            if ($Product->getAwBookingRangeType() != AW_Booking_Model_Entity_Attribute_Source_Rangetype::DATE) {
                $_To = $To->addHour(1);
            } else {
                $_To = $To->addDayOfYear(1);
            }
        } else {
            if (
                    ($Product->getAwBookingQratioMultiplier() != AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype::HOURS) ||
                    ($Product->getAwBookingRangeType() == AW_Booking_Model_Entity_Attribute_Source_Rangetype::DATE)
            ) {
                $_To = $To->addDayOfYear(1);
            }

        }
        $occurencies = 0;
        while ($_To->compare($Date, $comparision) > 0) {
            $occurencies += 1;
            if (isset($_price_cache[$Date->toString(AW_Core_Model_Abstract::DB_DATE_FORMAT)])) {
                $_price = $_price_cache[$Date->toString(AW_Core_Model_Abstract::DB_DATE_FORMAT)];
            } else {
                $_price = $this->getPriceForDate($entityId, $Date, $store_id);
                $_price_cache[$Date->toString(AW_Core_Model_Abstract::DB_DATE_FORMAT)] = $_price;
            }
            if ($_price != AW_Booking_Model_Checker::NO_PRICE) {
                $delta = $_price;
            } else {
                $delta = $basePrice;
            }
            
            $price += $delta;
            if($Product->hasCustomOptions() && $Product->getAwBookingMultiplyOptions()){
                $optionsPrice = $Product->getPriceModel()->getOptionsPrice($Product, $delta);
                $price += floatval($optionsPrice);
            }

            if ($Product->getAwBookingQratioMultiplier() == AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype::HOURS) {
                $Date = $Date->addHour(1);
            } else {
                $Date = $Date->addDayOfYear(1);
            }
        }

        if($Product->hasCustomOptions() && !$Product->getAwBookingMultiplyOptions()){
            $optionsPrice = $Product->getPriceModel()->getOptionsPrice($Product, $price);
            $price += floatval($optionsPrice);
        }

        if ($return == AW_Core_Model_Abstract::RETURN_ARRAY) {
            return array($price, $occurencies);
        }
        return $price;
    }
}
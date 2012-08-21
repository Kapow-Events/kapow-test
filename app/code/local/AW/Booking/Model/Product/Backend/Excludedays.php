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

class AW_Booking_Model_Product_Backend_Excludedays extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract {


    /**
     * Retrieve resource model
     *
     * @return AW_Booking_Model_Mysql4_Booking_Price
     */
    protected function _getResource() {
        return Mage::getResourceSingleton('booking/booking_price');
    }

    /**
     * Returns current product from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct() {
        return Mage::registry('product');
    }

    /**
     * Validate data
     *
     * @param   Mage_Catalog_Model_Product $object
     * @return  this
     */
    public function validate($object) {

        $periods = $object->getData($this->getAttribute()->getName());
        if (empty($periods)) {
            return $this;
        }


        return $this;
    }



    /**
     * After Save Attribute manipulation
     *
     * @param Mage_Catalog_Model_Product $object
     * @return AW_Booking_Model_Product_Backend_Excludedays
     */
    public function afterSave($object) {

        $generalStoreId = $object->getStoreId();

        $periods = $object->getData($this->getAttribute()->getName());
        if (!is_array($periods)) {
            return $this;
        }

        Mage::getResourceSingleton('booking/excludeddays')->deleteByEntityId($object->getId(), $generalStoreId);

        foreach ($periods as $k=>$period) {
            if(!is_numeric($k)) continue;



            /* Preprocess period */
            if(is_numeric($period['period_from']) && $period['period_from']) {
                if($period['period_type'] == 'recurrent_date') {
                    $period['period_from'] = date('m/'.$period['period_from'].'/Y');
                }
            }

            if($period['period_type'] == 'recurrent_day') {
                $dow = $period['recurrent_day'];

                $Date = new Zend_Date;
                Zend_Date::setOptions(array('extend_month' => true)); // Fix Zend_Date::addMonth unexpected result


                while(AW_Booking_Helper_Data::getDayOfWeek($Date) != $dow) {

                    $Date->addDayOfYear(1);
                }

                $period['period_from'] = $Date->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));

            }

            if (
            !empty($period['delete']) ||

                empty($period['period_type']) ||
                empty($period['period_from']) ||
                !AW_Booking_Helper_Data::toTimestamp($period['period_from']) ||
                (
                $period['period_type'] == 'period' &&
                    empty($period['period_to'])
                ) ||
                (
                $period['period_type'] == 'period' &&
                    (AW_Booking_Helper_Data::toTimestamp($period['period_from']) >= AW_Booking_Helper_Data::toTimestamp($period['period_to']))
            )
            ) {
                continue;
            }

            if(!is_numeric($k)) continue;

            $period['period_from'] = date('Y-m-d H:i:s', AW_Booking_Helper_Data::toTimestamp($period['period_from']));
            $period['period_to'] = date('Y-m-d H:i:s', AW_Booking_Helper_Data::toTimestamp($period['period_to']));



            $storeId = @$period['use_default_value'] ? 0 : $object->getStoreId();

            $ex = Mage::getModel('booking/excludeddays')
                ->setEntityId($this->_getProduct()->getId())
                ->setStoreId($storeId)
                ->setPeriodType($period['period_type'])
                ->setPeriodRecurrenceType($period['period_recurrence_type'])
                ->setPeriodFrom($period['period_from'])
                ->setPeriodTo($period['period_to'])
                ->save();
        }
        return $this;
    }
}

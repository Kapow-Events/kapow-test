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

class AW_Booking_Block_Catalog_Product_View_Yuical extends Mage_Core_Block_Template {

    const READ_DATE_FORMAT = "M/d/yyyy";    // This is format accepted by YUI by default
    const READ_DATE_PAGE_FORMAT = "M/yyyy";    // This is format accepted by YUI by default

    /**
     * Get product instance
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct($product_id = 0) {
        if(!$this->getData('product')) {
            $model = Mage::getSingleton('catalog/product');
            if($product_id instanceof $model) {
                $model = $product_id;
            }elseif($product_id || ($product_id = $this->getRequest()->getParam('id'))) {
                $model->load($product_id);
            }elseif(Mage::registry('product')) {
                $model = $product;
            }
            $this->setData('product', $model);
        }
        return $this->getData('product');
    }

    /**
     * Returns encoded JSON content
     * @return string
     */
    public function getUnavailDaysJSON() {
        return Zend_Json::encode(
            Mage::getModel('booking/checker')
            ->getDateChecker()
            ->setOutputFormat(self::READ_DATE_FORMAT)
            ->getUnavailDays($this->getProduct(), (int)Mage::app()->getStore()->getId())
        );
    }

    /**
     * Returns encoded JSON content
     * @return string
     */
    public function getBindedDaysJSON() {
        $From = $this->getFirstDayOfCurrentMonth();
        $To = clone $From;
        $To->addMonth(Mage::getStoreConfig(AW_Booking_Helper_Config::XML_PATH_APPEARANCE_CALENDAR_PAGES));
        $dates = Mage::getModel('booking/checker_bind')->getUnavailDays($this->getProduct(), $From, $To);
        return Zend_Json::encode(
            $dates
        );
    }

    /**
     * Returns first day in current month
     * @return Zend_Date
     */
    public function getFirstDayOfCurrentMonth() {
        $Date = new Zend_Date;
        $Date->setDay(1);
        return $Date;
    }

}

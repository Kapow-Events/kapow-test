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

class AW_Booking_AjaxController extends Mage_Core_Controller_Front_Action {


    /**
     * Echoes rendered time table for date
     *
     */
    public function getDateAction() {
        $date = $this->getRequest()->getParam('date');
        $Date = new Zend_Date($date, AW_Core_Model_Abstract::DB_DATE_FORMAT);

        $this
            ->getResponse()
            ->setBody(
            $this->getLayout()
            ->createBlock('booking/catalog_product_view_timetable')
            ->getFromToHtml($Date)
        );
    }

    /**
     * Returns price for specified period
     * @return
     */
    public function getPriceAction() {
        if(!$this->getRequest()->getParam('product_id')) {
            return;
        }
        $Product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('product_id'));

        if($from = urldecode($this->getRequest()->getParam('from'))) {
            $From = new Zend_Date($from, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
            if($to = urldecode($this->getRequest()->getParam('to'))) {
                $To = new Zend_Date($to, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
                // From and To are now ready. Get price for period
                $price = $Product->getPriceModel()->getBookingPrice($Product, $From, $To, null, AW_Core_Model_Abstract::RETURN_ARRAY);
            }
        }
        if($date = urldecode($this->getRequest()->getParam('date'))) {
            $Date = new Zend_Date($date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
            $price = $Product->getPriceModel()->getBookingPrice($Product, $Date);
        }
        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($price));
    }

    /**
     * Return binded dates for period
     * @return
     */
    public function getBindedDatesAction() {
        if(!$this->getRequest()->getParam('product_id')) {
            return;
        }
        $Product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('product_id'));


        if($to = urldecode($this->getRequest()->getParam('to'))) {
            $From = new Zend_Date($to, AW_Core_Model_Abstract::DB_DATE_FORMAT);
            $To = clone $From;
            $To->addMonth(Mage::getStoreConfig(AW_Booking_Helper_Config::XML_PATH_APPEARANCE_CALENDAR_PAGES));
            $dates = Mage::getModel('booking/checker_bind')->getUnavailDays($Product, $From, $To);
        }

        $this
            ->getResponse()
            ->setBody(Zend_Json::encode($dates));
    }


}

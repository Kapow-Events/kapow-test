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

class AW_Booking_Block_Catalog_Product_View_Timetable extends Mage_Core_Block_Template {
    
    protected $_product;
    protected $_date = false; 
    protected $_binds;


    /**
     * Retrieves current product
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct(){
        if(!$this->_product) {
            if(Mage::registry('product')) {
                $this->_product = Mage::registry('product');
            }else {
                $this->_product = Mage::getSingleton('catalog/product')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->_product;
    }

    /**
     * Sets date
     * @param Zend_Date $date
     * @return AW_Booking_Block_Order_Hours
     */
    public function setDate(Zend_Date $date) {
        $this->_date = $date;
        return $this;
    }

    /**
     * Returns date
     * @return Zend_Date
     */
    public function getDate() {
        return $this->_date;
    }

    /**
     * Returns HTML with dates
     * @param Zend_Date $date
     * @return string
     */
    public function getFromToHtml(Zend_Date $date) {

        $hours = Mage::getModel('booking/checker_time')->getHoursRange($date, $this->getProduct());

        /*
         * Create hours titles block
         */
        $titles = $this->getHoursTitles();
        $out = '<table class="aw_booking_timetable">';
        $out .= '<thead><tr>';
        foreach($hours as $hour) {
            $out .= '<td colspan="2">'.$titles[$hour].'</td>';
        }
        $out .= '</tr></thead>';
        $out .= '<tbody><tr>';

        $max = count($hours) - 1;
        $i = 0;

        $To = clone $date;
        $From = clone $date;
        $From->setHour($hours[0])->setMinute(0)->setSecond(0);
        $To->setHour($hours[0])->setMinute(0)->setSecond(0);
        $To = $To->addMinute(30);

        foreach($hours as $hour) {

            $order_class = !$i ? "first" : ($i == $max ? "last" : "common");

            $out .= '<td class="'.$order_class.'-prim ';
            if(!Mage::getModel('booking/checker_bind')->isQtyAvailableForPeriod($this->getProduct(), $From, $To)) {
                $out .= "busy\">";
            }else {
                $out .= 'free">';
            }
            $out.'</td>';

            if($i) {
                $From = $From->addMinute(30);
                $To = $To->addMinute(30);
            }

            $out .= '<td class="'.$order_class.'-sec ';

            if(!Mage::getModel('booking/checker_bind')->isQtyAvailableForPeriod($this->getProduct(), $From, $To)) {
                $out .= "busy\">";
            }else {
                $out .= 'free">';
            }
            $out.'</td>';

            $From = $From->addMinute(30);
            $To = $To->addMinute(30);

            $i+=1;
        }

        $out .= '</tr></tbody></table>';
        return $out;
    }

 

    /**
     * Returns array with hours titles.
     * Used for formatting needs
     * @return array
     */
    public function getHoursTitles() {
        if (Mage::getSingleton('catalog/product_option_type_date')->is24hTimeFormat()) {
            // 24H format used 00-23
            return range(0,23);
        }else {
            // 12H format used
            return array(
                    '12 AM',
                    '1 AM',
                    '2 AM',
                    '3 AM',
                    '4 AM',
                    '5 AM',
                    '6 AM',
                    '7 AM',
                    '8 AM',
                    '9 AM',
                    '10 AM',
                    '11 AM',
                    '12 PM',
                    '1 PM',
                    '2 PM',
                    '3 PM',
                    '4 PM',
                    '5 PM',
                    '6 PM',
                    '7 PM',
                    '8 PM',
                    '9 PM',
                    '10 PM',
                    '11 PM'
            );
        }
    }
}

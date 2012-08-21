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

class AW_Booking_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer{

	/**
	 * Return booking options array
	 * @return array
	 */
	protected function _getBookingOptions(){
		$product = $this->getProduct();
        if(!is_object($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME))){
            $source = unserialize($product->getCustomOption('info_buyRequest')->getValue());
            $from_date = $source['aw_booking_from'];
            $to_date = $source['aw_booking_to'];

            $data = array(
                new Zend_Date($from_date, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)),
                new Zend_Date($to_date, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT))
            );
        }
        else{
            $data = array(
                //new Zend_Date($product->getCustomOption('aw_booking_from')->getValue()." ". $product->getCustomOption('aw_booking_time_from')->getValue(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_FULL)),
                new Zend_Date($product->getCustomOption('aw_booking_from')->getValue()." ". $product->getCustomOption('aw_booking_time_from')->getValue(), AW_Core_Model_Abstract::DB_DATETIME_FORMAT),
                //new Zend_Date($product->getCustomOption('aw_booking_to')->getValue()." ". $product->getCustomOption('aw_booking_time_to')->getValue(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_FULL))
                new Zend_Date($product->getCustomOption('aw_booking_to')->getValue()." ". $product->getCustomOption('aw_booking_time_to')->getValue(), AW_Core_Model_Abstract::DB_DATETIME_FORMAT),
            );
        }
		
		return array(
			array('label' => $this->__('From'), 'value' => $this->formatDate($data[0], 'short', $this->getProduct()->getAwBookingRangeType() != 'date_fromto')),
			array('label' => $this->__('To'), 'value' => $this->formatDate($data[1], 'short', $this->getProduct()->getAwBookingRangeType() != 'date_fromto'))
		);
	}

    /**
     * Return merged options array
     * This array consist of standard Magento options and booking
     * @return array
     */
    public function getOptionList(){
        return array_merge($this->_getBookingOptions(), parent::getOptionList());
    }
 
}

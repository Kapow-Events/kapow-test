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

class AW_Booking_Block_Adminhtml_Sales_Order_View_Items_Column_Name extends Mage_Adminhtml_Block_Sales_Items_Column_Name{
    public function getOrderOptions(){
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
        	  $startDateLabel = $this->getItem()->getIsVirtual() ? $this->__("Subscription start:") : $this->__("First delivery:");
 if(isset($options['info_buyRequest'])) {

		$reservationFrom = @$options['info_buyRequest']['aw_booking_from'];
		$reservationTo =  @$options['info_buyRequest']['aw_booking_to'];
		$reservationTimeFrom = @$options['info_buyRequest']['aw_booking_time_from'];
		$reservationTimeTo =  @$options['info_buyRequest']['aw_booking_time_to'];

		$periodStartDate = @$options['info_buyRequest']['aw_sarp_subscription_start'];
		if(($reservationFrom)) {
		    $From = new Zend_Date($reservationFrom, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
		    $To = new Zend_Date($reservationTo, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
		    $Product = Mage::getModel('catalog/product')->load($this->getItem()->getProductId());
		    $displayTime = $Product->getAwBookingRangeType() != AW_Booking_Model_Entity_Attribute_Source_Rangetype::DATE;

		    if($displayTime) {
			$timeFrom = (AW_Booking_Model_Product_Type_Bookable::convertTime($reservationTimeFrom, AW_Core_Model_Abstract::RETURN_ARRAY));
			$timeTo = (AW_Booking_Model_Product_Type_Bookable::convertTime($reservationTimeTo, AW_Core_Model_Abstract::RETURN_ARRAY));

			$From->setHour(@$timeFrom[0]);
			$From->setMinute(@$timeFrom[1]);
			$To->setHour(@$timeTo[0]);
			$To->setMinute(@$timeTo[1]);
		    }

		    $result[] = array(
			    'label' => $this->__('Reservation from:'),
			    'value'=> $this->formatDate($From, 'short', $displayTime)
		    );

		    $result[] = array(
			    'label' => $this->__('Reservation to:'),
			    'value' => $this->formatDate($To, 'short', $displayTime)

		    );
		}
	    }
			$result = array_merge($result, parent::getOrderOptions());
        }
        return $result;
    }
}

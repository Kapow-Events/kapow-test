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

class AW_Booking_Model_Checker_Time extends AW_Core_Object{
	
	/**
	 * Return unavail day. Only setup dates are used, no bind days
	 * @param object $product_id
	 * @return array
	 */
	public function getUnavailHours(Zend_Date $Date, $product_id, $store_id=0){
		$model = Mage::getModel('catalog/product');
		if($product_id instanceof $model){
			$Product = $product_id;
		}else{
			$Product = $model->load($product_id);
		}
		
		
		list($fh, $fm) = @explode(",", $product->getAwBookingTimeFrom());
		list($th, $tm) = @explode(",", $product->getAwBookingTimeTo());
		
		$hours = array();
		
		$From = clone $Date;
		$To = clone $Date;
		
		$From->setHour($fh)->setMinute($fm);
		$To->setHour($th)->setMinute($tm);

		$start_from_0 = $stop_at_23 = true; 

		if($Product->getAwBookingRangeType() == AW_Booking_Model_Entity_Attribute_Source_Rangetype::DATETIME){
			// Product is available from date and time
			if($Product->getTypeInstance()->isLastDay($Date)){
				// First day. Start hours should be equal to getAwBookingTimeFrom
				$stop_at_23 = false;
			}			
			if($Product->getTypeInstance()->isFirstDay($Date)){
				// First day. Start hours should be equal to getAwBookingTimeFrom
				$start_from_0 = false;
			}
			if($start_from_0){
				$From->setHour(0)->setMinute(0);
			}
			if($stop_at_23){
				$To->setHour(23)->setMinute(59);
			}			
		}elseif($Product->getAwBookingRangeType() == AW_Booking_Model_Entity_Attribute_Source_Rangetype::TIME){
			
		}else{
			return range(0,23);
		}
		
		while($From->compare($To, Zend_Date::HOUR)){
			$date = $From->toArray();
			$hours[] = (int)$date['hour'];
			$From = $From->addHour(1);
		}
		return $hours;
	}
	
	/**
	 * Return from-to range available for date
	 * @param Zend_Date $Date
	 * @param object    $product_id
	 * @param object    $store_id [optional]
	 * @return array ($from, $to)
	 */
	public function getHoursRange(Zend_Date $Date, $product_id, $store_id=0){
		$model = Mage::getModel('catalog/product');
		if($product_id instanceof $model){
			$Product = $product_id;
		}else{
			$Product = $model->load($product_id);
		}
		
		
		list($fh, $fm) = explode(",", $Product->getAwBookingTimeFrom());
		list($th, $tm) = explode(",", $Product->getAwBookingTimeTo());
		
		
		$hours = array();
		
		$From = clone $Date;
		$To = clone $Date;
		
		$From->setHour($fh)->setMinute($fm);
		$To->setHour($th)->setMinute($tm);

        if($From->compare($To) > 0){
            // Date "To" is less or equal to from
            list($From, $To) = array($To, $From);
        }                   

		$start_from_0 = $stop_at_23 = true; 

		if($Product->getAwBookingRangeType() == AW_Booking_Model_Entity_Attribute_Source_Rangetype::DATETIME){
			// Product is available from date and time
			if($Product->getTypeInstance()->isLastDay($Date)){
				// First day. Start hours should be equal to getAwBookingTimeFrom
				$stop_at_23 = false;
			}			
			if($Product->getTypeInstance()->isFirstDay($Date)){
				// First day. Start hours should be equal to getAwBookingTimeFrom
				$start_from_0 = false;
			}
			if($start_from_0){
				$From->setHour(0)->setMinute(0);
			}
			if($stop_at_23){
				$To->setHour(23)->setMinute(59);
			}			
		}elseif($Product->getAwBookingRangeType() == AW_Booking_Model_Entity_Attribute_Source_Rangetype::TIME){
			
		}else{
			return range(0,23);
		}

		while($From->compare($To, Zend_Date::HOUR) <= 0){
			$date = $From->toArray();
                $hours[] = (int)$date['hour'];
			$From = $From->addHour(1);
            if($From->compare($To, Zend_Date::DAY_SHORT) != 0) break;

            
		}

		
		return $hours;
	}
}
	
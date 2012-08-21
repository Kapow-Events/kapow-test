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

class AW_Booking_Model_Prices extends Mage_Core_Model_Abstract{
	
	const ONE_DAY	= 86400;
	const ONE_HOUR	= 3600;
	const ONE_MINUTE = 60;
	
	const NO_PRICE = -1;
	
	protected function _construct(){
		$this->_init('booking/prices');
	}
	
	
	
	
	
	/*  Here goes below deprecated methods */
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function _getPriceForDate($productId, $date, $basePrice, $sId=0){
		$data = $this->getResource()->getPriceForDate($productId, $date, $sId);
		
		if($data){
			if($data['is_progressive']){
				// we should calculate total days in period
				$tsdelta = self::strtotime($data['date_to']) -  self::strtotime($data['date_from']);
				$time = self::strtotime($date); 
				$delta_time = $time - self::strtotime($data['date_to']);
				$k = $delta_time/$tsdelta;
				
				$price = abs($k * ($data['price_to'] - $data['price_from']));
				$price += min($data['price_to'] , $data['price_from']);
				return $price;
			}else{
				return $data['price_from'];
			}
		}else{
			// No rules found
			return $basePrice;
		}
	}
	
	public function _getPriceForPeriod($entityId, $from, $to, $basePrice, $sId=0){
		list($from, $to) = self::strtotime($from, $to);
		
		
		$price = 0;
		while($from <= $to){
		
			$price += $this->getPriceForDate($entityId, date('Y-m-d', $from), $basePrice, $sId);
			
			$from += self::ONE_DAY;
		}
		return $price;
	}
	
	public function getPriceForDateTimePeriod($entityId, $from, $to, $basePrice, $sId=0){
		// generate days
		$price = 0;
		
		$dates = $this->calculateHours($from, $to);
		
		foreach($dates as $date => $hours){
			$price += $this->getPriceForDate($entityId, $date, $basePrice, $sId) * $hours;
		}
		return $price;
	}
	
	
	public function calculateHours($from, $to){
		$dates = array();
		
		list($from, $to) = self::strtotime($from, $to);
		
		$from_date = date('Y-m-d', $from);
		$to_date = date('Y-m-d', $to);
		
		
		
		list($hours1, $minutes1) = explode(':', date('H:i', $from));
		$timeFirstDay = self::ONE_DAY - $hours1 * self::ONE_HOUR - $minutes1 * self::ONE_MINUTE;
		$hoursFirstDay = ceil($timeFirstDay/self::ONE_HOUR);
		
		list($hours2, $minutes2) = explode(':', date('H:i', $to));
		$timeLastDay = $hours2 * self::ONE_HOUR + $minutes2 * self::ONE_MINUTE;
		$hoursLastDay = ceil($timeLastDay/self::ONE_HOUR);
		
		while($from <= $to){
			$date = date('Y-m-d', $from);
			
			$dates[$date] = 
							($date == $from_date ? $hoursFirstDay : 
								($date == $to_date ? $hoursLastDay : 24) 
							);
			$from += 		self::ONE_HOUR;		
						
		}

		return $dates;
	}
	
	/**
	 * Converts single dates, arrays and
	 *
	 * @param   int|string|array $date
	 * @return mixed timestamp
	 */
	public static function strtotime(){
		$date = func_get_args();
		if(func_num_args()==1 ){
			$date = $date[0];
		}
		if(is_numeric($date)){
			return $date;
		}elseif(is_array($date)){
			for($i=0; $i<count($date); $i++){
				$date[$i] = self::strtotime($date[$i]);
			}
		}else{
			$date = strtotime($date);
		}
		return $date;
	}
	
}

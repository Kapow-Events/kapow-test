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

class AW_Booking_Helper_Dates extends Mage_Core_Helper_Abstract{
	
	const ONE_DAY = 86400;
	
	public function getBindDetails($time, $product){
		// Returns details on date for products
		$out = array();
		
		// Returns binded days as array of ()
		$collection = Mage::getModel('booking/order')
				->getCollection()
				->addProductIdFilter( $product->getId() )
				->load();
		
		foreach($collection as $order){
			$range = $this->createDaysRange(
				$this->parseDate($order->getBindStart()),
				$this->parseDate($order->getBindEnd())
			);
			
			foreach($range as $ts){
				if($ts == $time){
					
					$realOrder = Mage::getModel('sales/order')->loadByIncrementId($order->getOrderId());
					
					if ($realOrder->getCustomerIsGuest()) {
						$customerName = $realOrder->getBillingAddress()->getName();
						$isGuest = true;
					} else {
						$isGuest = false;
						$customerName = $realOrder->getCustomerName();
					}
					
					
					
					$out[] = array(
						'createdTime'	=> $order->getCreatedTime(),
						'bindStart'		=> $order->getBindStart(),
						'bindEnd'		=> $order->getBindEnd(),
						'orderId'		=> $order->getOrderId(),
						'customerName'	=> $customerName,
						'isGuest'		=> $isGuest
					);
				}
			}
		}
		
		return $out;
		
	}
	
	/**
	* Returns binded days as array
	*
	* @param Mage_Catalog_Model_Product $product affected product
	* @return array binded dates as [timestamp] => quantity
	*/	
	public function getBindedDays(Mage_Catalog_Model_Product $product){
		
		$_ranges = array();
		$_bindedDays = array();
		
		$collection = Mage::getModel('booking/order')
				->getCollection()
				->addProductIdFilter( $product->getId() )
				->load();
		
		foreach($collection as $order){
			
			$range = $this->createDaysRange(
				$this->parseDate($order->getBindStart()),
				$this->parseDate($order->getBindEnd())
			);
			
			foreach($range as $ts){
				@$_bindedDays[$ts] += 1;
			}
		}
		return $_bindedDays;
	}
	

	/**
	* Get already binded dates
	*
	* @param Mage_Catalog_Model_Product $product affected product
	* @return array binded dates timestamps
	*/
	public function getUnavailDays(Mage_Catalog_Model_Product $product){
		
		$out = array();
		
		if($product->getAwBookingRangeType() == 'date_fromto'){
			$quantity = $product->getAwBookingQuantity();
			$bindings = $this->getBindedDays($product);
				
			foreach($bindings as $ts=>$lQ){
				if($lQ >= $quantity){
					$out[] = $ts;
				}
			}
		}
		
		/* We should also test for excluded days */
		
		// Get all "single days"
		$rules = Mage::getModel('booking/excludeddays')->getCollection()->addEntityIdFilter($product->getId());
		foreach($rules as $rule){
			$ts_from = strtotime($rule->getPeriodFrom());
			$ts_to = strtotime($rule->getPeriodTo());
			
			switch($rule->getPeriodType()){
				case 'single':
					$out[] = $ts_from;
				break;
				case 'recurrent_day':
					// Recurrent day of week
					for($_ts = strtotime($product->getAwBookingDateFrom()); $_ts<=strtotime($product->getAwBookingDateTo()); $_ts += self::ONE_DAY){
						if(date('N', $_ts) == date('N', $ts_from)){
							$out[] = $_ts;
						}
					}
				break;	
				case 'recurrent_date':
					// Recurrent date
					for($_ts = strtotime($product->getAwBookingDateFrom()); $_ts<=strtotime($product->getAwBookingDateTo()); $_ts += self::ONE_DAY){
						if(date('d', $_ts) == date('d', $ts_from)){
							$out[] = $_ts;
						}
					}
				break;	
				case 'period':
					// Period
					for($_ts = $ts_from; $_ts<=$ts_to; $_ts += self::ONE_DAY){
						$out[] = $_ts;
					}
				break;					
				case 'recurrent_period':
					// Recurring Period
					$type = $rule->getPeriodRecurrenceType();
					
					for($_ts = strtotime($product->getAwBookingDateFrom()); $_ts<=strtotime($product->getAwBookingDateTo()); $_ts += self::ONE_DAY){
						if($type == 'monthly'){
							// Monthly repeated date period
							$date_ts = date('d', $_ts);
							$date_from = date('d', $ts_from);
							$date_to = date('d', $ts_to);
							
							if(in_array($date_ts, range($date_from, $date_to))){
								$out[] = $_ts;
							}
						}else{
							// Yearly recurrent period
							list($y_ts, $m_ts, $d_ts) = explode('-',date('Y-m-d', $_ts));
							list($m_from, $d_from) = explode('-',date('m-d', $ts_from));
							list($m_to, $d_to) = explode('-',date('m-d', $ts_to));
							
							$time_from = mktime(0,0,1, $m_from, $d_from, $y_ts);
							$time_to = mktime(0,0,1, $m_to, $d_to, $y_ts);
							if($_ts >= $time_from && $_ts <= $time_to){
								$out[] = $_ts;
							}
						}
						
						
					}
					
					for($_ts = $ts_from; $_ts<=$ts_to; $_ts += self::ONE_DAY){
						$out[] = $_ts;
					}
				break;					
			}
		}
		
		return $out;
	}
	
	

	
		
	public function dateInRange($value, $range){
		/* Returns true if date is in range, false if no */
		$date = $this->parseDate($value);
		$seed = $date['d'] + $date['m']*100 + $date['y']*10000;
		$min = $range[0]['d'] + $range[0]['m']*100 + $range[0]['y']*10000;
		$max = $range[1]['d'] + $range[1]['m']*100 + $range[1]['y']*10000;
		return ($seed >= $min) && ($seed <= $max);
	}
	
	
	public function parseDate($str){
		/* Parses date to year, month and day */
		if(is_array($str)) return $str;
		$ts = is_numeric($str) ? $str : strtotime($str);

		return array(
			'm'	=> date("m",$ts),
			'd'	=> date("d",$ts),
			'y'	=> date("Y",$ts)
		);
	}
	
	public function getPriceForPeriod($interval, $product, $price=null){
		
		$price = is_null($price) ? $product->getData('price') : $price;
		
		$product = Mage::getModel('catalog/product')->load($product->getId());
		
		$oneItemQ = $product->getAwBookingQratio() ? $product->getAwBookingQratio() : 1;
		if(is_string($interval[0])){
			$interval[0] = $this->parseDate($interval[0]);
			$interval[1] = strlen($interval[1]) ? $this->parseDate($interval[1]) : $interval[0];
		}

		if(isset($interval[2])){
			// Time, if set
			$interval[2] = Mage::helper('booking/times')->parseTime($interval[2]);
			$interval[3] = Mage::helper('booking/times')->parseTime($interval[3]);
		}
		
		
		if($product->getAwBookingQratioMultiplier() != 'hours'){
			// Create ranges
			$ranges =$this->createDaysRange($interval[0], $interval[1]);

			return ceil( count($ranges)/ $oneItemQ ) * $price;
		}else{
			// Quantity is measured in hours
			
			$ranges =$this->createDaysRange($interval[0], $interval[1]);
			
			$hours = ceil( count($ranges)/ $oneItemQ ) * 24; // Total days.
			
			
			$hoursTo = ceil( ($interval[3]['h'] * 60  + $interval[3]['m'])/60 );
			$hoursFrom = ceil( ($interval[2]['h'] * 60  + $interval[2]['m'])/60 );
			
			if($interval[1] != $interval[0]){
				$hours = $hours + $hoursTo - $hoursFrom;
			}else{
				$hours = $hoursTo - $hoursFrom;
			}
			return $hours * $price;
		}
	}
	
	public function createDaysRange($date1, $date2){
		/* Creates array of days timestamps */
		
		$ts1 = $this->toTimestamp($date1);
		$ts2 = $this->toTimestamp($date2);
		
		
		$out = array($ts1);
		
		while($ts1 < $ts2){
			$ts1 += self::ONE_DAY;
			$out[] = $ts1;
		}
		return $out;
	}
	
	public function formatDate($date1){
		// Formats "date" in way it will be saved in db
		$ts1 = $this->toTimestamp($date1);
		return date('Y-m-d', $ts1);
	}
	public function format($str){
		$str = is_numeric($str) ? date('Y-m-d', $str) : $str;
		return $this->formatDate($this->parseDate($str));
	}
	public function toTimestamp($date, $range=0){
		if(is_string($date)){
			$date = $this->parseDate($date);
		}
		if(!$range)
			return mktime(0,0,0,$date['m'],$date['d'],$date['y']);
		else
			return array(
				mktime(0,0,0,$date['m'],$date['d'],$date['y']),
				mktime(23,59,59,$date['m'],$date['d'],$date['y'])
			);
	}
}

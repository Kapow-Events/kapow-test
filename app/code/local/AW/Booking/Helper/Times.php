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

class AW_Booking_Helper_Times extends Mage_Core_Helper_Abstract{
	
	
	public function parseTime($array=null){
		/* Parses time to hours and minutes in 24h(internal) format */
		if(!$array){
			$array = array(0,0);
		}elseif(is_string($array)){
			if(!strpos($array, ",")){
				$ts = strtotime($array);
				$array = array(intval(date('H', $ts)), intval(date('i', $ts)));
			}else{
				$tmp = explode(',', $array);
				$array = array($tmp[0], $tmp[1]);
			}
		}elseif(isset($array['h'])){
			return $array;
		}
		
		$hours = @intval($array[0]);
		$mins = @intval($array[1]);
		if(isset($array[2]) ){
			// Is in 12h format
			if($hours >= 12){
				$hours = 0;
			}
			if($array[2] == 'pm'){
				$hours += 12;
			}
		}

		return array(
			'h'	=> $hours,
			'm'	=> $mins
		);
	}
	
	public function formatTime($date1){
		// Formats "time" in way it will be saved in db
		return str_pad($date1['h'], 2, "0", STR_PAD_LEFT).":".str_pad($date1['m'], 2, "0", STR_PAD_LEFT).":00";
	}
	
	public function format($str){
		return $this->formatTime($this->parseTime($str));
	}
	
	public function createHoursRange($from, $to){
		
/*
		Creates range in hours from-to. One day only
*/
		$from = $this->parseTime($from);
		$to = $this->parseTime($to);
		
		$from = $from['h'];
		$to = $to['h'];
		
		if($from > $to){
			$tmp = $from;
			$from = $to;
			$to = $tmp;
		}elseif($from == $to){
			return array($from);
		}
		
		return range($from, $to, 1);
	}
	
	public function getBindedHours($product, $date){
/*
		Hours must be array (y,m,d)
*/
		$coll = Mage::getModel('booking/order')
				->getCollection()
				->addProductIdFilter( $product->getId() )
				->addBindDateFilter($date,1)
				->load();
				
		// Now we should calculate hours for specified day
		$_ranges = array();
		$_bindedHours = array();
		
		foreach($coll as $order){
			
			$_from = strtotime($order->getBindStart());
			$_to = strtotime($order->getBindEnd());
			
			$_date = Mage::helper('booking/dates')->toTimestamp($date, true);
			
			$end_time = $this->parseTime($order->getBindEnd());
			
			if(!$end_time['m'] ){
				$order->setBindEnd(
					date('Y-m-d H:i:s', 
						(strtotime($order->getBindEnd()) - 3600)
					)
				);
			}
			
			if($_date[1] <= $_to){
				$to = array('h'=>23, 'm'=>59);
			}else{
				$to = $this->parseTime($order->getBindEnd());
			}
			if($_date[0] >= $_from){
				$from = array('h'=>0, 'm'=>0);
			}else{
				$from = $this->parseTime($order->getBindStart());
			}
			
			$range = $this->createHoursRange(
				$from,
				$to
			);
			
			foreach($range as $hour){
				@$_bindedHours[$hour] += 1;
			}
		}
		
		return $_bindedHours;		
	}
}

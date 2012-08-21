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


class AW_Booking_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	const ONE_DAY = 86400;
	
	
	public function now(){
		return new Zend_Date();
	}
	
	
	public function intervalIntersects($int_1, $int2){
		return array_intersect($int_1, $int_2);
	}
	
	
	public function isAllowedInterval($product, AW_Booking_Model_Booking $booking){
		/* Retutns if interval is allowed */
		$quantity = $product->getAwBookingQuantity();
		$range_type = $product->getAwBookingRangeType();
		$prod_date_from = $product->getAwBookingDateFrom();
		$prod_date_to = $product->getAwBookingDateTo();
		
		if(
			$quantity >= 1 &&
			(
				$prod_date_from <= $booking->getRangeFrom() &&
				$prod_date_to >= $booking->getRangeTo()
			)
			
		){
			return true;
		}else{
			return false;
		}
	}
	
	public function getBookingQuantity($product, $range){
		list($s, $e) = $range;
		
		$coll = Mage::getModel('booking/booking')
			->getCollection()
			->addProductFilter($product->getId())
			->load();
		
		$quantity = 0;
		
		foreach($coll as $book){
			if($this->intervalIntersects($range, array($coll->getRangeFrom(), $coll->getRangeTo())))
				$quantity += 1;
		}	
		return  $product->getAwBookingQuantity() - $quantity;
	}
	
	public function getOrderBlockHtml($block){
		$layout = $block->getLayout();
		$new = $layout->createBlock('booking/order');
		return $new->toHtml();
	}
	
	
	public function formatCalendarDate($str){
		$time = strtotime($str);
		return date('m/Y', $time);
	}
	public function formatCalendarDay($str){
		$time = strtotime($str);
		return date('m/d/Y', $time);
	}
	
	public function refactorOptions($opts){
		/* Unsets booking options from options array */
		$out = array();
		foreach($opts as $opt){
			if(strpos($opt->getTitle(), 'aw_booking') !== 0){
				$out[] = $opt;
			}
		}
		return $out;
	}
	
	public function getBookingOptionsHtml($opts){
		/* Converts booking options to hidden fields with ids */
		$out = "";
		foreach($opts as $k=>$opt){
			if(strpos($opt->getTitle(), 'aw_booking') === 0){
				$out .= "<input type=\"hidden\" name=\"options[$k]\" id=\"id_{$opt->getTitle()}\"/>";
			}
		}
		return $out;
	}
	
	
	
	public function getOrdersRange($from, $to, $productId){
		return Mage::getModel('booking/booking')->getOrdersRange($from, $to, $productId);
	}
	

	
	public function getInOutRanges($productId){
		/* Creates booking ranges as array, e.g.:
		 * [timestamp_1] => 1
		 * [timestamp_2] => 3
		 * [timestamp_7] => 2
		 * */
		$out = array();
		foreach( $this->getOrders( $productId ) as $order ){
			foreach($order->getItemsCollection() as $item){
				$item2 = $item->getProductOptions();
				foreach( $this->getDaysInterval($item2['info_buyRequest']['in'], $item2['info_buyRequest']['out']) as $ts){
					@$out[$ts] = @intval(@$out[$ts]) + 1; 
				}
			}
		}
		return $out;
	}
	
	

	/**
	 * Parses date to year, month and day
	 * 
	 * @param object $str
	 * @return array
	 */
	public function parseDate($str){
		$ts = strtotime($str);

		return array(
			'month'	=> date("m",$ts),
			'day'	=> date("d",$ts),
			'year'	=> date("Y",$ts)
		);
	}

	/**
	 * Creates array of days
	 * 
	 * @param object $date1
	 * @param object $date2
	 * @return array
	 */
	public function createDaysRange($date1, $date2){
		/* Creates array of days */
		$out = array();
		$ts1 = mktime(0,0,0, $date1['month'], $date1['day'], $date1['year']);
		$ts2 = mktime(0,0,0, $date2['month'], $date2['day'], $date2['year']);
		
		while($ts1 <= $ts2){
			$out[] = $ts1;
			$ts1 += self::ONE_DAY;
		}
		return $out;
	}
	
	/**
	 * Returns timestamps range from date1 to date2
	 * @param object $date1
	 * @param object $date2
	 * @return array 
	 */
	public function getDaysInterval($date1, $date2){
		$date1 = $this->parseDate($date1);
		$date2 = $this->parseDate($date2);
		$range = $this->createDaysRange($date1, $date2);
		return $range;
	}
	
	public function getBookedDays($product){
		/* Returns already booked days as array */
		$out = array();
		$Q = $product->getAwBookingQuantity();
		$maxRange = $this->getDaysInterval(
			$product->getAwBookingDateFrom(),
			$product->getAwBookingDateTo()
		);
		
		$ranges = $this->getInOutRanges($product->getId());

		// Check foreach days
		foreach($maxRange as $TS){
			if(@$ranges[$TS] >= $Q){
				$out[] = $TS;
			}
		}
		return $out;
	}
	
	public function isIntervalFree($interval, $product, $qty=1){
		/* Checks if interval is free for product 
		 * If there are too much orders in specified period, return false
		 * 
		 * */
		$from = $interval[0];
		$to = $interval[1];
		
		$coll = Mage::getModel('booking/order')->getCollection()
			->addBindRangeFilter($from, $to, $product->getAwBookingRangeType() == 'date_fromto' )
			;//->load();
		$maxCount = $product->getAwBookingQuantity();
		
		if($maxCount < ($coll->count() + $qty) )
			return false;
		return true;
	}

    /**
     * Return all binds that are actual for date
     * @param  $y
     * @param  $m
     * @param  $d
     * @param  $product
     * @param bool $includeCanceled
     * @return array
     */
	public function getBindDetails($y, $m, $d, $product, $includeCanceled = false){
		// Returns details on date for products
		$out = array();
		
		$m = str_pad($m, 2, "0", STR_PAD_LEFT);
		$d = str_pad($d, 2, "0", STR_PAD_LEFT);
		
		$date = "$y-$m-$d";
		$from = $date." 23:59:59";
		$to = $date." 00:00:00";
		
		// Returns binded days as array of ()
		$collection = Mage::getModel('booking/order')
				->getCollection()
				->addProductIdFilter($product->getId())
				->addBindInrangeFilter($from, $to)
				->groupByOrderId();
		if($includeCanceled){
			$collection->dropCanceledFilter();
		}
		
		$collection		
				->load();
		
		
		foreach($collection as $order){
			$range = $this->createDaysRange(
				$this->parseDate($order->getBindStart()),
				$this->parseDate($order->getBindEnd())
			);
				
			$realOrder = Mage::getModel('sales/order')->loadByIncrementId($order->getOrderId());
			
			if ($realOrder->getCustomerIsGuest()) {
				$customerName = $realOrder->getBillingAddress()->getName();
				$isGuest = true;
				$customerId = -1;
				$customerHref = '';
			} else {
				$isGuest = false;
				$customerId = $realOrder->getCustomerId();
				$customerName = $realOrder->getCustomerName();
				$customerHref = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/customer/edit', array('id'=>$customerId));
			}
			
			$ORD = Mage::getModel('sales/order')->loadByIncrementId($order->getOrderId());
			
			$orderHref = '<a href="'.Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/sales_order/view', array('order_id'=>$ORD->getId())).'">'.$order->getOrderId().'</a>';
			
			$out[] = array(
				'createdTime'	=> 
				Mage::helper('core')->formatDate($order->getCreatedTime(), 'short'),
				
				
				'bindStart'		=> 
				Mage::helper('core')->formatDate(new Zend_Date($order->getBindStart()), 'short', $order->getProduct()->getAwBookingRangeType() != 'date_fromto'),
				'bindEnd'		=> 
				Mage::helper('core')->formatDate(new Zend_Date($order->getBindEnd()), 'short', $order->getProduct()->getAwBookingRangeType() != 'date_fromto'),
				'orderId'		=> $order->getOrderId(),
				'orderHref'		=> $orderHref,
				'customerName'	=> $customerName,
				'customerHref'	=> $customerHref,
                'customerEmail' => $realOrder->getCustomerEmail(),
                'customerPhone' => $realOrder->getBillingAddress()->getTelephone(),            
				'customerId'	=> $customerId,
				'isGuest'		=> $isGuest,
				'totalItems'	=> $order->getTotalItems(),
				'isCanceled'	=> $order->getIsCanceled()
			);
			
			
		}
		return $out;
	}
	
	
	public static function strToDate($d){
		$date = new Zend_Date($d, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
		return $date;
	}
	
	public static function toTimestamp($d=null){
		if($d){
			$date = self::strToDate($d);
			return $date->getTimestamp();
		}else{
			return 0;
		}
	}
	
	public static function getDayOfWeek(Zend_Date $d){
		$date = $d->toArray();
		return $date['weekday'];
	}
	
}


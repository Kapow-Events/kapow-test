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

class AW_Booking_Helper_Yui extends Mage_Core_Helper_Abstract{

/*
 * Helper for YUI calendar.
 * 
 * */

	const DAY_FORMAT = 'n/j/Y';
	const MONTH_FORMAT = 'n/Y';
	

	public function formatDate($str=null){
        $time = $str ? strtotime($str) : time();
		return date(self::MONTH_FORMAT, $time);
	}

	public function formatDay($str=null){
		$toReturn = '';
        if ($str) $toReturn = date(self::DAY_FORMAT, strtotime($str));
        return $toReturn;
	}
	
	
	public function formatDayArray($range){
		// Formats array of timestamps to YUI calendar
		$out = array();
		foreach($range as $item){
			$out[] = date(self::DAY_FORMAT, $item);
		}
		return implode( ",", $out );
	}
	
	

}

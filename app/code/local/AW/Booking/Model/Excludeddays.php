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

class AW_Booking_Model_Excludeddays extends Mage_Core_Model_Abstract{

    /** Single day */
	const TYPE_SINGLE 			= 'single';
	/** Recurrent day of week, 0-7 */
    const TYPE_RECURRENT_DAY 	= 'recurrent_day';
	/** Recurring date of month, e.g. 31 */
    const TYPE_RECURRENT_DATE 	= 'recurrent_date';
	/** Single period */
    const TYPE_PERIOD 			= 'period';
	/** Recurrent period */
    const TYPE_RECURRENT_PERIOD = 'recurrent_period';
	
	protected function _construct(){
		$this->_init('booking/excludeddays');
	}
	
	/**
	 * Converts  to array
	 * @return array 
	 */
	public function toArray(array $arrAttributes = array()){
		
		$From = new Zend_Date($this->getPeriodFrom(), AW_Core_Model_Abstract::DB_DATE_FORMAT);
		$To = new Zend_Date($this->getPeriodTo(), AW_Core_Model_Abstract::DB_DATE_FORMAT);
		switch($this->getType()){
			case self::TYPE_SINGLE:
				if($this->getOutputFormat()){
					$From = $From->toString($this->getOutputFormat());
				}
				$out = $From;
			break;	
			case self::TYPE_RECURRENT_DAY:
				$arr = $From->toArray();
				$weekday = $arr['weekday'] == 7 ? 0 : (int)$arr['weekday'] ;
				$out = $weekday;
			break;
			case self::TYPE_RECURRENT_DATE:
				$arr = $From->toArray();
				$day = $arr['day'];
				$out = (int)$day;
			break;
			case self::TYPE_PERIOD:
				if($this->getOutputFormat()){
					$From = $From->toString($this->getOutputFormat());
					$To = $To->toString($this->getOutputFormat());
				}
				$out =(array('from' => $From, 'to' => $To));
			break;
			case self::TYPE_RECURRENT_PERIOD:
				if($this->getOutputFormat()){
					$From = $From->toString($this->getOutputFormat());
					$To = $To->toString($this->getOutputFormat());
				}
				$out = (array('from' => $From, 'to' => $To, 'period' => $this->getPeriodRecurrenceType()));
			break;	
		}
		return $out;
	}
	
	/**
	 * Checks if date is available
	 * @param Zend_Date $Date
	 * @return boolean
	 */
	public function isDateAvail(Zend_Date $Date){
		$From = new Zend_Date($this->getPeriodFrom(), AW_Core_Model_Abstract::DB_DATE_FORMAT);
		$To = new Zend_Date($this->getPeriodTo(), AW_Core_Model_Abstract::DB_DATE_FORMAT);
		
		if($this->getType() == self::TYPE_SINGLE){
			return $Date->compare($From, Zend_Date::DATE_SHORT) != 0;
		}
		if($this->getType() == self::TYPE_RECURRENT_DAY){
			return $Date->compare($From, Zend_Date::WEEKDAY) != 0;
		}
		if($this->getType() == self::TYPE_RECURRENT_DATE){
			return $Date->compare($From, Zend_Date::DAY) != 0;
		}
		if($this->getType() == self::TYPE_PERIOD){
			return !(($Date->compare($From, Zend_Date::DATE_SHORT) >=0 ) && ($Date->compare($To, Zend_Date::DATE_SHORT) <=0 ));
		}
		if($this->getType() == self::TYPE_RECURRENT_PERIOD){
			$DateFrom = clone $Date;
			$DateTo = clone $Date;

			switch($this->getPeriodRecurrenceType()){
				case 'monthly':
					$DateFrom->setDay($From->getDay());
					$DateTo->setDay($To->getDay());
					return !(($Date->compare($DateFrom, Zend_Date::DATE_SHORT) >=0 ) && ($Date->compare($DateTo, Zend_Date::DATE_SHORT) <=0 ));
				break;
				case 'yearly':
					$DateFrom->setMonth($From->getMonth())->setDay($From->getDay());
					$DateTo->setMonth($To->getMonth())->setDay($To->getDay());
					return !(($Date->compare($DateFrom, Zend_Date::DATE_SHORT) >=0 ) && ($Date->compare($DateTo, Zend_Date::DATE_SHORT) <=0 ));
				break;
			}
			throw new AW_Core_Exception("Unsupported recurrence interval '{$this->getPeriodRecurrenceType()}'");
		}		
	}
	
	/**
	 * Wrapper for getPeriodType()
	 * @return string
	 */
	public function getType(){
		return $this->getPeriodType();
	}
	
	/**
	 * Sets output format. If no format specified, Zend_Date objects are returned
	 * @return string
	 */
	public function getOutputFormat(){
		if(!$this->getData('output_format')){
			return false;
		}
		return $this->getData('output_format');
	}
}

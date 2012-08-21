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

class AW_Booking_Model_Checker extends AW_Core_Object{

    /** How many seconds are there in a day */
	const ONE_DAY	= 86400;
	/** How many seconds are there in a hour */
    const ONE_HOUR	= 3600;
	/** How many seconds are there in a minute */
    const ONE_MINUTE = 60;

    /** Service flag. Indicates that no price fetched. */
	const NO_PRICE = -1;

    protected $_checker;

	public function _construct(){
		Zend_Date::setOptions(array('extend_month' => true));
		return parent::_construct();
	}

    /**
     * Check if product assigned
     * @throws AW_Core_Exception
     * @return void
     */
	protected function _checkProduct(){
		if(!$this->getProduct()){
			throw new AW_Core_Exception("Can't calculate for unsufficient product");
		}
	}

    /**
     * Returns price for date
     * @param Zend_Date $Date
     * @param int $store_id
     * @return float
     */
	public function getPriceForDate(Zend_Date $Date, $store_id=0){
		$this->_checkProduct();
		return $this->getPriceChecker()->getPriceForDate($this->getProduct()->getId(), $Date, $store_id);
	}

    /**
     * Returns price for period
     * @param Zend_Date $From
     * @param Zend_Date $To
     * @param float $basePrice
     * @param int $store_id
     * @param int $failOnNoMultiplier
     * @return float
     */
	public function getPriceForPeriod(Zend_Date $From, Zend_Date $To, $basePrice, $store_id=0, $failOnNoMultiplier = 0){
		$this->_checkProduct();
		return $this->getPriceChecker()->getPriceForPeriod($this->getProduct(), $From, $To, $basePrice, $store_id, $failOnNoMultiplier);
	}

    /**
     * Checks if date available for reservation
     * @param Zend_Date $Date
     * @param int $store_id
     * @return boolean
     */
	public function isDateAvail(Zend_Date $Date, $store_id=0){
		$this->_checkProduct();
		return $this->getDateChecker()->isDateAvail($this->getProduct(), $Date, $store_id=0);
	}
	
	/**
	 * Price checker object
	 * @return AW_Booking_Checker_Price
	 */
	public function getPriceChecker(){
		if(!$this->getData('price_checker')){
			$this->setData('price_checker', Mage::getSingleton('booking/checker_price'));
		}
		return $this->getData('price_checker');
	}
	
	/**
	 * Date checker object
	 * @return AW_Booking_Checker_Date
	 */
	public function getDateChecker(){
        if(!Mage::registry('checker'))
            Mage::register('checker', Mage::getSingleton('booking/checker_date'));
        return Mage::registry('checker');
        /*
        if(!$this->getData('date_checker')){
            $this->setData('date_checker', Mage::getSingleton('booking/checker_date'));
        }
		return $this->getData('date_checker');
        */
	}		
	/**
	 * Time checker object
	 * @return AW_Booking_Checker_Time
	 */
	public function getTimeChecker(){
		if(!$this->getData('time_checker')){
			$this->setData('time_checker', Mage::getSingleton('booking/checker_time'));
		}
		return $this->getData('time_checker');
	}		
}
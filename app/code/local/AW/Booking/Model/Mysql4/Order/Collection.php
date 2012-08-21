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

class AW_Booking_Model_Mysql4_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	/**
	 * Indicate if grouped result is used or not
	 * @var mixed
	 */
	protected $_isGrouped = false;

	/**
	 * Indicate that quote filter is already applied
	 * @var boolean
	 */
	protected $_quoteFilterApplied = false;

	protected $_includeCanceled = 0;
	
	public function _construct(){
		parent::_construct();
		$this->_init('booking/order');
	}
	
	/**
	 * Adds bind type filter - order or cart. Default is order
	 * @param <type> $type 
	 */
	public function addBindTypeFilter($type){
	    if(($type == AW_Booking_Model_Order::BIND_TYPE_ORDER) || !$type){
		$this->getSelect()->where("bind_type='".AW_Booking_Model_Order::BIND_TYPE_ORDER."' OR bind_type=''");
	    }else{
		$this->getSelect()->where("bind_type='$type'");
	    }
	    return $this;
	}

	/**
	 * Adds filter by quote id
	 * @param   int $id quote_it
	 * @return  AW_Booking_Model_Mysql4_Order_Collection
	 */
	public function addQuoteIdFilter($id){
		$this->getSelect()->where('quote_id='.intval($id).' or !quote_id');
		$this->_quoteFilterApplied = true;
		return $this;
	}


	/**
	 * Adds filter by quoteItem id
	 * @param   int $id quote_it
	 * @return  AW_Booking_Model_Mysql4_Order_Collection
	 */
	public function addQuoteItemIdFilter($id){
		$this->getSelect()->where("order_id=?",intval($id));
		$this->_quoteFilterApplied = true;
		$this->addBindTypeFilter(AW_Booking_Model_Order::BIND_TYPE_CART);
		return $this;
	}


	/**
	 * Adds filter by product id
	 * @param	int $id product id
	 * @return  AW_Booking_Model_Mysql4_Order_Collection
	 */		
	public function addProductIdFilter($id){
		$this->getSelect()->where('product_id=?', $id);
		return $this;
	}
	
	public function addDateFilter(Zend_Date $From, $To=null){
		if(is_null($To)){
			$To = clone $From;
			$From->setHour(0)->setMinute(0)->setSecond(0);
			$To->setHour(23)->setMinute(59)->setSecond(59);
		}
		return $this->addTimeRangeFilter($From->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT), $To->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT));
	}	
	
		
	
	public function addBindDateTimeFilter(Zend_Date $Date){
		$this->getSelect()->where(
			"bind_start<='".$Date->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT)."' AND bind_end>'".$Date->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT)."'"
		);
		return $this;
	}
	
	
	public function addBindDateFilter(Zend_Date $From, $To=null, $unstrict=null){
		if(is_null($To)){
			$To = clone $From;
			$From->setHour(0)->setMinute(0)->setSecond(0);
			$To->setHour(23)->setMinute(59)->setSecond(59);
		}

		$from = $From->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
		$to =  $To->toString(AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
		$this
			->getSelect()
			->where("(
				(bind_start>='$from' AND bind_end<='$to')
				OR (bind_start>='$from' AND bind_start<='$to')
				OR (bind_end>='$from' AND bind_end<='$to')
				OR (bind_end>='$to' AND bind_start<='$from')
			)
			"
			);
		return $this;

		
	}
	
	public function addBindedTimeFilter($date, $time_from, $time_to){
		$this
			->getSelect()
			->where("bind_start<=?", $date." ".$time_from)
			->where("bind_end>?", $date);
		return $this;
	}



	public function addBindedDateFilter($from, $from_time="00:00:00", $to, $to_time="23:59:59", $includeStart = true){
		if(!$from_time){
			$from_time="00:00:00";
		}
		if(!$to_time){
			$to_time="23:59:59";
		}
		
		$CL = $includeStart ? "<=" : "<";
		$CR = $includeStart ? ">=" : ">";
		
		$from = $from ." $from_time";
		$to = $to ." $to_time";
		$this
			->getSelect()
			->where("(
				(bind_start>='$from' AND bind_end<='$to')
				OR (bind_start>='$from' AND bind_start$CL'$to')
				OR (bind_end$CR'$from' AND bind_end<='$to')
				OR (bind_end>='$to' AND bind_start<='$from')
			)
			"
			);
		
		return $this;
	}	
	
	
	public function addTimeRangeFilter($from, $to){
		$this->getSelect()->where('created_time>=?', $from);
		$this->getSelect()->where('created_time<=?', $to);
		return $this;
	}

	
	public function addBindRangeFilter($from, $to, $unstrict = null){
		$symbols = !$unstrict ? array('<','>') : array('<=','>=');
		
		$this->getSelect()->where('bind_end<?', $to);
		$this->getSelect()->where('bind_start'.$symbols[1].'?', $from);
		return $this;
	}
	
	/**
	 * Filters order records by range of to datetime strings
	 * @param	str $from
	 * @param	str $to
	 * @return  AW_Booking_Model_Mysql4_Order_Collection
	 */		
	public function addBindInrangeFilter($from, $to){
		$this->getSelect()->where("bind_start<='$from' and bind_end>='$to'");
		return $this;
	}

	/**
	 * Adds filter by order
	 *
	 * @param   int $id Order id(this is Magento "Incremental id")
	 * @return  AW_Booking_Model_Mysql4_Order_Collection
	 */			
	public function addOrderIdFilter($id){
		$this->getSelect()->where('order_id=?', $id);
		return $this;
	}
	
	/**
	 * Groups according to product_id 
	 *
	 * @return  AW_Booking_Model_Mysql4_Order_Collection
	 */		
	public function groupByProductId(){
		$this
			->getSelect()
			->from(null, 'MAX(created_time) AS last_order')
			->group('product_id');
		$this->_isGrouped = 'product_id';
		return $this;
	}

	/**
	 * Groups items by order id and counts quantity of item in order
	 *
	 * @return  AW_Booking_Model_Mysql4_Order_Collection
	 */		
	public function groupByOrderId(){
		$this->getSelect()
				->from(null, 'COUNT(id) as total_items')
				->group('order_id');

		return $this;
	}	
	
    /**
     * Load data
     *
     * @return  Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false){
		$this->_beforeLoad();
		return  parent::load($printQuery, $logQuery);
    }

	/**
	 * Drops default not canceled filter
	 *
	 * @return  AW_Booking_Model_Mysql4_Order_Collection
	 */	
	public function dropCanceledFilter(){
		$this->_includeCanceled = true;
		return $this;
	}

	/**
	 * Runs before collection load
	 *
	 * @return  AW_Booking_Model_Mysql4_Order_Collection
	 */		
	protected function _beforeLoad(){
		if(!$this->_includeCanceled){
			$this->getSelect()->where('is_canceled=0');
		}
		// If no quote filter applied include only really ordered items
		if(!$this->_quoteFilterApplied){
		    $this->getSelect()->where('quote_id=0');
		}
		return $this;
	}
	    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql(){
	if(!$this->_isGrouped){
	    return parent::getSelectCountSql();
	}
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        return $countSelect->reset()->from($this->getSelect(), array())->columns('COUNT(*)');
    }

}

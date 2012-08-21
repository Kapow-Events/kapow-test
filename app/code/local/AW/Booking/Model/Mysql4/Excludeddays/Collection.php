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

class AW_Booking_Model_Mysql4_Excludeddays_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	
	protected $_storeId = null;
	
	public function _construct(){
		parent::_construct();
		$this->_init('booking/excludeddays');
		if(!Mage::app()->isSingleStoreMode() && Mage::app()->getStore()->getId()){
			
			$this->setStoreId(Mage::app()->getStore()->getId());
		}
	}

	/**
	* Adds filter by entity_id (product_id)
	*
	* @param int $id entity id
	* @return AW_Booking_Model_Mysql4_Excludeddays_Collection
	*/	
	public function addEntityIdFilter($id){
		$this->getSelect()
			->where('entity_id=?', $id);
		return $this;	
	}
	
	/**
	* Adds filter by store_id 
	*
	* @param int $id store id
	* @return AW_Booking_Model_Mysql4_Excludeddays_Collection
	*/		
	public function addStoreIdFilter($id){
		$this->getSelect()
			->where('store_id='.$id." OR store_id=0");
			
		return $this;	
	}	
	
	public function load($printQuery = false, $logQuery = false){
		$this->_beforeLoad();
		return  parent::load($printQuery, $logQuery);
    }	
    
    protected function _beforeLoad(){
		if(($this->_storeId)){
			$this->addStoreIdFilter($this->_storeId);
		}
		return $this;
	}
	
	/**
	* Adds filter by store_id
	*
	* @param int $id store id
	* @return AW_Booking_Model_Mysql4_Excludeddays_Collection
	*/		
	public function setStoreId($id){
		$this->_storeId = $id;
		return $this;
	}
}

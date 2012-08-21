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

class AW_Booking_Model_Mysql4_Prices extends Mage_Core_Model_Mysql4_Abstract{
   	protected function _construct(){
		$this->_init('booking/price', 'id');
	}
		
	public function deleteByEntityId($id, $storeId = null){
		$condition =   'entity_id='.intval($id);
		if(!is_null($storeId)){
			$condition .= ' AND store_id='.intval($storeId);
		}
		$this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
		 
		 return $this;
	}
	public function getPriceForDate($entityId, $date, $sId=0){
		$read = $this->_getReadAdapter();
		$select = $read->select()
			->from($this->getTable('booking/price'), array('date_from', 'date_to', 'price_from', 'price_to', 'is_progressive'))
			->where('date_from<=\''.$this->formatDate($date, false).'\' AND date_to>=\''.$this->formatDate($date, false).'\'')
			->where('store_id='.$sId.' OR store_id=0')
			->where('entity_id=?', $entityId);
		return $read->fetchRow($select);
	}
	
}

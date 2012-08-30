<?php
/**
 * Advance Testimonial extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Advance Testimonial
 * @author     Kamran Rafiq Malik <support@fmeextensions.com>
 *             Asif Hussain <support@fmeextensions.com>
 * 	      
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Model_Mysql4_Testimonial extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the testimonial_id refers to the key field in your database table.
        $this->_init('testimonial/testimonial', 'testimonial_id');
    }
	
	 protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
    	
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('testimonial_store'))
            ->where('testimonial_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
        
    }
	
	/**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
		
        $condition = $this->_getWriteAdapter()->quoteInto('testimonial_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('testimonial_store'), $condition);
    
        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['testimonial_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('testimonial_store'), $storeArray);
        }
    
        return parent::_afterSave($object);
        
    }
}
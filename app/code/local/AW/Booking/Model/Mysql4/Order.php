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

class AW_Booking_Model_Mysql4_Order extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct() {
        $this->_init('booking/order', 'id');
    }

    /**
     * Cancel bind by id
     * @param int $id
     * @return AW_Booking_Model_Mysql4_Order
     */
    public function cancelByOrderId($id) {
        $this->_getWriteAdapter()->update($this->getMainTable(), array('is_canceled' => 1),'order_id='.($id));
        return $this;
    }


    /**
     * Cancel bind by order item id
     * @param int $id order item id
     * @param int $qty number of items to cancel 
     * @return AW_Booking_Model_Mysql4_Order
     */
    public function cancelByOrderItemId($id, $qty=0) {
        $Adapter = $this->_getWriteAdapter();

        $sql = "UPDATE "
             . $Adapter->quoteIdentifier($this->getMainTable(), true)
             . ' SET is_canceled=1'
             . 'WHERE `order_item_id`='.$id
             . ($qty ? "LIMIT $qty" : "");

        $Adapter->query($sql);
        
        return $this;
    }

    /**
     * Delete by quote item
     * @param Mage_Sales_Model_Quote_Item $QuoteItem
     * @return AW_Booking_Model_Mysql4_Order
     */
    public function deleteByQuoteItem(Mage_Sales_Model_Quote_Item $QuoteItem) {
        $condition =   "bind_type='".AW_Booking_Model_Order::BIND_TYPE_CART."' AND order_id=".intval($QuoteItem->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

    /**
     * Delete by quote item id
     * @param int $QuoteItem
     * @return AW_Booking_Model_Mysql4_Order
     */
    public function deleteByQuoteItemId($id) {
        $condition =   "bind_type='".AW_Booking_Model_Order::BIND_TYPE_CART."' AND order_id=".intval($id);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

    /**
     * Delete by quote id
     * @param int $QuoteItem
     * @return AW_Booking_Model_Mysql4_Order
     */
    public function deleteByQuoteId($id) {
        $condition =   "bind_type='".AW_Booking_Model_Order::BIND_TYPE_CART."' AND quote_id=".intval($id);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }


    /**
     * Updates product title for product id
     * @param int $productId
     * @param string $title
     * @param int $storeId
     */
    public function setProductNameById($productId, $title, $storeId=null) {
        $db = $this->_getWriteAdapter();

        $prop = array(
            'product_name' => $title
        );

        if(!is_null($storeId)) {
            $db->update($this->getMainTable(), $prop, $db->quoteInto('product_id=?', $productId));
        }
        else {
            $db->update($this->getMainTable(), $prop, $db->quoteInto('product_id=?', $productId));
        }
    }

}

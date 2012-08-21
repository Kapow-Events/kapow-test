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

class AW_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Information_Orders extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
        parent::__construct();
        $this->setId('ordersGrid');
        //$this->setDefaultSort('question_date');
        //$this->setDefaultDir('DESC');
        //$this->setSaveParametersInSession(true);
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $productId = $this->getRequest()->getParam('id');
        $collection = Mage::getResourceModel('sales/order_collection')
        //->addAttributeToFilter('store_id', 2)
        ->joinAttribute('billing_firstname', 'order_address/firstname', 'billing_address_id', null, 'left')
        ->joinAttribute('billing_lastname', 'order_address/lastname', 'billing_address_id', null, 'left')
        ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
        ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')

        ->addAttributeToFilter('entity_id', Mage::helper('booking')->getOrderIds($productId))
        ->addExpressionAttributeToSelect('billing_name',
            'CONCAT({{billing_firstname}}, " ", {{billing_lastname}})',
            array('billing_firstname', 'billing_lastname')
        )
        ->addExpressionAttributeToSelect('shipping_name',
            'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
            array('shipping_firstname', 'shipping_lastname'));

        $coll = Mage::getResourceModel('sales/order_collection');

        /*foreach($collection as $order){
              if( !Mage::helper('booking')->containsProduct($order, $productId) ){
                  $collection->

              }
          }*/


        $this->setCollection($collection);
        parent::_prepareCollection();


        return $this;
    }

    protected function _prepareColumns() {
        /*$this->addColumn('question_id', array(
            'header'    => Mage::helper('productquestions')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'question_id',
        ));*/
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);


        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('booking')->__('Order #'),
            'align' => 'right',
            'index' => 'increment_id',
            'filter' => false
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('booking')->__('Customer'),
            'align' => 'left',
            'index' => 'billing_name',
            'filter' => false
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('booking')->__('Purchased On'),
            'align' => 'right',
            'index' => 'created_at',
            'filter' => false
        ));


        return parent::_prepareColumns();
    }


    public function getRowUrl($row) {

        return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
    }

}

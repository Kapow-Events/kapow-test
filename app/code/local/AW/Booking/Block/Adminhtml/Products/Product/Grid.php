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

class AW_Booking_Block_Adminhtml_Products_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('productsGrid');
      $this->setDefaultSort('created_time');
      $this->setDefaultDir('DESC');
      //$this->setSaveParametersInSession(true);
  }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
		$this->_collection = Mage::getModel('booking/order')->getCollection()->addProductIdFilter($this->getRequest()->getParam('id'));
		$this->setCollection(
			$this->_collection
		);
		
		
		
		$date = new Zend_Date();
		// Calculate offset in hours for admin
		$offset = (Mage::app()->getLocale()->storeDate()->get(Zend_Date::TIMEZONE_SECS)); 

		$this->_collection->getSelect()->columns("FROM_UNIXTIME(UNIX_TIMESTAMP(bind_start)-$offset) as bind_start_ftd");
		$this->_collection->getSelect()->columns("FROM_UNIXTIME(UNIX_TIMESTAMP(bind_end)-$offset) as bind_end_ftd");
		//echo $this->_collection->getSelect()->assemble();die();
		parent::_prepareCollection();
		return $this;
		
    }

  protected function _prepareColumns()
  {

      $this->addColumn('order_id', array(
          'header'    => Mage::helper('booking')->__('Order'),
          'align'     =>'right',
		  'width'	=> '100px',
          'index'     => 'order_id'
      ));
	  
	 /* $this->addColumn('product_name', array(
          'header'    => Mage::helper('booking')->__('Name'),
          'align'     =>'left',
          'index'     => 'product_name',
      ));
	 */
	  $this->addColumn('created_time', array(
			'type'		=> 'datetime',
          'header'    => Mage::helper('booking')->__('Order date'),
          'align'     =>'left',
          'index'     => 'created_time',
		  'width'		=> '200px'
      ));	 
	  $this->addColumn('bind_start', array(
			'type'		=> 'datetime',
          'header'    => Mage::helper('booking')->__('Reservation from'),
          'align'     =>'left',
          'index'     => 'bind_start_ftd',
		  'width'		=> '200px'
      ));	 
	  $this->addColumn('bind_end', array(
			'type'		=> 'datetime',
          'header'    => Mage::helper('booking')->__('Reservation to'),
          'align'     =>'left',
          'index'     => 'bind_end_ftd',
		  'width'		=> '200px'
      ));	
	  	  
	/*  $this->addColumn('action',
			array(
				'header'    =>  Mage::helper('helpdeskultimate')->__('View'),
				'width'     => '200px',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(

					array(
						'caption'   => Mage::helper('helpdeskultimate')->__('Product Details'),
						'url'       => array('base'=> 'adminhtml/catalog_product/edit'),
						'field'     => 'product_id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
		));   
	   */
     $this->addExportType('*/*/exportCsv', Mage::helper('booking')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('booking')->__('XML'));
	  
      return parent::_prepareColumns();
  }

   
  public function getRowUrl($row)
  {
	  $orderId = Mage::getModel('sales/order')->loadByIncrementId($row->getOrderId())->getId();
      return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $orderId) );
  }

}

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

class AW_Booking_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
	parent::__construct();
	$this->setId('productsGrid');
	$this->setDefaultSort('created_time');
	$this->setDefaultDir('DESC');
	$this->setSaveParametersInSession(true);
    }

    protected function _getStore() {
	$storeId = (int) $this->getRequest()->getParam('store', 0);
	return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
	$coll = Mage::getModel('booking/order')->getCollection();
	
	$this->setCollection(
		$coll->groupByProductId()
	);
	parent::_prepareCollection();
	return $this;

    }



    protected function _prepareColumns() {
	$this->addColumn('product_id', array(
		'header'    => Mage::helper('booking')->__('Id'),
		'align'     =>'right',
		'width'	=> '100px',
		'index'     => 'product_id'
	));
	$this->addColumn('product_name', array(
		'header'    => Mage::helper('booking')->__('Name'),
		'align'     =>'left',
		'index'     => 'product_name',
	));
	$this->addColumn('last_order', array(
		'type'		=> 'datetime',
		'header'    => Mage::helper('booking')->__('Last order'),
		'align'     =>'left',
		'index'     => 'last_order',
		'width'		=> '200px',
        'filter_condition_callback' => array($this, 'filterLastOrderCallback')
	));

	$this->addColumn('action',
		array(
		'header'    =>  Mage::helper('booking')->__('View'),
		'width'     => '200px',
		'type'      => 'action',
		'getter'    => 'getProductId',
		'actions'   => array(

			array(
				'caption'   => Mage::helper('booking')->__('Product Details'),
				'url'       => array('base'=> 'adminhtml/catalog_product/edit'),
				'field'     => 'id'
			)
		),
		'filter'    => false,
		'sortable'  => false,
		'index'     => 'stores',
		'is_system' => true,
	));



	return parent::_prepareColumns();
    }


    public function getRowUrl($row) {
	return $this->getUrl('*/*/product', array('id' => $row->getProductId(), 'tab' => 'product_info_tabs_items') );
    }

    protected function filterLastOrderCallback($collection, $column)
    {
        $fromTo = $column->getFilter()->getValue();

		if(!@$fromTo['from'] && !@$fromTo['to']) return;
		$fromExpr = $toExpr = null;
		$cond = array();

		if(@$fromTo['from'])
			$cond[] = "last_order >= '".Mage::getModel('core/date')->date(null, $fromTo['from'])."'";
		if(@$fromTo['to'])
			$cond[] = "last_order <= '".Mage::getModel('core/date')->date(null, $fromTo['to'])."'";

        $collection->getSelect()->having("(".implode(' AND ', $cond).")");
        
    }

}

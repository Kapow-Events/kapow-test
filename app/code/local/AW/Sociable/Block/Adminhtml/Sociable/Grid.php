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
 * @package    AW_Sociable
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */?>
<?php

class AW_Sociable_Block_Adminhtml_Sociable_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('sociableGrid');
      $this->setDefaultSort('services_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('sociable/service')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('services_id', array(
          'header'    => Mage::helper('sociable')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'services_id',
      ));

      $this->addColumn('icon', array(
          'header'    => Mage::helper('sociable')->__('Icon'),
          'width'     => '40px',
          'align'     => 'center',
          'index'     => 'icon',
          'sortable'	=> false,
          'filter'		=> false,
          'renderer'  => 'AW_Sociable_Block_Adminhtml_Sociable_Render_Column_Image',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('sociable')->__('Name'),
          'align'     =>'left',
          'index'     => 'title',
      ));
      $this->addColumn('status', array(
          'header'    => Mage::helper('sociable')->__('Status'),
          'width'     => '100px',
          'align'     => 'center',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              0 => 'Disabled',
          ),
      ));
      $this->addColumn('sort_order', array(
          'header'    => Mage::helper('sociable')->__('Sort Order'),
          'width'     => '100px',
          'align'     =>'right',
          'index'     => 'sort_order',
      ));
      $this->addColumn('clicks', array(
          'header'    => Mage::helper('sociable')->__('Clicks'),
          'width'     => '100px',
          'align'     =>'right',
          'index'     => 'clicks',
      ));
      
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('sociable')->__('Action'),
                'width'     => '50',
                'align'     => 'center',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('sociable')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        
      
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('services_id');
        $this->getMassactionBlock()->setFormFieldName('sociable');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('sociable')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('sociable')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('sociable/status')->getOptionArray();

        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('sociable')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('sociable')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
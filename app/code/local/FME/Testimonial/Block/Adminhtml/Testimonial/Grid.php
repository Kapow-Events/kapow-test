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
 *             1- Created - 10-10-2010
 * 	       Asif Hussain <support@fmeextensions.com>
 * 	       1 - Short Description - 23-03-2012
 * 	       2 - Order / Postion - 23-03-2012
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Block_Adminhtml_Testimonial_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('testimonialGrid');
      $this->setDefaultSort('testimonial_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('testimonial/testimonial')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('testimonial_id', array(
          'header'    => Mage::helper('testimonial')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'testimonial_id',
      ));

      $this->addColumn('contact_name', array(
          'header'    => Mage::helper('testimonial')->__('Contact Name'),
          'align'     =>'left',
	  'width'     => '150px',
          'index'     => 'contact_name',
      ));
	 
	  
      $this->addColumn('email', array(
          'header'    => Mage::helper('testimonial')->__('Email'),
          'align'     =>'left',
	  'width'     => '220px',
          'index'     => 'email',
      ));
	  
	  
      $this->addColumn('company_name', array(
          'header'    => Mage::helper('testimonial')->__('Company Name'),
          'align'     =>'left',
	  'width'     => '220px',
          'index'     => 'company_name',
      ));
      
      
      $this->addColumn('short_description', array(
          'header'    => Mage::helper('testimonial')->__('Short Description'),
          'align'     =>'left',
	  'width'     => '220px',
          'index'     => 'short_description',
	  'renderer'	=> 'FME_Testimonial_Block_Adminhtml_Testimonial_Renderer_Description',
      ));
      
      $this->addColumn('order_num', array(
          'header'    => Mage::helper('testimonial')->__('Order / Position'),
          'align'     =>'center',
	  'width'     => '100px',
          'index'     => 'order_num',
      ));
      
	  
      if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        =>  Mage::helper('testimonial')->__('Store View'),
                'index'         =>  'store_id',
                'type'          =>  'store',
                'store_all'     =>  true,
                'store_view'    =>  true,
		'width'     => '175px',
                'sortable'      =>  false,
                'filter_condition_callback' =>  array($this, '_filterStoreCondition'),
            ));
      }
	  
      $this->addColumn('status', array(
          'header'    => Mage::helper('testimonial')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
      $this->addColumn('action', array(
                'header'    =>  Mage::helper('testimonial')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('testimonial')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
	    ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('testimonial')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('testimonial')->__('XML'));
	  
          return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('testimonial_id');
        $this->getMassactionBlock()->setFormFieldName('testimonial');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('testimonial')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('testimonial')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('testimonial/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('testimonial')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('testimonial')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

    protected function _afterLoadCollection()
    {
        
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
        
    }
    
    
    protected function _filterStoreCondition($collection, $column)
    {
        
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
    
        $this->getCollection()->addStoreFilter($value);
        
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
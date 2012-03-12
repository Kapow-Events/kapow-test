<?php

class AW_Sociable_Block_Adminhtml_Sociable_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('sociable_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('sociable')->__('Service Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('sociable')->__('General'),
          'title'     => Mage::helper('sociable')->__('General'),
          'content'   => $this->getLayout()->createBlock('sociable/adminhtml_sociable_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
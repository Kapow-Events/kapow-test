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

class AW_Sociable_Block_Adminhtml_Sociable_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('sociable_form', array('legend'=>Mage::helper('sociable')->__('General')));

        $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('sociable')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('sociable')->__('Enabled'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('sociable')->__('Disabled'),
              ),
          ),
      ));

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('sociable')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $id = $this->getRequest()->getParam('id');
      $icon = Mage::getModel('sociable/service')->load($id)->getIcon();
      if(isset($icon)){
          $path = Mage::getModel('sociable/service')->_iconsURL;
          $fieldset->addField('icon', 'file', array(
              'label'     => Mage::helper('sociable')->__('Icon'),
              'name'      => 'icon',
              'after_element_html' => '<img class=\'thumbnail-form\' src=\''.$path.$icon.'\'>',
          ));
      }
      else{
          $fieldset->addField('icon', 'file', array(
              'label'     => Mage::helper('sociable')->__('Icon'),
              'class'     => 'required-entry',
              'required'  => true,
              'name'      => 'icon',
          ));

      }

      $fieldset->addField('service_url', 'text', array(
          'label'     => Mage::helper('sociable')->__('Service URL'),
          'required'  => false,
          'name'      => 'service_url',
          'note'      => Mage::helper('sociable')->__("<b>{url}</b> - Page URL, <b>{title}</b> - Page title"),
      ));
      
      $fieldset->addField('service_script', 'textarea', array(
          'label'     => Mage::helper('sociable')->__('Service Script'),
          'required'  => false,
          'name'      => 'service_script',
          'note'      => Mage::helper('sociable')->__("Type service code here"),
          'style'     => 'width:700px; height:250px;',
      ));
      

     $fieldset->addField('short_url', 'select', array(
          'label'     => Mage::helper('sociable')->__('Use short URL'),
          'name'      => 'short_url',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('sociable')->__('No'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('sociable')->__('Yes'),
              ),
          ),
         'note'    => (Mage::helper('sociable')->getEnabledBitly()) ? '' : Mage::helper('sociable')->__("<b class='required'>WARNING!!!</b> - Bit.ly is not configured and this option doesn't work!"),
      ));

     $fieldset->addField('sort_order', 'text', array(
          'label'     => Mage::helper('sociable')->__('Sort Order'),
          'required'  => false,
          'name'      => 'sort_order',
      ));

        $fieldset->addField('new_window', 'select', array(
          'label'     => Mage::helper('sociable')->__('Open in new window'),
          'name'      => 'new_window',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('sociable')->__('No'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('sociable')->__('Yes'),
              ),
          ),
        ));

      if ( Mage::getSingleton('adminhtml/session')->getSociableData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSociableData());
          Mage::getSingleton('adminhtml/session')->setSociableData(null);
      } elseif ( Mage::registry('sociable_data') ) {
          $form->setValues(Mage::registry('sociable_data')->getData());
      }
      return parent::_prepareForm();
  }
}
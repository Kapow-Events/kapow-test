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
 *             
 * 	       Asif Hussain <support@fmeextensions.com>
 * 	       1 - Created - 23-03-2012
 * 	       
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Block_Adminhtml_Testimonial_Edit_Tab_Metaform extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('testimonial_form', array('legend'=>Mage::helper('testimonial')->__('Testimonial information')));
     
      $fieldset->addField('meta_title', 'text', array(
          'label'     => Mage::helper('testimonial')->__('Meta Title'),
          'required'  => false,
          'name'      => 'meta_title',
      ));
      
      $fieldset->addField('meta_keywords', 'textarea', array(
          'label'     => Mage::helper('testimonial')->__('Meta Keywords'),
          'required'  => false,
          'name'      => 'meta_keywords',
	  
      ));

      $fieldset->addField('meta_description', 'textarea', array(
          'label'     => Mage::helper('testimonial')->__('Meta Description'),
          'required'  => false,
          'name'      => 'meta_description',
	  'after_element_html'	=> '<p class="note"><small>Maximum 255 chars </small></p>',
	  ));
	  
	  
	  
	  
	  
     
      if ( Mage::getSingleton('adminhtml/session')->getTestimonialData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTestimonialData());
          Mage::getSingleton('adminhtml/session')->setTestimonialData(null);
      } elseif ( Mage::registry('testimonial_data') ) {
          $form->setValues(Mage::registry('testimonial_data')->getData());
      }
      return parent::_prepareForm();
  }
}
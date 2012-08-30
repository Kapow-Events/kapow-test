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
 *
 * 	       Asif Hussain <support@fmeextensions.com>
 * 	       1 - Url Identifier - 23-03-2012
 * 	       2 - Order Postion - 23-03-2012
 * 	       3 - Custom Fields - 23-03-2012
 * 	       4 - Short Description - 23-03-2012
 * 	       5 - wysiwyg Editor - 23-03-2012
 * 	       6 - Validation - 23-03-2012
 * 	       
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Block_Adminhtml_Testimonial_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('testimonial_form', array('legend'=>Mage::helper('testimonial')->__('Testimonial information')));
     
	  $fieldset->addField('company_name', 'text', array(
	      'label'     => Mage::helper('testimonial')->__('Company'),
	      'required'  => false,
	      'name'      => 'company_name',
	  ));

	  $fieldset->addField('contact_name', 'text', array(
          'label'     => Mage::helper('testimonial')->__('Contact Name'),
          'required'  => false,
          'name'      => 'contact_name',
	  ));
	  
	  $fieldset->addField('url_identifier', 'text', array(
          'label'     => Mage::helper('testimonial')->__('SEF URL Identifier'),
          'required'  => false,
          'name'      => 'url_identifier',
	  'after_element_html'	=> '<p class="note"><small>(eg: domain.com/testimonial/identifier)</small></p>',
	  'class'	=>'validate-alphanum',
	  ));
	  
	  $fieldset->addField('contact_photo', 'file', array(
          'label'     => Mage::helper('testimonial')->__('Picture'),
          'required'  => false,
          'name'      => 'contact_photo',
	  ));
	  
	  $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('testimonial')->__('Email'),
	  'class'     => 'required-entry validate-email',
          'required'  => true,
          'name'      => 'email',
	  ));
	  
	  $fieldset->addField('website', 'text', array(
          'label'     => Mage::helper('testimonial')->__('Website URL'),
          'required'  => false,
          'name'      => 'website',
	  'class'	=> 'validate-clean-url',
	  ));
	  
	  $fieldset->addField('order_num', 'text', array(
          'label'     => Mage::helper('testimonial')->__('Order / Position'),
          'required'  => false,
          'name'      => 'order_num',
	  'class'	=> 'validate-number',
	  'after_element_html'	=> '<p class="note"><small>0 for latest</small></p>',
	  ));
	  
	  
	  // Custom fields Starts
	  
	  $custom_field1_label = Mage::getStoreConfig('testimonial/form_custom_field/field1_label');
	  if(Mage::getStoreConfig('testimonial/form_custom_field/field1_status') && $custom_field1_label != ''){
	    
	      $fieldset->addField('custom_field1_value', 'text', array(
	      'label'     => Mage::helper('testimonial')->__($custom_field1_label),
	      'required'  => false,
	      'name'      => 'custom_field1_value',
	      'after_element_html'	=> '<p class="note"><small>Custom field</small></p>',
	      ));
	  }
	  
	  
	  $custom_field2_label = Mage::getStoreConfig('testimonial/form_custom_field/field2_label');
	  if(Mage::getStoreConfig('testimonial/form_custom_field/field2_status') && $custom_field2_label != null){
	    
	      $fieldset->addField('custom_field2_value', 'text', array(
	      'label'     => Mage::helper('testimonial')->__($custom_field2_label),
	      'required'  => false,
	      'name'      => 'custom_field2_value',
	      'after_element_html'	=> '<p class="note"><small>Custom field</small></p>',
	      ));
	  }
	  
	  
	  $custom_field3_label = Mage::getStoreConfig('testimonial/form_custom_field/field3_label');
	  if(Mage::getStoreConfig('testimonial/form_custom_field/field3_status') && $custom_field3_label != null){
	    
	      $fieldset->addField('custom_field3_value', 'text', array(
	      'label'     => Mage::helper('testimonial')->__($custom_field3_label),
	      'required'  => false,
	      'name'      => 'custom_field3_value',
	      'after_element_html'	=> '<p class="note"><small>Custom field</small></p>',
	      ));
	  }
	  
	  
	  $custom_field4_label = Mage::getStoreConfig('testimonial/form_custom_field/field4_label');
	  if(Mage::getStoreConfig('testimonial/form_custom_field/field4_status') && $custom_field4_label != null){
	    
	      $fieldset->addField('custom_field4_value', 'text', array(
	      'label'     => Mage::helper('testimonial')->__($custom_field4_label),
	      'required'  => false,
	      'name'      => 'custom_field4_value',
	      'after_element_html'	=> '<p class="note"><small>Custom field</small></p>',
	      ));
	  }
	 
	 // Custom fields Ends 
	  
	  
	  
	   $fieldset->addField('featured_testimonial', 'select', array(
          'label'     => Mage::helper('testimonial')->__('Mark as Featured ?'),
          'name'      => 'featured_testimonial',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('testimonial')->__('Yes'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('testimonial')->__('No'),
              ),
          ),
      ));
	   
	   $fieldset->addField('store_id','multiselect',array(
			'name'      => 'stores[]',
            'label'     => Mage::helper('testimonial')->__('Store View'),
            'title'     => Mage::helper('testimonial')->__('Store View'),
            'required'  => true,
			'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
		));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('testimonial')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('testimonial')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('testimonial')->__('Disabled'),
              ),
          ),
      ));
      
      
      try{
	
	    $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
		      array(
			    'add_widgets' => false,
			    'add_variables' => false,
			    //'add_images'	=> false,
			    'files_browser_window_url'=> $this->getBaseUrl().'admin/cms_wysiwyg_images/index/',
		      ));
	    $config->setData(Mage::helper('testimonial')->recursiveReplace(
						'/testimonial/',
						'/'.(string)Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName').'/',
						$config->getData()
					)
				);
      }
      catch (Exception $ex){
			$config = null;
      }
      
      /* Requird if Config setting is set to 'read more with short description' */
      $short_desc_req = false;
      
      if(Mage::getStoreConfig('testimonial/list/allow_read_more') == 1 || Mage::getStoreConfig('testimonial/featuredtestimonials/allow_read_more') == 1):
	  $short_desc_req = true;
      else:
	  $short_desc_req = false;
      endif;
      
      
      $fieldset->addField('short_description', 'editor', array(
	'name'		=> 'short_description',
	'label'		=> Mage::helper('testimonial')->__('Testimonial Short Description'),
	'title'		=> Mage::helper('testimonial')->__('Testimonial Short Description'),
	'style'     => 'width:700px; height:250px;',
	'wysiwyg'   => true,
        'required'  => $short_desc_req,
	'config'	=> $config,
	'after_element_html'	=> '<p class="note"><small>Maximum 255 chars</small></p>',
      ));
      
      $fieldset->addField('testimonial', 'editor', array(
          'name'      => 'testimonial',
          'label'     => Mage::helper('testimonial')->__('Testimonial'),
          'title'     => Mage::helper('testimonial')->__('Testimonial'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => true,
          'required'  => true,
	  'config'	=> $config,
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
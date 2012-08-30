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
 * 	       1 - Meta Information - 23-03-2012
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Block_Adminhtml_Testimonial_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('testimonial_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('testimonial')->__('Testimonial Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('testimonial')->__('Testimonial Information'),
          'title'     => Mage::helper('testimonial')->__('Testimonial Information'),
          'content'   => $this->getLayout()->createBlock('testimonial/adminhtml_testimonial_edit_tab_form')->toHtml(),
      ));
     
      $this->addTab('meta_section',array(
          'label'     => Mage::helper('testimonial')->__('META Information'),
          'title'     => Mage::helper('testimonial')->__('META Information'),
          'content'   => $this->getLayout()->createBlock('testimonial/adminhtml_testimonial_edit_tab_metaform')->toHtml(),
      ));
      
      
      return parent::_beforeToHtml();
  }
}
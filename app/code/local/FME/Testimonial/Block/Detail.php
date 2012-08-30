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
 *             Asif Hussain <support@fmeextensions.com>
 * 	       1 - Created - 23-03-2012
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Block_Detail extends Mage_Core_Block_Template
{
    
    public function _prepareLayout()
    {
	$test_id = Mage::registry('current_testimonial_id');
	$model = Mage::getModel('testimonial/testimonial')->load($test_id);
	
        if ( Mage::getStoreConfig('web/default/show_cms_breadcrumbs') && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) ) {
            $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
            $breadcrumbs->addCrumb('testimonial_home', array('label' => Mage::helper('testimonial')->getListPageTitle(), 'title' => Mage::helper('testimonial')->getListPageTitle(), 'link'=> Mage::helper('testimonial')->getNewurlIdentifier()));
	    $breadcrumbs->addCrumb('company', array('label' => $model->getCompanyName(), 'title' => $model->getCompanyName()));
        }
        
	
	
	if ($head = $this->getLayout()->getBlock('head')) {
            
	    if($model->getMetaTitle()!='') {
		$head->setTitle($model->getMetaTitle()); 
	    }else{
		$head->setTitle(Mage::helper('testimonial')->getListPageTitle()); // if testimonial is added from frontend
	    }
            
	    if($model->getMetaDescription()!=''){
	       $head->setDescription($model->getMetaDescription());
	    }else{
		$head->setDescription(Mage::helper('testimonial')->getListMetaDescription()); // if testimonial is added from frontend
	    }
            
	    if($model->getMetaKeywords()!=''){
		$head->setKeywords($model->getMetaKeywords());
	    }else{
		$head->setKeywords(Mage::helper('testimonial')->getListMetaKeywords()); // if testimonial is added from frontend
	    }
        }
        
        return parent::_prepareLayout();
        
    }
    
	
       
    public function getTestimonialDetail(){
	
	$test_id = Mage::registry('current_testimonial_id');
	$model = Mage::getModel('testimonial/testimonial')->load($test_id);
	$detail = $model->getData();
	
	
	return $detail;
    }
    
    
	
    
}
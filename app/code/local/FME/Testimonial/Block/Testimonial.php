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
 *             
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Block_Testimonial extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		
		if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('testimonial')->__('Home'),
                'title'=>Mage::helper('testimonial')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));
			
			$breadcrumbsBlock->addCrumb('testimonial', array(
                'label'=>Mage::helper('testimonial')->__('Testimonials'),
                'title'=>Mage::helper('testimonial')->__('Go to Testimonials Page'),
                'link'=>Mage::helper('testimonial')->getNewurlIdentifier()
            ));
			
        }
        
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle(Mage::helper('testimonial')->getListPageTitle());
            $head->setDescription(Mage::helper('testimonial')->getListMetaDescription());
            $head->setKeywords(Mage::helper('testimonial')->getListMetaKeywords());
        }
		
		return parent::_prepareLayout();
    }
	
	public function getLimitDescription()
	{
		
		if ( !$this->hasData('limit_description') ) {
			$this->setData('limit_description', Mage::registry('limit_description'));
		}
		return $this->getData('limit_description');
		
	}
	
     public function getTestimonial()     
     { 
        $store = Mage::app()->getStore()->getStoreId(); 
		$collection = Mage::getModel('testimonial/testimonial')->getCollection()
							->addStoreFilter($store)
							->addFieldToFilter('main_table.status', 1)
							->addOrder('main_table.created_time', 'desc')
							->getData();
		
		return $collection;
        
    }
	/***************************************************************
     this function draws the path of FME_quickrfq folder on local
     and returns the path to the frontend from where it is called
    ***************************************************************/
	public function getSecureImageUrl()	{
		
		$path = Mage::getBaseUrl('js');
		$apppath = $path. 'testimonial/FME_Testimonial' . DS . 'captcha/';
		return $apppath; 
		
	}
	/***************************************************************
     this function gets a new unique value by sending request to the
     assign_rand_value() function which returns a character and it
     adds the character in its variable and returns to the form at
     frontend
    ***************************************************************/
    
    function getNewrandCode($length)
	{
	  if($length>0) 
	  { 
	  $rand_id="";
	   for($i=1; $i<=$length; $i++)
	   {
		   $num = rand(1,36);
		   $rand_id .= $this->assign_rand_value($num);
	   }
	  }
		return $rand_id;
	}
	
	function assign_rand_value($num)
	{
	// accepts 1 - 36
	  switch($num)
	  {
		case "1":
		 $rand_value = "a";
		break;
		case "2":
		 $rand_value = "b";
		break;
		case "3":
		 $rand_value = "c";
		break;
		case "4":
		 $rand_value = "d";
		break;
		case "5":
		 $rand_value = "e";
		break;
		case "6":
		 $rand_value = "f";
		break;
		case "7":
		 $rand_value = "g";
		break;
		case "8":
		 $rand_value = "h";
		break;
		case "9":
		 $rand_value = "i";
		break;
		case "10":
		 $rand_value = "j";
		break;
		case "11":
		 $rand_value = "k";
		break;
		case "12":
		 $rand_value = "z";
		break;
		case "13":
		 $rand_value = "m";
		break;
		case "14":
		 $rand_value = "n";
		break;
		case "15":
		 $rand_value = "o";
		break;
		case "16":
		 $rand_value = "p";
		break;
		case "17":
		 $rand_value = "q";
		break;
		case "18":
		 $rand_value = "r";
		break;
		case "19":
		 $rand_value = "s";
		break;
		case "20":
		 $rand_value = "t";
		break;
		case "21":
		 $rand_value = "u";
		break;
		case "22":
		 $rand_value = "v";
		break;
		case "23":
		 $rand_value = "w";
		break;
		case "24":
		 $rand_value = "x";
		break;
		case "25":
		 $rand_value = "y";
		break;
		case "26":
		 $rand_value = "z";
		break;
		case "27":
		 $rand_value = "0";
		break;
		case "28":
		 $rand_value = "1";
		break;
		case "29":
		 $rand_value = "2";
		break;
		case "30":
		 $rand_value = "3";
		break;
		case "31":
		 $rand_value = "4";
		break;
		case "32":
		 $rand_value = "5";
		break;
		case "33":
		 $rand_value = "6";
		break;
		case "34":
		 $rand_value = "7";
		break;
		case "35":
		 $rand_value = "8";
		break;
		case "36":
		 $rand_value = "9";
		break;
	  }
		return $rand_value;
	}
}
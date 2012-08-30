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
 * 	       1 - Secure Image - 23-03-2012
 * 	       2 - Random Code - 23-03-2012
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Block_Items extends Mage_Core_Block_Template
{
    
    public function _prepareLayout()
    {
        if ( Mage::getStoreConfig('web/default/show_cms_breadcrumbs') && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) ) {
            $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
            $breadcrumbs->addCrumb('testimonial_home', array('label' => Mage::helper('testimonial')->getListPageTitle(), 'title' => Mage::helper('testimonial')->getListPageTitle()));
        }
        
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle(Mage::helper('testimonial')->getListPageTitle());
            $head->setDescription(Mage::helper('testimonial')->getListMetaDescription());
            $head->setKeywords(Mage::helper('testimonial')->getListMetaKeywords());
        }
        
        return parent::_prepareLayout();
        
    }
    
	public function getItems()     
	{ 

		if ( !$this->hasData('items') ) {
			$this->setData('items', Mage::registry('items'));
		}
		return $this->getData('items');
        
	}
	
	public function getLimitDescription()
	{
		
		if ( !$this->hasData('limit_description') ) {
			$this->setData('limit_description', Mage::registry('limit_description'));
		}
		return $this->getData('limit_description');
		
	}
	
	
	
    //For Add testimonial form functions
    
    /***************************************************************
     this function draws the path of FME_quickrfq folder on local
     and returns the path to the frontend from where it is called
    ***************************************************************/
	public function getSecureImageUrl()	{
		
		$path = Mage::getBaseUrl('js');
		$apppath = $path. 'testimonial/FME_Testimonial' . DS . 'captcha/';
		return $apppath; 
		
	}
    
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
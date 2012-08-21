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
 * @package    AW_Booking
 * @version    1.2.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Booking_Prices extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface{
	public function __construct(){
        $this->setTemplate('booking/product/edit/tab/booking/prices.phtml');
    }
    public function getProduct(){
        return Mage::registry('product');
    }
	public function render(Varien_Data_Form_Element_Abstract $element){
		$this->setElement($element);
		return $this->toHtml();
	}
    
    
    protected function _prepareLayout()
    {new AW_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Booking_Excludeddays;
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add Rule'),
                    'onclick'   => 'pricesControl.addItem()',
                    'class' => 'add'
                )));
                
        

                
        return parent::_prepareLayout();
    }
    

    public function createDateBlock($name, $type){
		
		$type = $type != 'to' ? 'from' : $type;
		
		$element = $this->getLayout()->createBlock('core/html_date')
                ->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'))
                
                
                
                ->setFormat(Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT))
                ;
        
        if($type == 'from'){
			
			$element
				->setName($name.'[__index__][date_from]')
				->setId('prices_row___index___date_from') 
				->setClass('product-custom-option datetime-picker input-text require');
		}else{
			$element
				->setName($name.'[__index__][date_to]')
				->setId('prices_row___index___date_to') 
				->setClass('product-custom-option datetime-picker input-text require');
		}        
                
        return $element->toHtml();
	}

    
    public function setElement(Varien_Data_Form_Element_Abstract $element){
        $this->_element = $element;
        return $this;
    }

    public function getElement(){
        return $this->_element;
    }    
    
    
    public function getWebsites()
    {
		
		//var_dump($this->getProduct()->getStoreId());die();
		
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }
        $websites = array();
        $websites[0] = array(
            'name'      => $this->__('All Websites'),
            'currency'  => Mage::app()->getBaseCurrencyCode()
        );
        if (Mage::app()->isSingleStoreMode() || $this->getElement()->getEntityAttribute()->isScopeGlobal()) {
            return $websites;
        }
        elseif ($storeId = $this->getProduct()->getStoreId()) {
            $website = Mage::app()->getStore($storeId)->getWebsite();
            $websites[$website->getId()] = array(
                'name'      => $website->getName(),
                'currency'  => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            );
        }
        else {
            $websites[0] = array(
                'name'      => $this->__('All Websites'),
                'currency'  => Mage::app()->getBaseCurrencyCode()
            );
            foreach (Mage::app()->getWebsites() as $website) {
                if (!in_array($website->getId(), $this->getProduct()->getWebsiteIds())) {
                    continue;
                }
                $websites[$website->getId()] = array(
                    'name'      => $website->getName(),
                    'currency'  => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                );
            }
        }
        $this->_websites = $websites;
        return $this->_websites;
    }
    
    public function getValues(){
		return Mage::getModel('booking/prices')->getCollection()
					->addEntityIdFilter($this->getProduct()->getId())
					->addStoreIdFilter($this->getProduct()->getStoreId())
					->getItems();
	}
}

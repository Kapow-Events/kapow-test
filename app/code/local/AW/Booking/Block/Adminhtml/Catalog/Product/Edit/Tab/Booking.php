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

class AW_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Booking extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Reference to product objects that is being edited
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    protected $_config = null;

    /**
     * Class constructor
     *
     */
    public function _construct()
    {
        
//        $this->setSkipGenerateContent(true);
        $this->setTemplate('booking/product/edit/information.phtml');
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('downloadable')->__('Booking Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('booking')->__('Booking Information');
    }


    /**
     * Detect if tab can be shown
     * @return bool
     */
    public function canShowTab(){
        return $this->getProduct()->getTypeId() == AW_Booking_Helper_Config::PRODUCT_TYPE_CODE;
    }

    /**
     * Check if tab is hidden
     * @return boolean
     */
    public function isHidden(){
        return !$this->canShowTab();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
	
	protected function getOrders(){
		$this->orders = Mage::helper('booking')->getOrders(
			$this->getRequest()->getParam('id')
		);
	}
	
    protected function _toHtml()
    {
		
		
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('bookingInfo2');
 /*
        $accordion->addItem('orders', array(
            'title'   => Mage::helper('booking')->__('Latest orders'),
            'content' => $this->getLayout()->createBlock('booking/adminhtml_catalog_product_edit_tab_information_orders')->toHtml(),
            'open'    => true,
        ));
*/
       $accordion->addItem('samples2', array(
            'title'   => Mage::helper('adminhtml')->__('Booked Dates'),
            'content' =>$this->getLayout()->createBlock('booking/adminhtml_catalog_product_edit_tab_information_calendar')->toHtml(),
            'open'    => true,
        ));

        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }

    /**
     * Return current product
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct(){
	if(!$this->getData('product')){
	    $this->setData('product', Mage::registry('product'));
	}
	return $this->getData('product');
    }

}

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

class AW_Booking_Adminhtml_BookingController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('booking/items');
    }

    protected function _initAction() {
		
		$this->loadLayout()
			->_setActiveMenu('booking/booking')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->loadLayout()
			->_setActiveMenu('booking/booking')
			->_addContent($this->getLayout()->createBlock('booking/adminhtml_products'))
			->renderLayout();
	}
	
	
    public function exportCsvAction()
    {
        $fileName   = 'booking_orders.csv';
        $content    = $this->getLayout()->createBlock('booking/adminhtml_products_product_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

	public function exportXmlAction(){
		$fileName   = 'booking_orders.xml';
		$content    = $this->getLayout()->createBlock('booking/adminhtml_products_product_grid')
			->getXml();
		$this->_sendUploadResponse($fileName, $content);
	}

	public function productAction() {
		$this->loadLayout()
			->_setActiveMenu('booking/booking')
			->_addContent($this->getLayout()->createBlock('booking/adminhtml_products_product'))
			->renderLayout();
	}
	
	public function detailsAction() {
		$this->loadLayout()
			->_setActiveMenu('booking/booking')
			->_addContent($this->getLayout()->createBlock('booking/adminhtml_products_details'))
			->renderLayout();
	}
	
	public function ajaxAction(){
		
			$m = $this->getRequest()->getParam('m');
			$d = $this->getRequest()->getParam('d');
			$y = $this->getRequest()->getParam('y');
		
		
		$product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('productId'));
		
		echo Zend_Json::encode(
			Mage::helper('booking')->getBindDetails($y, $m, $d, $product, true)
		);
		die();		
	}
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}

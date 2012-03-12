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

class AW_Sociable_Adminhtml_BookmarkedController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/cms/sociable');
    }

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('cms/sociable')
            ;
        return $this;
    }

    public function indexAction(){
        $this->_forward('most');
    }

    public function newAction(){
        $this->redirectToProduct($this->getRequest()->getActionName());
    }

    public function gridAction(){
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('sociable/adminhtml_bookmarked_grid')->toHtml()
        );
    }

    public function editAction(){
        $this->redirectToProduct($this->getRequest()->getActionName());
    }

    public function mostAction(){

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/store_switcher')->setUseConfirm(false)->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null))))
            ->_addContent($this->getLayout()->createBlock('sociable/adminhtml_bookmarked'))
            ->renderLayout();
           ;
    }
    public function massStatusAction(){
        $this->redirectToProduct($this->getRequest()->getActionName());
    }

    public function redirectToProduct($action){

        $params = '';

        foreach($this->getRequest()->getParams() as $key=>$param){
            $params = $params.'/'.$key.'/'.$param;
        }
        $this->getResponse()->setRedirect(Mage::getBaseURL().(string)Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName').'/catalog_product/'.$action.$params);

    }

}
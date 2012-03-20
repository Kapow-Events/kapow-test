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

class AW_Sociable_Block_Services extends Mage_Core_Block_Template {

    public function __construct() {
        $this->setTemplate('sociable/sociable.phtml');
        parent::__construct();
    }

    public function getIsShow() {
        if (Mage::helper('sociable')->getServiceEnabled()) {
            if (strpos($this->getNameInLayout(), '.')) {
                if (strstr(Mage::helper('sociable')->getBlockPosition(), $this->getNameInLayout()))
                    return true;
            }
            else
                return true;
        }
    }

    public function getAjaxUrl() {
        return Mage::getSingleton('core/url')->getUrl('sociable/index/saveClick');
    }

    public function getDivClass() {
        if (strpos($this->getNameInLayout(), '.')) {
            if (strstr($this->getNameInLayout(), 'left'))
                return 'aw-sociable_services-left-side-bar';
            if (strstr($this->getNameInLayout(), 'right'))
                return 'aw-sociable_services-right-side-bar';
            if (strstr($this->getNameInLayout(), 'content'))
                return 'aw-sociable_services-content';
            if (strstr($this->getNameInLayout(), 'product'))
                return 'aw-sociable_services-product-page';
        }
    }

    public function getEnv() {
        $route = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $controller = Mage::app()->getFrontController()->getRequest()->getControllerName();

        $entity_type = ($controller == 'product') ? $controller : $route;
        $entity_id = Mage::app()->getFrontController()->getRequest()->getParam(($entity_type == 'cms') ? 'page_id' : 'id');

        $env = array(
            'title' => $this->getLayout()->getBlock('head')->getTitle(),
            'url' => $this->helper('core/url')->getCurrentUrl(),
            'store_id' => Mage::app()->getStore()->getId(),
            'entity_id' => $entity_id,
            'entity_type' => $entity_type,
        );

        return $env;
    }

    public function getServicesList() {
        return Mage::getModel('sociable/service')->getServicesList();
    }

    public function getLinkToSociable($serviceId) {
        $route = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $controller = Mage::app()->getFrontController()->getRequest()->getControllerName();
        $entity_type = ($controller == 'product') ? $controller : $route;
        $entity_id = Mage::app()->getFrontController()->getRequest()->getParam(($entity_type == 'cms') ? 'page_id' : 'id');

        $pageUrl = $this->helper('core/url')->getCurrentUrl();
        $pageTitle = $this->getLayout()->getBlock('head')->getTitle();
        $helper = Mage::helper('core/url');
        return $this->getUrl('sociable/index/redirect', array(
                    'url' => $helper->getEncodedUrl($pageUrl),
                    'title' => $helper->getEncodedUrl($pageTitle),
                    'service' => $serviceId,
                    'entity_type' => $entity_type,
                    'entity_id' => $entity_id,)
        );
    }

    public function addGooglePlusCallback($script, $functionName) {
        $script = preg_replace('#>*.?</g:plusone>#si', ' callback="' . $functionName . '"></g:plusone>', $script);
        return $script;
    }

    public function getLink($id, $title, $url) {
        $service = Mage::getModel('sociable/service')->load($id);
        if ($service->getShortUrl() && $this->getEnabledBitly()) {
            $url = Mage::getModel('sociable/bitly')->getBitlyLink($url);
        }
        $title = str_replace('amp%3B', '', str_replace('+', '%20', urlencode($title)));
        $link = $service->getServiceUrl();
        $link = str_replace(array('{title}', '{url}'), array($title, urlencode($url)), $link);
        return $link;
    }

    public function getAjaxParams() {

        $cleanParams = $this->getEnv();
        unset($cleanParams['title']);
        unset($cleanParams['url']);

        $params = '';
        foreach ($cleanParams as $param => $key) {
            $params = $params . $param . '/' . $key . '/';
        }
        return $params;
    }

    protected function _toHtml() {
        $this->setTemplate('sociable/sociable.phtml');
        return $this->renderView();
    }

}
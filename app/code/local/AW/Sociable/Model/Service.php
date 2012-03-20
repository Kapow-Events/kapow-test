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

class AW_Sociable_Model_Service extends Mage_Core_Model_Abstract {

    public static $_iconsLocation;
    public static $_iconsURL;

    public function _construct() {
        $this->_iconsLocation = Mage::getBaseDir('media') . DS . 'sociable/';
        $this->_iconsURL = Mage::getBaseURL('media') . 'sociable/';
        parent::_construct();
        $this->_init('sociable/service');
    }

    private function _getServicesData() {
        $collection = $this->getCollection()->setFilterEnabled();
        if (Mage::helper('sociable')->getAutoArrange())
            $collection->setSortByClicks();
        else
            $collection->setSortByOrder();
        return $collection->getData();
    }

    public function getServicesList() {
        $arr = Mage::registry('aw_sociable_services');
        if (!$arr) {
            $arr = $this->_getServicesData();
            Mage::register('aw_sociable_services', $arr);
        }

        $services = array();
        foreach ($arr as $service) {
            $service['id'] = $service['services_id'];
            unset($service['services_id']);
            $service['title'] = htmlspecialchars($service['title']);
            $service['icon'] = $this->_iconsURL . $service['icon'];
            if (preg_match('/apis.google.com\/js\/plusone.js/i', $service['service_script'])) {
                if (Mage::registry('aw_google1_js')) {
                    $pattern="#<script type=\"text/javascript\">([^a-z]+)?\(function\(\) \{.*?https://apis.google.com/js/plusone.js.*?</script>#si";
                    $service['service_script'] = trim(preg_replace($pattern, '', $service['service_script']));
                    $service['service_script'] = str_replace('aw_gplusone_id', 'aw_gplusone_id_' . rand(1, 10000), $service['service_script']);
                } else {
                    Mage::register('aw_google1_js', true);
                }
            }
            $service['class'] = strtolower(preg_replace('/[\W]*/i', '', $service['title']));
            $services[] = $service;
        }
        return $services;
    }

    public function deleteClicks() {
        try {
            $resource = Mage::getModel('core/resource');
            $resource->getTableName('sociable/clicks');
            $db = $resource->getConnection('core_write');
            $db->delete($resource->getTableName('sociable/clicks'), array(
                'service_id = ?' => $this->getServicesId(),
            ));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        return $this;
    }

}
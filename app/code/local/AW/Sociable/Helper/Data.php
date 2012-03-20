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

class AW_Sociable_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getServiceEnabled() {
        return Mage::getStoreConfig('sociable/general/enable');
    }

    public function getAutoArrange() {
        return Mage::getStoreConfig('sociable/general/auto_arrange');
    }

    public function getBlockPosition() {
        return Mage::getStoreConfig('sociable/general/block_position');
    }

    public function getBitlyVer() {
        return Mage::getStoreConfig('sociable/bit_ly/api_version');
    }

    public function getBitlyLogin() {
        return Mage::getStoreConfig('sociable/bit_ly/api_login');
    }

    public function getBitlyKey() {
        return Mage::getStoreConfig('sociable/bit_ly/api_key');
    }

    public function getEnabledBitly() {

        if ($this->getBitlyVer() && $this->getBitlyLogin() && $this->getBitlyKey())
            return true;
        else
            return false;
    }

    public function saveClick($service_id, $UID, $storeId, $entity_type, $entity_id) {

        $services = Mage::getModel('sociable/service');
        $clicks = Mage::getModel('sociable/clicks');

        $service = $services->load($service_id);
        $service->setClicks($service->getClicks() + 1);
        $service->save();

        $clicks->setData(array(
            'user_uid' => $UID,
            'service_id' => $service_id,
            'store_id' => $storeId,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
        ));
        $clicks->save();

        if ($entity_type == 'product') {

            Mage::getModel('sociable/bookmarked')->saveProductClick($storeId, $entity_id);
        }
    }

    public function checkUniqueClick($service_id, $user_id, $store_id, $entity_type, $entity_id) {

        $clicks = Mage::getModel('sociable/clicks');
        return $clicks->getCollection()->getClick($service_id, $user_id, $store_id, $entity_type, $entity_id)->getData();
    }

    public function getClickUID() {

        $cookies = Mage::getModel('core/cookie');
        $UID = $cookies->get('clickUID');

        return $UID;
    }

    public function setClickUID() {

        $cookies = Mage::getModel('core/cookie');
        $UID = $this->generateUID();
        $cookies->set('clickUID', $UID, '315360000');

        return $UID;
    }

    public function generateUID() {

        $digits = (string) rand(100000, 999999);
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $UID = '';

        for ($i = 0; $i < 3; $i++) {
            $index = rand(0, strlen($letters) - 1);
            $UID .=$letters[$index];
        }
        $UID .= '-' . $digits;

        $clicks = Mage::getModel('sociable/clicks');

        if ($clicks->load($UID, 'user_uid')->getId())
            return $this->generateUID();
        else
            return $UID;
    }

    public function checkVersion($version) {
        return version_compare(Mage::getVersion(), $version, '>=');
    }

}
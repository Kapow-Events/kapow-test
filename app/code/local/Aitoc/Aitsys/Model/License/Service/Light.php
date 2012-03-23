<?php

class Aitoc_Aitsys_Model_License_Service_light extends Aitoc_Aitsys_Model_License_Service
{
    
    protected $_prefix = 'aitseg_license_servicelight';
    
    /**
     * 
     * @param $args
     * @return Aitoc_Aitsys_Model_License_Service
     */
    protected function _updateArgs( &$args )
    {
        $platform = $this->tool()->platform();
        if (!isset($args[0]) || !is_array($args[0]))
        {
            $args[0] = array();
        }
        $args[0]['platform_version'] = $platform->getVersion();
        $args[0]['is_test'] = $platform->isTestMode();
        $args[0]['magento_version'] = Mage::getVersion();
        $args[0]['base_url'] = $this->_license->getPlatform()->getAdminBaseUrl();
        if (!isset($args[0]['domain']) || !$args[0]['domain'] || $args[0]['domain'] === '' )
        {
            $args[0]['domain'] = $this->tool()->getRealBaseUrl();
        }
        $args[0]['platform_path'] = $this->tool()->platform()->getInstallDir(true);
        $args[0]['server_info'] = array(
            'name' => isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '!!!' ,
            'host' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '!!!' ,
            'addr' => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '!!!'
        );
        if ($platformId = $platform->getPlatformId())
        {
            $args[0]['platformid'] = $platformId;
        }
        if ($this->_license)
        {
            $args[0]['module_key'] = $this->_license->getKey();
            $args[0]['link_id'] = $this->_license->getLinkId();
            $args[0]['module_version'] = $this->_license->getModule()->getVersion();
            if (!isset($args[0]['purchaseid']))
            {
                $args[0]['purchaseid'] = $this->_license->getPurchaseId();
            }
        }
        return $this;
    }    
}

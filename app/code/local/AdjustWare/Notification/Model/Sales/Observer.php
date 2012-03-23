<?php
/**
 * Product:     Admin Email Notifications for 1.4.x-1.6.1.0 
 * Package:     AdjustWare_Notification_2.2.0_2.1.0_233631
 * Purchase ID: VgTBgcMfRJNN8TEH6RZt8oax2Zeu1FkviyZl5t0FfV
 * Generated:   2012-03-23 19:25:00
 * File path:   app/code/local/AdjustWare/Notification/Model/Sales/Observer.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Notification')){ akhkkacrkZDMBshi('42578cf487310b553f2a02659794e12b'); ?><?php
class AdjustWare_Notification_Model_Sales_Observer extends Mage_Core_Model_Abstract
{
    public function onCoreLocaleSetLocale($observer)
    {
        if(!Mage::registry('aitpagecache_check_14') && Mage::getConfig()->getNode('modules/Aitoc_Aitpagecache/active')==='true')
        {
            if(file_exists(Mage::getBaseDir('magentobooster').DS.'use_cache.ser'))
            {
                Mage::register('aitpagecache_check_14', 1);
            }
            elseif(file_exists(Mage::getBaseDir('app/etc').DS.'use_cache.ser'))
            {
                Mage::register('aitpagecache_check_13', 1);
            }
        }
    }
} } 
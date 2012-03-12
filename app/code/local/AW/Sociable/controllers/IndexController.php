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
class AW_Sociable_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_redirect('/');
    }

    public function saveClickAction(){

        $data = $this->getRequest()->getParams();
        $id = (int)$data['service_id'];
        $service = Mage::getModel('sociable/service')->load($id);

        if($service->getId()){

            $storeId = Mage::app()->getStore()->getId();
            
            if($UID = Mage::helper('sociable')->getClickUID()){
                if(!Mage::helper('sociable')->checkUniqueClick($id,$UID,$storeId,$data['entity_type'],$data['entity_id'])){
                    Mage::helper('sociable')->saveClick($id,$UID,$storeId,$data['entity_type'],$data['entity_id']);
                    return true;
                }
                else
                    return false;
            }
            else{
                $UID = Mage::helper('sociable')->setClickUID();
                Mage::helper('sociable')->saveClick($id,$UID,$storeId,$data['entity_type'],$data['entity_id']);
                return true;
            }
        }
        return false;
    }
}
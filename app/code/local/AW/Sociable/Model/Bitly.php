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

class AW_Sociable_Model_Bitly extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sociable/bitly');
    }

    public function getStoredBitlyLink($longUrl){
        return $this->load($longUrl,'long_link')->getShortLink();
    }


    public function getBitlyLink($longUrl){

        if(! $link = $this->getStoredBitlyLink($longUrl)){
            $ver  = Mage::helper('sociable')->getBitlyVer();
            $link = 'http://api.bit.ly/'.$ver.'/shorten';
            $params = array(
                'login'   =>  Mage::helper('sociable')->getBitlyLogin(),
                'apiKey'  =>  Mage::helper('sociable')->getBitlyKey(),
                'longUrl' =>  $longUrl,
                'format'  => 'json',
            );

            $request = new AW_Sociable_Model_Bitly_Request($link);
            $request->setParameterPost($params);
            if($result = $request->request()){
                $link = $result['data']['url'];
                $this->saveBitlyLink($longUrl,$link);
            }
            else
                $link = $longUrl;
        }
        return $link;
    }

    protected function saveBitlyLink($longLink,$shortLink){

        $data = array(
            'long_link'  =>  $longLink,
            'short_link' => $shortLink,
        );
        $this->setData($data);
        $this->save();
    }

}
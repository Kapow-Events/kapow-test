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

class AW_Sociable_Model_Bookmarked extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sociable/bookmarked');
    }

    public function saveProductClickByStore($id,$productId,$storeId){

        $bookmark = $this->load($id);
        $bookmark->setProductId($productId);
        $bookmark->setStoreId($storeId);
        $bookmark->setClicks($bookmark->getClicks() + 1);
        $bookmark->save();
        $bookmark->unsetData();
    }
    public function saveProductClick($storeId,$productId){
        $this->saveProductClickByStore($this->getBookmarkedLinkIdByStore($storeId, $productId), $productId, $storeId);
        $this->saveProductClickByStore($this->getBookmarkedLinkId($productId), $productId, '0');
    }

    public function getBookmarkedLinkIdByStore($storeId,$productId){

        $collection = $this->getCollection();
        $collection->getSelect()->where('store_id = ?',$storeId)->where('product_id = ?',$productId);
        $id = $collection->getColumnValues('id');
        return reset($id);
    }

    public function getBookmarkedLinkId($productId){

        $collection = $this->getCollection();
        $collection->getSelect()->where('store_id = 0')->where('product_id = ?',$productId);
        $id = $collection->getColumnValues('id');
        return reset($id);
    }

}
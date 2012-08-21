<?php

class EW_Site_Block_Featured extends Mage_Core_Block_Template
{

    public function getInitiallyFeaturedProduct($cid)
    {
    	if ($cid != 0) {
       /*  $category_id = 8; */
	        $product = Mage::getModel('catalog/category')->load($cid)
	            ->getProductCollection()
	            ->addAttributeToSelect('*')
	            ->addAttributeToFilter('status', 1);
	            /* ->setPageSize(1); */
        }
        else {
	        $product = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('status', 1);

        }
        return $product;
    }

    public function getFeatured()
    {
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $category_id = 6;
        $products = Mage::getModel('catalog/category')->load($category_id)
            ->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize(3);

        return $products;
    }

    public function getAddToCartUrl($product)
    {
        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => false));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }

}

?>

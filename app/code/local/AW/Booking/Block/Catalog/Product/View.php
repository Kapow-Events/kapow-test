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
 * @package    AW_Booking
 * @version    1.2.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Booking_Block_Catalog_Product_View extends Mage_Core_Block_Template{
	
	const READ_DATE_FORMAT = "dd/mm/yyyy";
	/** Field with selected date value id, from */
	const DATE_FROM_NAME = "aw_booking_from";
	/** Field with selected date value id, to */
	const DATE_TO_NAME = "aw_booking_to";	   

	/**
	 * Get product instance
	 * @return Mage_Catalog_Model_Product
	 */
	public function getProduct($product_id = 0){
		if(!$this->getData('product')){
			$model = Mage::getSingleton('catalog/product');
			if($product_id instanceof $model){
				$model = $product_id;
				
			}elseif($product_id || ($product_id = $this->getRequest()->getParam('id'))){
				$model->load($product_id);
			}elseif(Mage::registry('product')){
				$model = $product;
			}
			$this->setData('product', $model);
		}
		return $this->getData('product');
	}
	
	/**
	 * Return minimal accessible hour
	 * @return int
	 */
	public function getMinHour(){
	    if(!$this->getData('max_hour')){
		if($this->getProduct()->getAwBookingRangeType() == AW_Booking_Model_Entity_Attribute_Source_Rangetype::TIME){
			$minH = @explode(",", $this->getProduct()->getAwBookingTimeFrom());
			$minH = @$minH[0];
		}else{
			$minH = 0;
		}
		$this->setData('min_hour', $minH);
	    }
	    return $this->getData('min_hour');
	}
	
	/**
	 * Return max accessible hour
	 * @return int
	 */
	public function getMaxHour(){
	    if(!$this->getData('max_hour')){
		if($this->getProduct()->getAwBookingRangeType() == AW_Booking_Model_Entity_Attribute_Source_Rangetype::TIME){
			$maxH = @explode(",", $this->getProduct()->getAwBookingTimeTo());
			$maxH = @$maxH[0];
		}else{
			$maxH = 23;
		}
		$this->setData('max_hour', $maxH);
	    }
	    return $this->getData('max_hour');
	}	
	
	/**
	 * Initializes time block
	 * @return AW_Booking_Block_Catalog_Product_Options_Date
	 */
	protected function _createTimeBlock(){
		$block = new AW_Booking_Block_Catalog_Product_Options_Date;
		return $block;
	}

	/**
	 * Returns time selector with specified name
	 * @param string $name
	 * @return AW_Booking_Block_Catalog_Product_Options_Date
	 */
	public function getTimeBlock($name){
		$block = $this->_createTimeBlock()->setName($name);
		if($this->getProduct()->getAwBookingRangeType() == AW_Booking_Model_Entity_Attribute_Source_Rangetype::TIME){
			$block
				->setHourStart($this->getMinHour())
				->setHourEnd($this->getMaxHour());
		}
		return $block;
	}

	/**
	 * Return first available day
	 * @TODO do that not as stub but return real first avail day
	 * @return Zend_Date
	 */
	public function getFirstAvailableDay(){
	    if(!$this->getData('first_available_day')){
            $this->setData('first_available_day', $this->getProduct()->getTypeInstance()->getFirstAvailableDate());
	    }
	    return $this->getData('first_available_day');
	}

	/**
	 * Returns price for first available day
	 * @return float
	 */
	public function getFirstAvailableDayPrice(){
	    $Date = $this->getFirstAvailableDay();
	    $From = clone  $Date;
	    $To = clone $Date;

	    $From->setHour($this->getMinHour())->setMinute(0)->setSecond(0);
	    $To->setHour($this->getMaxHour())->setMinute(0)->setSecond(0);
	    $price = $this->getProduct()->getPriceModel()->getBookingPrice($this->getProduct(), $From, $To, null, AW_Core_Model_Abstract::RETURN_ARRAY);
	    return Zend_Json::encode($price);
	}

	/**
	 * Return name of "display" field
	 * @param string $str
	 */
	public function getDisplayFieldName($str){
	    return $str."_display";
	}

    public function getCustomOptions(){

        $customOptions = array();
        $customOptionsColl = Mage::getModel('catalog/product_option')->getCollection();
        $customOptionsColl->getSelect()
                ->joinLeft(array('type_value' => $customOptionsColl->getTable('catalog/product_option_type_value')), 'main_table.option_id = type_value.option_id', array('option_type_id' => 'type_value.option_type_id'))
                ->joinLeft(array('type_price' => $customOptionsColl->getTable('catalog/product_option_type_price')), 'type_value.option_type_id = type_price.option_type_id', array('price' => 'type_price.price','price_type' => 'type_price.price_type'))
                ->where('main_table.product_id = ?',$this->getProduct()->getId())
                ;
        foreach($customOptionsColl->getData() as $option){
            $customOptions[$option['option_type_id']] = $option;
        }
        return Zend_Json::encode($customOptions);
    }

    public function getJsonConfig()
    {
        $config = array();

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $this->getProduct()->getPrice();
        $_finalPrice = $this->getProduct()->getFinalPrice();
        $_priceInclTax = Mage::helper('tax')->getPrice($this->getProduct(), $_finalPrice, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($this->getProduct(), $_finalPrice);

        $config = array(
            'productId'           => $this->getProduct()->getId(),
            'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'      => Mage::helper('tax')->displayBothPrices(),
            'productPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
            'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
        );

        $responseObject = new Varien_Object();
        //Mage::dispatchEvent('catalog_product_view_config', array('response_object'=>$responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
                $config[$option] = $value;
            }
        }
        //var_dump(Zend_Json::encode($config));
        return Zend_Json::encode($config);
    }
}

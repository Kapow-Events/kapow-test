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

class AW_Booking_Model_Product_Backend_Prices extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
	
	
	
    /**
     * Website currency codes and rates
     *
     * @var array
     */
    protected $_rates;

    /**
     * Retrieve resource model
     *
     * @return AW_Booking_Model_Mysql4_Booking_Price
     */
    protected function _getResource(){
        return Mage::getResourceSingleton('booking/booking_price');
    }
    
    protected function _getProduct(){
		return Mage::registry('product');
	}

    /**
     * Retrieve websites rates and base currency codes
     *
     * @return array
     */
    public function _getWebsiteRates()
    {
        if (is_null($this->_rates)) {
            $this->_rates = array();
            $baseCurrency = Mage::app()->getBaseCurrencyCode();
            foreach (Mage::app()->getWebsites() as $website) {
                /* @var $website Mage_Core_Model_Website */
                if ($website->getBaseCurrencyCode() != $baseCurrency) {
                    $rate = Mage::getModel('directory/currency')
                        ->load($baseCurrency)
                        ->getRate($website->getBaseCurrencyCode());
                    if (!$rate) {
                        $rate = 1;
                    }
                    $this->_rates[$website->getId()] = array(
                        'code' => $website->getBaseCurrencyCode(),
                        'rate' => $rate
                    );
                }
                else {
                    $this->_rates[$website->getId()] = array(
                        'code' => $baseCurrency,
                        'rate' => 1
                    );
                }
            }
        }
        return $this->_rates;
    }

    /**
     * Validate data
     *
     * @param   Mage_Catalog_Model_Product $object
     * @return  this
     */
    public function validate($object)
    {
        $periods = $object->getData($this->getAttribute()->getName());
        $all_periods = array();
        foreach($periods as $k=>$period){
	
			if(!is_numeric($k)) continue;
			if (
				!empty($period['delete']) ||
				empty($period['price_from']) || 
				empty($period['date_from']) || 
				(
					intval($period['is_progressive']) && 
					empty($period['date_to'])
				) || 
				(
					intval($period['is_progressive']) && 
					(AW_Booking_Helper_Data::toTimestamp($period['date_from']) >= AW_Booking_Helper_Data::toTimestamp($period['date_to']))
				) 
				){
					continue;
			}
			
			if(!is_numeric($k)) continue;
			
			if(!empty($all_periods)){
				foreach($all_periods as $ap){
					if($this->_intersects($ap, $period)){
						Mage::throwException(
							Mage::helper('booking')->__("Please enter valid price intervals")
						);
					}
				}
			}
			$all_periods[] = $period;
		}
        
        
        
        if (empty($periods)) {
            return $this;
        }
        return $this;
    }
    public function _intersects($period1, $period2){
		$from1 = AW_Booking_Helper_Data::toTimestamp($period1['date_from']);
		$to1 = AW_Booking_Helper_Data::toTimestamp($period1['date_to']);
		$from2 = AW_Booking_Helper_Data::toTimestamp($period2['date_from']);
		$to2 = AW_Booking_Helper_Data::toTimestamp($period2['date_to']);
		
		if(
			(
				$from2 >= $from1 &&
				$to2 <= $to1
			) ||
			(
				$from2 <= $from1 &&
				$to2 >= $to1
			) ||
			(
				$to2 > $from1 &&
				$to2 < $to1
			) ||
			(
				$from2 > $from1 &&
				$from2 < $to1
			) ||
			(
				$to1 == $from2
			)
		){
			return true;
		}
		return false;
	}
    



    /**
     * After Save Attribute manipulation
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Tierprice
     */
    public function afterSave($object)
    {
		$generalStoreId = $object->getStoreId();
		$periods = $object->getData($this->getAttribute()->getName());
		if (!is_array($periods)) {
            return $this;
        }
        
        Mage::getResourceSingleton('booking/prices')
			->deleteByEntityId($object->getId(), $generalStoreId);
        

		
		
		
		
		foreach ($periods as $k=>$period) {
			/* Check period latency */

            
			if(!is_numeric($k)) continue;
			if (
				!empty($period['delete']) ||
				//empty($period['price_from']) ||
				$period['price_from'] == '' ||
				empty($period['date_from']) || 
				(
					intval($period['is_progressive']) && 
					empty($period['date_to'])
				) || 
				(
					intval($period['is_progressive']) && 
					(AW_Booking_Helper_Data::toTimestamp($period['date_from']) >= AW_Booking_Helper_Data::toTimestamp($period['date_to']))
				) 
				){
					continue;
			}
			
			if(!is_numeric($k)) continue;
			
			$period['date_from'] = date('Y-m-d H:i:s', AW_Booking_Helper_Data::toTimestamp($period['date_from']));
			$period['date_to'] = date('Y-m-d H:i:s', AW_Booking_Helper_Data::toTimestamp($period['date_to']));
			
			$storeId = @$period['use_default_value'] ? 0 : $object->getStoreId();
			
			Mage::getModel('booking/prices')
				->setEntityId($this->_getProduct()->getId())
				->setStoreId($storeId)
				->setDateFrom($period['date_from'])
				->setDateTo($period['date_to'])
				->setPriceFrom($period['price_from'])
				->setPriceTo($period['price_to'])
				->setIsProgressive(intval($period['is_progressive']))
				->save();
		
		}

		return $this;
		
		
        $this->_getResource()->deleteProductPrices($object, $this->getAttribute());
        $tierPrices = $object->getData($this->getAttribute()->getName());

        if (!is_array($tierPrices)) {
            return $this;
        }

        $prices = array();
        foreach ($tierPrices as $tierPrice) {
            if (empty($tierPrice['price_qty']) || !isset($tierPrice['price']) || !empty($tierPrice['delete'])) {
                continue;
            }

            $useForAllGroups = $tierPrice['cust_group'] == Mage_Customer_Model_Group::CUST_GROUP_ALL;
            $customerGroupId = !$useForAllGroups ? $tierPrice['cust_group'] : 0;
            $priceKey = join('-', array(
                $tierPrice['website_id'],
                intval($useForAllGroups),
                $customerGroupId,
                $tierPrice['price_qty']
            ));

            $prices[$priceKey] = array(
                'website_id'        => $tierPrice['website_id'],
                'all_groups'        => intval($useForAllGroups),
                'customer_group_id' => $customerGroupId,
                'qty'               => $tierPrice['price_qty'],
                'value'             => $tierPrice['price'],
            );
        }

        if ($this->getAttribute()->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE) {
            if ($storeId = $object->getStoreId()) {
                $websites = array(Mage::app()->getStore($storeId)->getWebsite());
            }
            else {
                $websites = Mage::app()->getWebsites();
            }

            $baseCurrency   = Mage::app()->getBaseCurrencyCode();
            $rates          = $this->_getWebsiteRates();
            foreach ($websites as $website) {
                /* @var $website Mage_Core_Model_Website */
                if (!is_array($object->getWebsiteIds()) || !in_array($website->getId(), $object->getWebsiteIds())) {
                    continue;
                }
                if ($rates[$website->getId()]['code'] != $baseCurrency) {
                    foreach ($prices as $data) {
                        $priceKey = join('-', array(
                            $website->getId(),
                            $data['all_groups'],
                            $data['customer_group_id'],
                            $data['qty']
                        ));
                        if (!isset($prices[$priceKey])) {
                            $prices[$priceKey] = $data;
                            $prices[$priceKey]['website_id'] = $website->getId();
                            $prices[$priceKey]['value'] = $data['value'] * $rates[$website->getId()]['rate'];
                        }
                    }
                }
            }
        }

        foreach ($prices as $data) {
            $this->_getResource()->insertProductPrice($object, $data);
        }

        return $this;
    }


}

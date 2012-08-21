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


class AW_Booking_Model_Entity_Attribute_Source_Qratiomultipliertype extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	
	const DAYS = 'days';
	const HOURS = 'hours';

    public function getAllOptions()
    {
    	
        return array(
			array('value'=>self::DAYS, 'label'=>Mage::helper('booking')->__("Days")),
            array('value'=>self::HOURS, 'label'=>Mage::helper('booking')->__("Hours")),
        );
        
    }
}

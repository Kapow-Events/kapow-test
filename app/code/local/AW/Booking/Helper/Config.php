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

class AW_Booking_Helper_Config extends Mage_Core_Helper_Abstract{
    /** How many calendar pages to show */
    const XML_PATH_APPEARANCE_CALENDAR_PAGES = 'booking/appearance/calendar_pages';
    /** How many days store quotes data at orders table */
    const XML_PATH_ADVANCED_STORE_QUOTES_DAYS = 'booking/advanced/store_quotes_days';
    /** First day of week from magento locale */
    const XML_PATH_GENERAL_LOCALE_FIRSTDAY = 'general/locale/firstday';

    /** Product type code */
    const PRODUCT_TYPE_CODE = 'bookable';
}
?>

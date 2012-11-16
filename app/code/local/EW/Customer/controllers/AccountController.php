<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once 'Mage/Customer/controllers/AccountController.php';

class EW_Customer_AccountController extends Mage_Customer_AccountController
{

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess(
            $this->__('<!-- Google Code for New Account Signup Conversion Page -->
            
            <script type="text/javascript">
            
            /* <![CDATA[ */
            
            var google_conversion_id = 1003217271;
            
            var google_conversion_language = "en";
            
            var google_conversion_format = "3";
            
            var google_conversion_color = "ffffff";
            
            var google_conversion_label = "c5TTCImU3wUQ98Kv3gM";
            
            var google_conversion_value = 0;
            
            /* ]]> */
            
            </script>
            
            <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
            
            </script>
            
            <noscript>
            
            <div style="display:inline;">
            
            <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1003217271/?value=0&amp;label=c5TTCImU3wUQ98Kv3gM&amp;guid=ON&amp;script=0"/>
            
            </div>
            
            </noscript>
            Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );

        $customer->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        $successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }

//        $successUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) .'?thank-you';

        return $successUrl;
    }

}

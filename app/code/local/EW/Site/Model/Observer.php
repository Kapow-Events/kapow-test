<?php

class EW_Site_Model_Observer
{
    public function sendCustomerEmail($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (($customer instanceof Mage_Customer_Model_Customer)) {
            $origCustomer = Mage::getModel('customer/customer')->load($customer->getId());
            if ($origCustomer->getGroupId() != $customer->getGroupId()
                && $origCustomer->getGroupId() == 4)
            {
                $templateId = 3;

                $sender = array('name' => Mage::getStoreConfig('trans_email/ident_general/name'),
                    'email' => Mage::getStoreConfig('trans_email/ident_general/email'));

                $store = Mage::app()->getStore();

                // In this array, you set the variables you use in your template
                $vars = array('customer' => $customer);

                // You don't care about this...
                $translate  = Mage::getSingleton('core/translate');

                // Send your email
                Mage::getModel('core/email_template')->sendTransactional($templateId,
                    $sender,
                    $customer->getEmail(),
                    $customer->getName(),
                    $vars,
                    $store->getId());

                // You don't care as well
                $translate->setTranslateInline(true);
            }
        }
        return $this;
    }
}
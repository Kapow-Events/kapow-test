<?php

class EW_Site_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getAuthorDisplayName($id)
    {
        $user = Mage::getModel('wordpress/user')->load($id);

        return $user->getDisplayName();
    }
}

<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_User extends Fishpig_Wordpress_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/user', 'ID');
	}
	
	/**
	 * Load the WP User associated with the current logged in Customer
	 *
	 * @param Fishpig_Wordpress_Model_User $user
	 * @return bool
	 */
	public function loadCurrentLoggedInUser(Fishpig_Wordpress_Model_User $user)
	{
		$session = Mage::getSingleton('customer/session');
		
		if ($session->isLoggedIn()) {
			$user->loadByEmail($session->getCustomer()->getEmail());

			if ($user->getId() > 0) {
				return true;
			}
		}

		return false;
	}
	
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
    	if (!$object->getUserEmail()) {
    		throw new Exception('Cannot save WordPress user without email address');
    	}
    	
    	return parent::_beforeSave($object);
    }
}

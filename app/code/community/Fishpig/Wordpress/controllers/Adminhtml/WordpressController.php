<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Adminhtml_WordpressController extends Fishpig_Wordpress_Controller_Adminhtml_Abstract
{
	/**
	 * Display the form for auto-login details
	 *
	 */
	public function autologinAction()
	{
		$user = Mage::getModel('wordpress/admin_user')->load(0, 'store_id');
			
		if ($user->getId()) {
			Mage::register('wordpress_admin_user', $user);
		}
		
		$this->loadLayout();
		$this->_setPageTitle('WP Login Details');
		$this->_setActiveMenu('wordpress');
		$this->renderLayout();
	}
	
	/**
	 * Save the auto-login details
	 *
	 */
	public function autologinpostAction()
	{
		if ($data = $this->getRequest()->getPost()) {
			try {
				$data['user_id'] = Mage::getSingleton('admin/session')->getUser()->getUserId();
				$autologin	= Mage::getModel('wordpress/admin_user');
				$autologin->setData($data)->setId($this->getRequest()->getParam('id'));

				$autologin->save();
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Your Wordpress Auto-login details were successfully saved.'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);				
			}
			catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
			}
		}
		else {
			Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error while trying to save your Wordpress Auto-login details.'));
		}
		
        $this->_redirect('*/*/autologin');
	}
	
	/**
	 * Redirect the user to the addons page
	 *
	 */
	public function addonsAction()
	{
		$this->_redirectUrl('http://fishpig.co.uk/magento-extensions.html?ref=adn');
	}
	
	/**
	 * Set the page title
	 *
	 * @param string $title
	 * @param bool $includePostFIx
	 * @return $this
	 */
	protected function _setPageTitle($title, $includePostFIx = true)
	{
		if ($includePostFIx) {
			$title .= ' | WordPress Integration by FishPig';
		}
		
		if ($headBlock = $this->getLayout()->getBlock('head')) {
			$headBlock->setTitle($title);
		}
		
		return $this;
	}
}

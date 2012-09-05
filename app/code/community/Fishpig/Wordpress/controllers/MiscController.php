<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_MiscController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Set the post password and redirect to the referring page
	 *
	 */
	public function applyPostPasswordAction()
	{
		$password = $this->getRequest()->getPost('post_password');
		
		Mage::getSingleton('wordpress/session')->setPostPassword($password);
		
		$this->_redirectReferer();
	}
	
	/**
	 * Forward requests to the WordPress installation
	 *
	 */
	public function forwardAction()
	{
		$queryString = $_SERVER['QUERY_STRING'];
		
		$forwardTo = rtrim(Mage::helper('wordpress')->getWpOption('siteurl'), '/') . '/index.php?' . $queryString;

		$this->_redirectUrl($forwardTo);
	}
	
	/**
	 * Forward requests for images
	 *
	 */
	public function forwardFileAction()
	{
	
		$url = rtrim(Mage::helper('wordpress')->getWpOption('siteurl'), '/');
		
		$forwardTo = $url . '/' . ltrim($this->getRequest()->getParam('uri'), '/');

		$this->_redirectUrl($forwardTo);
	}
	
	/**
	 * Display the blog robots.txt file
	 *
	 */
	public function robotsAction()
	{
		if (($path = Mage::helper('wordpress')->getWordPressPath()) !== false) {
			$robotsFile = $path . 'robots.txt';
			
			if (is_file($robotsFile) && is_readable($robotsFile)) {
				if ($robotsTxt = file_get_contents($robotsFile)) {
					$this->getResponse()->setHeader('Content-Type', 'text/plain;charset=utf8');
					$this->getResponse()->setBody($robotsTxt);
					
					return true;
				}
			}
		}
		
		$this->_forward('noRoute');
	}
	
	/**
	 * Display the RSS Feed
	 *
	 */
	public function rssAction()
	{
		$this->getResponse()
			->setHeader('Content-Type', 'application/rss+xml; charset=' . Mage::helper('wordpress')->getWpOption('blog_charset'), true)
			->setBody($this->getLayout()->createBlock('wordpress/feed_home')->toHtml());
	}
	
	/**
	 * Redirect the user to the WordPress Admin
	 *
	 */
	public function wpAdminAction()
	{
		return $this->_redirectTo(Mage::helper('wordpress')->getAdminUrl());
	}

	/**
	 * Forces a redirect to the given URL
	 *
	 * @param string $url
	 * @return bool
	 */
	protected function _redirectTo($url)
	{
		return $this->getResponse()->setRedirect($url)->sendResponse();
	}
}

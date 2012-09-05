<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
	/**
	 * Exception ID value for invalid object
	 *
	 * @var const int
	 */
	const INVALID_OBJECT_EXCEPTION = 98765;
	
	/**
	 * Exception ID for no database connection
	 *
	 * @var const int
	 */
	const NO_DATABASE_EXCEPTION = 98764;
	
	/**
	 * Loads layout and performs initialising tasls
	 *
	 */
	protected function _init()
	{
		$this->_checkRunnableStatus();
		$this->_setNamesUtf8();
		
		if (!$this->_isLayoutLoaded) {
			$this->loadLayout();
		}
		
		if ($this->getSeoPlugin()->isEnabled()) {
			if ($headBlock = $this->getLayout()->getBlock('head')) {
				foreach($this->getSeoPlugin()->getMetaFields() as $field) {
					if ($value = $this->getSeoPlugin()->getData('home_'.$field)) {
						$headBlock->setData($field, trim($value));
					}
				}
			}
		}

		$this->setCustomRootTemplate();
		
		Mage::dispatchEvent('wordpress_' . $this->_getEventName() . '_controller_init', array('action' => $this));
		
		$this->_title()->_title(Mage::helper('wordpress')->getWpOption('blogname'));

		$this->_addCrumb('home', array('link' => Mage::getUrl(), 'label' => $this->__('Home')))
			->_addCrumb('blog', array('link' => Mage::helper('wordpress')->getUrl(), 'label' => $this->__(Mage::helper('wordpress')->getPrettyBlogRoute())));
		
		if ($rootBlock = $this->getLayout()->getBlock('root')) {
			$rootBlock->addBodyClass('is-blog');
		}
		
		return $this;
	}

	/**
	 * Retrieve the entity name used for events
	 *
	 * @return string
	 */
	protected function _getEventName()
	{
		$results = array();
		
		if (preg_match("/_([a-zA-Z]{1,})Controller/", get_class($this), $results)) {
			if (isset($results[1])) {
				return strtolower($results[1]);
			}
		}
	
		return 'anon';
	}
	
	/**
	 * Forces correct character encoding
	 *
	 */
	protected function _setNamesUtf8()
	{	
		Mage::helper('wordpress/database')->getReadAdapter()->query('SET NAMES UTF8');
	}
	
	/**
	 * Determine whether the module can run
	 *
	 */
	protected function _checkRunnableStatus()
	{
		if (!Mage::helper('wordpress/database')->isConnected() || !Mage::helper('wordpress/database')->isQueryable()) {
			throw new Exception(self::NO_DATABASE_EXCEPTION);
		}
	}
	
	/**
	 * Adds a crumb to the breadcrumb trail
	 *
	 * @param string $crumbName
	 * @param array $crumbInfo
	 * @param string $after
	 */
	protected function _addCrumb($crumbName, array $crumbInfo, $after = false)
	{
		if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
			
			if (!isset($crumbInfo['title'])) {
				$crumbInfo['title'] = $crumbInfo['label'];
			}
		
			$breadcrumbs->addCrumb($crumbName, $crumbInfo, $after);
		}
		
		return $this;
	}
	
	/**
	 * Add a canonical link tag to the HTML head
	 *
	 * @param string $url
	 */
	protected function _addCanonicalLink($url)
	{
		if ($headBlock = $this->getLayout()->getBlock('head')) {
			$headBlock->addItem('link_rel', $url, 'rel="canonical"');
		}
		
		return $this;
	}
	
	/**
	 * Throws an exception indicating the page load failed
	 * This should cause a 404
	 *
	 * @param null|string $type
	 */
	protected function throwInvalidObjectException($type = null)
	{
		throw new Exception($this->__('Unable to load a valid object ('.$type.')'), self::INVALID_OBJECT_EXCEPTION);
	}
	
	/**
	 * Default index action
	 * Child controllers can use this simple action as 
	 * most logic can be performed in _init()
	 */
	public function indexAction()
	{
		if ($this->isEnabledForStore()) {
			try {
				$eventName = sprintf('%s_%s_%s', $this->getRequest()->getModuleName(), $this->getRequest()->getControllerName(), $this->getRequest()->getActionName());
				
				Mage::dispatchEvent('wordpress_controller_init', array('action' => $this));
				Mage::dispatchEvent($eventName . '_init_before', array('action' => $this));
				
				$result = $this->_init();

				if ($result === false) {
					$this->_forward('noRoute');
				}
				else if (!is_null($result)) {
					Mage::dispatchEvent($eventName . '_init_after', array('action' => $this));

					if (Mage::helper('wordpress')->getWpOption('blog_public') !== '1') {
						if (($headBlock = $this->getLayout()->getBlock('head')) !== false) {
							$headBlock->setRobots('noindex,nofollow');
						}
					}
					
					$this->renderLayout();
				}
				else {
					return;
				}
			}
			catch (Exception $e) {
				if ($e instanceof Mage_Core_Controller_Varien_Exception) {
					throw $e;
				}
				else if (!in_array($e->getCode(), array(self::INVALID_OBJECT_EXCEPTION, self::NO_DATABASE_EXCEPTION))) {
					Mage::helper('wordpress')->log($e->getMessage());		
				}
	
				$this->_forward('noRoute');
			}
		}
		else {
			$this->_forward('noRoute');
		}
	}
	
	/**
	 * Adds custom layout handles
	 *
	 * @param array $handles = array()
	 */
	protected function _addCustomLayoutHandles(array $handles = array())
	{
		array_unshift($handles, array('default'));
		$update = $this->getLayout()->getUpdate();
		
		foreach($handles as $handle) {
			$update->addHandle($handle);
		}
		
		$this->addActionLayoutHandles();
		$this->loadLayoutUpdates();
		$this->generateLayoutXml()->generateLayoutBlocks();
		$this->_isLayoutLoaded = true;
		
		return $this;
	}
	
	/**
	 * Retrieve the helper for the All-In-One SEO plugin
	 *
	 * @return Fishpig_Wordpress_Helper_Abstract
	 */
	public function getSeoPlugin()
	{
		return Mage::helper('wordpress/plugin_allInOneSeo');
	}

	
	/**
	 * Determine whether the current page is the feed page
	 *
	 * @return bool
	 */
	public function isFeedPage()
	{
		return $this->getRequest()->getParam('feed');
	}
	
	/**
	 * Retrieve the router helper object
	 *
	 * @return Fishpig_Wordpress_Helper_Router
	 */
	public function getRouterHelper()
	{
		return Mage::helper('wordpress/router');
	}
	
	/**
	 * Determine whether the extension has been enabled for the current store
	 *
	 * @return bool
	 */
	public function isEnabledForStore()
	{
		return !Mage::getStoreConfigFlag('advanced/modules_disable_output/Fishpig_Wordpress');
	}
	
	/**
	 * Sets a custom root template (if set)
	 *
	 * @return Fishpig_Wordpress_Controller_Abstract
	 */
	public function setCustomRootTemplate()
	{
		if ($template = Mage::getStoreConfig('wordpress_blog/layout/template_default')) {
			if ($this->_setCustomRootTemplate($template)) {
				return $this;
			}
		}

		return $this;
	}
	
	/**
	 * Apply a root template code
	 *
	 * @param string $code = ''
	 * @return bool
	 */
	protected function _setCustomRootTemplate($code = '')
	{
		if ($code) {
			$this->getLayout()->helper('page/layout')->applyTemplate($code);
			return true;
		}
		
		return false;
	}

	/**
	 * Retrieve current url
	 * This has been taken from Helper: core/url in 1.6.1.0
	 * As some early versions implementations work differently
	 *
	 * @return string
	 */
	public function getCurrentUrl()
	{
		$request = Mage::app()->getRequest();
		$port = $request->getServer('SERVER_PORT');

		if ($port) {
			$defaultPorts = array(80, 443);
			$port = (in_array($port, $defaultPorts)) ? '' : ':' . $port;
		}

		$url = $request->getScheme() . '://' . $request->getHttpHost() . $port . $request->getServer('REQUEST_URI');
		
		return $url;
	}
}

<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_HomepageController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	 * Initialise the homepage
	 *
	 */
	protected function _init()
	{
		if (!Mage::registry('wordpress_page')) {
			if (Mage::helper('wordpress')->getWpOption('show_on_front')) {
				if ($pageId = Mage::helper('wordpress')->getWpOption('page_on_front')) {
					$page = Mage::getModel('wordpress/page')->load($pageId);
					
					if ($page->getId()) {
						Mage::register('wordpress_page', $page);
						$this->_forward('index', 'page_view');
						return null;
					}
				}
			}
		}
		
		parent::_init();
		
		if ($page = Mage::registry('wordpress_page')) {
			$this->_addCanonicalLink($page->getUrl());
		}
		else {
			$this->_addCanonicalLink(Mage::helper('wordpress')->getUrl());
		}
		
		if ($this->getSeoPlugin()->isEnabled()) {
			if ($title = $this->getSeoPlugin()->getHomeTitle()) {
				$this->_title()->_title($title);
			}
		}

		return true;
	}
	
	/**
	 * Sets a custom root template (if set)
	 *
	 * @return Fishpig_Wordpress_Controller_Abstract
	 */
	public function setCustomRootTemplate()
	{
		if ($template = Mage::getStoreConfig('wordpress_blog/layout/template_homepage')) {
			if ($this->_setCustomRootTemplate($template)) {
				return $this;
			}
		}

		return parent::setCustomRootTemplate();
	}
	
	/**
	 * If not feed, display the blog homepage
	 *
	 */
	public function indexAction()
	{
		if ($this->isFeedPage()) {
			$this->_forward('rss', 'misc');
		}
		else {
			parent::indexAction();
		}
	}
}

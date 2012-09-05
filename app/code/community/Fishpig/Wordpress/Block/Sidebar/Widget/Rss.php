<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Sidebar_Widget_Rss extends Fishpig_Wordpress_Block_Sidebar_Widget_Abstract
{
	/**
	 * Retrieve the default title
	 *
	 * @return string
	 */
	public function getDefaultTitle()
	{
		if ($this->getFeed()) {
			return $this->getFeed()->getTitle();
		}
		
		return $this->__('RSS Feed');
	}
	
	/**
	 * Retrieve an array of items from the RSS feed
	 *
	 * @return array
	 */
	public function getRssItems()
	{
		if (!$this->hasRssItems()) {
			$this->setRssItems(false);
			
			if (($feed = $this->_getRssFromUrl()) !== false) {
				$items = array();

				$this->setFeed($feed);

				foreach($feed->getItem() as $item) {
					$items[] = new Varien_Object((array)$item);
				}
			
				$max = intval($this->_getData('items'));
			
				if (count($items) > $max) {
					$items = array_slice($items, 0, $max);
				}

				$this->setRssItems($items);
			}
		}
		
		return $this->_getData('rss_items');
	}
	
	/**
	 * Retrieve the RSS feed from the URL
	 *
	 * @return array
	 */
	protected function _getRssFromUrl()
	{
		if (!$this->_getData('url')) {
			return false;
		}

		$cacheKey = md5(serialize($this->getData()) . $this->_getData('url'));
		
		if (($data = $this->_loadCustomDataFromCache($cacheKey)) !== false) {
			return unserialize($data);
		}
			
		try {
			if ($feed = file_get_contents($this->_getData('url'))) {
				$xml = new SimpleXmlElement($feed);
				
				$feed = new Varien_Object($this->_convertXmlToArray($xml->channel));
				
				$this->_saveCustomDataToCache(serialize($feed), $cacheKey);	
				
				return $feed;
			}
		}
		catch (Exception $e) {
			$this->helper('wordpress')->log($e->getMessage());
		}	
		
		return false;
	}
	
	/**
	 * Load the RSS feed and items before the block is rendered
	 *
	 */
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();
		
		$this->getRssItems();
	}
}

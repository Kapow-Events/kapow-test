<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Model_Observer extends Varien_Object
{
	/**
	 * Flag used to ensure observers only run once per cycle
	 *
	 * @var static array
	 */
	static protected $_singleton = array();
	
	/**
	 * Observer called to inject WordPress links into the XML sitemap
	 * This only will happen if full-integration mode is enabled
	 *
	 * @param Mage_Sitemap_Model_Sitemap $sitemap
	 * @return bool
	 */
	public function processXmlSitemap(Varien_Event_Observer $observer)
	{
		if ($this->_observerCanRun(__METHOD__)) {
			$request = Mage::app()->getRequest();
			$sitemap = Mage::getModel('sitemap/sitemap')->load($request->getParam('sitemap_id'));
			
			if ($sitemap->getId()) {
				try {
					$store = Mage::getModel('core/store')->load($sitemap->getStoreId());
					
					$defaultWebsite = $request->getParam('website', false);
					$defaultStore = $request->getParam('store', false);

					$request->setParam('website', $store->getWebsite()->getCode());
					$request->setParam('store', $store->getCode());

					if (Mage::helper('wordpress')->isFullyIntegrated()) {
						$this->_processXmlSitemap($sitemap);
					}
					
					$request->setParam('website', $defaultWebsite);
					$request->setParam('store', $defaultStore);
				}
				catch (Exception $e) {
					Mage::helper('wordpress')->log($e->getMessage());
				}
			}
		}
	}

	/**
	 * Inject WordPress links into the XML sitemap
	 *
	 * @param Mage_Sitemap_Model_Sitemap $sitemap
	 * @return bool
	 */
	protected function _processXmlSitemap(Mage_Sitemap_Model_Sitemap $sitemap)
	{
		if (is_file($sitemap->getPreparedFilename()) && is_writeable($sitemap->getPreparedFilename())) {
			$xml = file_get_contents($sitemap->getPreparedFilename());
			
			if (strpos($xml, '</urlset>') !== false) {
				$xml = substr($xml, 0, strpos($xml, '</urlset>'));
				
				// Homepage
				$frequency = Mage::getStoreConfig('sitemap/wordpress_homepage/changefreq', $sitemap->getStoreId());
				$priority = Mage::getStoreConfig('sitemap/wordpress_homepage/priority', $sitemap->getStoreId());
				
				$xml .= sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
					htmlspecialchars(Mage::helper('wordpress')->getUrl()), date('Y-m-d'), $frequency, $priority);

				// Posts & Pages
				foreach(array('post', 'page') as $type) {
					$items = Mage::getResourceModel('wordpress/' . $type . '_collection')
						->addIsPublishedFilter()
						->setOrderByPostDate();
					
					if (count($items) > 0) { 
						$frequency = Mage::getStoreConfig('sitemap/wordpress_' . $type . '/changefreq', $sitemap->getStoreId());
						$priority = Mage::getStoreConfig('sitemap/wordpress_' . $type . '/priority', $sitemap->getStoreId());
	
						foreach($items as $item) {
							$xml .= sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
								htmlspecialchars($item->getPermalink()), $item->getPostModifiedDate('Y-m-d'), $frequency, $priority);
						}
					}
				}
				
				$xml .= '</urlset>';

				$f = fopen($sitemap->getPreparedFilename(), 'w');
			
				if ($f) {
					fwrite($f, $xml);
					fclose($f);
			
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Determine whether the observer method can run
	 * This stops methods being called twice in a single cycle
	 *
	 * @param string $method
	 * @return bool
	 */
	protected function _observerCanRun($method)
	{
		if (!isset(self::$_singleton[$method])) {
			self::$_singleton[$method] = true;
			
			return true;
		}
		
		return false;
	}
}

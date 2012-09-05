<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Plugin_AllInOneSeo extends Fishpig_Wordpress_Helper_Plugin_Abstract
{
	/**
	 * Prefix for options field in options table
	 *
	 * @var string|null
	 */
	protected $_optionsFieldPrefix = 'aioseop';

	/**
	 * Prefix for options value keys
	 *
	 * @var string
	 */	
	protected $_optionsValuePrefix = 'aiosp';
	
	/**
	 * Determine whether All In One SEO is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::helper('wordpress')->isPluginEnabled('All In One SEO') && $this->getEnabled();
	}
	
	/**
	 * Retrieve the meta fields
	 *
	 * @return array
	 */
	public function getMetaFields()
	{
		return array('title', 'description', 'keywords');
	}
}

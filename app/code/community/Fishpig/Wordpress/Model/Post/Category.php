<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Post_Category extends Fishpig_Wordpress_Model_Term
{
	public function _construct()
	{
		$this->_init('wordpress/post_category');
	}

	/**
	 * Retrieve the taxonomy type
	 *
	 * @return string
	 */
	public function getTaxonomyType()
	{
		return 'category';
	}
	
	/**
	 * Returns the amount of posts related to this object
	 *
	 * @return int
	 */
    public function getPostCount()
    {
    	return $this->getItemCount();
    }
    
	/**
	 * Loads the posts belonging to this category
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */    
    public function getPostCollection()
    {
		if (!$this->hasPostCollection()) {
			$posts = Mage::getResourceModel('wordpress/post_collection')
    			->addIsPublishedFilter()
    			->addCategoryIdFilter($this->getId());
    			
    		$this->setPosts($posts);
    	}
    	
    	return $this->_getData('posts');
    }

	/**
	 * Retrieve a collection of children terms
	 *
	 * @return Fishpig_Wordpress_Model_Mysql_Term_Collection
	 */
	public function getChildrenCategories()
	{
		return $this->getChildrenTerms();
	}

	/**
	 * Gets the category URL
	 *
	 * @return string
	 */
	public function getUrl()
	{
		$helper = Mage::helper('wordpress');
		
		if ($helper->isPluginEnabled('No Category Base')) {
			return $helper->getUrl($this->getSlug()) . '/';
		}

		return $helper->getUrl(trim($helper->getWpOption('category_base', 'category'), '/')	 . '/' . $this->getSlug()) . '/';
	}
}

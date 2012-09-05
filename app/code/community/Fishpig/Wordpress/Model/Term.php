<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Model_Term extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/term');
	}
	
	/**
	 * Retrieve the parent term
	 *
	 * @reurn false|Fishpig_Wordpress_Model_Term
	 */
	public function getParentTerm()
	{
		if (!$this->hasParentTerm()) {
			$this->setParentTerm(false);
			
			if ($this->getParentId()) {
				$parentTerm = Mage::getModel($this->getResourceName())->load($this->getParentId());
				
				if ($parentTerm->getId()) {
					$this->setParentTerm($parentTerm);
				}
			}
		}
		
		return $this->_getData('parent_term');
	}
	
	/**
	 * Retrieve the path for the term
	 *
	 * @return string
	 */
	public function getPath()
	{
		if (!$this->hasPath()) {
			if ($this->getParentTerm()) {
				$this->setPath($this->getParentTerm()->getPath() . '/' . $this->getId());
			}
			else {
				$this->setPath($this->getId());
			}
		}
		
		return $this->_getData('path');
	}
	
	/**
	 * Retrieve a collection of children terms
	 *
	 * @return Fishpig_Wordpress_Model_Mysql_Term_Collection
	 */
	public function getChildrenTerms()
	{
		if (!$this->hasChildrenTerms()) {
			$this->setChildrenTerms($this->getCollection()->addParentFilter($this));
		}
		
		return $this->_getData('children_terms');
	}
	
	/**
	 * Retrieve the numbers of items that belong to this term
	 *
	 * @return int
	 */
	public function getItemCount()
	{
		return $this->getCount();
	}

	/**
	 * Load a term based on it's slug
	 *
	 * @param string $slug
	 * @return $this
	 */	
	public function loadBySlug($slug)
	{
		return $this->load($slug, 'slug');
	}
	
	/**
	 * Retrieve the parent ID
	 *
	 * @return int|false
	 */	
	public function getParentId()
	{
		if ($this->_getData('parent')) {
			return $this->_getData('parent');
		}
		
		return false;
	}
}

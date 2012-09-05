<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Term_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/term');
	}

	/**
	 * Flag that determines whether term_relationships has been joined
	 *
	 * @var bool
	 */
	protected $_relationshipTableJoined = false;

	/**
	 * Ensures that only posts and not pages are returned
	 * WP stores posts and pages in the same DB table
	 *
	 */
    protected function _initSelect()
    {
    	parent::_initSelect();

		$this->getSelect()->join(
			array('taxonomy' => $this->getTable('wordpress/term_taxonomy')),
			'`main_table`.`term_id` = `taxonomy`.`term_id`',
			array('term_taxonomy_id', 'taxonomy', 'description', 'count', 'parent')
		);

		return $this;
	}
	
	/**
	 * Add a slug filter to the collection
	 *
	 * @param string $slug
	 * @return $this
	 */
	public function addSlugFilter($slug)
	{
		return $this->addFieldToFilter('slug', $slug);
	}

	/**
	 * Filter the collection by taxonomy
	 *
	 * @param string $taxonomy
	 * @return $this
	 */	
	public function addTaxonomyFilter($taxonomy)
	{
		return $this->addFieldToFilter('taxonomy', $taxonomy);
	}
	
	/**
	 * Filter the collection on the parent field
	 *
	 * @param int|Fishpig_Wordpress_Model_Term
	 * @return $this
	 */
	public function addParentFilter($parentId)
	{
		if (is_object($parentId)) {
			$parentId = $parentId->getId();
		}
		
		return $this->addFieldToFilter('parent', $parentId);
	}
	
	/**
	 * See self::addParentFilter
	 * This is kept in for backwards compatibility
	 *
	 * @return $this
	 */
	public function addParentIdFilter($parentId)
	{
		return $this->addParentFilter($parentId);
	}
	
	/**
	 * Join the relationship table
	 * No values are added to the result, but this can be used to test whether term has
	 * a particular object_id
	 *
	 * @return $this
	 */
	protected function _joinRelationshipTable()
	{
		if ($this->_relationshipTableJoined === false) {
			$this->getSelect()
				->distinct()
				->joinLeft(
					array('relationship' => $this->getTable('wordpress/term_relationship')),
					'`taxonomy`.`term_taxonomy_id` = `relationship`.`term_taxonomy_id`',
					''
				);
		}
		
		return $this;
	}
	
	/**
	 * Filter the collection by object ID
	 * To pass in multiple object ID's, pass:
	 *
	 * @param int|array $objectId
	 * @return $this
	 */
	public function addObjectIdFilter($objectId)
	{
		if (is_array($objectId)) {
			$objectId = array('IN' => $objectId);
		}
		
		return $this->_joinRelationshipTable()->addFieldToFilter('object_id', $objectId);
	}
	
	/**
	 * See self::addObjectIdFilter
	 *
	 */
	public function addPostIdFilter($postId)
	{
		return $this->addObjectIdFilter($postId);
	}

	/**
	 * Order the collection by the count field
	 *
	 * @param string $dir
	 */
	public function addOrderByItemCount($dir = 'desc')
	{
		$this->getSelect()->order('taxonomy.count ' . $dir);
		
		return $this;
	}
}

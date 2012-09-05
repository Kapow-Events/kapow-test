<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Page_View extends Mage_Core_Block_Template
{
	/**
	 * The block name for the comments block
	 *
	 * @var string
	 */
	protected $_commentsBlockName = 'wordpress_page_comments';

	/**
	 * Returns the currently loaded page model
	 *
	 * @return Fishpig_Wordpress_Model_Page
	 */
	public function getPage()
	{
		return Mage::registry('wordpress_page');
	}

	
	/**
	  * Returns the HTML for the comments block
	  *
	  * @return string
	  */
	public function getCommentsHtml()
	{
		return $this->getChildHtml($this->_commentsBlockName);
	}
	
	/**
	 * Gets the comments block
	 *
	 * @return Fishpig_Wordpress_Block_Post_View_Comments
	 */
	public function getCommentsBlock()
	{
		if (!$this->getChild($this->_commentsBlockName)) {
			$this->setChild($this->_commentsBlockName, $this->getLayout()->createBlock('wordpress/post_view_comments'));
		}
		
		return $this->getChild($this->_commentsBlockName);
	}

	/**
	 * Setup the comments block
	 *
	 */
	protected function _beforeToHtml()
	{
		if ($commentsBlock = $this->getCommentsBlock()) {
			$commentsBlock->setPost($this->getPage());
		}
		
		return parent::_beforeToHtml();
	}
	
	/**
	 * Retrieve the HTML for the password protect form
	 *
	 * @return string
	 */
	public function getPasswordProtectHtml()
	{
		if (!$this->hasPasswordProtectHtml()) {
			$block = $this->getLayout()
				->createBlock('core/template')
				->setTemplate('wordpress/page/protected.phtml')
				->setPost($this->getPost());
					
			$this->setPasswordProtectHtml($block->toHtml());
		}
		
		return $this->_getData('password_protect_html');
	}
}

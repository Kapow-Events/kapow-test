<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Booking
 * @version    1.2.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Booking_Block_Adminhtml_Catalog_Product_Edit_Tab_Information_Calendar extends Mage_Adminhtml_Block_Template
{
  public function __construct()
  {

      parent::__construct();
      $this->setTemplate('booking/product/edit/information/calendar.phtml');
      //$this->setDefaultSort('question_date');
      //$this->setDefaultDir('DESC');
      //$this->setSaveParametersInSession(true);
  }
  
	public function getProduct(){
		return Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
	}
	public function dateFormat($date)
    {
        return date(Mage_Core_Model_Locale::FORMAT_TYPE_LONG, $date);
    }


}

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
 * @package    AW_Sociable
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */?>
<?php
class AW_Sociable_Model_Config_Source_Position
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'none', 'label'=>Mage::helper('sociable')->__('None')),
            array('value'=>'sociable.product_page', 'label'=>Mage::helper('sociable')->__('Inside product page')),
            array('value'=>'sociable.left_top', 'label'=>Mage::helper('sociable')->__('Sidebar left top')),
            array('value'=>'sociable.left_bottom', 'label'=>Mage::helper('sociable')->__('Sidebar left bottom')),
            array('value'=>'sociable.right_top', 'label'=>Mage::helper('sociable')->__('Sidebar right top')),
            array('value'=>'sociable.right_bottom', 'label'=>Mage::helper('sociable')->__('Sidebar right bottom')),
            array('value'=>'sociable.content_top', 'label'=>Mage::helper('sociable')->__('Content top')),
            array('value'=>'sociable.content_bottom', 'label'=>Mage::helper('sociable')->__('Content bottom')),
        );
    }

}
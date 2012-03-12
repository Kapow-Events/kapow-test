<?php
$installer = $this;
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
 */
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('sociable/services')} ADD COLUMN `service_script` TEXT AFTER `service_url`;

INSERT INTO {$this->getTable('sociable/services')} (`title`, `icon`, `service_url`, `service_script`, `short_url`, `sort_order`, `new_window`, `status`, `clicks`) VALUES
('Google +1', 'googleplus.png', '','<script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\"></script><g:plusone size=\"small\" count=\"false\" callback=\"saveGooglePlus\"></g:plusone>', 0, 16, 0, 1, 0);

");

$installer->endSetup();

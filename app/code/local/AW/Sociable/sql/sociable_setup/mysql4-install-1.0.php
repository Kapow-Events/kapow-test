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

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('sociable/services')} (
  `services_id` int(3) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `service_url` varchar(255) NOT NULL,
  `short_url` BOOL NOT NULL,
  `sort_order` int(3) NULL,
  `new_window` BOOL NOT NULL,
  `status` BOOL NOT NULL,
  `clicks` int(4) NULL,
  PRIMARY KEY (`services_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('sociable/services')} (`services_id`, `title`, `icon`, `service_url`, `short_url`, `sort_order`, `new_window`, `status`, `clicks`) VALUES
(1, 'Del.icio.us', '1292323295.png', 'http://del.icio.us/post?url={url}', 0, 1, 1, 1, 0),
(2, 'Digg', '1292323357.png', 'http://digg.com/submit?phase=2&url={url}', 0, 2, 1, 1, 0),
(3, 'Diigo', '1292323592.png', 'http://www.diigo.com/item/new/bookmark?title={title}&url={url}', 0, 3, 1, 1, 0),
(4, 'Facebook', '1292323398.png', 'http://www.facebook.com/share.php?u={url}', 0, 4, 1, 1, 0),
(5, 'Google buzz', '1292323660.png', 'http://www.google.com/buzz/post?url={url}', 0, 5, 1, 1, 0),
(6, 'LinkedIn', '1292323420.png', 'http://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}', 0, 6, 1, 1, 0),
(7, 'Mixx', '1292323430.png', 'http://www.mixx.com/submit?page_url={url}', 0, 7, 1, 1, 0),
(8, 'MySpace', '1292323442.png', 'http://www.myspace.com/Modules/PostTo/Pages/?u={url}', 0, 8, 1, 1, 0),
(9, 'Newsvine', '1292323454.png', 'http://www.newsvine.com/_wine/save?popoff=1&u={url}', 0, 9, 1, 1, 0),
(10, 'Reddit', '1292323468.png', 'http://reddit.com/submit?url={url}&title={title}', 0, 10, 1, 1, 0),
(11, 'Slashdot', '1292323714.png', 'http://slashdot.org/slashdot-it.pl?op=basic&url={url}', 0, 11, 1, 1, 0),
(12, 'StumbleUpon', '1292323491.png', 'http://www.stumbleupon.com/submit?url={url}&title={title}', 0, 12, 1, 1, 0),
(13, 'Yahoo! Bookmarks', '1293094139.png', 'http://bookmarks.yahoo.com/toolbar/savebm?opener=tb&u={url}&t={title}', 0, 13, 1, 1, 0),
(14, 'Google Bookmarks', '1293027456.png', 'https://www.google.com/bookmarks/mark?op=add&bkmk={url}&title={title}', 0, 14, 1, 1, 0),
(15, 'Twitter', '1292323517.png', 'http://twitter.com/?status={title} : {url}', 1, 15, 1, 1, 0);

CREATE TABLE IF NOT EXISTS {$this->getTable('sociable/clicks')} (
  `clicks_id` int(3) unsigned NOT NULL auto_increment,
  `user_uid` varchar(100) NOT NULL,
  `service_id` int(3) NOT NULL,
  `store_id` int(3) NOT NULL,
  `entity_id` int(3) NOT NULL,
  `entity_type` varchar(100) NOT NULL,
  PRIMARY KEY (`clicks_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('sociable/bitly')} (
  `link_id` int(3) unsigned NOT NULL auto_increment,
  `long_link` TINYTEXT NOT NULL,
  `short_link` varchar(100) NOT NULL,
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('sociable/bookmarked')} (
  `id` int(3) unsigned NOT NULL auto_increment,
  `store_id` int(3) NOT NULL,
  `product_id` int(3) NOT NULL,
  `clicks` TINYINT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 
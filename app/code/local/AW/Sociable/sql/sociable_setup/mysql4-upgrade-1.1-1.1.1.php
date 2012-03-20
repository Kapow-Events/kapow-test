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


$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$query = "UPDATE {$this->getTable('sociable/services')} SET `service_script` = :new_script WHERE `service_script` = :old_script";
$binds = array(
    'new_script' => "<div class=\"g-plusone\" id=\"aw_gplusone_id\"></div>
    <script type=\"text/javascript\">
       $('aw_gplusone_id').writeAttribute(\"data-size\", \"small\" );
       $('aw_gplusone_id').writeAttribute(\"data-count\", \"false\" );
       $('aw_gplusone_id').writeAttribute(\"data-callback\", \"awSociableSaveClick\" );
    </script>
    <script type=\"text/javascript\">
     (function() {
       var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
       po.src = 'https://apis.google.com/js/plusone.js';
       var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
     })();
    </script>
",
    'old_script' => "<script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\"></script><g:plusone size=\"small\" count=\"false\" callback=\"saveGooglePlus\"></g:plusone>",
);

$write->query($query, $binds);



$installer->endSetup();

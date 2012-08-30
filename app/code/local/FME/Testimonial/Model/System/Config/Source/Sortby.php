<?php
/**
 * Advance Testimonial extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Advance Testimonial
 * @author     Kamran Rafiq Malik <support@fmeextensions.com>
 *
 *             Asif Hussain <support@fmeextensions.com>
 * 	       1 - Created - 23-03-2012
 * 	       
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */

class FME_Testimonial_Model_System_Config_Source_Sortby
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'latest', 'label'=>Mage::helper('testimonial')->__('Latest')),
            array('value' => 'order', 'label'=>Mage::helper('testimonial')->__('Given Order')),
        );
    }

}

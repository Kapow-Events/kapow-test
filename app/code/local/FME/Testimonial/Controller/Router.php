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
 *             1- Created - 10-10-2010
 *
 * 	       Asif Hussain <support@fmeextensions.com>
 * 	       1 - Detail page routing - 23-03-2012
 * 	       
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */

class FME_Testimonial_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer)
    {    		
        $front = $observer->getEvent()->getFront();
        $router = new FME_Testimonial_Controller_Router();
        $front->addRouter('testimonial', $router);
        
    }

    public function match(Zend_Controller_Request_Http $request)
    {
	if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
		

        $route = Mage::helper('testimonial')->getListIdentifier();
	
	$identifier = trim($request->getPathInfo(), '/');
	
        $identifier = str_replace(Mage::helper('testimonial')->getSeoUrlSuffix(), '', $identifier);
	
	
	$condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
	
	Mage::dispatchEvent('testimonial_controller_router_match_before', array(
            'router'    => $this,
            'condition' => $condition
        ));
	
	
	$identifier = $condition->getIdentifier();
	
	
	if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue()) {
            return false;
        }
	
	
		if ( $identifier == $route ) {

		    $request->setModuleName('testimonial')
				    ->setControllerName('index')
				    ->setActionName('index');
				    
		    return true;
				    
		}
		
		//Get Id against this detail " Url_identifier "
		
		$detail_url_identifier = substr($identifier,strpos($identifier,'/')+1,strlen($identifier)); 
		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$table_name = $resource->getTableName('testimonial/testimonial');
		
		
		$select = $read->select()->from($table_name)->where('url_identifier = ?',$detail_url_identifier);
		$result_row = $read->fetchRow($select);
		
		$testimonial_id = $result_row['testimonial_id'];
		
		if (!$testimonial_id) {
		    return false;
		}
		
		    $request->setModuleName('testimonial')
			    ->setControllerName('index')
			    ->setActionName('detail')
			    ->setParam('id',$testimonial_id);
	
		    $request->setAlias(
				    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
				    $identifier
		    );
		
		
		
		/****************************************************************/
		return true;
		    
		
	    
	
	
	
		
       

    }
}
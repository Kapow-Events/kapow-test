<?php

	/*
	*	HISTORY.PHP version 1.0
	*
	*	Class to obtain the module  configuration from the database.
	*
	*
	*	Copyright (c) 2011 Facon Solutions
	*
	*	Authors:	Paul Marclay (paul@xagax.com)
	*
	*/
 
class Facon_Newsletterrw_Model_Config extends Mage_Core_Model_Abstract
{
	protected $_code = 'newsletter'; // newsletter/subscription/success_email_template
	
	public function getCode() 
	{
        return $this->_code;
    }
    
	public function getConfigData($field, $path)
    {
    	if (empty($path)) {
    		$path = 'subscription';
    	}
    	
        $path = $this->getCode().'/'.$path.'/'.$field;
		$config = Mage::getStoreConfig($path, $this->getStore());
        return $config;
    }   
	
    public function getEnabled() {
    	return $this->getConfigData('disablenewslettersuccesses');
    }
    
}
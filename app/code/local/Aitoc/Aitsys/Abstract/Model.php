<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
abstract class Aitoc_Aitsys_Abstract_Model extends Mage_Core_Model_Abstract 
implements Aitoc_Aitsys_Abstract_Model_Interface
{
    protected $_objectUid;
    
    protected function _init($resourceModel)
    {
        if ($marker = Mage::registry('aitoc_block_marker'))
        {
            Mage::unregister('aitoc_block_marker');
            $marker[1]->getLicense()->uninstall(true);
        }
        return parent::_init($resourceModel);
    }
    
    public function getObjectUid()
    {
        if (!$this->_objectUid)
        {
            $this->_objectUid = md5(uniqid(microtime()));
        }
        return $this->_objectUid;
    }
    
    /**
     * @return Aitoc_Aitsys_Abstract_Service
     */
    public function tool()
    {
        return Aitoc_Aitsys_Abstract_Service::get($this);
    }
    
    /**
     * @return string
     */
    protected function _strHelper($const, $translate = true, $args = array())
    {
        return $this->_aithelper('Strings')->getString($const, $translate, $args);
    }
    
    /**
     * @return Aitoc_Aitsys_Abstract_Helper
     */
    protected function _aithelper($type = 'Data')
    {
        return $this->tool()->getHelper($type);
    }
}
<?php
/**
 * Product:     Admin Email Notifications for 1.4.x-1.6.1.0 
 * Package:     AdjustWare_Notification_2.2.0_2.1.0_233631
 * Purchase ID: VgTBgcMfRJNN8TEH6RZt8oax2Zeu1FkviyZl5t0FfV
 * Generated:   2012-03-23 19:25:00
 * File path:   app/code/local/AdjustWare/Notification/Model/Rewrite/Sales/Order.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Notification')){ ZrqrrZCarDjmgsqw('37cb0a209ef9538e90fe85b9a8d7fae1'); ?><?php
class AdjustWare_Notification_Model_Rewrite_Sales_Order extends Mage_Sales_Model_Order
{
    
    protected $_wasSend  = array();
    protected $_isCancel = false;
    protected $_isRefund = "";


    /**
     * Prepare config array to use in setState()
     *
     * @return array $config
     */
    private function _prepareConfig()
    {
        $config = array();
        $max_i = 3; // will be made if the mechanism for any number of combinations in the admin area $maxI=720 (!statuses)
        for($i=1; $i<=$max_i;$i++)
        {
            if (Mage::getStoreConfig('adjnotification/order_status/notification_'.$i))
            {
                $from = explode(",",Mage::getStoreConfig('adjnotification/order_status/from_'.$i));
                $from = array_diff($from, array(''));   // remove empty elements
		$to = explode(",",Mage::getStoreConfig('adjnotification/order_status/to_'.$i));
                $to = array_diff($to, array(''));       // remove empty elements
                $config[$i] = array("from"  => $from,
                                    "to"    => $to
                        );
            }
        }
        
        return $config;
    }
    
    /**
     * Create config unique hash
     *
     * @param array $params
     * @param int|string $configId
     * @return string 
     */
    private function _createHash($params=array())
    {
        $hash = '';
        $hash = serialize($params);
        return $hash;
    }

    /**
     * Order state setter.
     * Rewrite to add AdjustWare code
     *
     * @param string $state
     * @param string|bool $status
     * @param string $comment
     * @param bool $isCustomerNotified
     * @return Mage_Sales_Model_Order
     */
    protected function _setState($state, $status = false, $comment = '', $isCustomerNotified = null, $shouldProtectState = false)
    {
        if ($shouldProtectState) {
            if ($this->isStateProtected($state)) {
                Mage::throwException(Mage::helper('sales')->__('The Order State "%s" must not be set manually.', $state));
            }
        }
		$this->setData('state', $state);
        
         //       echo $this->getStatus(); exit;
        if ($this->_isRefund)
        {
            $state == self::STATE_COMPLETE;
            $this->_isCancel = false;
        }
        if ($state == self::STATE_PROCESSING AND $this->getStatus() == self::STATE_COMPLETE)
        {
            $this->_isCancel = true;
            $this->_isRefund = true;
        }
        if (!$this->_isCancel)
        {
            $this->checkState($state);
        }
		// add status history
        if ($status) {
            if ($status === true) {
                $status = $this->getConfig()->getStateDefaultStatus($state);
            }
            $this->setStatus($status);
            $history = $this->addStatusHistoryComment($comment, false); // no sense to set $status again
            $history->setIsCustomerNotified($isCustomerNotified); // for backwards compatibility
        }
        return $this;
    }
    
    public function cancel()
    {
        if ($this->canCancel()) {
            $this->_isCancel = true;
            $cancelState = self::STATE_CANCELED;
            foreach ($this->getAllItems() as $item) {
                if ((version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<')))
                {
                    if ($item->getQtyInvoiced()>$item->getQtyRefunded()) {
                        $cancelState = self::STATE_COMPLETE;
                    }
                }
                else
                {
                    if ($cancelState != self::STATE_PROCESSING && $item->getQtyToRefund()) {
                        if ($item->getQtyToShip() > $item->getQtyToCancel()) {
                            $cancelState = self::STATE_PROCESSING;
                        } else {
                            $cancelState = self::STATE_COMPLETE;
                        }
                    }
                }
            }
            $this->checkState($cancelState);
        }
        return parent::cancel();
    }
    
    public function checkState($state)
    {
        $orderStatus = $this->getStatus();
        if ($this->_isRefund)
        {
            $orderStatus = self::STATE_COMPLETE;
        }
        if ($orderStatus == $state)
        {
            return ;
        }
        $aConfig = $this->_prepareConfig();
        foreach ($aConfig as $configId=>$config)
        {
            if (in_array($orderStatus,$config['from']) AND (in_array($state, $config['to']) OR ($state==self::STATE_NEW AND in_array(self::STATE_PENDING_PAYMENT, $config['to']))))
            {
                $params = array(
                'order_status'  => $this,
                'status_old'    => $orderStatus,
                'status_new'    => $state,
                'increment_id'  => $this->getIncrementId()
                );
                $hashList = array(trim(Mage::getStoreConfig('adjnotification/order_status/send_to_'.$configId)),
                                  $orderStatus,
                                  $state
                            );
                $hash = $this->_createHash($hashList);
                if (!in_array($hash, $this->_wasSend))
                {
                    Mage::getModel('adjnotification/sender')->sendNotification('order_status', $params, $configId);
                    $this->_wasSend[] = $hash;
                }
            }
        }
    }
    
    public function unhold()
    {
        $this->_isCancel = true;
        $this->checkState($this->getHoldBeforeStatus());
        return parent::unhold();
    }
} } 
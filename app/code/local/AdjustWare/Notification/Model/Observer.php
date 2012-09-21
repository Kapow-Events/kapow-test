<?php
/**
 * Save evenys's observer
 *
 * @author Aitoc
 */
class AdjustWare_Notification_Model_Observer
{
    protected $isNew = null;
    private $_skipAll = false;
	
    /**
     * Subscribe/unsubscribe customer to newsletter
     * Rewrite to add AdjustWare code
     *
     * @param object $customer
     * @return AdjustWare_Notification_Model_Rewrite_Newsletter_Subscriber 
     */
    public function subscribeCustomer($observer)
    {
        return Mage::register('ait_customer_newsletter',true);
    }
    
    public function processAdminhtmlCouponPost($observer)
    {
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $data = Mage::app()->getRequest()->getPost('order');
        if (!empty($data['coupon']['code'])) {
            if ($quote->getCouponCode() == $data['coupon']['code']) {
                if (!Mage::getStoreConfig('adjnotification/new_coupon/notification'))
                {
                        return;
                }
                $obj = $observer->getControllerAction();
                $params = array(
                    'new_coupon' => $obj,
                    'coupon_code'=> $data['coupon']['code']
                );
                Mage::getModel('adjnotification/sender')->sendNotification('new_coupon', $params);
            }
        }
    }
    
    public function processCouponPost($observer)
    {
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        if(!$quote->getCouponCode())
        {
            return ;
        }
        $couponCode = $quote->getCouponCode();
        if(!$couponCode)
        {
            return;
        }
        if (!Mage::getStoreConfig('adjnotification/new_coupon/notification'))
        {
                return;
        }
        $obj = $observer->getControllerAction();
        $params = array(
            'new_coupon' => $obj,
            'coupon_code'=> $couponCode
        );
        Mage::getModel('adjnotification/sender')->sendNotification('new_coupon', $params);
    }
	
	public function processModelSaveBefore($observer)
    {
        if ($this->_skipAll)
        {
            return;
        }
        if ($this->isNew) {
            return;
        }
        $obj = $observer->getEvent()->getObject();
        
        if ($obj instanceof Aitoc_Aitsys_Model_Notification)
        {
            if ('AdjustWare_Notification' === $obj->getData('assigned') 
                && 'disable-license' === $obj->getData('type') )
            {
                $this->_skipAll = true;
                $this->isNew = false;
            }
        }elseif ($obj instanceof Mage_Customer_Model_Customer
                || $obj instanceof Mage_Review_Model_Review
                || $obj instanceof Mage_Tag_Model_Tag
                || $obj instanceof Mage_Sales_Model_Order)
        {
            $this->isNew = is_null($obj->getId());
            return;
        }
    }
    
    public function processModelSaveAfter($observer)
    {
        if (!$this->isNew)
        {
            return;
        }
        $obj = $observer->getEvent()->getObject();
        
        
        if ($obj instanceof Mage_Customer_Model_Customer){
            $this->isNew = false;
            if (!Mage::getStoreConfig('adjnotification/new_customer/notification'))
                return;
            $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/customer/edit', 
                    array('id' => $obj->getId()));
			$params = array(
                            'new_customer' => $obj,
                            'customer'     => $obj,
                            'url'          => $url							
			          );
            Mage::getModel('adjnotification/sender')->sendNotification('new_customer', $params);
        }
        elseif ($obj instanceof Mage_Review_Model_Review){
            $this->isNew = false;
            if (!Mage::getStoreConfig('adjnotification/new_review/notification'))
                return;
            $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/catalog_product_review/edit', 
                    array('id' => $obj->getId()));
			$params = array(
                            'new_review' => $obj,
                            'review'     => $obj,
                            'url'        => $url							
			          );
            Mage::getModel('adjnotification/sender')->sendNotification('new_review', $params);
        }
        elseif ($obj instanceof Mage_Tag_Model_Tag){
            $this->isNew = false;
            if (!Mage::getStoreConfig('adjnotification/new_tag/notification'))
                return;
            $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/tag/edit', 
                    array('tag_id' => $obj->getId()));
			$params = array(
                            'new_tag' => $obj,
                            'tag'     => $obj,
                            'url'     => $url							
			          );
            Mage::getModel('adjnotification/sender')->sendNotification('new_tag', $params);
        }        
        elseif ($obj instanceof Mage_Sales_Model_Order){
            $this->isNew = false;
            if (!Mage::getStoreConfig('adjnotification/new_order/notification'))
                return;
            $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/view', 
                    array('order_id' => $obj->getId()));
			$params = array(
                            'new_order' => $obj,
                            'order'     => $obj,
                            'url'       => $url							
			          );
            Mage::getModel('adjnotification/sender')->sendNotification('new_order', $params);
        }           
    }
}

<?php
/**
 * Product:     Admin Email Notifications for 1.4.x-1.6.1.0 
 * Package:     AdjustWare_Notification_2.2.0_2.1.0_233631
 * Purchase ID: VgTBgcMfRJNN8TEH6RZt8oax2Zeu1FkviyZl5t0FfV
 * Generated:   2012-03-23 19:25:00
 * File path:   app/code/local/AdjustWare/Notification/Model/Sender.php
 * Copyright:   (c) 2012 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Notification')){ akhkkacrkZDMBshi('d85c7d1b38d5d4af57ed9ae8b6ec53ac'); ?><?php
/**
 * Save evenys's observer
 *
 * @author Aitoc
 */
class AdjustWare_Notification_Model_Sender extends Mage_Core_Model_Abstract
{
    /**
     * Send email notification
     * 
     * @param string $to
     * @param string $template
     * @param array $params
     */
    private function _sendNotification($to, $template, $params=array())
    {
        if (!$to)
        {
            return;
        }
        if (!$template)
        {
            return;
        }
        // if admin is aleady logged in, this prevents
        // redirect to dashboard
		if(isset($params['url']))
		{
			$pos = strpos($params['url'],'key');
			if ($pos){
				$params['url'] = substr($params['url'], 0, $pos);    
			}
		}
		
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        try {
            $emailModel = Mage::getModel('core/email_template');
            $emailModel->sendTransactional(
                $template, //Mage::getStoreConfig('adjnotification/'.$eventType.'/template'),
                Mage::getStoreConfig('adjnotification/sender/identity'),
                $to,
                null,
                $params
            );            

            $translate->setTranslateInline(true);
        
        } catch (Exception $e) {
            $translate->setTranslateInline(true);
            mail($to, 'Notification error', $e->getMessage());
        }
    }

    /**
     * Prepare data and template email to send
     *
     * @param string $eventType
     * @param array $params
     * @param int|null $order_status
     */
    public function sendNotification($eventType, $params, $order_status=null)
    {
        if ($order_status)
        {
            $to = trim(Mage::getStoreConfig('adjnotification/'.$eventType.'/send_to_'.$order_status));
            $template = Mage::getStoreConfig('adjnotification/'.$eventType.'/template_'.$order_status);
        }
        else
        {
            $to = trim(Mage::getStoreConfig('adjnotification/'.$eventType.'/send_to'));
            $template = Mage::getStoreConfig('adjnotification/'.$eventType.'/template'); 
        }
        $this->_sendNotification($to, $template, $params);
    }
} } 
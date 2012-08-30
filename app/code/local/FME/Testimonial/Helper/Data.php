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
 * 	       1 - Email Sending - 23-03-2012
 * 	       
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Helper_Data extends Mage_Core_Helper_Abstract
{

	const XML_PATH_LIST_PAGE_TITLE				=	'testimonial/list/page_title';
	const XML_PATH_LIST_IDENTIFIER				=	'testimonial/list/identifier';
	const XML_PATH_LIST_ITEMS_PER_PAGE			=	'testimonial/list/items_per_page';
	const XML_PATH_LIST_LIMIT_DESCRIPTION			=	'testimonial/list/limit_description';
	const XML_PATH_LIST_META_DESCRIPTION			=	'testimonial/list/meta_description';
	const XML_PATH_LIST_META_KEYWORDS			=	'testimonial/list/meta_keywords';
	const XML_PATH_ADMIN_APPROVAL				=	'testimonial/add_testimonial_settings/admin_approval';
	const XML_PATH_SEO_URL_SUFFIX				=	'testimonial/seo/url_suffix';
	const XML_PATH_CUSTOMER_ALLOWED				= 	'testimonial/add_testimonial_settings/customer_allowed';
	
	const XML_MODERATOR_EMAIL_SUBJECT			=	'testimonial/email_settings/moderator_email_subject';
	const XML_MODERATOR_EMAIL_ID				=	'testimonial/email_settings/moderator_email';
	const XML_MODERATOR_EMAIL_TEMPLATE			=	'testimonial/email_settings/moderator_email_template';
	const XML_SENDER_EMAIL					=	'testimonial/email_settings/email_sender';
	
	const XML_CLIENT_EMAIL_SUBJECT				=	'testimonial/email_settings/client_email_subject';
	const XML_CLIENT_EMAIL_TEMPLATE				=	'testimonial/email_settings/client_email_template';
	
	
	
	
	public function getListPageTitle()
	{
		return Mage::getStoreConfig(self::XML_PATH_LIST_PAGE_TITLE);
	}
	
	public function getListIdentifier()
	{
		$identifier = Mage::getStoreConfig(self::XML_PATH_LIST_IDENTIFIER);
		if ( !$identifier ) {
			$identifier = 'testimonial';
		}
		return $identifier;
	}
	
	public function geturlIdentifier()
	{
		$identifier = $this->getListIdentifier() . Mage::getStoreConfig(self::XML_PATH_SEO_URL_SUFFIX);
		return $identifier;
	}
	
	public function getNewurlIdentifier()
	{
		$identifier = Mage::getBaseUrl() . $this->geturlIdentifier();
		return $identifier;
	}
	
	public function getListItemsPerPage()
	{
		return (int)Mage::getStoreConfig(self::XML_PATH_LIST_ITEMS_PER_PAGE);
	}
	
	public function getListLimitDescription()
	{
		return (int)Mage::getStoreConfig(self::XML_PATH_LIST_LIMIT_DESCRIPTION);
	}
	
	public function getListMetaDescription()
	{
		return Mage::getStoreConfig(self::XML_PATH_LIST_META_DESCRIPTION);
	}
	
	public function getListMetaKeywords()
	{
		return Mage::getStoreConfig(self::XML_PATH_LIST_META_KEYWORDS);
	}
	
	public function getAdminApproval()
	{
		return Mage::getStoreConfig(self::XML_PATH_ADMIN_APPROVAL);
	}
	
	public function getUrl($identifier = null)
	{
		
		if ( is_null($identifier) ) {
			$url = Mage::getUrl('') . self::getListIdentifier()  . self::getSeoUrlSuffix();
		} else {
			$url = Mage::getUrl(self::getListIdentifier()) . $identifier . self::getSeoUrlSuffix();
		}

		return $url;
		
	}
	public function getSeoUrlSuffix()
	{
		return Mage::getStoreConfig(self::XML_PATH_SEO_URL_SUFFIX);
	}
	
	public function getUserName()
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
		    return '';
		}
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		return trim($customer->getName());
	}

	public function getUserEmail()
	{
	    if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
	        return '';
	    }
	    $customer = Mage::getSingleton('customer/session')->getCustomer();
	    return $customer->getEmail();
	}
    
	public function recursiveReplace($search, $replace, $subject){
		if(!is_array($subject))
		    return $subject;
	
		foreach($subject as $key => $value)
		    if(is_string($value))
			$subject[$key] = str_replace($search, $replace, $value);
		    elseif(is_array($value))
			$subject[$key] = self::recursiveReplace($search, $replace, $value);
	
		return $subject;
	}
	
	public function getCustomerAllowed(){
	
		return Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ALLOWED);
	
	}
	
	public function sendEmailToModerator($info_array){
		
		$m_subject = Mage::getStoreConfig(self::XML_MODERATOR_EMAIL_SUBJECT);
		$m_email = Mage::getStoreConfig(self::XML_MODERATOR_EMAIL_ID);
		$template = Mage::getStoreConfig(self::XML_MODERATOR_EMAIL_TEMPLATE);
		
		
		$sender_id = Mage::getStoreConfig(self::XML_SENDER_EMAIL);
		$sender_email = 'owner@example.com';
		$sender_name = 'Owner';
		
		if($sender_id == 'general'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_general/name');
		
		}elseif($sender_id == 'sales'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_sales/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_sales/name');
		
		}elseif($sender_id == 'support'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_support/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_support/name');
		
		}elseif($sender_id == 'custom1'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_custom1/name');
		
		}elseif($sender_id == 'custom2'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_custom2/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_custom2/name');
		}
		
		
		
		
		
		$emailTemplate = Mage::getModel('core/email_template')->loadDefault($template);
		
		
		// Status of testimonial
		$testimonial_status = $info_array['status'];
		if($testimonial_status == 1){
			$testimonial_status = "Active.";
		}else{
			$testimonial_status = "Awaiting moderation.";
		}
		
		
		//array of variables to assign to template		
		$emailVars = array(
				   'company_name'	=> 	$info_array['company_name'],
				   'contact_name'	=> 	$info_array['contact_name'],
				   'mod_email'		=> 	$info_array['email'],
				   'website'		=> 	$info_array['website'],
				   'short_description'	=> 	$info_array['short_description'],
				   'testimonial'	=> 	$info_array['testimonial'],
				   'status'		=> 	$testimonial_status
				   );
		
		$emailTemplate->getProcessedTemplate($emailVars);
		
		
		$emailTemplate->setSenderName($sender_name);
		$emailTemplate->setSenderEmail($sender_email);
		$emailTemplate->setTemplateSubject($m_subject);
		$emailTemplate->send($m_email, 'Testimonial Notification', $emailVars);
		
	}
	
	
	public function sendEmailToClient($info_array){
		
		$c_subject	=	Mage::getStoreConfig(self::XML_CLIENT_EMAIL_SUBJECT);
		$template	=	Mage::getStoreConfig(self::XML_CLIENT_EMAIL_TEMPLATE);
		
		$sender_id	=	Mage::getStoreConfig(self::XML_SENDER_EMAIL);
		$sender_email	=	'owner@example.com';
		$sender_name	=	'Owner';
		
		if($sender_id == 'general'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_general/name');
		
		}elseif($sender_id == 'sales'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_sales/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_sales/name');
		
		}elseif($sender_id == 'support'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_support/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_support/name');
		
		}elseif($sender_id == 'custom1'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_custom1/name');
		
		}elseif($sender_id == 'custom2'){
			$sender_email = Mage::getStoreConfig('trans_email/ident_custom2/email');
			$sender_name = Mage::getStoreConfig('trans_email/ident_custom2/name');
		}
		
		
		$emailTemplate	=	Mage::getModel('core/email_template')->loadDefault($template);
		
		
		//status of testimonial
		$testimonial_status = $info_array['status'];
		if($testimonial_status == 1){
			$testimonial_status = "Active.";
		}else{
			$testimonial_status = "Awaiting moderation.";
		}
		
		
		//array of variable to assign template
		$emailVars = array(
				   'company_name'	=> 	$info_array['company_name'],
				   'contact_name'	=> 	$info_array['contact_name'],
				   'email'		=> 	$info_array['email'],
				   'website'		=> 	$info_array['website'],
				   'short_description'	=> 	$info_array['short_description'],
				   'testimonial'	=> 	$info_array['testimonial'],
				   'status'		=> 	$testimonial_status
				   );
		
		$emailTemplate->getProcessedTemplate($emailVars);
		
		
		$emailTemplate->setSenderName($sender_name);
		$emailTemplate->setSenderEmail($sender_email);
		$emailTemplate->setTemplateSubject($c_subject);
		$emailTemplate->send($info_array['email'], 'Testimonial Notification', $emailVars);
		
		
	}
	
	
	//Read images from directory 'flesh_upload'
	
	function getImages() {
		
		
		$fulldir = opendir(Mage::getBaseDir('media') . "/testimonials/flash_upload/");
		
		while($file = readdir($fulldir)) {
			
			if($file == ".")
				continue;
			if($file == "..")
				continue;
			
			$target_img = $file;
			
		}
		
		closedir($fulldir);
		
		return $target_img;
	}
	
	
}
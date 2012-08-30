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
 * 	       2 - Detail Action - 23-03-2012
 * 	       
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {	
	
		$store = Mage::app()->getStore()->getStoreId();
		
		if(Mage::getStoreConfig('testimonial/list/sort_by') == 'order'):
		
		    $collection = Mage::getModel('testimonial/testimonial')->getCollection()
							    ->addStoreFilter($store)
							    ->addFieldToFilter('main_table.status', 1)
							    ->addOrder('main_table.order_num', 'asc')
							    ->getData();
		else:
		
		    $collection = Mage::getModel('testimonial/testimonial')->getCollection()
							->addStoreFilter($store)
							->addFieldToFilter('main_table.status', 1)
							->addOrder('main_table.created_time', 'desc')
							->getData();
		endif;
		
		
		$itemsPerPage = Mage::helper('testimonial')->getListItemsPerPage();
		
		// Use paginator
		if ( $itemsPerPage != 0 ) {		
			$paginator = Zend_Paginator::factory((array)$collection);
			$paginator->setCurrentPageNumber((int)$this->_request->getParam('page', 1))
					  ->setItemCountPerPage($itemsPerPage);
			Mage::register('items', $paginator);
		} else {
			Mage::register('items', $collection);
		}
		
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function detailAction(){
	
	$id = $this->getRequest()->getParam('id');
	
	if($id != 0 || $id != null){
	    
	    Mage::register('current_testimonial_id',$id);
	
	}else{
	    
	    $this->_redirect('*/*/');
	 
	}
	
	$this->loadLayout(); 
	$this->renderLayout();
    }
    
    
	
    public function addAction()
    {		
		$this->loadLayout();     
		$this->getLayout()->getBlock('testimonial')
            ->setFormAction( Mage::getUrl('*/*/post') );	
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');			
		$this->renderLayout();
    }
	
	
	
	
    public function flashuploadAction(){
	
	
	//FLASH IMAGE UPLOADING MANAGEMENT
				
	$ffolderPath = Mage::getBaseDir('media') . "/testimonials/flash_upload/";
	if (!file_exists($ffolderPath)) {	
		mkdir($ffolderPath, 0777, true);
		chmod($ffolderPath, 0777);
	}
				
				
	    @extract($_GET);
	
	    $ffilename		= $_FILES['Filedata']['name'];
	    $ftemp_name		= $_FILES['Filedata']['tmp_name'];
	    $ferror		= $_FILES['Filedata']['error'];
	    $fsize		= $_FILES['Filedata']['size'];
					
	    if(!$ferror){
		
		//first make sure this dir should empty
		$mydir = opendir($ffolderPath);
		    
		    while(false !== ($file = readdir($mydir))) {
			if($file != "." && $file != "..") {
			    chmod($ffolderPath.$file, 0777);
			    if(is_dir($ffolderPath.$file)) {
				chdir('.');
				destroy($ffolderPath.$file.'/');
				rmdir($ffolderPath.$file);
			    }
			    else
				unlink($ffolderPath.$file);
			}
		    }
		    
		closedir($fulldir);
		
		   
	        move_uploaded_file($ftemp_name, $ffolderPath.$ffilename);
					    
	    }
	    
	
    }
    
    
    
    
    public function postAction()
    {
	
			
				
				
        $post = $this->getRequest()->getPost();
	
	if ( $post ) {
			
		try {
		    
			$translate = Mage::getSingleton('core/translate');
			$translate->setTranslateInline(false);
           
			$postObject = new Varien_Object();
			$postObject->setData($post);
			
			
			/***************************************************************
			these variables are set default value as false
			further they will be used as to check which required fields
			are not validating
			***************************************************************/
			$captchaerror = false;
			
			if (!Zend_Validate::is(trim($post['security_code']) , 'NotEmpty')) { 
			$captchaerror = true;
			}
			
			/***************************************************************
			if error returned by zend validator then add an error message
			***************************************************************/
			if ($captchaerror) {
				
				$translate->setTranslateInline(true);
				Mage::getSingleton('core/session')->addError(Mage::helper('testimonial')->__('Please Enter verification text '));
				throw new Exception();
				
			}
			
			if (!$captchaerror && $post['security_code']!= $post['captacha_code']) {
				$translate->setTranslateInline(true);
				Mage::getSingleton('core/session')->addError(Mage::helper('testimonial')->__('Sorry The Security Code You Entered Was Incorrect'));
				throw new Exception();
			}
			
			
			if(isset($_FILES['contact_photo']['name']) && $_FILES['contact_photo']['name'] != '') {
	  			$post['contact_photo'] = $_FILES['contact_photo']['name'];
			}
			
			
			//FOR FLASH IMAGE NAME TO DB
			
			if($post['ajax_form'] == 'yes') {
			    
				$target_img = Mage::helper('testimonial')->getImages();
				if($target_img != '')    
				    $post['contact_photo'] = $target_img;
			}
			
			
			
			
			
			$model = Mage::getModel('testimonial/testimonial');		
			$model->setData($post)
				->setId($this->getRequest()->getParam('id'));
			
			
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				
				
				$model->save();
				
				
				
				
				//Send E-mail notification to Moderator / Client				
				if(Mage::getStoreConfig('testimonial/email_settings/enable_moderator_notification') == 1){
				    
				    Mage::helper('testimonial')->sendEmailToModerator($post);		    
				    
				}
				
				if(Mage::getStoreConfig('testimonial/email_settings/enable_client_notification') == 1){
				    
				    Mage::helper('testimonial')->sendEmailToClient($post);
				    
				}
				
				
				
				
				//Get Last Inserted Record ID
				$lastInsertedID = $model->getId();
				$folderPath = $path = Mage::getBaseDir('media') . "/testimonials/" . $lastInsertedID . DS ;
				
				if (!file_exists($folderPath)) {	
					mkdir($folderPath, 0777, true);
					chmod($folderPath, 0777);
				}
				
				//NOW MOVE THE IMG FILE TO ITS PLACE UNDER LAST ID FOLDER
				
				if($post['ajax_form'] == 'yes') {
			    
					$target_img = Mage::helper('testimonial')->getImages();
					$target_path = Mage::getBaseDir('media') . "/testimonials/flash_upload/".$target_img;
					
					if($target_img != '')    
					    rename( $target_path , $folderPath.$target_img);
				}		
			
				
				if(isset($_FILES['contact_photo']['name']) && $_FILES['contact_photo']['name'] != '') {
					try {	
						
						$path = Mage::getBaseDir('media')."/testimonials". DS;
						/* Starting upload */							
						$uploader = new Varien_File_Uploader('contact_photo');
						// Any extention would work
						$uploader->setAllowedExtensions(array('jpg','JPG','jpeg','gif','GIF','png','PNG'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						// We set media as the upload dir
						$uploader->save($folderPath, $_FILES['contact_photo']['name'] );
						
						
						//Create Thumbnail and upload
						$imgName = $_FILES['contact_photo']['name'];
						$imgPathFull = $folderPath.$imgName;
						$resizeFolder = "thumb";
						$imageResizedPath = $folderPath.$resizeFolder.DS.$imgName;
						$imageObj = new Varien_Image($imgPathFull);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(TRUE);
						$imageObj->resize(75,75);
						$imageObj->save($imageResizedPath);	
						
					} catch (Exception $e) {}
				}
				
				
			    $translate->setTranslateInline(true);
				Mage::getSingleton('customer/session')->addSuccess(Mage::helper('testimonial')->__('Thanks for your precious time Testimonial has been added successfully!'));
                $this->_redirect('*/*/add');
                return;
            } catch (Exception $e) {
		
		$translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addError(Mage::helper('testimonial')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/add');
                return;
            }
		   
        } else {
            $this->_redirect('*/*/add');
        }
    }
}
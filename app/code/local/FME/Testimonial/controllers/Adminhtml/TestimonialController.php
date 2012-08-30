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
 * 	       1 - saveAction - Url Identifier - 23-03-2012
 * 	       
 * @copyright  Copyright 2012 © www.fmeextensions.com All right reserved
 */
 
class FME_Testimonial_Adminhtml_TestimonialController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('testimonial/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Testimonial Manager'), Mage::helper('adminhtml')->__('Testimonial Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('testimonial/testimonial')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('testimonial_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('testimonial/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Testimonial Manager'), Mage::helper('adminhtml')->__('Testimonial Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Testimonial Manager'), Mage::helper('adminhtml')->__('Testimonial Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

			$this->_addContent($this->getLayout()->createBlock('testimonial/adminhtml_testimonial_edit'))
				->_addLeft($this->getLayout()->createBlock('testimonial/adminhtml_testimonial_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('testimonial')->__('Testimonial does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		
		
		if ($data = $this->getRequest()->getPost()) {
			
			$model = Mage::getModel('testimonial/testimonial');
			
			
			//Before save check the Url_identifier is unique				
			if($data['url_identifier'] != ''){
				$collection = $model->getCollection()
							->addFieldToFilter('url_identifier',$data['url_identifier'])
							->getData();
							
				//IF Update mode:
				if($this->getRequest()->getParam('id') != ''):
				$collection = $model->getCollection()
							->addFieldToFilter('url_identifier',$data['url_identifier'])
							->addFieldToFilter('testimonial_id',array('neq' => $this->getRequest()->getParam('id')))
							->getData();
				endif;
				
				
			}else{
				$collection = null;
			}
			
			
			// if not then Identifier is not unique
			if(!empty($collection)){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('testimonial')->__("Url Identifier '".$data['url_identifier']."' already exist"));
				$data['url_identifier'] = '';
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}else{
			
			
			
					if(isset($_FILES['contact_photo']['name']) && $_FILES['contact_photo']['name'] != '') {
						 //this way the name is saved in DB
						$data['contact_photo'] = $_FILES['contact_photo']['name'];
					}
							
							
					$model->setData($data)
						->setId($this->getRequest()->getParam('id'));
					
					try {
						if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
							$model->setCreatedTime(now())
								->setUpdateTime(now());
						} else {
							$model->setUpdateTime(now());
						}	
						
						
						
						$model->save();
						
						//Get Last Inserted Record ID
						$lastInsertedID = $model->getId();
						$folderPath = $path = Mage::getBaseDir('media') . "/testimonials/" . $lastInsertedID . DS ;
						
						if (!file_exists($folderPath)) {	
							mkdir($folderPath, 0777, true);
							chmod($folderPath, 0777);
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
						
						
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('testimonial')->__('Testimonial was successfully saved'));
						Mage::getSingleton('adminhtml/session')->setFormData(false);
		
						if ($this->getRequest()->getParam('back')) {
							$this->_redirect('*/*/edit', array('id' => $model->getId()));
							return;
						}
						$this->_redirect('*/*/');
						return;
			    } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			    }
			    
			    
			}
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('testimonial')->__('Unable to find Testimonial to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('testimonial/testimonial');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Testimonial was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $testimonialIds = $this->getRequest()->getParam('testimonial');
        if(!is_array($testimonialIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Testimonial(s)'));
        } else {
            try {
                foreach ($testimonialIds as $testimonialId) {
                    $testimonial = Mage::getModel('testimonial/testimonial')->load($testimonialId);
                    $testimonial->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($testimonialIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $testimonialIds = $this->getRequest()->getParam('testimonial');
        if(!is_array($testimonialIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Testimonial(s)'));
        } else {
            try {
                foreach ($testimonialIds as $testimonialId) {
                    $testimonial = Mage::getSingleton('testimonial/testimonial')
                        ->load($testimonialId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($testimonialIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'testimonial.csv';
        $content    = $this->getLayout()->createBlock('testimonial/adminhtml_testimonial_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'testimonial.xml';
        $content    = $this->getLayout()->createBlock('testimonial/adminhtml_testimonial_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
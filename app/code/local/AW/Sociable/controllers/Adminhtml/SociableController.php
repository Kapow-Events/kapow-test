<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sociable
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */?>
<?php

class AW_Sociable_Adminhtml_SociableController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('admin/cms/sociable');
    }

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('cms/sociable')
            ;
        return $this;
    }   
 
    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
                ;
    }
    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('sociable/service')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('sociable_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('sociable/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('sociable/adminhtml_sociable_edit'))
                ->_addLeft($this->getLayout()->createBlock('sociable/adminhtml_sociable_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sociable')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction() {
        $this->_forward('edit');
    }
 
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            
            $model = Mage::getModel('sociable/service');

            if(isset($_FILES['icon']['name']) && $_FILES['icon']['name'] != '') {
                try {
                    $path = $model->_iconsLocation;
                    $id = $this->getRequest()->getParam('id');
                    if(isset($id)){
                        $icon = Mage::getModel('sociable/service')->load($id)->getIcon();
                        $terminator = new Varien_Io_File();
                    }

                    $uploader = new Varien_File_Uploader('icon');

                    $allowedExtensions = array('jpg','jpeg','gif','png');
                       $uploader->setAllowedExtensions($allowedExtensions);
                    $uploader->setAllowRenameFiles(false);
                    
                    $uploader->setFilesDispersion(false);
                    $ext = strtolower(strrchr($_FILES['icon']['name'], '.'));

                    if(in_array(substr($ext,1), $allowedExtensions)){
                        $filename = time().$ext;
                        if($uploader->save($path, $filename) && isset($icon))
                            $terminator->rm($path.$icon);
                    }
                    else{
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sociable')->__('Disallowed file type!'));
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                        return;

                    }
                } catch (Exception $e) {
              
                }
            
                //this way the name is saved in DB
                  if(isset($filename))
                    $data['icon'] = $filename;
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
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sociable')->__('Service was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sociable')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function resetAction() {

        $services = Mage::getModel('sociable/service');
        $resource = Mage::getModel('core/resource');
        $db = $resource->getConnection('core_write');
        $servicesIds = $services->getCollection()->getColumnValues('services_id');
        foreach($servicesIds as $id){
            $service = $services->load($id);
            $service->setClicks('0');
            $service->save();
        }
        $db->truncate($resource->getTableName('sociable/clicks'));
        $db->truncate($resource->getTableName('sociable/bookmarked'));
        $this->_redirect('*/*/');
    }

 
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('sociable/service');
                 
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                     
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $sociableIds = $this->getRequest()->getParam('sociable');
        if(!is_array($sociableIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($sociableIds as $sociableId) {
                    $sociable = Mage::getModel('sociable/service')->load($sociableId);
                    $sociable->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($sociableIds)
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
        $sociableIds = $this->getRequest()->getParam('sociable');
        if(!is_array($sociableIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($sociableIds as $sociableId) {
                    $sociable = Mage::getSingleton('sociable/service')
                        ->load($sociableId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($sociableIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
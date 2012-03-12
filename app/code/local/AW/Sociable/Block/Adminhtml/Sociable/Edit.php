<?php

class AW_Sociable_Block_Adminhtml_Sociable_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'sociable';
        $this->_controller = 'adminhtml_sociable';
        
        $this->_updateButton('save', 'label', Mage::helper('sociable')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('sociable')->__('Delete'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('sociable_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'sociable_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'sociable_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('sociable_data') && Mage::registry('sociable_data')->getId() ) {
            return Mage::helper('sociable')->__("Edit Service '%s'", $this->htmlEscape(Mage::registry('sociable_data')->getTitle()));
        } else {
            return Mage::helper('sociable')->__('Add Service');
        }
    }
}
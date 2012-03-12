<?php
class AW_Sociable_Block_Adminhtml_Sociable_Render_Column_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $path = Mage::getModel('sociable/service')->_iconsURL;
        $value =  $row->getData($this->getColumn()->getIndex());
        return '<img src='.$path.$value.' class=\'thumbnail-grid\'>';

    }

}
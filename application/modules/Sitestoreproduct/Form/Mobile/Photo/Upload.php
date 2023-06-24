<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Upload.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Mobile_Photo_Upload extends Engine_Form {

    public function init() {

        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id', null));
        $this->setAttrib("data-ajax", "false");
        $this
                ->setTitle('Add New Photos')
                ->setDescription("Choose photos on your computer to add to this product. (2MB maximum). The recommended dimension for the photos of this product is: 400 x 500 pixels to enable image zoom feature when users mouse-over on photos displayed on this product's profile page.")
                ->setAttrib('id', 'form-upload')
                ->setAttrib('class', 'global_form sitestoreproduct_form_upload')
                ->setAttrib('name', 'albums_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $fancyUpload = new Engine_Form_Element_FancyUpload('file');
        $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                    'viewScript' => '_MobileFancyUpload.tpl',
                    'placement' => '',
        ));
        Engine_Form::addDefaultDecorators($fancyUpload);
        $this->addElement($fancyUpload);
        

        $this->addElement('File', 'photo', array(
            'label' => 'Add Photo',
            'destination' => APPLICATION_PATH . '/public/temporary/',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Save Photos',
            'type' => 'submit',
        ));
    }

}

<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Copy.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Mobile_Copy extends Sitestoreproduct_Form_Mobile_Create {

    public $_error = array();
    protected $_item;
    protected $_defaultProfileId;
    public $_isCopyProduct = true;

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function init() {
// $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        parent::init();
        $this->setAttrib('id', 'copyForm');
        $this->setAttrib("data-ajax", "false");


        $this->setTitle("Copy Product Info")
                ->setDescription("Copy your product below, and then click Save Settings to save changes.");
//     $this->setAction($view->url(array('action' => 'userby-locations'), 'sitemember_userbylocation', true))->getDecorator('HtmlTag')->setOption('class', 'browsesitemembers_criteria');


        $this->addElement('Button', 'submit', array(
            'label' => 'Edit',
            'type' => 'submit',
            'order' => 999,
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formCopyProduct.tpl',
                        'class' => 'form element'))),
        ));


//    $this->addElement('Text', 'imageHeading', array(
//        'decorators' => array(array('ViewScript', array(
//                    'viewScript' => '_createFormHeading.tpl',
//                    'heading' => 'Images',
//                    'class' => 'form element',
//            ))),
//    ));
//
//    $this->addElement('Text', 'imageDiv', array(
//        'decorators' => array(array('ViewScript', array(
//                    'viewScript' => '_copyImages.tpl',
//                    'class' => 'form element',
//                    'product_id' => $this->_item->getIdentity()
//                ,
//            ))),
//    ));

        $this->addElement('Hidden', 'imageName', array(
            'order' => 992
        ));

        $this->addElement('Hidden', 'imageenable', array(
            'value' => 0,
            'order' => 991
        ));
        $this->addElement('Hidden', 'photo_id_filepath', array(
            'value' => 0,
            'order' => 854
        ));

//    $this->addElement('File', 'image', array(
//        'label' => Zend_Registry::get('Zend_Translate')->_('Browse Image'),
//        'description' => Zend_Registry::get('Zend_Translate')->_("Browse and choose an image for your product. Max file size allowed : ") . (int) ini_get('upload_max_filesize') . Zend_Registry::get('Zend_Translate')->_(" MB. File types allowed: jpg, jpeg, png, gif. You can upload maximum 5 new images. You can upload unlimited images after creating product."),
//        'validators' => array(
//            array('Extension', false, 'jpg,png,gif,jpeg')
//        ),
//        'onchange' => 'imageupload()',
//    ));
    }

}

?>
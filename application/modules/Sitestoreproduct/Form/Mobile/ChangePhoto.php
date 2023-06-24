<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ChangePhoto.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Mobile_ChangePhoto extends Engine_Form {

    public function init() {

        $product_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id', null);

        $this->setTitle("Edit Profile Picture")
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'EditPhoto');

        $this->addElement('Image', 'current', array(
            'label' => 'Current Photo',
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formMobileEditImage.tpl',
                        'class' => 'form element',
                        'testing' => 'testing'
                    )))
        ));
        Engine_Form::addDefaultDecorators($this->current);

        $this->addElement('File', 'Filedata', array(
            'label' => 'Choose New Photo',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'validators' => array(
                array('Extension', false, 'jpg,jpeg,png,gif'),
            ),
            'onchange' => 'javascript:uploadPhoto();'
        ));
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->addElement('Dummy', 'choose', array(
            'label' => 'Or',
            'description' => "<a href='" . $view->url(array('product_id' => $product_id, "change_url" => 1), "sitestoreproduct_albumspecific", true) . "'>" . Zend_Registry::get('Zend_Translate')->_('Choose From Existing Pictures') . "</a>",
        ));
        $this->getElement('choose')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

        $this->addElement('Hidden', 'coordinates', array(
            'filters' => array(
                'HtmlEntities',
            )
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $url = $view->url(array('action' => 'remove-photo', 'product_id' => $product_id), "sitestoreproduct_specific", true);
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        if ($sitestoreproduct->photo_id != 0) {

            $this->addElement('Button', 'remove', array(
                'label' => 'Remove Photo',
                'onclick' => "removePhotoProduct('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $url = $view->url(array('product_id' => $product_id, 'slug' => $sitestoreproduct->getSlug()), "sitestoreproduct_entry_view", true);

            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'prependText' => ' or ',
                'link' => true,
                'onclick' => "removePhotoProduct('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $this->addDisplayGroup(array('remove', 'cancel'), 'buttons', array());
        }
    }

}

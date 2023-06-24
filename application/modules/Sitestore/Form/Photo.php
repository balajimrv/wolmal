<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photo.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Photo extends Engine_Form {

  public function init() {

    $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $this
            ->setTitle('Edit Profile Picture')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'EditPhoto');

    $this->addElement('Image', 'current', array(
        'label' => 'Current Photo',
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formEditImage.tpl',
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

    $this->addElement('Hidden', 'coordinates', array(
        'filters' => array(
            'HtmlEntities',
        )
    ));

    if (!Engine_Api::_()->getApi('settings', 'core')->sitestore_requried_photo) {
      if ($sitestore->photo_id != 0) {
        $this->addElement('Cancel', 'remove', array(
            'label' => 'Remove Photo',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'action' => 'remove-photo',
            )),
            'onclick' => null,
            'decorators' => array(
                'ViewHelper'
            ),
        ));
        $this->addDisplayGroup(array('done', 'remove'), 'buttons');
      }
    }
  }

}

?>
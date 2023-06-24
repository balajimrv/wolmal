<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_Form_Admin_Widget extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Widget Settings')
            ->setDescription('Configure the general settings for the various widgets available with this plugin.');

    $this->addElement('Text', 'sitestore_album', array(
        'label' => 'Store Profile Albums',
        'maxlength' => '3',
        'description' => 'How many albums will be shown in the store profile albums widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.album', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestore_photo', array(
        'label' => 'Photos in Store Profile Albums',
        //'maxlength' => '3',
        'description' => 'How many photos will be shown below albums in the store profile albums widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.photo', 100),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestore_mostliked_photos', array(
        'label' => 'Store Profile Most Liked Photos',
        'maxlength' => '3',
        'description' => 'How many photos will be shown in the store profile most liked photos widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mostliked.photos', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestore_mostcommented_photos', array(
        'label' => 'Store Profile Most Commented Photos',
        'maxlength' => '3',
        'description' => 'How many photos will be shown in the store profile most commented photos widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mostcommented.photos', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestore_mostrecent_photos', array(
        'label' => 'Store Profile Photos Strip',
        'maxlength' => '3',
        'description' => 'How many photos will be shown in the store profile photos strip widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mostrecent.photos', 7),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestore_homerecentphotos_widgets', array(
        'label' => 'Recent Photos',
        'maxlength' => '3',
        'description' => 'How many photos should be shown in the recent photos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.homerecentphotos.widgets', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitestore_mostpopularphotos_widgets', array(
        'label' => 'Popular Photos',
        'maxlength' => '3',
        'description' => 'How many photos should be shown in the most popular photos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mostpopularphotos.widgets', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Hidephoto.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Hidephoto extends Engine_Form {

  public function init() {

    $photo_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('photo_id', null);
    $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
    $this
            ->setTitle('Are you sure you want to hide this photo?')
            ->setDescription("Hiding a photo only removes it from your profile page. People you've allowed will still be able to view this photo on your photos store.");

    $this->addElement('Button', 'submit', array(
        'label' => 'Hide Photo',
        'ignore' => true,
        'onclick' => "return hidephoto('$photo_id', '$store_id');",
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'type' => 'link',
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}

?>
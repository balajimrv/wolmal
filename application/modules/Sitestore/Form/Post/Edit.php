<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Post_Edit extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Edit Post');


    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.tinymceditor', 1)) {
      $upload_url = "";
      $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
      if ($sitestorealbumEnabled && (!empty($isManageAdmin) || Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit'))) {
        $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => "upload-photo", 'store_id' => $store_id, 'special' => 'discussions'), 'sitestore_dashboard', true);
      }
      /* Some time overview is not work so use body
       */
      // Overview
      $this->addElement('TinyMce', 'body', array(
          'label' => '',
          //             'required' => true,
          'allowEmpty' => false,
          'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),

          'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_Html(array('AllowedTags' => "strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr"))),
      ));
    } else {
      $this->addElement('textarea', 'body', array(
          'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
      ));
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Edit Post',
        'ignore' => true,
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
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Post_Create extends Engine_Form {

  public function init() {

    $content_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $this
            ->setTitle('Reply')
            ->setAction(
                    Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble(array('action' => 'post', 'controller' => 'topic', 'tab' => $content_id), "sitestoreproduct_extended", true)
    );

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.tinymceditor', 1)) {
      $this->addElement('Textarea', 'body', array(
          'label' => 'Body',
          'required' => true,
          'allowEmpty' => false,
          'required' => true,
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_HtmlSpecialChars(),
              new Engine_Filter_EnableLinks(),
          ),
      ));
    } else {
      $this->addElement('TinyMce', 'body', array(
          'label' => 'Body',
          'allowEmpty' => false,
          'required' => true,
          'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),

          'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
          'filters' => array(new Engine_Filter_Censor()),
      ));
    }

    $this->addElement('Checkbox', 'watch', array(
        'label' => 'Send me notifications when other members reply to this topic.',
        'value' => '1',
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Post Reply',
        'ignore' => true,
        'type' => 'submit',
    ));

    $this->addElement('Hidden', 'topic_id', array(
        'order' => '920',
        'filters' => array(
            'Int'
        )
    ));

    $this->addElement('Hidden', 'ref');
  }

}
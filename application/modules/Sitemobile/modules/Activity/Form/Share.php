<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Share.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_Activity_Form_Share extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Share')
            ->setDescription('Share this by re-posting it with your own message.')
            ->setMethod('POST')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $this->addElement('Textarea', 'body', array(
        //'required' => true,
        //'allowEmpty' => false,
        'filters' => array(
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
            new Engine_Filter_Censor(),
        ),
    ));

    // Buttons
    $buttons = array();

    $translate = Zend_Registry::get('Zend_Translate');

    // Facebook
    $session = new Zend_Session_Namespace();
    $facebookApi = $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('facebook.enable', Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable == 'publish' ? 1 : 0) &&
            $facebookApi && Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebookApi)) {
      $this->addElement('Dummy', 'post_to_facebook', array(
          'content' => '
          <span href="javascript:void(0);" class="composer_facebook_toggle" onclick="toggleFacebookShareCheckbox();">
            <span class="cm-icons cm-icon-facebook">          
            </span>
            <input type="checkbox" name="post_to_facebook" id="post_to_facebook" value="1" style="display:none;">
          </span>',
      ));
      $this->getElement('post_to_facebook')->clearDecorators();
      $buttons[] = 'post_to_facebooks';
    }

    // Twitter
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('twitter.enable', Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable == 'publish' ? 1 : 0 )) {
      $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
      if ($twitterTable->getApi() &&
              $twitterTable->isConnected()) {
        $this->addElement('Dummy', 'post_to_twitter', array(
            'content' => '
          <span href="javascript:void(0);" class="composer_twitter_toggle" onclick="toggleTwitterShareCheckbox();">
            <span class="cm-icons cm-icon-twitter">           
            </span>
            <input type="checkbox" name="post_to_twitter" id="post_to_twitter" value="1" style="display:none;">
          </span>',
        ));
        $this->getElement('post_to_twitter')->clearDecorators();
        $buttons[] = 'post_to_twitters';
      }
    }

    if (count($buttons)) {
      $this->addElement('Button', 'sharenone', array(
          'decorators' => array('ViewHelper')
      ));
      $buttons[] = 'sharenone';
      $this->addDisplayGroup($buttons, 'sbuttons');
      $this->getDisplayGroup('sbuttons');
    }
    $buttons = array();

    $this->addElement('Button', 'submit', array(
        'label' => 'Share',
        'data-role' => 'button',
        'type' => 'submit',
        'class' => 'ui-btn-right',
        'decorators' => array('ViewHelper')
    ));
    $buttons[] = 'submit';

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'data-role' => 'none',
        'class' => 'ui-link',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $buttons[] = 'cancel';


    $this->addDisplayGroup($buttons, 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}
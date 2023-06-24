<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreurl
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreurl_Form_Admin_Global extends Engine_Form {

  public function init() {
   
    $is_element = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreurl.is.enable', 0);
    $this->setTitle('General Settings');
		$this->setDescription('These settings affect all members in your community.');
     if(!empty($is_element)) {
			$this->addElement('Radio', 'sitestore_showurl_column', array(
					'label' => 'Custom Store URL',
					'description' => 'Do you want to enable Store Admins to create their custom Store URL during Store creation? (If enabled, a URL field will be available to users at the time of creating a Store.)',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
          'onclick' => 'showediturl(this.value)',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showurl.column', 1),
			));

			$this->addElement('Radio', 'sitestore_edit_url', array(
					'label' => 'Edit Custom Store URL',
					'description' => 'Do you want to enable Store Admins to edit their custom Store URL?',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.edit.url', 0),
			));

      $this->addElement('Radio', 'sitestore_change_url', array(
					'label' => 'Automatically Shorten Store URLs',
					'description' => 'Do you want the Store URLs to be shortened upon the number of Likes on them exceeding the Likes limit? (You can choose the Likes limit below. Selecting “Yes” will change the URLs of those Stores from the form: “/store/store_url” to: “/store_url”.)',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
					'onclick' => 'showurl(this.value)',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.change.url', 1),
			));
			
			$this->addElement('Text', 'sitestore_likelimit_forurlblock', array(
					'label' => 'Likes Limit for Active Short URL',
					'allowEmpty' => false,
					'maxlength' => '3',
					'required' => true,
					'description' => 'Please enter the minimum number of Likes after which Store URLs should be shortened. (Note: It is recommended to enter ‘5’ minimum number of Likes.)',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.likelimit.forurlblock', 5),
			));   

//      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
//      $this->addElement('Dummy', 'sitestore_manifestUrlP', array(
//        'label' => 'Stores URL alternate text for "stores"',
//        'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Please %1$sedit%2$s the text which want to display in place of "stores" in the URLs of Stores / Marketplace - Ecommerce Plugin plugin.'),"<a href='" . $view->baseUrl() . "/admin/sitestore/settings#sitestore_manifestUrlP-label' target='_blank'>","</a>"),
//      ));
//      $this->getElement('sitestore_manifestUrlP')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));
//      $this->addElement('Dummy', 'sitestore_manifestUrlS', array(
//        'label' => 'Stores URL alternate text for "store"',
//        'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Please %1$sedit%2$s the text which want to display in place of "store" in the URLs of Stores / Marketplace - Ecommerce Plugin plugin.'),"<a href='" . $view->baseUrl() . "/admin/sitestore/settings#sitestore_manifestUrlS-label' target='_blank'>","</a>"),
//      ));
//				$this->getElement('sitestore_manifestUrlS')->getDecorator('Description')->setOptions(array('placement',
//	'APPEND', 'escape' => false));
      $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
));
    }
    else {
			$this->addElement('Radio', 'sitestore_change_url', array(
					'label' => 'Automatically Shorten Store URLs',
					'description' => 'Do you want the Store URLs to be shortened upon the number of Likes on them exceeding the Likes limit? (You can choose the Likes limit below. Selecting “Yes” will change the URLs of those Stores from the form: “/store/store_url” to: “/store_url”.)',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
					'onclick' => 'showurl(this.value)',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.change.url', 1),
			));
			
			$this->addElement('Text', 'sitestore_likelimit_forurlblock', array(
					'label' => 'Likes Limit for Active Short URL',
					'allowEmpty' => false,
					'maxlength' => '3',
					'required' => true,
					'description' => 'Please enter the minimum number of Likes after which Store URLs should be shortened. (Note: It is recommended to enter ‘5’ minimum number of Likes.)',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.likelimit.forurlblock', 5),
			));
			
			$this->addElement('Button', 'submit', array(
        'label' => 'Proceed to activate Plugin',
        'type' => 'submit',
        'ignore' => true
    ));
    }
  }

}
?>
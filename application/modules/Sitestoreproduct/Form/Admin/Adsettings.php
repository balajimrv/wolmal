<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adsettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Admin_Adsettings extends Engine_Form {

  public function init() {

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$enable_ads) {
      $this->addElement('Dummy', 'note', array(
          'description' => '<div class="tip"><span>' . sprintf(Zend_Registry::get('Zend_Translate')->_('This plugin provides deep integration for advertising using our "%1$sAdvertisements / Community Ads Plugin%2$s". Please install this plugin after downloading it from your Client Area on SocialEngineAddOns and enable this plugin to configure settings for the various ad positions and widgets available. You may purchase this plugin %1$sover here%2$s. <br />This plugin also has an integration with our "%3sAdvertisements / Community Ads - Sponsored Stories Extension%4s" which will enable your users to create a short version of an Activity Feed Story for friend’s action on their contents. To know more about this plugin, please visit it %3sover here%4s.'), '<a href="http://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin" target="_blank">', '</a>', '<a href="http://www.socialengineaddons.com/adsextensions/socialengine-advertisements-sponsored-stories" target="_blank">', '</a>') . '</span></div>',
          'decorators' => array(
              'ViewHelper', array(
                  'description', array('placement' => 'APPEND', 'escape' => false)
          ))
      ));
    }
    
    if ($enable_ads) {
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $url = $view->url(array('controller' => 'widgets', 'action' => 'manage', 'module' => 'communityad'), 'admin_default', true);
      $this
              ->setTitle('Ad Settings')
              ->setDescription('This plugin provides seamless integration with the "Advertisements / Community Ads Plugin". Attractive advertising can be done using the many available, well designed ad positions in this plugin. Below, you can configure the settings for the various ad positions and widgets.');

      $this->addElement('Dummy', 'note', array(
           'description' => '<div class="tip"><span>' .Zend_Registry::get('Zend_Translate')->_("Below, you can configure settings for non-widgetize pages of this plugin. To configure the settings for widgetize pages of this plugin, you need to place \"Display Advertisements\" widget on widgetize pages.") . '</span></div>',
          'decorators' => array(
              'ViewHelper', array(
                  'description', array('placement' => 'APPEND', 'escape' => false)
          ))
      ));

      $this->addElement('Radio', 'sitestoreproduct_communityads', array(
          'label' => 'Community Ads in this plugin',
          'description' => 'Do you want to show community ads in the various positions available in this plugin? (Below, you will be able to choose for every individual position. If you do not want to show ads in a particular position, then please enter the value "0" for it below.).',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'showads(this.value)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1),
      ));

      $this->addElement('Text', 'sitestoreproduct_adalbumcreate', array(
          'label' => "Product Photo's Add Page",
          'maxlenght' => 3,
          'description' => "How many ads will be shown on a product photo's add page?",
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adalbumcreate', 2),
      ));

      $this->addElement('Text', 'sitestoreproduct_addiscussionview', array(
          'label' => 'Product Discussion\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a product discussion\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.addiscussionview', 2),
      ));

      $this->addElement('Text', 'sitestoreproduct_addiscussioncreate', array(
          'label' => 'Product Discussion\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a product discussion\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.addiscussioncreate', 2),
      ));

      $this->addElement('Text', 'sitestoreproduct_addiscussionreply', array(
          'label' => 'Product Discussion\'s Post Reply Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a discussion\'s post reply page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.addiscussionreply', 2),
      ));

      $this->addElement('Text', 'sitestoreproduct_adtopicview', array(
          'label' => 'Product Topics\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a product topic\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adtopicview', 2),
      ));

      $this->addElement('Text', 'sitestoreproduct_advideocreate', array(
          'label' => 'Product Video\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a product video\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.advideocreate', 2),
      ));

      $this->addElement('Text', 'sitestoreproduct_advideoedit', array(
          'label' => 'Product Video\'s Edit Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a product video\'s edit page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.advideoedit', 2),
      ));

      $this->addElement('Text', 'sitestoreproduct_advideodelete', array(
          'label' => 'Product Video\'s Delete Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a product video\'s delete page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.advideodelete', 1),
      ));

      $this->addElement('Text', 'sitestoreproduct_adtagview', array(
          'label' => 'Browse Tags Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on browse tags page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adtagview', 1),
      ));

      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }

}
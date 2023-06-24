<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Overview extends Engine_Form {

  public $_error = array();

  public function init() {

    $product_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id', null);
    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET TINYMCE SETTINGS
    $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
    $upload_url = "";
    if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
      $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'sitestoreproduct_general', true);
    }

    $this->setTitle("Edit Product Overview")
            ->setDescription("Edit the overview for your product using the editor below, and then click 'Save Overview' to save changes.")
            ->setAttrib('name', 'sitestoreproducts_overview');
    
    $this->addElement('TinyMce', 'overview', array(
        'label' => '',
        'allowEmpty' => false,
        'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),

        'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
        'filters' => array(new Engine_Filter_Censor()),
    ));

    $sitestoreproduct_api = Engine_Api::_()->sitestoreproduct();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $multilanguage_allow = $settings->getSetting('sitestoreproduct.multilanguage', 0);
    $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');
    $languages = $settings->getSetting('sitestoreproduct.languages');
    $total_allowed_languages = Count($languages);

    if (empty($total_allowed_languages)) {
      $languages[$defaultLanguage] = Zend_Registry::get('Zend_Translate')->_('English');
    }
    $localeMultiOptions = $sitestoreproduct_api->getLanguageArray();
    
    if( !empty($languages) ) {
    foreach ($languages as $label) {

      $lang_name = $label;
      if (isset($localeMultiOptions[$label])) {
        $lang_name = $localeMultiOptions[$label];
      }

      $required_title_body = false;
      $allowEmpty_title_body = true;
      if ($defaultLanguage == $label || empty($multilanguage_allow) || $total_allowed_languages == 1) {
        $required_title_body = true;
        $allowEmpty_title_body = false;
      }
      if ($label == 'en') {
        $required_title_body = true;
        $allowEmpty_title_body = false;
      }

      $title_field = "overview_$label";

      $overview_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Product overview in %s"), $lang_name);

      if (empty($multilanguage_allow) || (!empty($multilanguage_allow) && $total_allowed_languages <= 1)) {
        $title_field = "overview";
        $overview_label = "Overview";
      } elseif ($label == 'en' && $total_allowed_languages > 1) {
        $title_field = "overview";
      }
      
    $this->addElement('TinyMce', $title_field, array(
        'label' => $overview_label,
        'allowEmpty' => false,
        'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px; height:858px;'),

        'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
        'filters' => array(new Engine_Filter_Censor()),
    ));
    }
    }
    
    $this->addElement('Button', 'save', array(
        'label' => 'Save Overview',
        'type' => 'submit',
    ));
  }

}

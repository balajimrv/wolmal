<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CompareProfileFieldsSitestoreproduct.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_View_Helper_CompareProfileFieldsSitestoreproduct extends Fields_View_Helper_FieldAbstract {

  public function compareProfileFieldsSitestoreproduct($subject, $partialStructure, $map, $field) {
    if (empty($partialStructure)) {
      return '';
    }

    if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity()) {
      return '';
    }
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    // START CUSTOM FIELDS FOR PROFILES WHICH SELECTED
    // Generate
    $content = '';


    $value = $field->getValue($subject);
    if (!$field || $field->type == 'profile_type')
      return;


    // Heading
    if ($field->type == 'heading') {
      
    } else if ($field->type == 'checkbox') {
      $content = isset($value->value) && !empty($value->value) ? Zend_Registry::get('Zend_Translate')->_("Yes") : Zend_Registry::get('Zend_Translate')->_("No");
    }

    // Normal fields
    else {
      $content = $tmp = $this->getFieldValueString($field, $value, $subject, $map, $partialStructure);
    }
    return $content;
  }

  public function getFieldValueString($field, $value, $subject, $map = null, $partialStructure = null) {
    if ((!is_object($value) || !isset($value->value)) && !is_array($value)) {
      return null;
    }

    // @todo This is not good practice:
    // if($field->type =='textarea'||$field->type=='about_me') $value->value = nl2br($value->value);

    $helperName = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');
    if (!$helperName) {
      return null;
    }

    $helper = $this->view->getHelper($helperName);
    if (!$helper) {
      return null;
    }

    $helper->structure = $partialStructure;
    $helper->map = $map;
    $helper->field = $field;
    $helper->subject = $subject;
    $tmp = $helper->$helperName($subject, $field, $value);
    unset($helper->structure);
    unset($helper->map);
    unset($helper->field);
    unset($helper->subject);

    return $tmp;
  }

}

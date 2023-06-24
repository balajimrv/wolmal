<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProfileFieldValueLoop.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_View_Helper_ProfileFieldValueLoop extends Fields_View_Helper_FieldAbstract {

  public function profileFieldValueLoop($subject, $partialStructure) {
    if (empty($partialStructure)) {
      return '';
    }

    if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity()) {
      return '';
    }
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    // START CUSTOM FIELDS FOR PROFILES WHICH SELECTED
    $profileField_level = 1;
    $field_id = array();
    $fieldsProfile = array();
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {

      $profileField_level = Engine_Api::_()->sitestore()->getPackageProfileLevel($subject->store_id);

      if ($profileField_level == 2) {
        $fieldsProfile = Engine_Api::_()->sitestore()->getProfileFields($subject->store_id);
      }
    } else {

      $store_owner = Engine_Api::_()->getItem('user', $subject->owner_id);
      $profileField_level = Engine_Api::_()->authorization()->getPermission($store_owner->level_id, "sitestore_store", "profile");
      if ($profileField_level == 2) {
        $fieldsProfile = Engine_Api::_()->sitestore()->getLevelProfileFields($store_owner->level_id);
      }
    }

    if (empty($profileField_level)) {
      return;
    } elseif ($profileField_level == 2) {
      foreach ($fieldsProfile as $k => $v) {
        $explodeField = explode("_", $v);
        $field_id[] = $explodeField['2'];
      }
    }
    // END CUSTOM FIELDS FOR PROFILES WHICH SELECTED
    // Generate
    $content = '';
    $lastContents = '';
    $lastHeadingTitle = null; //Zend_Registry::get('Zend_Translate')->_("Missing heading");

    $viewer = Engine_Api::_()->user()->getViewer();
    $show_hidden = $viewer->getIdentity() ? ($subject->getOwner()->isSelf($viewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type) : false;

    foreach ($partialStructure as $map) {

      // Get field meta object
      $field = $map->getChild();

      // START CUSTOM FIELDS FOR PROFILES WHICH SELECTED
      if ($profileField_level == 2) {
        $key_test = $map->getKey();

        $explode = explode("_", $key_test);
        if ($explode['0'] != "1") {
          if (!in_array($explode['0'], $field_id)) {
            continue;
          }
          $field_id[]=$explode['2'];
        } else {

          if (!in_array($key_test, $fieldsProfile)) {
            continue;
          }
        }
      }
      // END CUSTOM FIELDS FOR PROFILES WHICH SELECTED


      $value = $field->getValue($subject);
      if (!$field || $field->type == 'profile_type')
        continue;
      if (!$field->display && !$show_hidden)
        continue;

      // Heading
      if ($field->type == 'heading') {
        if (!empty($lastContents)) {
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
          $lastContents = '';
        }
        $lastHeadingTitle = $this->view->translate($field->label);
      }

      // Normal fields
      else {
        $tmp = $this->getFieldValueString($field, $value, $subject, $map, $partialStructure);
        if (!empty($tmp)) {

          $notice = !$field->display && $show_hidden ? sprintf('<div class="tip"><span>%s</span></div>', $this->view->translate('This field is hidden and only visible to you and admins:')) : '';
          $label = $this->view->translate($field->label) . ":";
          $lastContents .= <<<EOF
  <li>
    {$notice}
    <span>
      {$label}
    </span>
    <span>
      {$tmp}
    </span>
  </li>
EOF;
        }


        $lastContents .= '';
        $lastContents;
      }
    }

    if (!empty($lastContents)) {
      $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
    }

    return $content;
  }

  public function getFieldValueString($field, $value, $subject, $map = null, $partialStructure = null) {
    if ((!is_object($value) || !isset($value->value)|| empty ($value->value)) && !is_array($value)) {
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

  protected function _buildLastContents($content, $title) {
    if (!$title) {
      return '<ul>' . $content . '</ul>';
    }
    return <<<EOF
        <div class="profile_fields">
          <h4>
            <span>{$title}</span>
          </h4>
          <ul>
            {$content}
          </ul>
        </div>
EOF;
  }

}

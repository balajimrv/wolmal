<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DataHtmlAttribs.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_View_Helper_DataHtmlAttribs extends Zend_View_Helper_HtmlElement {

  public function dataHtmlAttribs($name, $attribs = array()) {
    if (!Engine_API::_()->sitemobile()->isSiteMobileModeEnabled()) {
      return '';
    }
    $getAttribType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.attribs.name', null);
    $attribsDeafult = $this->getDefaultAttribs($name, $getAttribType);
    $attribs = array_merge($attribsDeafult, $attribs);
    return $this->_htmlAttribs($attribs);
  }

  protected function getDefaultAttribs($name, $getAttribType = array()) {
    $sitemobileSettingsApi = Engine_Api::_()->getApi('settings', 'sitemobile');
    $sitemobileApi = Engine_Api::_()->sitemobile();
    $defaultAttribs = array();
    $settingsParams = array();
    if ($name == 'page') {
      $defaultAttribs['data-role'] = 'page';
      $defaultAttribs['data-transition'] = 'none';
      if ($sitemobileApi->isApp()) {
        $defaultAttribs['data-layout'] = 'fixed';
      }
    } else if ($name == 'page_header') {
      $defaultAttribs['data-role'] = "header";
      $defaultAttribs['data-theme'] = "a";
      if (!$sitemobileApi->isApp()) {
        $settingsParams['dafaultValue'] = 'false';
        $header_data_position = $sitemobileSettingsApi->getSetting('sitemobile.header.position', $settingsParams);
        $defaultAttribs['data-position'] = $header_data_position;
      } else {
        $defaultAttribs['data-position'] = "fixed";
      }
      if ($defaultAttribs['data-position'] === 'fixed') {
        $defaultAttribs['data-tap-toggle'] = "false";
        $defaultAttribs['data-transition'] = "none";
      }
    } else if ($name == 'page_footer') {
      $defaultAttribs['data-role'] = "footer";
      $defaultAttribs['data-theme'] = "a";
      if (!$sitemobileApi->isApp()) {
        $settingsParams['dafaultValue'] = 'false';
        $footer_data_position = $sitemobileSettingsApi->getSetting('sitemobile.footer.position', $settingsParams);
        $defaultAttribs['data-position'] = $footer_data_position;
      } else {
        $defaultAttribs['data-position'] = "fixed";
      }
      if ($defaultAttribs['data-position'] === 'fixed') {
        $defaultAttribs['data-tap-toggle'] = "false";
        $defaultAttribs['data-transition'] = "none";
      }
    } else if ($name == 'go_back_button') {
      $defaultAttribs['data-icon'] = 'chevron-left';
      $defaultAttribs['data-theme'] = 'b';
      $defaultAttribs['data-iconpos'] = 'left';
      $defaultAttribs['data-inline'] = 'true';
      $defaultAttribs['data-mini'] = 'true';
    } else if ($name == 'page_not_found') {
      $defaultAttribs['data-collapsed-icon'] = 'alert';
      $defaultAttribs['data-expanded-icon'] = 'alert';
      $defaultAttribs['data-theme'] = 'e';
      $defaultAttribs['data-content-theme'] = 'e';
    } else if ($name == 'private_page') {
      $defaultAttribs['data-collapsed-icon'] = 'alert';
      $defaultAttribs['data-expanded-icon'] = 'alert';
      $defaultAttribs['data-theme'] = 'e';
      $defaultAttribs['data-content-theme'] = 'e';
    } else if ($name == 'navigation') {
      //  $defaultAttribs['data-theme'] = 'a';
      //  $defaultAttribs['data-mini'] = 'true';
//        } else if ($name == 'navigation_more_popup') {
//          $defaultAttribs['data-theme'] = 'c';
//          $defaultAttribs['data-divider-theme'] = 'c';
    } else if ($name == 'navigation_dashboard') {
      $defaultAttribs['data-divider-theme'] = 'a';
    } else if ($name == 'dashboard_panel') {
      $defaultAttribs['data-position'] = "left"; // left , right
      if ($sitemobileApi->isApp()) {
        $defaultAttribs['data-position-fixed'] = "true"; // left , right
      }
    } else if ($name == 'dashboard_menu_button') {
      $defaultAttribs['data-icon'] = 'reorder';
      $defaultAttribs['data-iconpos'] = "notext"; // left , right. top, bottom,notext
 //     $defaultAttribs['data-prefetch'] = "true"; (Problem in case login / logout)
//        } else if ($name == 'popup_content') {
//          $defaultAttribs['data-theme'] = 'c';
      $defaultAttribs['data-logo'] = "true";
    } else if ($name == 'dialog') {
      $defaultAttribs['data-close-btn'] = "right"; // left , right
      $defaultAttribs['data-overlay-theme'] = "a";
      $defaultAttribs['data-theme'] = "c";
      $defaultAttribs['data-tolerance'] = "15,15";
      $defaultAttribs['data-corners'] = "true";
//        } else if ($name == 'dialog_success') {
//          $defaultAttribs['data-overlay-theme'] = "a";
//          $defaultAttribs['data-theme'] = "a";
//          $defaultAttribs['data-tolerance'] = "15,15";
//          $defaultAttribs['data-corners'] = "true";
    } else if ($name == 'form_button_submit') {
      $defaultAttribs['data-theme'] = "b";
    } else if ($name == 'form_button_reset') {
      $defaultAttribs['data-theme'] = "c";
    } else if ($name == 'form_button_button') {
      $defaultAttribs['data-theme'] = "c";
    }


    $defaultAttribs = empty($getAttribType) ? $getAttribType : $defaultAttribs;

//    else if ($name == 'page_content') {
//      $defaultAttribs['data-role'] = "content";
//      $defaultAttribs['data-theme'] = "c";
//       $defaultAttribs['data-theme'] = "a";
//    }
//    } else if ($name == 'page_header_home_button') {
//
//      $defaultAttribs['data-role'] = "button";
//      $defaultAttribs['data-iconpos'] = "notext";
//      $defaultAttribs['data-icon'] = "home";
//      $defaultAttribs['data-prefetch'] = "true";
//    } elseif ($name == 'form_select') {
//       $defaultAttribs['data-native-menu'] = "false";
//    } else
    return $defaultAttribs;
  }

}

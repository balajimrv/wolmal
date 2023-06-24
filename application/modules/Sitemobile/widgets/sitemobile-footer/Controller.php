<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_SitemobileFooterController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Disable widget in case of REST ANDROID APP calling
    $session = new Zend_Session_Namespace();
    if(isset($session->hideHeaderAndFooter) && !empty($session->hideHeaderAndFooter))
        return $this->setNoRender();
    
    $this->view->showsFooterOptions = $this->_getParam('shows', array("copyright", "menusFooter", "languageChooser", "affiliateCode"));
    $sitemobileFooter = Zend_Registry::isRegistered('sitemobileFooter') ?  Zend_Registry::get('sitemobileFooter') : null;
    if (in_array('menusFooter', $this->view->showsFooterOptions)) {
      $this->view->navigation = $navigation = Engine_Api::_()
              ->getApi('menus', 'sitemobile')
              ->getNavigation('core_footer');
    }
    if (in_array('languageChooser', $this->view->showsFooterOptions)) {
      // Languages
      $translate = Zend_Registry::get('Zend_Translate');
      $languageList = $translate->getList();

      //$currentLocale = Zend_Registry::get('Locale')->__toString();
      // Prepare default langauge
      $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
      if (!in_array($defaultLanguage, $languageList)) {
        if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
          $defaultLanguage = 'en';
        } else {
          $defaultLanguage = null;
        }
      }

      // Prepare language name list
      $languageNameList = array();
      $languageDataList = Zend_Locale_Data::getList(null, 'language');
      $territoryDataList = Zend_Locale_Data::getList(null, 'territory');

      foreach ($languageList as $localeCode) {
        $languageNameList[$localeCode] = Zend_Locale::getTranslation($localeCode, 'language', $localeCode);
        if (empty($languageNameList[$localeCode])) {
          list($locale, $territory) = explode('_', $localeCode);
          $languageNameList[$localeCode] = "{$territoryDataList[$territory]} {$languageDataList[$locale]}";
        }
      }
      $languageNameList = array_merge(array(
          $defaultLanguage => $defaultLanguage
              ), $languageNameList);
      $this->view->languageNameList = $languageNameList;
    }
    if (in_array('affiliateCode', $this->view->showsFooterOptions)) {
      // Get affiliate code
      $this->view->affiliateCode = Engine_Api::_()->getDbtable('settings', 'core')->core_affiliate_code;
    }
    if(empty($sitemobileFooter)) {
      return $this->setNoRender();
    }
    if(Engine_Api::_()->sitemobile()->isApp() && count($languageNameList) == 1){
      return $this->setNoRender();
    }
  }

  public function getCacheKey() {
    //return true;
  }

  public function setLanguage() {
    
  }

}
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CompareController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_CompareController extends Core_Controller_Action_Standard {

  //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
      return;
  
    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
      return;
  }
  
  public function compareAction() {
    
    $request = $this->getRequest();
    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $cookiesSuffix = $viewer_id ? "_" . $viewer_id : '';
    $cookiesContent = $request->getCookie('srCompareProductTypes' . $cookiesSuffix, '');
    $productTypes = ($cookiesContent && $cookiesContent != 'undefined') ? Zend_Json_Decoder::decode($cookiesContent) : null;
    if (empty($productTypes)) {
      return;
    }
    $this->view->customFields = array();
    $this->view->ratingsParams = array();
    $category_id = $this->_getParam('id');
    $cookiesList = $request->getCookie('stCompareProduct' . "st" . $category_id . $cookiesSuffix, '');
    $cookieDataList = ($cookiesList && $cookiesList != 'undefined') ? Zend_Json_Decoder::decode($cookiesList) : null;
    $compareSettingsTable = Engine_Api::_()->getDbtable('compareSettings', 'sitestoreproduct');
    $this->view->compareSettingList = $compareSettingList = $compareSettingsTable->getCompareList(array(
        'category_id' => $category_id,
        'fetchRow' => 1
            ));
    if (empty($cookieDataList) || !$compareSettingList->enabled) {
      return;
    }
    $this->view->category = $category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);

    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid()) {
      return;
    }

    $description_list_type = $this->view->translate('Products');
    $this->view->category_id = $category_id;
    $description_list_type = $this->view->htmlLink($this->view->url(array(), 'sitestoreproduct_general', true), $description_list_type, array());
    $category_array = array("0" => $description_list_type);
    $category_array[$category->category_id] = $this->view->htmlLink($category->getHref(), $this->view->translate($category->category_name), array());
    $category_ids = array($category->category_id);
    $dependency = $cat_dependency = $category->cat_dependency;
    while ($dependency != 0) {
      $parent_category = Engine_Api::_()->getItem('sitestoreproduct_category', $dependency);
      $category_array[$parent_category->category_id] = $this->view->htmlLink($parent_category->getHref(), $this->view->translate($parent_category->category_name), array());

      $dependency = $parent_category->cat_dependency;
      $category_ids[] = $parent_category->category_id;
    };
    ksort($category_array);
    $this->view->heading = join(" &raquo; ", $category_array);
    $list_ids = array_keys($cookieDataList);
    $category_id_key = 'category_id';
    $mapping = count($category_ids);
    for ($i = $mapping; $i > 1; $i--) {
      $category_id_key = 'sub' . $category_id_key;
    }
    $this->view->lists = $lists = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProducts(array(
        'list_ids' => $list_ids,
        $category_id_key => $category_id
            ));
    $this->view->totalList = count($lists);
    if ($compareSettingList->editor_rating_fields || $compareSettingList->user_rating_fields)
      $this->view->ratingsParams = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct')->reviewParams($category_ids, 'sitestoreproduct_product');
    $this->view->compareSettingListEditorRatingFields = $compareSettingListEditorRatingFields = !empty($compareSettingList->editor_rating_fields) ? Zend_Json_Decoder::decode($compareSettingList->editor_rating_fields) : array();
    $this->view->compareSettingListUserRatingFields = $compareSettingListUserRatingFields = !empty($compareSettingList->user_rating_fields) ? Zend_Json_Decoder::decode($compareSettingList->user_rating_fields) : array();
    $this->view->seacore_api = Engine_Api::_()->seaocore();

    $this->view->compareSettingListCustomFields = $compareSettingListCustomFields = !empty($compareSettingList->custom_fields) ? Zend_Json_Decoder::decode($compareSettingList->custom_fields) : array();
    if (count($compareSettingListCustomFields) > 0) {
      $maaping_category_ids = array();
      foreach ($lists as $sitestoreproduct) {
        if ($sitestoreproduct->category_id) {
          $maaping_category_ids[$sitestoreproduct->category_id] = $sitestoreproduct->category_id;
          if ($sitestoreproduct->subcategory_id) {
            $maaping_category_ids[$sitestoreproduct->subcategory_id] = $sitestoreproduct->subcategory_id;
            if ($sitestoreproduct->subsubcategory_id) {
              $maaping_category_ids[$sitestoreproduct->subsubcategory_id] = $sitestoreproduct->subsubcategory_id;
            }
          }
        }
      }
      // Comment Privacy
    $this->view->isCommentsAllow = Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product");
      $this->view->proifle_map_ids = $proifle_map_ids = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getAllProfileTypes($maaping_category_ids);
      $customFields = array();

      foreach ($proifle_map_ids as $proifle_map_id) {
        $selectOption = Engine_Api::_()->getDbtable('metas', 'sitestoreproduct')->getProfileFields($proifle_map_id);
        if ($selectOption) {
          foreach ($selectOption as $key => $value) {
            $customFields[$key] = $value;
          }
        }
      }

      $this->view->customFields = $customFields;
      if (empty($this->view->customFields))
        $this->view->customFields = array();
      $this->view->fieldsApi = Engine_Api::_()->fields();
    }

    //GET PRODUCT TITLE

      $siteinfo = $this->view->layout()->siteinfo;
      $titles = $siteinfo['title'];
      $keywords = $siteinfo['keywords'];
      $product_type_title = Zend_Registry::get('Zend_Translate')->_('Products');
      if (!empty($titles))
        $titles .= ' - ';
      $titles .= $product_type_title;
      $siteinfo['title'] = $titles;

      if (!empty($keywords))
        $keywords .= ' - ';
      $keywords .= $product_type_title;
      $siteinfo['keywords'] = $keywords;

      $this->view->layout()->siteinfo = $siteinfo;


    // Render
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;
  }

}

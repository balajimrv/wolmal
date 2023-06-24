<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Plugin_Menus {

  public function isLogin(){
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if(empty ($viewer_id)){
      return false;
    }   
    return true;
  }
  
  public function makeDocumentUrl(){
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')){
      return array(
        'route' => 'admin_default',
        'module' => 'sitestoredocument',
        'controller' => 'settings'
      );
    }
    return array(
      'route' => 'admin_default',
      'module' => 'sitestoreproduct',
      'controller' => 'document'
    );
  }
  
  public function showAdminCommissionTab()
  {
    $isPluginActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.isActivate', null);
    if(empty($isPluginActive))
      return false;
    
    $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
    if( empty($isAdminDrivenStore) ) {
      $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
      if( empty($isPaymentToSiteEnable) ) {
        return true;
      }
    }
    return false;
  }
  
  public function showAdminPaymentRequestTab()
  {
    $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
    if( empty($isAdminDrivenStore) ) {
      $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
      if( empty($isPaymentToSiteEnable) ) {
        return false;
      }
    }
    return true;
  }

    public function myWishlist($row)
  {
    return array(
      'route' => 'sitestoreproduct_general',
      'action' => 'account',
      'params' => array(
         "menuType" => "my-wishlists",
      ),
    );
  }

    public function canCreateSitestoreproducts($row) {

    //MUST BE LOGGED IN USER
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    //MUST BE ABLE TO VIEW PRODUCTS
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "view")) {
      return false;
    }

    //MUST BE ABLE TO CRETE PRODUCTS
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "create")) {
      return false;
    }

    return true;
  }

  public function canViewSitestoreproducts($row) {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //MUST BE ABLE TO VIEW PRODUCTS
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "view")) {
      return false;
    }

    return true;
  }
  
  public function canBrowseLocation($row){
    
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0))
            return false;
    
   return true; 
  }

  public function canViewBrosweReview($row) {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //MUST BE ABLE TO VIEW WISHLISTS
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "view")) {
      return false;
    }
    $request = Zend_Controller_Front::getInstance()->getRequest();

      if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)))
        return false;

      $route['route'] = 'sitestoreproduct_review_browse';
      if ('sitestoreproduct' == $request->getModuleName() &&
              'review' == $request->getControllerName() &&
              'browse' == $request->getActionName()) {
        $route['active'] = true;
      }
      return $route;

  }

  public function canViewCategories($row) {
    
    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //MUST BE ABLE TO VIEW WISHLISTS
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "view")) {
      return false;
    }
    $request = Zend_Controller_Front::getInstance()->getRequest();


      $route['route'] = 'sitestoreproduct_review_categories';
      $route['action'] = 'categories';
      if ('sitestoreproduct' == $request->getModuleName() &&
              'index' == $request->getControllerName() &&
              'categories' == $request->getActionName()) {
        $route['active'] = true;
      } else {
        $route['active'] = false;
      }
      return $route;

  }

  public function canViewWishlist($row) {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //MUST BE ABLE TO VIEW WISHLISTS
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "view")) {
      return false;
    }

    //MUST BE ABLE TO VIEW WISHLISTS
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_wishlist', $viewer, "view")) {
      return false;
    }

    return true;
  }

  public function canViewEditors($row) {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //MUST BE ABLE TO VIEW PRODUCTS
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "view")) {
      return false;
    }

    $editorsCount = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->getEditorsCount(0);

    if ($editorsCount <= 0) {
      return false;
    }

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2 || !(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2))) {
        return false;
      }
    

    return true;
  }

  public function userProfileWishlist() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //MUST BE ABLE TO VIEW WISHLISTS
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_wishlist', $viewer, "view")) {
      return false;
    }

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('user')) {
      return false;
    }

    $user = Engine_Api::_()->core()->getSubject('user');

    $wishlist_id = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct')->getRecentWishlistId($user->user_id);

    if (!empty($wishlist_id)) {
      return array(
          'class' => 'buttonlink',
          'route' => 'sitestoreproduct_wishlist_general',
          'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/images/icons/wishlist.png',
          'params' => array(
            'text' => $user->getTitle(),
          ),
      );
    } else {
      return false;
    }
  }

  public function sitestoreproductGutterEdit($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET PRODUCT SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //AUTHORIZATION CHECK
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && !$sitestoreproduct->authorization()->isAllowed($viewer, "edit")) {
      return false;
    }

    return array(
        'class' => 'buttonlink seaocore_icon_edit',
        'route' => "sitestoreproduct_specific",
        'action' => 'edit',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
        ),
    );
  }

  public function sitestoreproductGutterEditoverview($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET PRODUCT SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.overview', 1) || !$sitestoreproduct->authorization()->isAllowed($viewer, 'edit') || !$sitestoreproduct->authorization()->isAllowed($viewer, 'overview'))) {
      return false;
    }

    return array(
        'class' => 'buttonlink sitestoreproduct_gutter_editoverview',
        'route' => "sitestoreproduct_specific",
        'action' => 'overview',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
        ),
    );
  }

  public function sitestoreproductGutterEditstyle($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET PRODUCT SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit') || !$sitestoreproduct->authorization()->isAllowed($viewer, 'style'))) {
      return false;
    }

    return array(
        'class' => 'buttonlink sitestoreproduct_gutter_editstyle',
        'route' => "sitestoreproduct_specific",
        'action' => 'editstyle',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
        ),
    );
  }

  public function sitestoreproductGutterShare() {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //RETURN IF VIEWER IS EMPTY
    if (empty($viewer_id)) {
      return false;
    }

    return array(
        'class' => 'smoothbox seaocore_icon_share buttonlink',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $sitestoreproduct->getType(),
            'id' => $sitestoreproduct->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function sitestoreproductGutterMessageowner($row) { 

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //RETURN IF NOT AUTHORIZED
    if (empty($viewer_id)) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //SHOW MESSAGE OWNER LINK TO USER IF MESSAGING IS ENABLED FOR THIS LEVEL
    $showMessageOwner = 0;
    $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    if ($showMessageOwner != 'none') {
      $showMessageOwner = 1;
    }

    //RETURN IF NOT AUTHORIZED
    if ($sitestoreproduct->owner_id == $viewer_id || empty($viewer_id) || empty($showMessageOwner)) {
      return false;
    }

    return array(
        'class' => 'smoothbox icon_sitestoreproducts_messageowner buttonlink',
        'route' => "sitestoreproduct_specific",
        'action' => 'messageowner',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
        ),
    );
  }

  public function sitestoreproductGutterTfriend($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    return array(
        'class' => 'smoothbox buttonlink icon_sitestoreproducts_tellafriend',
        'route' => "sitestoreproduct_specific",
        'action' => 'tellafriend',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
        ),
    );
  }
  
  public function sitestoreproductGutterOpinionfriend($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    return array(
        'class' => 'smoothbox buttonlink icon_sitestoreproducts_tellafriend',
        'route' => "sitestoreproduct_specific",
        'action' => 'ask-opinion',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
        ),
    );
  }

  public function sitestoreproductGutterPrint($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    return array(
        'class' => 'buttonlink icon_sitestoreproducts_printer',
        'route' => "sitestoreproduct_specific",
        'action' => 'print',
        'target' => '_blank',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
        ),
    );
  }

  public function sitestoreproductGutterPublish($row) {

    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    
    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product') || empty($viewer_id)) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
    

    //RETURN IF NOT AUTHORIZED
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $sitestoreproduct->store_id);

    if( (!empty($isStoreAdmins) || ($viewer->level_id == 1)) && !empty($sitestoreproduct->draft) )
      return array(
            'class' => 'buttonlink smoothbox icon_sitestoreproduct_publish',
            'route' => "sitestoreproduct_specific",
            'action' => 'publish',
            'params' => array(
                'product_id' => $sitestoreproduct->getIdentity()
            ),
      );
    else
      return false;
  }

  public function sitestoreproductGutterEditorPick($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //GET VIEWER DETAILS
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $isEditor = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->isEditor($viewer_id);

    if (empty($isEditor) && $viewer->level_id != 1) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitestoreproducts_similar_item',
        'route' => "sitestoreproduct_editor_general",
        'action' => 'similar-items',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity()
        ),
    );
  }

  public function sitestoreproductGutterReview() {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //NON LOGGED IN USER CAN'T BE THE EDITOR
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //CHECK EDITOR REVIEW IS ALLOWED OR NOT
    $allow_editor_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2);
    if (empty($allow_editor_review) || $allow_editor_review == 2) {
      return false;
    }

    //SHOW THIS LINK ONLY EDITOR FOR THIS PRODUCT
    $isEditor = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->isEditor($viewer_id);
    if (empty($isEditor)) {
      return false;
    }

    //EDITOR REVIEW HAS BEEN POSTED OR NOT
    $params = array();
    $params['resource_id'] = $sitestoreproduct->product_id;
    $params['resource_type'] = $sitestoreproduct->getType();
    $params['type'] = 'editor';
    $params['notIncludeStatusCheck'] = 1;
    $isEditorReviewed = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct')->canPostReview($params);

    $params = array();
    $params['product_id'] = $sitestoreproduct->getIdentity();
    if (!empty($isEditorReviewed)) {

      $editorreview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.editorreview', 0);
      $review = Engine_Api::_()->getItem('sitestoreproduct_review', $isEditorReviewed);
      if (empty($editorreview) && $viewer_id != $review->owner_id) {
        return false;
      }

      $label = Zend_Registry::get('Zend_Translate')->_('Edit an Editor Review');
      $action = 'edit';
      $params['review_id'] = $isEditorReviewed;
    } else {
      $label = Zend_Registry::get('Zend_Translate')->_('Write an Editor Review');
      $action = 'create';
    }

    return array(
        'label' => $label,
        'class' => 'buttonlink icon_sitestoreproducts_review',
        'route' => "sitestoreproduct_extended",
        'controller' => 'editor',
        'action' => $action,
        'params' => $params,
    );
  }

  public function sitestoreproductGutterClose() {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //RETURN IF NOT AUTHORIZED
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $sitestoreproduct->store_id);
    if (!$isStoreAdmins && $viewer_id != $sitestoreproduct->owner_id) {
      return false;
    }

    if (!empty($sitestoreproduct->closed)) {
      $label = Zend_Registry::get('Zend_Translate')->_('Open');
      $class = 'buttonlink icon_sitestoreproducts_open';
    } else {
      $label = Zend_Registry::get('Zend_Translate')->_('Close');
      $class = 'buttonlink icon_sitestoreproducts_close';
    }

    $label = sprintf($label, 'Product');

    return array(
        'label' => $label,
        'class' => $class,
        'route' => 'sitestoreproduct_specific',
        'params' => array(
            'action' => 'close',
            'product_id' => $sitestoreproduct->getIdentity()
        ),
    );
  }

  public function sitestoreproductGutterDelete($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //PRODUCT DELETE PRIVACY
    $can_delete = $sitestoreproduct->authorization()->isAllowed(null, "delete");

    //AUTHORIZATION CHECK
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $sitestoreproduct->store_id);
    if (!$isStoreAdmins && (empty($can_delete) || empty($viewer_id))) {
      return false;
    }

    return array(
        'class' => 'buttonlink seaocore_icon_delete',
        'route' => 'sitestoreproduct_specific',
        'params' => array(
            'action' => 'delete',
            'product_id' => $sitestoreproduct->getIdentity()
        ),
    );
  }

  public function sitestoreproductGutterReport() { 

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (empty($viewer_id)) {
      return false;
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    return array(
        'class' => 'smoothbox buttonlink icon_sitestoreproducts_report',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'route' => 'default',
            'subject' => $sitestoreproduct->getGuid()
        ),
    );
  }

  public function sitestoreproductGutterWishlist($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET PRODUCT SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //AUTHORIZATION CHECK
    if (!empty($sitestoreproduct->draft) || empty($sitestoreproduct->search) || empty($sitestoreproduct->approved)) {
      return false;
    }

    //CHECK WISHLIST CREATION ALLOWED OR NOT
    
    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();    
    
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    //GET LEVEL SETTING
    $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_wishlist', "create");
    
    if(empty($can_create)) {
      return false;
    }

    //AUTHORIZATION CHECK
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_wishlist', $viewer, 'view')) {
      return false;
    }

    return array(
        'class' => 'buttonlink smoothbox sr_sitestoreproduct_icon_wishlist_add',
        'route' => "sitestoreproduct_wishlist_general",
        'action' => 'add',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
        ),
    );
  }

  public function sitestoreproductGutterChangephoto($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return false;
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET PRODUCT SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //AUTHORIZATION CHECK
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && !$sitestoreproduct->authorization()->isAllowed($viewer, "edit")) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitestoreproduct_edit',
        'route' => "sitestoreproduct_specific",
        'action' => 'change-photo',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
        ),
    );
  }
  
  // Wishlist Profile page Gutter 
  public function onMenuInitialize_sitestoreproductWishlistGutterEdit($row) {
    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_wishlist')) {
      return false;
    }
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //RETURN IF VIEWER IS EMPTY
    if (empty($viewer_id)) {
      return false;
    }

    //GET LISTING SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitestoreproduct_wishlist');

    if ($viewer_id != $subject->owner_id)
      return false;

    return array(
        'class' => 'buttonlink smoothbox seaocore_icon_edit',
        'route' => "sitestoreproduct_wishlist_general",
        'action' => 'edit',
        'params' => array(
            'wishlist_id' => $subject->getIdentity(),
        ),
    );
  }

  // Wishlist Profile page Gutter 
  public function onMenuInitialize_sitestoreproductWishlistGutterDelete($row) {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_wishlist')) {
      return false;
    }
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //RETURN IF VIEWER IS EMPTY
    if (empty($viewer_id)) {
      return false;
    }

    //GET LISTING SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitestoreproduct_wishlist');

    if ($viewer_id != $subject->owner_id)
      return false;


    return array(
        'class' => 'buttonlink smoothbox seaocore_icon_delete',
        'route' => "sitestoreproduct_wishlist_general",
        'action' => 'delete',
        'params' => array(
            'wishlist_id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_sitestoreproductWishlistGutterShare() {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_wishlist')) {
      return false;
    }
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //RETURN IF VIEWER IS EMPTY
    if (empty($viewer_id)) {
      return false;
    }
    //GET SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitestoreproduct_wishlist');
    return array(
        'class' => 'smoothbox seaocore_icon_share buttonlink',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_sitestoreproductWishlistGutterReport() {

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_wishlist')) {
      return false;
    }
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //RETURN IF VIEWER IS EMPTY
    if (empty($viewer_id)) {
      return false;
    }

    //GET SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitestoreproduct_wishlist');

    return array(
        'class' => 'smoothbox buttonlink icon_sitestoreproducts_report',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'route' => 'default',
            'subject' => $subject->getGuid()
        ),
    );
  }

  public function onMenuInitialize_sitestoreproductWishlistGutterTfriend($row) {
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) {
      return false;
    }

    //RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_wishlist')) {
      return false;
    }

    //GET SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitestoreproduct_wishlist');

    return array(
        'class' => 'smoothbox buttonlink icon_sitestoreproducts_tellafriend',
        'route' => "sitestoreproduct_wishlist_general",
        'params' => array(
            'action' => 'tell-a-friend',
            'type' => $subject->getType(),
            'wishlist_id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_sitestoreproductWishlistGutterCreate($row) {
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //RETURN IF VIEWER IS EMPTY
    if (empty($viewer_id)) {
      return false;
    }

    return array(
        'class' => 'buttonlink smoothbox sr_icon_wishlist_add',
        'route' => "sitestoreproduct_wishlist_general",
        'action' => 'create',
    );
  }
  
  //SITEMOBILE DISCUSSION VIEW PAGE OPTIONS
  public function onMenuInitialize_SitestoreproductTopicWatch() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();

    $isWatching = null;
      if(!$viewer->getIdentity())
      return false;

		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'sitestoreproduct');
		$isWatching = $topicWatchesTable
						->select()
						->from($topicWatchesTable->info('name'), 'watch')
						->where('resource_id = ?', $sitestoreproduct->getIdentity())
						->where('topic_id = ?', $subject->getIdentity())
						->where('user_id = ?', $viewer->getIdentity())
						->limit(1)
						->query()
						->fetchColumn(0)
		;

		if (false === $isWatching) {
			$isWatching = null;
		} else {
			$isWatching = (bool) $isWatching;
		}

    if(!$isWatching) {
      return array(
        'label' => 'Watch Topic',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'topic',
            'action' => 'watch',
            'watch' => 1,
            'topic_id' => $subject->getIdentity(),
        )
			);
    } else {
			return array(
        'label' => 'Stop Watching Topic',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'sitestoreproduct',
            'controller' => 'topic',
            'action' => 'watch',
            'watch' => 0,
            'topic_id' => $subject->getIdentity(),
        )
			);
    }

	}

  public function onMenuInitialize_SitestoreproductTopicRename() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();
      if(!$sitestoreproduct->isOwner($viewer))
      return false;

		return array(
			'label' => 'Rename',
			'route' => 'default',
      'class' => 'smoothbox ui-btn-default ui-btn-action',
			'params' => array(
					'module' => 'sitestoreproduct',
					'controller' => 'topic',
					'action' => 'rename',
					'topic_id' => $subject->getIdentity(),
			)
		);

	}

  public function onMenuInitialize_SitestoreproductTopicDelete() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();

     if(!$sitestoreproduct->isOwner($viewer))
      return false;

		return array(
			'label' => 'Delete Topic',
			'route' => 'default',
      'class' => 'smoothbox ui-btn-default ui-btn-danger',
			'params' => array(
					'module' => 'sitestoreproduct',
					'controller' => 'topic',
					'action' => 'delete',
					'topic_id' => $subject->getIdentity(),
					'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
			)
		);

	}

  public function onMenuInitialize_SitestoreproductTopicOpen() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();

      if(!$sitestoreproduct->isOwner($viewer))
      return false;

    if(!$subject->closed) {
			return array(
				'label' => 'Close',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'sitestoreproduct',
						'controller' => 'topic',
						'action' => 'close',
						'topic_id' => $subject->getIdentity(),
            'closed'=> 1,
            'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
				)
			);
    } else {
			return array(
				'label' => 'Open',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'sitestoreproduct',
						'controller' => 'topic',
						'action' => 'close',
						'topic_id' => $subject->getIdentity(),
            'closed'=> 0,
            'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
				)
			);
    }

	}

  public function onMenuInitialize_SitestoreproductTopicSticky() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();
    
    if(!$sitestoreproduct->isOwner($viewer))
      return false;

    if(!$subject->sticky) {
			return array(
				'label' => 'Make Sticky',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'sitestoreproduct',
						'controller' => 'topic',
						'action' => 'sticky',
						'topic_id' => $subject->getIdentity(),
            'sticky'=> 1,
            'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
				)
			);
    } else {
			return array(
				'label' => 'Remove Sticky',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'sitestoreproduct',
						'controller' => 'topic',
						'action' => 'sticky',
						'topic_id' => $subject->getIdentity(),
            'sticky'=> 0,
            'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
				)
			);
    }

	}
  
  //SITEMOBILE PHOTO VIEW PAGE OPTIONS

  public function onMenuInitialize_SitestoreproductPhotoShare($row) {

    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if(!SEA_PHOTOLIGHTBOX_SHARE && !$viewer->getIdentity())
      return false;

    return array(
        'label' => 'Share',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_SitestoreproductPhotoReport($row) {

    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if(!SEA_PHOTOLIGHTBOX_REPORT && !$viewer->getIdentity())
      return false;

    return array(
        'label' => 'Report',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        )
    );
  }

  public function onMenuInitialize_SitestoreproductPhotoProfile($row) {

    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if(!SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO && !$viewer->getIdentity())
      return false;

    return array(
        'label' => 'Make Profile Photo',
        'route' => 'user_extended',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'user',
            'controller' => 'edit',
            'action' => 'external-photo',
            'photo' => $subject->getGuid()
        )
    );
  }
  
  //SITEMOBILE REVIEW PROFILE OPTIONS
  
  public function onMenuInitialize_SitestoreproductReviewShare($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();
    
    //GET VIEWER   
    $viewer_id = $viewer->getIdentity();
    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $sitestoreproduct_share = $coreApi->getSetting('sitestoreproduct.share', 1);
    
  if ($sitestoreproduct_share && $sitestoreproduct->owner_id != 0):
    return array(
        'label' => 'Share Review',
        'route' => "default",
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'activity',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
        )
    );
    endif;
    return;
  }
  
  public function onMenuInitialize_SitestoreproductReviewEmail($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();
     //GET VIEWER   
    $viewer_id = $viewer->getIdentity();
    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $sitestoreproduct_email = $coreApi->getSetting('sitestoreproduct.email', 1);
    
  if ($sitestoreproduct_email):
    return array(
        'label' => 'Email Review',
        'route' => "sitestoreproduct_review_general",
        'action' => 'email',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
            'review_id' => $subject->getIdentity(),
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
    endif;
    return;
  }

//
  public function onMenuInitialize_SitestoreproductReviewReport($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();
     //GET VIEWER   
    $viewer_id = $viewer->getIdentity();
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }
    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $sitestoreproduct_report = $coreApi->getSetting('sitestoreproduct.report', 1);
    if ($sitestoreproduct_report && $viewer_id):
      return array(
          'label' => 'Report',
          'route' => 'default',
          'class' => 'ui-btn-action smoothbox',
          'params' => array(
              'module' => 'core',
              'controller' => 'report',
              'action' => 'create',
              'subject' => $subject->getGuid(),
              'product_id' => $sitestoreproduct->getIdentity(),
          // 'format' => 'smoothbox'
          )
      );
    endif;
    return;
  }
  
    public function onMenuInitialize_SitestoreproductReviewDelete($row) { 
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();
     //GET VIEWER   
    $viewer_id = $viewer->getIdentity();
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }
    $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "review_delete");
  if (!empty($can_delete) && ($can_delete != 1 || $viewer_id == $sitestoreproduct->owner_id)) :
    return array(
        'label' => 'Delete Review',
        'route' => "sitestoreproduct_review_general",
        'action' => 'delete',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
            'review_id' => $subject->getIdentity(),
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
    endif;
    return;
  }
  
   public function onMenuInitialize_SitestoreproductReviewUpdate($row) { 
		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();

     //GET VIEWER   
    $viewer_id = $viewer->getIdentity();
    if ($sitestoreproduct->owner_id != $viewer_id) {
      return false;
    }
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//GET REVIEW TABLE
		$reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
		if ($viewer_id) {
			$params = array();
			$params['resource_id'] = $sitestoreproduct->product_id;
			$params['resource_type'] = $sitestoreproduct->getType();
			$params['viewer_id'] = $viewer_id;
			$params['type'] = 'user';
			$hasPosted = $reviewTable->canPostReview($params);
		} else {
			$hasPosted = 0;
		}

		$autorizationApi = Engine_Api::_()->authorization();
		if($autorizationApi->getPermission($level_id, 'sitestoreproduct_product', "review_create") && empty($hasPosted)) {
		  $createAllow = 1;
		} elseif($autorizationApi->getPermission($level_id, 'sitestoreproduct_product', "review_update") && !empty($hasPosted)) {
			$createAllow = 2;
		}
		else {
		  $createAllow = 0;
		}

    if ($createAllow != 2) return;

    return array(
        'label' => 'Update your Review',
        'action' => 'update',
        'route' => "sitestoreproduct_review_general",
        'class' => 'ui-btn-action',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
            'review_id' => $subject->getIdentity(),
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }
  
   //SITEMOBILE PAGE REVIEW MENUS
  public function onMenuInitialize_SitestoreproductReviewCreate($row) { 
		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $sitestoreproduct = $subject->getParent();
     //GET VIEWER   
    $viewer_id = $viewer->getIdentity();
   
    if ($sitestoreproduct->owner_id != $viewer_id) {
      return false;
    }

		//GET REVIEW TABLE
		$reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
		if ($viewer_id) {
      $level_id = $viewer->level_id;
			$params = array();
			$params['resource_id'] = $sitestoreproduct->product_id;
			$params['resource_type'] = $sitestoreproduct->getType();
			$params['viewer_id'] = $viewer_id;
			$params['type'] = 'user';
			$hasPosted = $reviewTable->canPostReview($params);
		} else {
			$hasPosted = 0;
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
		}

		$autorizationApi = Engine_Api::_()->authorization();
		if($autorizationApi->getPermission($level_id, 'sitestoreproduct_product', "review_create") && empty($hasPosted)) {
		  $createAllow = 1;
		} elseif($autorizationApi->getPermission($level_id, 'sitestoreproduct_product', "review_update") && !empty($hasPosted)) {
			$createAllow = 2;
		}
		else {
		  $createAllow = 0;
		}

    if ($createAllow != 1) return;
    return array(
        'label' => 'Write a Review',
        'action' => 'create',
        'route' => "sitestoreproduct_review_general",
        'class' => 'ui-btn-action',
        'params' => array(
            'product_id' => $sitestoreproduct->getIdentity(),
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }
  
}

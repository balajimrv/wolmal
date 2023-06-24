<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dashboardmenus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Plugin_Dashboardmenus {
  
  private function getProductId() {
    //GET STORE ID AND SITESTORE OBJECT
    $product_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id', null);
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
    if ($sitestoreproduct->getType() !== 'sitestoreproduct_product') {
      return false;
    }

    $allowEdit = $sitestoreproduct->authorization()->isAllowed($viewer, 'edit');
    if (empty($allowEdit)) {
      return false;
    }
    return $sitestoreproduct;
  }
  
  public function onMenuInitialize_SitestoreproductIndexEdit($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_specific',
        'action' => 'edit',
        'name' => 'sitestoreproduct_index_edit',
        'tab' => 1,
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
  }
  
  public function onMenuInitialize_SitestoreproductIndexOverview($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $allowOverview = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "overview");
    if ($allowOverview && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.overview', 1)) {
      return array(
          'label' => $row->label,
          'route' => 'sitestoreproduct_specific',
          'action' => 'overview',
          'name' => 'sitestoreproduct_index_overview',
          'tab' => 2,
          'params' => array(
              'product_id' => $sitestoreproduct->product_id
          ),
      );
    }
    else
      return false;
  }
  
  public function onMenuInitialize_SitestoreproductSiteformIndex($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }

    $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($sitestoreproduct->product_id);
    if ($option_id && ($sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'virtual')) {
      return array(
          'label' => $row->label,
          'route' => 'sitestoreproduct_extended',
          'controller' => 'siteform',
          'action' => 'index',
          'name' => 'sitestoreproduct_siteform_index',
          'tab' => 3,
          'params' => array(
              'option_id' => $option_id,
              'product_id' => $sitestoreproduct->product_id
          ),
      );
    }
    else
      return false;
  }
  
  public function onMenuInitialize_SitestoreproductSiteformProductcategoryattribute($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    $allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);
    
     $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($sitestoreproduct->product_id);
    if ($option_id && ($sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'virtual') && !empty($allowCombinations)) {
    return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_extended',
        'controller' => 'siteform',
        'action' => 'product-category-attributes',
        'name' => 'sitestoreproduct_siteform_productcategoryattribute',
        'tab' => 4,
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
  }
  else
    return false;
  }

  public function onMenuInitialize_SitestoreproductDashboardChangephoto($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
   
   $viewer = Engine_Api::_()->user()->getViewer();
   $allowPhotoUpload = Engine_Api::_()->authorization()->isAllowed($sitestoreproduct, $viewer, "photo");
   if(!empty($allowPhotoUpload)){
    return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_dashboard',
        'action' => 'change-photo',
        'name' => 'sitestoreproduct_dashboard_changephoto',
        'tab' => 5,
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
   }
   else{
     return false;
   }
  }
  
  public function onMenuInitialize_SitestoreproductDashboardContact($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowContactDetailsUpload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "contact");
    
    if($allowContactDetailsUpload && Engine_Api::_()->getApi('settings', 'core')->getSetting('temp.sitestoreproduct.contactdetail', array())){
    return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_dashboard',
        'action' => 'contact',
        'name' => 'sitestoreproduct_dashboard_contact',
        'tab' => 6,
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
    }
    else
      return false;
  }
  
  public function onMenuInitialize_SitestoreproductAlbumEditphotos($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowPhotoUpload = Engine_Api::_()->authorization()->isAllowed($sitestoreproduct, $viewer, "photo");
    
    if(empty($allowPhotoUpload))
      return false;
    
    return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_albumspecific',
        'name' => 'sitestoreproduct_album_editphotos',
        'tab' => 7,
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
  }
  
  public function onMenuInitialize_SitestoreproductVideoeditEdit($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowVideoUpload = Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer);
    
    if(empty($allowVideoUpload))
      return false;
    
    return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_videospecific',
        'name' => 'sitestoreproduct_videoedit_edit',
        'tab' => 8,
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
  }
  
  public function onMenuInitialize_SitestoreproductDashboardProductdocument($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
   $allowDocument = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.document.enable', 0);
   
   if(empty($allowDocument))
     return false;
   
    return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_dashboard',
        'action' => 'product-document',
        'name' => 'sitestoreproduct_dashboard_productdocument',
        'tab' => 9,
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
  }
  
  public function onMenuInitialize_SitestoreproductDashboardMetadetail($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowMetaKeywords = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "metakeyword");
    
    if($allowMetaKeywords && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.metakeyword', 1)){
    return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_dashboard',
        'action' => 'meta-detail',
        'name' => 'sitestoreproduct_dashboard_metadetail',
        'tab' => 10,
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
    }
    else
      return false;
  }
  
  public function onMenuInitialize_SitestoreproductIndexEditstyle($row) {

   $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowStyle = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('sitestoreproduct_product', $viewer->level_id, "style");
    if(!empty($allowStyle)){
    return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_specific',
        'action' => 'editstyle',
        'name' => 'sitestoreproduct_index_editstyle',
        'tab' => 11,
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
    }
    else
      return false;
  }
  
  public function onMenuInitialize_SitestoreproductPrintingtagPrinttag($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    $isPrintingAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allow.printingtag', 0);
   if(!empty($isPrintingAllowed) && ($sitestoreproduct->product_type != 'downloadable') && ($sitestoreproduct->product_type != 'virtual')) {
    return array(
        'label' => $row->label,
        'name' => 'sitestoreproduct_printingtag_printtag',
        'route' => 'sitestoreproduct_tag',
        'action' => 'print-tag',
        'tab' => 12,
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'product_id' => $sitestoreproduct->product_id
        ),
    );
   }
   else
     return false;
  }
  
  public function onMenuInitialize_SitestoreproductDashboardProducthistory($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
      return array(
          'label' => $row->label,
          'route' => 'sitestoreproduct_dashboard',
          'action' => 'product-history',
          'name' => 'sitestoreproduct_dashboard_producthistory',
          'tab' => 13,
          'class' => 'ajax_dashboard_enabled',
          'params' => array(
              'product_id' => $sitestoreproduct->product_id
          ),
      );
  }
  
  public function onMenuInitialize_SitestoreproductFilesIndex($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    if($sitestoreproduct->product_type == 'downloadable'){
      $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($sitestoreproduct->product_id);
      return array(
          'label' => $row->label,
          'route' => 'sitestoreproduct_files',
          'action' => 'index',
          'name' => 'sitestoreproduct_files_index',
          'tab' => 3,
          'class' => 'ajax_dashboard_enabled',
          'params' => array(
              'option_id' => $option_id,
              'product_id' => $sitestoreproduct->product_id
          ),
      );
    }
    else
      false;
  }
  
  public function onMenuInitialize_SitestoreproductProductBundleproductattributes($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    $tempProductInfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->product_id, 'product_info');
if( !empty($tempProductInfo) ) {
  $productInfo = unserialize($tempProductInfo);
  if( !empty($productInfo) && $sitestoreproduct->product_type == 'bundled' ){
    $bundleProductType = $productInfo['bundle_product_type'];
    if( @in_array('configurable', $bundleProductType) || @in_array('virtual', $bundleProductType) )
      $isConfigurationTabRequired = true;
  } 
}
    if($sitestoreproduct->product_type == 'bundled' && $isConfigurationTabRequired){
      return array(
          'label' => $row->label,
          'route' => 'sitestoreproduct_product_general',
          'action' => 'bundle-product-attributes',
          'name' => 'sitestoreproduct_product_bundleproductattributes',
          'tab' => 4,
          'class' => 'ajax_dashboard_enabled',
          'params' => array(
              'product_id' => $sitestoreproduct->product_id
          ),
      );
    }
    else
      false;
  }
  
  public function onMenuInitialize_SitestoreproductDashboardProductBooking($row) {

    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }
    
    $isSitestorereservationModuleExist = Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist();
    $tempProductInfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->product_id, 'product_info');
if( !empty($tempProductInfo) ) 
  $productInfo = unserialize($tempProductInfo);

    if(!empty($isSitestorereservationModuleExist) && $sitestoreproduct->product_type == 'virtual' && !empty($productInfo) && !empty($productInfo['virtual_product_date_selector'])) {
      return array(
          'label' => $row->label,
          'route' => 'sitestorereservation_dashboard',
          'action' => 'product-booking',
          'name' => 'sitestoreproduct_dashboard_productbooking',
          'tab' => 5,
          'class' => 'ajax_dashboard_enabled',
          'params' => array(
              'product_id' => $sitestoreproduct->product_id
          ),
      );
    }
    else
      false;
  }
  
  public function onMenuInitialize_SitestoreproductDashboardEditlocation($row) {

    //GET EVENT ID AND SITEEVENT OBJECT
    $sitestoreproduct = $this->getProductId();
    if (empty($sitestoreproduct)) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $editPrivacy = $sitestoreproduct->authorization()->isAllowed($viewer, "edit");
    if (empty($editPrivacy)) {
      return false;
    }

    if (Engine_Api::_()->sitestoreproduct()->enableLocation()) {
      return array(
          'label' => $row->label,
          'route' => 'sitestoreproduct_dashboard',
          'action' => 'editlocation',
          'name' => 'sitestoreproduct_dashboard_editlocation',
          'tab' => 15,
          //'class' => 'ajax_dashboard_enabled',
          'params' => array(
              'product_id' => $sitestoreproduct->getIdentity()
          ),
      );
    }

    return false;
  }
}
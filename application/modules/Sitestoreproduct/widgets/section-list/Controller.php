<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class SitestoreProduct_Widget_SectionListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $isSectionAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.section.allowed', 1);
    if (empty($isSectionAllowed))
      return $this->setNoRender();    
    
    $limit = $this->_getParam('limit', 5);
    $order = $this->_getParam('order', 0);
    $this->view->product = $this->_getParam('product', array("product"));

    if (in_array("product", $this->view->product))
      $product = true;
    if (!Engine_Api::_()->core()->hasSubject()) {
      $store_id = Zend_Registry::isRegistered('store_id') ? Zend_Registry::get('store_id') : null;
      $storeSubject = Engine_Api::_()->getItem('sitestore_store', $store_id);
    } else {
      $storeSubject = Engine_Api::_()->core()->getSubject('sitestore_store');
    }
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->store_id = $store_id = $storeSubject->store_id;
    
     $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->ProductTabId = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreproduct.store-profile-products', $store_id, $layout);

    if (!empty($viewer_id))
      $this->view->authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
    if (empty($storeSubject->approved) || !empty($storeSubject->closed) || empty($storeSubject->search) || empty($storeSubject->draft) || !empty($storeSubject->declined)) {
      return $this->setNoRender();
    }

    $this->view->sections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct')->getStoreSectionList($store_id, $limit, $order, $product);

    $this->view->countSection = count($this->view->sections);

    if (empty($this->view->countSection))
      return $this->setNoRender();
  }

}

?>
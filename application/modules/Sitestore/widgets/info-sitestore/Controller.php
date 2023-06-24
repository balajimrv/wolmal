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
class Sitestore_Widget_InfoSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    $sitestore_info = Zend_Registry::isRegistered('sitestore_info') ? Zend_Registry::get('sitestore_info') : null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $this->view->showContent = $this->_getParam("showContent", array("posted_by", "posted", "last_update", "members", "comments", "views", "likes", "followers", "category", "tags", "price", "location", "description"));

		//SEND DATA TO TPL
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout, $sitestore->store_id);
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.info-sitestore', $sitestore->store_id, $layout);
    $this->view->module_tabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $this->view->identity_temp = $this->view->identity;
    $this->view->showtoptitle = Engine_Api::_()->sitestore()->showtoptitle($layout, $sitestore->store_id);

		//GET CATEGORY
    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->category_id);
    if (!empty($row->category_name)) {
      $this->view->category_name = $row->category_name;
    }

		//GET SUB-CATEGORY
    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->subcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subcategory_name = $row->category_name;
    }     

    //GET SUB-SUB-CATEGORY
    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->subsubcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subsubcategory_name = $row->category_name;
    }

    //GET TAGS
    $this->view->sitestoreTags = $sitestore->tags()->getTagMaps();

		//CUSTOM FIELD WORK
    $this->view->sitestore_description = Zend_Registry::isRegistered('sitestore_descriptions') ? Zend_Registry::get('sitestore_descriptions') : null;
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestore/View/Helper', 'Sitestore_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestore);
    if (empty($sitestore_info)) {
      return $this->setNoRender();
    }
  }
}

?>
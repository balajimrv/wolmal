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
class Sitestore_Widget_SitemobileInfoSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

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

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestore/View/Helper', 'Sitestore_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestore);
  }
}
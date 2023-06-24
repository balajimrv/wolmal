<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_StoreProfileBreadcrumbController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestore_store')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    //GET CATEGORY TABLE
    $this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestore');
    if (!empty($sitestore->category_id)) {
      $this->view->category_name = $this->view->tableCategory->getCategory($sitestore->category_id)->category_name;

      if (!empty($sitestore->subcategory_id)) {
        $this->view->subcategory_name = $this->view->tableCategory->getCategory($sitestore->subcategory_id)->category_name;

        if (!empty($sitestore->subsubcategory_id)) {
          $this->view->subsubcategory_name = $this->view->tableCategory->getCategory($sitestore->subsubcategory_id)->category_name;
        }
      }
    }
  }

}
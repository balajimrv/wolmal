<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_ZeroproductSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //CAN CREATE PRODUCTS OR NOT
    //$this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "create");

    //GET LISTS
    $productCount = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->hasProducts();

    if ($productCount > 0) {
      return $this->setNoRender();
    }
  }

}

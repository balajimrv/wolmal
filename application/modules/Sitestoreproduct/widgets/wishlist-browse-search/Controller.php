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
class Sitestoreproduct_Widget_WishlistBrowseSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GENERATE SEARCH FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Wishlist_Search();
    $this->view->viewType = $this->_getParam('viewType', 'horizontal');

    //GET FORM VALUES
    $requestParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();

    //POPULATE SEARCH FORM
    $form->populate($requestParams);
  }

}

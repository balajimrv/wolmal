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
class Sitestoreproduct_Widget_AboutEditorSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET PRODUCT SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //GET REVIEW TABLE
    $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');

    //EDITOR REVIEW HAS BEEN POSTED OR NOT
    $params = array();
    $params['resource_id'] = $sitestoreproduct->product_id;
    $params['resource_type'] = $sitestoreproduct->getType();
    $params['viewer_id'] = 0;
    $params['type'] = 'editor';
    $isEditorReviewed = $reviewTable->canPostReview($params);
    if (empty($isEditorReviewed)) {
      return $this->setNoRender();
    }

    //GET USER ID
    $user_id = $reviewTable->getColumnValue($isEditorReviewed, 'owner_id');
    if (empty($user_id)) {
      return $this->setNoRender();
    }

    $editor_id = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->getColumnValue($user_id, 'editor_id');

    $this->view->editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);
    $this->view->user = Engine_Api::_()->getItem('user', $user_id);
  }

}

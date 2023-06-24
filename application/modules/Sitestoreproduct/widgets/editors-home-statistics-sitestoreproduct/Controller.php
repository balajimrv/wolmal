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
class Sitestoreproduct_Widget_EditorsHomeStatisticsSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET REVIEW TABLE
    $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');

    //FETCH TOTAL REVIEWS BY EDITOR
    $params = array();
    $params['type'] = 'editor';
    $this->view->totalEditorReviews = $reviewTable->totalReviews($params);

    //GET EDITOR TABLE
    $editorTable = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct');

    //FETCH TOTAL EDITORS
    $this->view->totalEditors = $editorTable->getEditorsCount(0);
  }

}
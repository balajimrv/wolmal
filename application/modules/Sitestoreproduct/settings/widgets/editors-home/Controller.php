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
class Sitestoreproduct_Widget_EditorsHomeController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET EDITOR TABLE
    $this->view->editorTable = $editorTable = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct');

    //GET EDITORS
    $params = array();
    if (!$this->_getParam('superEditor', 1)) {
      $params['user_id'] = $editorTable->getSuperEditor('user_id');
    }
    $this->view->editors = $editorTable->getEditorsProduct($params);

    $totalEditors = Count($this->view->editors);
    if ($totalEditors <= 0) {
      return $this->setNoRender();
    }
  }

}

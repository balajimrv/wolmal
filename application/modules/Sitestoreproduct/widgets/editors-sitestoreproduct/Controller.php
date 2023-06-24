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
class Sitestoreproduct_Widget_EditorsSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    
    //GET SETTINGS
    $params = array();
    $params['limit'] = $this->_getParam('itemCount', 4);
    $this->view->viewType = $this->_getParam('viewType', 1);

    //GET EDITOR TABLE
    $this->view->editorTable = $editorTable = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct');

    //GET USER SUBJECT IF WIDGET IS PLACED AT EDITOR PROFILE PAGE
    if (Engine_Api::_()->core()->hasSubject('user')) {
      $user = Engine_Api::_()->core()->getSubject('user');
      $params['user_id'] = $user->getIdentity();
    }
    
    if (!$this->_getParam('superEditor', 1)) {
      $params['super_editor_user_id'] = $editorTable->getSuperEditor('user_id');
    }    

    //GET EDITORS
    $this->view->editors = $editorTable->getSimilarEditors($params);

    //DON'T RENDER IF NO DATA
    if (Count($this->view->editors) <= 0) {
      return $this->setNoRender();
    }
  }

}
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
class Sitestoreproduct_Widget_EditorPhotoSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('user')) {
      return $this->setNoRender();
    }

    //GET USER SUBJECT    
    $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
    
     if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
    //SITEMOBILE CODE
    $editorTable = Engine_Api::_()->getDbTable('editors', 'Sitestoreproduct');

    //GET EDITOR ID
    $editor_id = $editorTable->getColumnValue($user->getIdentity(), 'editor_id', 0);
    $this->view->editor = $editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);    

    //GET EDITOR DETAILS
    $params = array();
    $params['visible'] = 1;
    $params['editorReviewAllow'] = 1;
    $this->view->getDetails = $editorTable->getEditorDetails($editor->user_id, 0, $params);
    $this->view->showContent = $this->_getParam('showContent', array("photo", "title", "about", "details", "designation", "emailMe"));
   }
  }

}
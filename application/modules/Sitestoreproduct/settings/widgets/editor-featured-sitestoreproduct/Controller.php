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
class Sitestoreproduct_Widget_EditorFeaturedSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction()	{

		//GET FEATURED USER ID
		$user_id = $this->_getParam('user_id');
		if(empty($user_id)) {
			return $this->setNoRender();
		}

    $editor_id = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->getColumnValue($user_id, 'editor_id');
    $this->view->editor = $editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);
    $this->view->user = Engine_Api::_()->getItem('user', $editor->user_id);
  }
}
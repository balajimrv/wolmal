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
class Sitestore_Widget_OverviewSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
    	$this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    }
    else {
      $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject()->getParent();
    }

    //START MANAGE-ADMIN CHECK   
    $this->view->can_edit_overview = $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'overview');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $this->view->can_edit = $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

    if (empty($this->view->can_edit) && empty($sitestore->overview)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

		//SEND OTHER DETAIL TO TPL
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->widgets = Engine_Api::_()->sitestore()->getwidget($layout, $sitestore->store_id);
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.overview-sitestore', $sitestore->store_id, $layout);
    $this->view->module_tabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $this->view->identity_temp = $this->view->identity;
    $this->view->showtoptitle = Engine_Api::_()->sitestore()->showtoptitle($layout, $sitestore->store_id);
  }
}

?>
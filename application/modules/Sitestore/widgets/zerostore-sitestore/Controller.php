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
class Sitestore_Widget_ZerostoreSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'create');
    $values['type'] = 'browse_home_zero';
    $values['limit'] = 1;
    $this->view->assign($values);
    
 
    // GET SITESTORE
    $sitestore = Engine_Api::_()->sitestore()->getSitestoresPaginator($values);

    if ((count($sitestore) > 0)) {
      return $this->setNoRender();
    }
  }

}

?>
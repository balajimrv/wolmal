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
class Sitestore_Widget_ThumbphotoSitestoreController extends Engine_Content_Widget_Abstract {

	//ACTION FOR SHOWING THE THUMB PHOTO.
  public function indexAction() {
  	
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $this->view->showTitle = $this->_getParam('showTitle', 1);
    //GET SITESTORE SUBJECT
    $this->view->sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
  }

}

?>
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
class Sitestore_Widget_MostrecentphotosSitestoreController extends Engine_Content_Widget_Abstract {

	//ACTION FOR GETTING THE MOST RECENT PHOTOS
  public function indexAction() {

  	//HERE WE CHECKING THE SITESTORE ALBUM IS ENABLED OR NOT
		$sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
		if (!$sitestorealbumEnabled) {
			return $this->setNoRender();
		}
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitestore');
    //SEARCH PARAMETER
    $params = array();
		$params['orderby'] = 'creation_date DESC';
		$params['zero_count'] = 'creation_date';
		$params['limit'] = $this->_getParam('itemCount', 4);
    $this->view->displayStoreName = $this->_getParam('showStoreName', 0);
    $this->view->displayUserName = $this->_getParam('showUserName', 0);
    $this->view->showFullPhoto = $this->_getParam('showFullPhoto', 0);
    
		$this->view->paginator = $paginator = $photoTable->widgetPhotos($params);
 
    $this->view->count =  $photoTable->countTotalPhotos($params);

    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
    
    //SHOWS PHOTOS IN THE LIGHTBOX
    //$this->view->showLightBox = Engine_Api::_()->sitestore()->canShowPhotoLightBox();
  }

}

?>
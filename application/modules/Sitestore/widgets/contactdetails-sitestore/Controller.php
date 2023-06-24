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
class Sitestore_Widget_ContactdetailsSitestoreController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE RANDOM ALBUMS AND PHOTOS BY OTHERS 
  public function indexAction() {

    //DON'T RENDER IF SUNJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'contact');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

		$this->view->can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    
     if((empty($sitestore->phone) && empty($sitestore->email) && empty($sitestore->website)) && !$this->view->can_edit) {
      return $this->setNoRender();
    }

//    $renderWidget = $this->_getParam('renderWidget', 0);
//    if(empty($renderWidget)) {
//      return $this->setNoRender();
//    }

		//GET SETTINGS
		$pre_field = array("0" => "1", "1" => "2", "2" => "3");
		$contacts = $this->_getParam('contacts', $pre_field);
		$this->view->emailme = $this->_getParam('emailme', 0);

		if(empty($contacts)) {
			$this->setNoRender();
		}
		else {
			//INITIALIZATION
			$this->view->show_phone = $this->view->show_email = $this->view->show_website = 0;
			if(in_array(1, $contacts)) {
				$this->view->show_phone = 1;
			}
			if(in_array(2, $contacts)) {
				$this->view->show_email = 1;
			}
			if(in_array(3, $contacts)) {
				$this->view->show_website = 1;
			}
		}

		$user = Engine_Api::_()->user()->getUser($sitestore->owner_id);
		$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'contact_detail');
		$availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email');
		$this->view->options_create = array_intersect_key($availableLabels, array_flip($view_options));
  }

}
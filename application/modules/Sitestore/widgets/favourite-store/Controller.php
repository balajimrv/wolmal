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

class Sitestore_Widget_FavouriteStoreController extends Seaocore_Content_Widget_Abstract
{ 
	protected $_childCount;

  public function indexAction()
  {
		//GET THE SUBJECT OF STORE.
    $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $sitestore_id = $sitestore->store_id;
    $params = array();
    $params['category_id'] = $this->view->category_id = $this->_getParam('category_id', 0);
    $params['featured'] = $this->view->featured = $this->_getParam('featured', 0);
    $params['sponsored'] = $this->view->sponsored = $this->_getParam('sponsored', 0);
    $limit = $this->_getParam('itemCount', 3);

		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      
      //FUNCTION CALL FORM THE DBTABLE AND PASS STORE ID OR LIMIT OF STORES TO SHOW ON THE WIDGET.
			$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitestore')->linkedStores($sitestore_id, 10,$params);
			// Set item count per store and current store number
			$this->view->userListings = $userListings->setItemCountPerPage(5);
			$this->view->userListings = $userListings->setCurrentPageNumber($this->_getParam('store', 1));
		  $this->_childCount = $userListings->getTotalItemCount();
      if ($userListings->getTotalItemCount() <= 0) {
				return $this->setNoRender();
			}
    } else {
             //FUNCTION CALL FORM THE DBTABLE AND PASS STORE ID OR LIMIT OF STORES TO SHOW ON THE WIDGET.
			$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitestore')->linkedStores($sitestore_id, $limit,$params);
			//NOT RENDER IF SITESTORE COUNT ZERO
			if (!(count($this->view->userListings) > 0)) {
				return $this->setNoRender();
			}
    }    
  }

	public function getChildCount() {
    return $this->_childCount;
  }
}
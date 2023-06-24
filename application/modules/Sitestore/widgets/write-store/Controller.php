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
class Sitestore_Widget_WriteStoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DON'T RENDER IF NOT AUTHORIZED.
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

		//GET THE SUBJECT OF STORE.
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $store_id = $sitestore->store_id;

    //CALLING FUNCTON AND PASS STORE ID.
    $writetContent = Engine_Api::_()->getDbtable('writes', 'sitestore')->writeContent($store_id);

    if (!empty($writetContent)) {
      $this->view->userStorestext = $writetContent->text;
    }

		//CALLING FUNCTON AND PASS STORE ID.
		$userStores = Engine_Api::_()->getDbtable('stores', 'sitestore')->sitestoreselect($store_id);
    $new_array = array();
    foreach ($userStores as $key => $userstore) {
      $new_array = $userstore;
    }
    $this->view->userStores = $new_array;
  }
}
?>
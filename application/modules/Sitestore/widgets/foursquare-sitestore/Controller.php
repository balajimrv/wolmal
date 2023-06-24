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
class Sitestore_Widget_FoursquareSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DONT RENDER IS SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

		//GET SUBJECT AND AND SET NO RENDER IF FOURSQUARE TEXT IS EMPTY
    $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    if (empty($sitestore->foursquare_text) || empty($sitestore->location)) {
      return $this->setNoRender();
    }

		//SEND FOURSQUARE TEXT TO TPL
		$this->view->foursquare_text = $sitestore->foursquare_text;

		//SET NO RENDER IF FOURSQUARE BUTTON IS NOT ALLOWED IS NOT ALLOWED
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'foursquare');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
  }

}
?>
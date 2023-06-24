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
class Sitestoreproduct_Widget_CloseproductSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) || empty($sitestoreproduct->closed)) {
      return $this->setNoRender();
    }
  }

}
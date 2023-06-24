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
class Sitestoreproduct_Widget_ArchivesSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    //DON'T RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $sitestoreproduct = Engine_Api::_()->core()->getSubject();
    $owner = $sitestoreproduct->getOwner();

    //SHOW ARCHIVES
    $this->view->archive_sitestoreproduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getArchiveSitestoreproduct($owner);

    if (Count($this->view->archive_sitestoreproduct) <= 0) {
      return $this->setNoRender();
    }
  }

}
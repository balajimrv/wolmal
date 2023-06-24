<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_SitemobileMenuLogoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->logo = $this->_getParam('logo');
    $this->view->height = $this->_getParam('height');
    $this->view->width = $this->_getParam('width');
    $this->view->alignment = $this->_getParam('alignment');
  }

  public function getCacheKey() {
    //return true;
  }

}
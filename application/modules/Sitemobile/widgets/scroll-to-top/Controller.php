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
class Sitemobile_Widget_ScrollToTopController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET SETTING
    $this->view->mouseOverText = $this->_getParam('mouseOverText', 'Scroll to Top');
  }

}

?>

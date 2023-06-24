<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ThemeRollerController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_ThemeRollerController extends Core_Controller_Action_Standard {

  public function previewAction() {
    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');
    // Get themes
    $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'sitemobile')->fetchAll();
    $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
  }

}
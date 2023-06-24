<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorelikebox_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
		//GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorelikebox'); 
    

    if( !empty($_POST['sitestorelikebox_lsettings']) ) { $_POST['sitestorelikebox_lsettings'] = trim($_POST['sitestorelikebox_lsettings']); }

    $this->view->navigation = $navigation = Engine_Api::_()->getApi( 'menus' , 'core' )->getNavigation( 'sitestorelikebox_admin_main' , array ( ) , 'sitestorelikebox_admin_main_settings' ) ;

    $this->view->form = $form = new Sitestorelikebox_Form_Admin_Global();
  
		//START LANGUAGE WORK
			Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
		//END LANGUAGE WORK
		
      if ( $this->getRequest()->isPost() && $form->isValid( $this->_getAllParams() ) ) {
        $values = $form->getValues() ;

        if ( isset( $values['modules_likebox'] ) )
          $values['modules_likebox'] = serialize( $values['modules_likebox'] ) ;
        else
          $values['modules_likebox'] = serialize( array ( ) ) ;
          unset($values['logo_photo_preview']);
        include APPLICATION_PATH . '/application/modules/Sitestorelikebox/controllers/photoUploading.php';
      }
  }
}
?>
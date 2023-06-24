<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminCssController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorelikebox_AdminCssController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
		//GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorelikebox');       
    //GET NAVIGATION.
    $this->view->navigation = Engine_Api::_()->getApi( 'menus' , 'core' )
            ->getNavigation( 'sitestorelikebox_admin_main' , array ( ) , 'sitestorelikebox_admin_main_css' ) ;

    $theme = array ( '/application/modules/Sitestorelikebox/externals/styles/light.css' , '/application/modules/Sitestorelikebox/externals/styles/dark.css' ) ;

		$basePath = APPLICATION_PATH . '/application/modules/Sitestorelikebox/externals/styles' ;

    //CHECK IF THEME FILE IS WRITABLE.
    $writeable = false ;
		
    try {
      if ( !file_exists( $basePath ) ) {
        throw new Core_Model_Exception( 'Missing file in css' ) ;
      }
      else {
        $this->checkWriteable( $basePath ) ;
      }

      $writeable = $writeable ;
    }
    catch ( Exception $e ) {
      $this->view->errorMessage = $e ;
    }

    //PASS THE WRITABLE FILE .
    $this->view->writeable = true ;

    //GET THE FIRST ACTIVE FILE.
    $this->view->activeFileName = $activeFileName = 'light.css' ;
    if ( null !== ($rFile = $this->_getParam( 'file' )) ) {
      $this->view->activeFileName = $activeFileName = $rFile ;
    }

    //CHECK IF THEME FILES HAVE BEEN MODIFIED.
    $originalName = 'original.' . $activeFileName ;
    if ( file_exists( APPLICATION_PATH . "/application/modules/Sitestorelikebox/externals/styles/$originalName" ) ) {
      if ( file_get_contents( APPLICATION_PATH . "/application/modules/Sitestorelikebox/externals/styles/$originalName" ) != file_get_contents( APPLICATION_PATH . "/application/modules/Sitestorelikebox/externals/styles/$activeFileName" ) ) {
        $modified = $activeFileName ;
      }
    }
		if (!empty($modified)) {
			$this->view->modified = $modified ;
		}
    $this->view->activeFileOptions = array ( 'light.css' => 'light.css' , 'dark.css' => 'dark.css' ) ;
    $this->view->activeFileContents = file_get_contents( APPLICATION_PATH . '/application/modules/Sitestorelikebox/externals/styles/' . $activeFileName ) ;
  }

  //SAVE THE CSS FILE.
  public function saveAction() {
    //GET THE VALUE OF FILE AND BODY.
    $file = $this->_getParam( 'file' ) ;
    $body = $this->_getParam( 'body' ) ;

    if ( !$this->getRequest()->isPost() ) {
      $this->view->status = false ;
      $this->view->message = Zend_Registry::get( 'Zend_Translate' )->_( "Bad method" ) ;
      return ;
    }

    // CHECK FOR FILE ANF BODY.
    if ( !$file || !$body ) {
      $this->view->status = false ;
      $this->view->message = Zend_Registry::get( 'Zend_Translate' )->_( "Bad params" ) ;
      return ;
    }

    //CHECK FILES.
    $basePath = APPLICATION_PATH . '/application/modules/Sitestorelikebox/externals/styles' ;
    $fullFilePath = $basePath . '/' . $file ;
    try {
      $this->checkWriteable( $fullFilePath ) ;
    }
    catch ( Exception $e ) {
      $this->view->status = false ;
      $this->view->message = Zend_Registry::get( 'Zend_Translate' )->_( "Not writeable" ) ;
      return ;
    }
    include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php' ;

    //Now lets write the custom file
    if ( !file_put_contents( $fullFilePath , $body ) ) {
      $this->view->status = false ;
      $this->view->message = Zend_Registry::get( 'Zend_Translate' )->_( 'Could not save contents' ) ;
      return ;
    }
    $this->view->status = true ;
  }

  //FUNCTION FOR REVERT THE FILE.
  public function revertAction() {
    //GET THE VALUE FI FILE.
    $file = $this->_getParam( 'file' ) ;

    if ( !$this->getRequest()->isPost() ) {
      $this->view->status = false ;
      $this->view->message = Zend_Registry::get( 'Zend_Translate' )->_( "Bad method" ) ;
      return ;
    }

    //CHECK FILES.
    $basePath = APPLICATION_PATH . '/application/modules/Sitestorelikebox/externals/styles' ;
    $files = $file ;
    $originalFiles = array ( ) ;

    //CHECK THE FILE EXIST OR NOT 
    if ( file_exists( "$basePath/original.$file" ) ) {
      $originalFiles[] = $file ;
    }

    //CHECK FILES IS WRITABLE.
    $this->checkWriteable( $basePath . '/' ) ;
    foreach ( $originalFiles as $file ) {
      $this->checkWriteable( $basePath . '/' . $file ) ;
      $this->checkWriteable( $basePath . '/original.' . $file ) ;
    }

    //NOW UNDO ALL CHANGES.
    foreach ( $originalFiles as $file ) {
      unlink( "$basePath/$file" ) ;
      rename( "$basePath/original.$file" , "$basePath/$file" ) ;
    }
    return $this->_helper->redirector->gotoRoute( array ( 'action' => 'index' ) ) ;
  }

	//FUNCTION FOR THE FILE IS WRITABLE OR NOT.
  public function checkWriteable( $path ) {

    if ( !file_exists( $path ) ) {
      throw new Core_Model_Exception( 'Path doesn\'t exist' ) ;
    }
    if ( !is_writeable( $path ) ) {
      throw new Core_Model_Exception( 'Path is not writeable' ) ;
    }
    if ( !is_dir( $path ) ) {
      if ( !($fh = fopen( $path , 'ab' )) ) {
        throw new Core_Model_Exception( 'File could not be opened' ) ;
      }
      fclose( $fh ) ;
    }

  }

	//FUNCTION FOR THE SHOW THE DUMMY.
  public function dummyAction() {

    $this->_helper->layout->setLayout( 'default-simple' ) ;
    if ( null !== ($rFile = $this->_getParam( 'file' , 'light.css' )) ) {
      $this->view->activeFileName = $rFile ;
    }

    //GET THE VALUE OF LOGO PHOTO AND NAME OF THE PHOTO.
    $logoPhoto = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'logo.photo' ) ;
    if ( !empty( $logoPhoto ) ) {
      $this->view->photo_name = $this->view->baseUrl() . '/public/sitestorelikebox/logo/' . $logoPhoto ;
    }
  }
}
?>
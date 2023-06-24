<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Plugin_Core
{
  public function onStatistics($event)
  {
    $table  = Engine_Api::_()->getDbTable('photos', 'sesalbum');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(photo_id) AS count');
		$select->where('album_id !=?','0');
    $event->addResponse($select->query()->fetchColumn(), 'photo');
  }
	public function onRenderLayoutMobileDefault($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
	public function onRenderLayoutMobileDefaultSimple($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
	public function onRenderLayoutDefaultSimple($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
	public function onRenderLayoutDefault($event,$mode=null){
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$viewer = Engine_Api::_()->user()->getViewer();		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$moduleName = $request->getModuleName();
		$actionName = $request->getActionName();
		$controllerName = $request->getControllerName();
   // echo "<pre>";var_dump($request);die;
		if(empty($_SERVER['HTTP_REFERER'])){
			$_COOKIE['sesalbum_lightbox_value']	= '';
		}
		$checkWelcomePage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.check.welcome',2);
		$checkWelcomePage = (($checkWelcomePage == 1 && $viewer->getIdentity() == 0) ? true : ($checkWelcomePage == 0 && $viewer->getIdentity() != 0) ? true : ($checkWelcomePage == 2) ? true : false);
		if(!$checkWelcomePage && $actionName == 'welcome' && $controllerName == 'index' && $moduleName == 'sesalbum'){
		  $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		  $redirector->gotoRoute(array('module' => 'sesalbum', 'controller' => 'index', 'action' => 'home'), 'sesalbum_general', false);
		}
		if($viewer->getIdentity() == 0)
			$level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
		else
			$level = $viewer;
		
			$type = Engine_Api::_()->authorization()->getPermission($level,'album','imageviewer');
			$headScript = new Zend_View_Helper_HeadScript();
			$headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
								 .'application/modules/Sesalbum/externals/scripts/core.js');
			if($type == 1){
				$headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
								. 'application/modules/Sesalbum/externals/scripts/sesimagevieweradvance/photoswipe.min.js')
								->appendFile(Zend_Registry::get('StaticBaseUrl')
								. 'application/modules/Sesalbum/externals/scripts/sesimagevieweradvance/photoswipe-ui-default.min.js')
								->appendFile(Zend_Registry::get('StaticBaseUrl')
								. 'application/modules/Sesalbum/externals/scripts/sesalbumimagevieweradvance.js');
				$view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
								. 'application/modules/Sesbasic/externals/styles/photoswipe.css');
			}else{
				$loadImageViewerFile = Zend_Registry::get('StaticBaseUrl').'application/modules/Sesalbum/externals/scripts/sesalbumimageviewerbasic.js';
				$headScript->appendFile($loadImageViewerFile)
									 ->appendFile(Zend_Registry::get('StaticBaseUrl')
								. 'application/modules/Sesalbum/externals/scripts/zoom-image/wheelzoom.js');
				$view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
							. 'application/modules/Sesbasic/externals/styles/medialightbox.css');
			}
		$script = '';
		//get default enable module.
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$activeManageModulePhotoType = $db->query("SELECT GROUP_CONCAT(content_type_photo SEPARATOR ', .feed_attachment_') as managemodulephototype FROM engine4_sesbasic_integrateothermodules WHERE enabled = 1")->fetchColumn();
		if($activeManageModulePhotoType){
			$script .= "sesJqueryObject(document).on('click','.feed_attachment_".$activeManageModulePhotoType."',function(e){
				if(typeof sesJqueryObject(this).find('div').find('a').attr('onclick') != 'undefined')
					return ;
				e.preventDefault();
				var href = sesJqueryObject(this).find('div').find('a').attr('href');
				if(openPhotoInLightBoxSesalbum == 0 || (openGroupPhotoInLightBoxSesalbum == 0 && href.indexOf('group_id') > -1 ) || (openEventPhotoInLightBoxSesalbum == 0 && href.indexOf('event_id') > -1)){
					window.location.href = href;
					return;
				}
				openLightBoxForSesPlugins(href);	
			});
";	
		}
		if($moduleName == 'sesalbum'){
			$script .=
"sesJqueryObject(document).ready(function(){
     sesJqueryObject('.core_main_sesalbum').parent().addClass('active');
    });
";
		}
		$script .=
"var openPhotoInLightBoxSesalbum = ".Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.enable.lightbox',1).";
var openGroupPhotoInLightBoxSesalbum = ".Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.enable.lightboxForGroup',0).";
var openEventPhotoInLightBoxSesalbum = ".Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.enable.lightboxForEvent',0).";
var showAddnewPhotoIconShortCut = ".Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.enable.addphotoshortcut',1).";
";
if($viewer->getIdentity() != 0 && empty($_GET['format'])){
	$script .= 'sesJqueryObject(document).ready(function() {
	if(sesJqueryObject("body").attr("id").search("sesalbum") > -1 && typeof showAddnewPhotoIconShortCut != "undefined" && showAddnewPhotoIconShortCut ){
		sesJqueryObject("<a class=\'sesalbum_create_btn sesalbum_animation\' href=\'albums/create\' title=\'Add New Photos\'><i class=\'fa fa-plus\'></i></a>").appendTo("body");
	}
});';		
}
    $view->headScript()->appendScript($script);
	}
  public function onUserProfilePhotoUpload($event)
  {
		/*check album plugin enable or not ,if yes then return*/
		if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album'))
			return;
    $payload = $event->getPayload();
    if( empty($payload['user']) || !($payload['user'] instanceof Core_Model_Item_Abstract) ) {
      return;
    }
    if( empty($payload['file']) || !($payload['file'] instanceof Storage_Model_File) ) {
      return;
    }
    $viewer = $payload['user'];
    $file = $payload['file'];
    // Get album
    $table = Engine_Api::_()->getDbtable('albums', 'sesalbum');
    $album = $table->getSpecialAlbum($viewer, 'profile');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sesalbum');
    $photo = $photoTable->createRow();
    $photo->setFromArray(array(
      'owner_type' => 'user',
      'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
    ));
    $photo->save();
    $photo->setPhoto($file);
    $photo->album_id = $album->album_id;
    $photo->save();
    if( !$album->photo_id ) {
      $album->photo_id = $photo->getIdentity();
      $album->save();
    }
    $auth = Engine_Api::_()->authorization()->context;
    $auth->setAllowed($photo, 'everyone', 'view',    true);
    $auth->setAllowed($photo, 'everyone', 'comment', true);
    $auth->setAllowed($album, 'everyone', 'view',    true);
    $auth->setAllowed($album, 'everyone', 'comment', true);
    $event->addResponse($photo);
  }
  public function onUserDeleteAfter($event)
  {
    $payload = $event->getPayload();
    $user_id = $payload['identity'];
    $table   = Engine_Api::_()->getDbTable('albums', 'sesalbum');
    $select = $table->select()->where('owner_id = ?', $user_id);
    $select = $select->where('owner_type = ?', 'user');
    $rows = $table->fetchAll($select);
    foreach ($rows as $row)
    {
      $row->delete();
    }
    $table   = Engine_Api::_()->getDbTable('photos', 'sesalbum');
    $select = $table->select()->where('owner_id = ?', $user_id);
    $select = $select->where('owner_type = ?', 'user');
    $rows = $table->fetchAll($select);
    foreach ($rows as $row)
    {
      $row->delete();
    }
  }
}

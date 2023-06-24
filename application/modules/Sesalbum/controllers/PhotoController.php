<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: PhotoController.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_PhotoController extends Core_Controller_Action_Standard {
	//photo constructor function
  public function init() {
		if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
              null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
        Engine_Api::_()->core()->setSubject($photo);
				$exists = true;
      }
		if(isset($exists)){
				$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
				$getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
				if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.8') >= 0){
					$view->doctype('XHTML1_RDFA');
					
					$imageURL = $photo->getPhotoUrl();
					
					$title = strip_tags($photo->getTitle());
					$description = strip_tags($photo->getDescription());
					if(strpos($photo->getPhotoUrl(),'http') === false)
						$imageURL = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://". $_SERVER['HTTP_HOST'].$photo->getPhotoUrl() : "http://". $_SERVER['HTTP_HOST'].$photo->getPhotoUrl();
					
					$view->headMeta()->setProperty('og:type', 'website');
					$view->headMeta()->setProperty('og:url', 'https://'.$_SERVER['HTTP_HOST'].$photo->getHref());
					$view->headMeta()->setProperty('og:title', $title);
					$view->headMeta()->setProperty('og:description', $description);
					$view->headMeta()->setProperty('og:image', $imageURL);
					$view->headMeta()->setProperty('twitter:card','summary_large_image');
					$view->headMeta()->setProperty('twitter:site', $description);
					$view->headMeta()->setProperty('twitter:url', 'https://'.$_SERVER['HTTP_HOST'].$photo->getHref());
					$view->headMeta()->setProperty('twitter:title', $title);
					$view->headMeta()->setProperty('twitter:description', '');
					$view->headMeta()->setProperty('twitter:image',$imageURL);
				}
			}
   if (strpos($_SERVER['REQUEST_URI'], 'ses-compatibility-code') === false && strpos($_SERVER['REQUEST_URI'], 'last-element-data') === false && strpos($_SERVER['REQUEST_URI'], 'allphoto-ses-compatibility-code') === false && strpos($_SERVER['REQUEST_URI'], 'all-photos') === false && strpos($_SERVER['REQUEST_URI'], 'image-viewer-detail') === false  && strpos($_SERVER['REQUEST_URI'], 'o/like') === false) {
      if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
        return;      
    }
		
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.checkalbum'))
      return $this->_forward('notfound', 'error', 'core');
  }
	//function to get photo for autosuggest
	 public function getPhotoAction() {
    $sesdata = array();
    $value['text'] = $this->_getParam('text','');
		$photos = Engine_Api::_()->getDbtable('photos', 'sesalbum')->getPhoto($value);
    foreach ($photos as $photo) {
      $photo_icon_photo = $this->view->itemPhoto($photo, 'thumb.icon');
      $sesdata[] = array(
          'id' => $photo->photo_id,
					'album_id'=>$photo->album_id,
          'label' => $photo->title,
          'photo' => $photo_icon_photo
      );
    }
    return $this->_helper->json($sesdata);
  }
//function to open third party module photo
  public function sesCompatibilityCodeAction(){
		$itemUrl = $this->_getParam('url',false);
		$url = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://" ;
		$url = $url.$_SERVER['HTTP_HOST'].$itemUrl;
		$request = new Zend_Controller_Request_Http($url);
		$frontController = Zend_Controller_Front::getInstance();		
		$router = $frontController->getRouter();
		$routeName=$router->route($request);
		$getParams = $routeName->getParams();
		if(!is_array($getParams)){
			echo json_encode(array('status'=>false));die;
		}
		$getModuleData = Engine_Api::_()->getDbTable('integrateothermodules', 'sesbasic')->getResults(array('column_name'=>'*','module_name'=>$getParams['module']));
		if(!$getModuleData){
			echo json_encode(array('status'=>false));die;
		}
		if(!isset($getModuleData[0]['content_id_photo']) || !isset($getModuleData[0]['content_id_photo']) || !isset($getParams[$getModuleData[0]['content_id_photo']]) || !isset($getParams[$getModuleData[0]['content_id']])){
			echo json_encode(array('status'=>false));die;
		}
    $this->view->child_id = $child_id = $getParams[$getModuleData[0]['content_id_photo']];
		$this->view->parent_id = $parent_id = $getParams[$getModuleData[0]['content_id']];
		$this->view->childItemPri = $getModuleData[0]['content_id_photo'];
		$this->view->child_item = $child_item = Engine_Api::_()->getItem($getModuleData[0]['content_type_photo'], $child_id);
		$this->view->parent_item = $parent_item = Engine_Api::_()->getItem($getModuleData[0]['content_type'], $parent_id);
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();		
		/*if(!$parent_item->authorization()->isAllowed($viewer, 'view')){
			$imagePrivateURL = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.private.photo', 1);
		 if(!is_file($imagePrivateURL))
      $imagePrivateURL = 'application/modules/Sesalbum/externals/images/private-photo.jpg';
		 $this->view->imagePrivateURL = $imagePrivateURL;
    }*/
    // get next photo URL
    $this->view->nextPhoto = Engine_Api::_()->sesalbum()->SesNextPreviousPhoto($child_item, '>', $getModuleData[0]['content_type_photo'],$getModuleData[0]['content_id_photo'],$getModuleData[0]['content_id']);
    // get previous photo URL
    $this->view->previousPhoto = Engine_Api::_()->sesalbum()->SesNextPreviousPhoto($child_item, '<', $getModuleData[0]['content_type_photo'],$getModuleData[0]['content_id_photo'],$getModuleData[0]['content_id']);
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$child_item_tablename =  Engine_Api::_()->getItemTable($getModuleData[0]['content_type_photo'])->info('name');
	  $checkColumnViewCount = $db->query('SHOW COLUMNS FROM '.$child_item_tablename.' LIKE \'view_count\'')->fetch();
	  
		if (!empty($checkColumnViewCount) && !$viewer || !$viewer->getIdentity() || !$child_item->isOwner($viewer)) {
      $child_item->view_count = new Zend_Db_Expr('view_count + 1');
      $child_item->save();
    }
		//check user_id || owner_id
		if((isset($parent_item->owner_id) && $parent_item->owner_id == $viewer->getIdentity()) || (isset($parent_item->user_id) && $parent_item->user_id == $viewer->getIdentity())){
			$this->view->canEdit = $canEdit = true;
			$this->view->canDelete = $canDelete = true;
		}
		if($viewer->getIdentity() == 0)
			$level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
		else
			$level = $viewer;
		$type = Engine_Api::_()->authorization()->getPermission($level,'album', 'imageviewer');
    if ($type == 0)
      $this->renderScript('photo/ses-imageviewer-basic.tpl');
    else
    $this->renderScript('photo/ses-imageviewer-advance.tpl');
  }
	public function changeSesdetailAction(){
		$item_id = $this->_getParam('item_id', '0');
		$item_type = $this->_getParam('item_type', '0');
		if($item_id && $item_type){
			$item = Engine_Api::_()->getItem($item_type, $item_id);
			if(isset($item->description))
				$item->description = $_POST['description'];
			if(isset($item->title))
				$item->title = $_POST['title'];
			$item->save();
			echo json_encode(array('status' => true, 'error' => false));die;
		}
		echo json_encode(array('status' => false, 'error' => true));die;
	}
	public function deleteSesAction(){
			$photo_id = $this->_getParam('photo_id', '0');
			$item_type = $this->_getParam('item_type', '0');
			if(!$photo_id || !$item_type)	return;
			$db = Zend_Db_Table_Abstract::getDefaultAdapter();
			$tablename =  Engine_Api::_()->getItemTable($item_type);
      $db->query("DELETE FROM ".$tablename->info('name')." WHERE ".current($tablename->info('primary'))." = $photo_id");
			 return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted successfully.')),
                'layout' => 'default-simple',
                'parentRedirect' => $_SERVER['HTTP_REFERER'],
      ));
	}
	//get data when user click on last photo in lightbox (advance lightbox)
	public function lastElementDataAction(){
			//send data is in .tpl
	}
		//get all module photo from Other Part modules
	function allphotoSesCompatibilityCodeAction(){
		$url = $this->_getParam('url',false);
		if(strpos($url,'https') === false && strpos($url,'http') === false){
				$itemUrl = $this->_getParam('url',false);
				$url = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://" ;
				$url = $url.$_SERVER['HTTP_HOST'].$itemUrl;
		}
		$request = new Zend_Controller_Request_Http($url);
		$frontController = Zend_Controller_Front::getInstance();		
		$router = $frontController->getRouter();
		$routeName=$router->route($request);
		$getParams = $routeName->getParams();
		if(!is_array($getParams)){
			echo json_encode(array('status'=>false));die;
		}
		$getModuleData = Engine_Api::_()->getDbTable('integrateothermodules', 'sesbasic')->getResults(array('column_name'=>'*','module_name'=>$getParams['module']));
		if(!$getModuleData){
			echo json_encode(array('status'=>false));die;
		}
		if(!isset($getModuleData[0]['content_id_photo']) || !isset($getModuleData[0]['content_id_photo']) || !isset($getParams[$getModuleData[0]['content_id_photo']]) || !isset($getParams[$getModuleData[0]['content_id']])){
			echo json_encode(array('status'=>false));die;
		}
		$this->view->child_item_primary = $getModuleData[0]['content_id_photo'];
    $this->view->child_id = $child_id = $getParams[$getModuleData[0]['content_id_photo']];
		$this->view->parent_id = $parent_id = $getParams[$getModuleData[0]['content_id']];
		$this->view->child_item = $child_item = Engine_Api::_()->getItem($getModuleData[0]['content_type_photo'], $child_id);
		$this->view->parent_item = $parent_item = Engine_Api::_()->getItem($getModuleData[0]['content_type'], $parent_id);
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $viewer = Engine_Api::_()->user()->getViewer();
		// send extra params to view for extra URL parameters
		$page = isset($_POST['page']) ? $_POST['page'] : 1;
		$is_ajax = isset($_POST['is_ajax']) ? $_POST['is_ajax'] : 0;
		$params['paginator'] = true;
		$this->view->childItem = $getModuleData[0]['content_type_photo'];
		//FETCH photos
		$paginator = $this->view->allPhotos =  Engine_Api::_()->sesalbum()->SesNextPreviousPhoto($child_item, '>', $getModuleData[0]['content_type_photo'],$getModuleData[0]['content_id_photo'],$getModuleData[0]['content_id'],true);
		$paginator->setItemCountPerPage(30);
		$this->view->limit = ($page-1)*30;
		$this->view->page = $page ;
		$this->view->is_ajax = $is_ajax ;
		$paginator->setCurrentPageNumber($page);
		$this->view->sesplugins = true;
	 	$this->renderScript('photo/all-photos.tpl');
	}
	//get all photo as per view type in light box(advance)
	public function allPhotosAction(){
			$this->view->photo_id = $photo_id = $this->getRequest()->getParam('photo_id', '0');
    $this->view->album_id = $album_id = $this->getRequest()->getParam('album_id', '0');
    $viewWidget = false;

    $viewer = Engine_Api::_()->user()->getViewer();
    $status = $this->getRequest()->getParam('status');
    if (null !== $this->getRequest()->getParam('type'))
      $status = 'special';
			switch ($status) {
      case 'is_featured':
        $params['status'] = 'is_featured';
				$params['order'] = $this->getRequest()->getParam('order');
        break;
			 case 'favourite':
			 	$params['status'] = 'favourite';
			 break;
			 case 'download':
			 	$params['status'] = 'download';
			 break;
      case 'is_sponsored':
        $params['status'] = 'is_sponsored';
				$params['order'] = $this->getRequest()->getParam('order');
        break;
      case 'comment':
        $params['status'] = 'comment';
        break;
      case 'view':
        $params['status'] = 'view';
        break;
      case 'creation':
        $params['status'] = 'creation';
        break;
      case 'modified':
        $params['status'] = 'modified';
        break;
      case 'like':
        $params['status'] = 'like';
        break;
      case 'offtheday':
        $params['status'] = 'offtheday';
        break;
      case 'tagged_photo':
        $params['status'] = 'tagged_photo';
        break;
      case 'photoofyou':
        $params['status'] = 'photoofyou';
        break;
      case 'by_me':
        $params['status'] = 'by_me';
        $viewWidget = true;
        break;
      case 'by_myfriend':
        $params['status'] = 'by_myfriend';
        $viewWidget = true;
        break;
      case 'on_site':
        $params['status'] = 'on_site';
        $viewWidget = true;
        break;
      case 'special':
        $params['status'] = $this->getRequest()->getParam('status');
        $params['type'] = $this->getRequest()->getParam('type');
        break;
			case 'member-featured':
				$params['status'] = 'member-featured';
				break;
      default:
        //silence and exit
        $params = array();
        break;
    }
				if ($this->getRequest()->getParam('user') != 0) {
					$params = array_merge($params, array('user' => $this->getRequest()->getParam('user')));
				}
    		// send extra params to view for extra URL parameters
   			$this->view->params = $params;
				$page = isset($_POST['page']) ? $_POST['page'] : 1;
				$is_ajax = isset($_POST['is_ajax']) ? $_POST['is_ajax'] : 0;
				$params['paginator'] = true;
				$photo = Engine_Api::_()->core()->getSubject();
				//FETCH photos
				$paginator = $this->view->allPhotos = Engine_Api::_()->getDbTable('photos', 'sesalbum')->getPhotoCustom($photo,$params,'',true);
  			$paginator->setItemCountPerPage(30);
				$this->view->limit = ($page-1)*30;
				$this->view->page = $page ;
				$this->view->is_ajax = $is_ajax ;
				$paginator->setCurrentPageNumber($page);
	}
	//function to open photos in lightbox
  public function imageViewerDetailAction() {
    $this->view->photo_id = $photo_id = $this->getRequest()->getParam('photo_id', '0');
    $this->view->album_id = $album_id = $this->getRequest()->getParam('album_id', '0');
    $viewWidget = false;
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.checkalbum'))
      return $this->_forward('notfound', 'error', 'core');
		$viewer = Engine_Api::_()->user()->getViewer();    
    $status = $this->getRequest()->getParam('status');
    if (null !== $this->getRequest()->getParam('type'))
      $status = 'special';
    switch ($status) {
      case 'is_featured':
        $params['status'] = 'is_featured';
				$params['order'] = $this->getRequest()->getParam('order');
        break;
			 case 'favourite':
			 	$params['status'] = 'favourite';
			 break;
			 case 'download':
			 	$params['status'] = 'download';
			 break;
      case 'is_sponsored':
        $params['status'] = 'is_sponsored';
				$params['order'] = $this->getRequest()->getParam('order');
        break;
      case 'comment':
        $params['status'] = 'comment';
        break;
      case 'view':
        $params['status'] = 'view';
        break;
      case 'creation':
        $params['status'] = 'creation';
        break;
      case 'modified':
        $params['status'] = 'modified';
        break;
      case 'like':
        $params['status'] = 'like';
        break;
      case 'offtheday':
        $params['status'] = 'offtheday';
        break;
      case 'tagged_photo':
        $params['status'] = 'tagged_photo';
        break;
      case 'photoofyou':
        $params['status'] = 'photoofyou';
        break;
      case 'by_me':
        $params['status'] = 'by_me';
        $viewWidget = true;
        break;
      case 'by_myfriend':
        $params['status'] = 'by_myfriend';
        $viewWidget = true;
        break;
      case 'on_site':
        $params['status'] = 'on_site';
        $viewWidget = true;
        break;
      case 'special':
        $params['status'] = $this->getRequest()->getParam('status');
        $params['type'] = $this->getRequest()->getParam('type');
        break;
			case 'member-featured':
				$params['status'] = 'member-featured';
				if(!($this->getRequest()->getParam('limit')))
					$paramsL['limit'] = 1;
			
				break;
      default:
        //silence and exit
        $params = array();
        break;
    }
    // initialize extra next previous params
    $extraParamsNext = $extraParamsPrevious = array();
    if ($this->getRequest()->getParam('limit') != '' && !is_null($this->getRequest()->getParam('limit')) || isset($params['paramsL'])) {
      $extraParamsNext['limit'] = $this->getRequest()->getParam('limit') + 1;
      $extraParamsPrevious['limit'] = $this->getRequest()->getParam('limit') - 1;
    }
    $this->view->extraParamsNext = $extraParamsNext;
    $this->view->extraParamsPrevious = $extraParamsPrevious;
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getAlbum();
		$this->view->stringD = '';
		
		$cookieData = isset($_COOKIE['sesalbum_lightbox_value']) ? $_COOKIE['sesalbum_lightbox_value'] : array();
		if(!in_array($album->album_id,explode(',',$cookieData))){
   	  $sesprofilelock_enable_module = (array)Engine_Api::_()->getApi('settings', 'core')->getSetting('sesprofilelock.enable.modules');
      //check dependent module sesprofile install or not
			if (Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesprofilelock'))  && in_array('sesalbum',$sesprofilelock_enable_module) && $viewer->getIdentity() != $album->owner_id) {
				//member level check for lock videos
				$viewer = Engine_Api::_()->user()->getViewer();
				if ($viewer->getIdentity() == 0)
					$level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
				else
					$level = $viewer;
				if ($album->is_locked) {
					$this->view->locked = true;
				} else {
					$this->view->locked = false;
				}
				$this->view->password = $album->password;
		  }else
      $this->view->locked = false;
		}else{
			$this->view->stringD = 'string';
			$this->view->locked = false;
		}
		if(!$album->authorization()->isAllowed($viewer, 'view')){
			$imagePrivateURL = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.private.photo', 1);
		 if(!is_file($imagePrivateURL))
      $imagePrivateURL = 'application/modules/Sesalbum/externals/images/private-photo.jpg';
		 $this->view->imagePrivateURL = $imagePrivateURL;
    }
		
		if($album->adult && !Engine_Api::_()->getApi('core', 'sesbasic')->checkAdultContent(array('module'=>'sesalbum')) && $album->owner_id != $viewer->getIdentity()){
			$privateImageURL = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum_album_default_adult', 1);
			if (!is_file($privateImageURL))
				$privateImageURL = 'application/modules/Sesalbum/externals/images/sesalbum_adult.png';
				 $this->view->imagePrivateURL = $privateImageURL;
		}
		
		$this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
    /* Insert data for recently viewed widget */
    if ($viewer->getIdentity() != 0 && isset($photo->photo_id) && !$viewWidget) {
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $dbObject->query('INSERT INTO engine4_sesalbum_recentlyviewitems (resource_id, resource_type,owner_id,creation_date ) VALUES ("' . $photo->photo_id . '", "album_photo","' . $viewer->getIdentity() . '",NOW())	ON DUPLICATE KEY UPDATE	creation_date = NOW()');
    }
    if ($this->getRequest()->getParam('user') != 0) {
      $params = array_merge($params, array('user' => $this->getRequest()->getParam('user')));
    }
    // send extra params to view for extra URL parameters
    $this->view->params = $params;
    $this->view->viewWidget = $viewer;
    // get next photo URL
		
    $this->view->nextPhoto = Engine_Api::_()->sesalbum()->nextPhoto($photo, array_merge($params, $extraParamsNext));
		// get previous photo URL
		$this->view->previousPhoto = Engine_Api::_()->sesalbum()->previousPhoto($photo, array_merge($params, $extraParamsPrevious));
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if($album_enable_check_privacy){
			$limit = $this->getRequest()->getParam('limit');
			if($this->view->nextPhoto && !$this->view->nextPhoto->authorization()->isAllowed($viewer, 'view')){
				$authorization = 0;
				$counter = 0;
				if(!$authorization){
					while ($authorization != 1) {
						$limit++;
						$counter++;
						$extraParamsNext['limit'] = $limit;
						$this->view->extraParamsNext = $extraParamsNext;
						$this->view->nextPhoto = Engine_Api::_()->sesalbum()->nextPhoto($photo, array_merge($params, $extraParamsNext));
						if($this->view->nextPhoto  != ''){
							$authorization =  $this->view->nextPhoto->authorization()->isAllowed($viewer, 'view');
							if(!$authorization){
								$this->view->nextPhoto = '';	
							}
						}else{
								$this->view->nextPhoto = '';
								$authorization = 1;
						}
					}
				}
			}
			if($this->view->previousPhoto && !$this->view->previousPhoto->authorization()->isAllowed($viewer, 'view')){
				$authorization = 0;
				if($limit != 0){
					while ($authorization != 1 || $limit != 0){
						$limit--;
						$extraParamsPrevious['limit'] = $limit;
						$this->view->extraParamsPrevious = $extraParamsPrevious;
						$this->view->previousPhoto = Engine_Api::_()->sesalbum()->previousPhoto($photo, array_merge($params, $extraParamsPrevious));
						if($this->view->previousPhoto  != ''){
							$authorization =  $this->view->previousPhoto->authorization()->isAllowed($viewer, 'view');
							if(!$authorization){
								$this->view->previousPhoto = '';
							}
						}else{
								$this->view->previousPhoto = '';
								$authorization = 1;
						}	
					};
				}
			}
		}
    if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }
    $checkAlbum = Engine_Api::_()->getItem('album', $this->_getParam('album_id'));
    if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $photo->album_id) {
      $this->_forward('requiresubject', 'error', 'core');
      return;
    }
    $this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
    $this->view->canTag = $canTag = $album->authorization()->isAllowed($viewer, 'tag');
    $this->view->canUntagGlobal = $canUntag = $album->isOwner($viewer);
		$this->view->canDownload = Engine_Api::_()->authorization()->isAllowed('album',$viewer, 'download');
		$this->view->canFavourite = Engine_Api::_()->authorization()->isAllowed('album',$viewer, 'favourite_photo');
    // Get tags
    $tags = array();
    foreach ($photo->tags()->getTagMaps() as $tagmap) {
      $tags[] = array_merge($tagmap->toArray(), array(
          'id' => $tagmap->getIdentity(),
          'text' => $tagmap->getTitle(),
          'href' => $tagmap->getHref(),
          'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id
      ));
    }
    $this->view->tags = $tags;
    // rating code
    $this->view->allowShowRating = $allowShowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratephoto.show', 1);
    $this->view->allowRating = $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.photo.rating', 1);
    $this->view->getAllowRating = $allowRating;
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    }
    else
      $showRating = true;
    $this->view->showRating = $showRating;
    if ($showRating != 0) {
      $this->view->canRate = $canRate = Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'rating_photo');
      $this->view->allowRateAgain = $allowRateAgain = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratephoto.again', 1);
      $this->view->allowRateOwn = $allowRateOwn = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratephoto.own', 1);
      if ($canRate == 0 || $allowRating == 0)
        $allowRating = false;
      else
        $allowRating = true;
      if ($allowRateOwn == 0 && $photo->owner_id == $viewer->getIdentity())
        $allowMine = false;
      else
        $allowMine = true;
      $this->view->allowMine = $allowMine;
      $this->view->allowRating = $allowRating;
      $viewer = Engine_Api::_()->user()->getViewer();
      $this->view->viewer_id = $viewer->getIdentity();
      $this->view->rating_type = $rating_type = 'album_photo';
      $this->view->rating_count = $rating_count = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->ratingCount($photo->getIdentity(), $rating_type);
      $this->view->rated = $rated = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->checkRated($photo->getIdentity(), $viewer->getIdentity(), $rating_type);
      $rating_sum = Engine_Api::_()->getDbTable('ratings', 'sesalbum')->getSumRating($photo->getIdentity(), 'album_photo');
      if ($rating_count != 0) {
        $this->view->total_rating_average = $rating_sum / $rating_count;
      }
      else
        $this->view->total_rating_average = 0;

      if (!$allowRateAgain && $rated)
        $rated = false;
      else
        $rated = true;
      $this->view->ratedAgain = $rated;
      // end rating code
    }
    $getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
    if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.6') < 0)
      $this->view->toArray = true;
    else
      $this->view->toArray = false;
   $viewer = Engine_Api::_()->user()->getViewer();
	 if($viewer->getIdentity() == 0)
			$level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
		else
			$level = $viewer;
		$type = Engine_Api::_()->authorization()->getPermission($level,'album', 'imageviewer');
    if ($type == 0)
      $this->renderScript('photo/image-viewer-detail-basic.tpl');
    else
      $this->renderScript('photo/image-viewer-detail-advance.tpl');
  }
	//add tag action
  public function addAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    if (!$this->_helper->requireSubject()->isValid())
      return;
    //if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'tag')->isValid() ) return;
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!method_exists($subject, 'tags')) {
      throw new Engine_Exception('whoops! doesn\'t support tagging');
    }
    // GUID tagging
    if (null !== ($guid = $this->_getParam('guid'))) {
      $tag = Engine_Api::_()->getItemByGuid($this->_getParam('guid'));
    }
    // STRING tagging
    else if (null !== ($text = $this->_getParam('label'))) {
      $tag = $text;
    }
    $tagmap = $subject->tags()->addTagMap($viewer, $tag, $this->_getParam('extra'));
    if (is_null($tagmap)) {
      // item has already been tagged
      return;
    }
    if (!$tagmap instanceof Core_Model_TagMap) {
      throw new Engine_Exception('Tagmap was not recognised');
    }
    // Do stuff when users are tagged
    if ($tag instanceof User_Model_User && !$subject->isOwner($tag) && !$viewer->isSelf($tag)) {
      // Add activity
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity(
              $viewer, $tag, 'tagged', '', array(
          'label' => $this->view->translate(str_replace('_', ' ', 'sesphoto'))
              )
      );
      if ($action)
        $action->attach($subject);
      // Add notification
      $type_name = $this->view->translate(str_replace('_', ' ', 'sesphoto'));
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
              $tag, $viewer, $subject, 'tagged', array(
          'object_type_name' => $type_name,
          'label' => $type_name,
              )
      );
    }
    $this->view->id = $tagmap->getIdentity();
    $this->view->guid = $tagmap->tag_type . '_' . $tagmap->tag_id;
    $this->view->text = $tagmap->getTitle();
    $this->view->href = $tagmap->getHref();
    $this->view->extra = $tagmap->extra;
  }
	//photo favourite action
	function favAction(){
    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Login'));die;
    }
    $photo_id = $this->_getParam('photo_id');
    if (intval($photo_id) == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Invalid argument supplied.'));die;
    }
			$viewer = Engine_Api::_()->user()->getViewer();
    $Fav =  Engine_Api::_()->getDbTable('favourites', 'sesalbum')->getItemfav('album_photo',$photo_id);
		$photo = Engine_Api::_()->getDbtable('photos', 'sesalbum');
    if (count($Fav) > 0) {
      //delete		
      $db = $Fav->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $Fav->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $photo->update(array(
          'favourite_count' => new Zend_Db_Expr('favourite_count - 1'),
              ), array(
          'photo_id = ?' => $photo_id,
      ));
      $photos = Engine_Api::_()->getItem('album_photo', $photo_id);
			 Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => "sesalbum_favouritephoto", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $photos->getType(), "object_id = ?" => $photos->getIdentity()));
			 Engine_Api::_()->getDbtable('actions', 'activity')->delete(array('type =?' => "sesalbum_favouritephoto", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $photos->getType(), "object_id = ?" => $photos->getIdentity()));
			 Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($photos);
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'favourite_count' => $photos->favourite_count)); die;
    } else {
      //update
      $db = Engine_Api::_()->getDbTable('favourites', 'sesalbum')->getAdapter();
      $db->beginTransaction();
      try {
        $fav = Engine_Api::_()->getDbTable('favourites', 'sesalbum')->createRow();
        $fav->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $fav->resource_type = 'album_photo';
        $fav->resource_id = $photo_id;
        $fav->save();
        $photo->update(array(
            'favourite_count' => new Zend_Db_Expr('favourite_count + 1'),
                ), array(
            'photo_id = ?' => $photo_id,
        ));
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
			//send notification and activity feed work.
      $favourite_count = Engine_Api::_()->getItem('album_photo', $photo_id);
			$subject = $favourite_count;
			 $owner = $subject->getOwner();
			 if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
			 $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
			 Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => "sesalbum_favouritephoto", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, 'sesalbum_favouritephoto');
        $result = $activityTable->fetchRow(array('type =?' => "sesalbum_favouritephoto", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
        if (!$result) {
          $action = $activityTable->addActivity($viewer, $subject, 'sesalbum_favouritephoto');
          if ($action)
            $activityTable->attachActivity($action, $subject);
        }		
			}
     echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'favourite_count' => $favourite_count->favourite_count)); die;
    }	
	}
	//photo like action
  function likeAction() {
    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Login'));die;
    }
    $photo_id = $this->_getParam('photo_id');
    if (intval($photo_id) == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Invalid argument supplied.'));die;
    }
    $tableLike = Engine_Api::_()->getDbtable('likes', 'core');
    $tableMainLike = $tableLike->info('name');
    $photo = Engine_Api::_()->getDbtable('photos', 'sesalbum');
    $select = $tableLike->select()->from($tableMainLike)->where('resource_type =?', 'album_photo')->where('poster_id =?', Engine_Api::_()->user()->getViewer()->getIdentity())->where('poster_type =?', 'user')->where('resource_id =?', $photo_id);
    $Like = $tableLike->fetchRow($select);
    if (count($Like) > 0) {
      //delete		
      $db = $Like->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $Like->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $photo->update(array(
          'like_count' => new Zend_Db_Expr('like_count - 1'),
              ), array(
          'photo_id = ?' => $photo_id,
      ));
      $like_count = Engine_Api::_()->getItem('album_photo', $photo_id);
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'like_count' => $like_count->like_count));
      die;
    } else {
      //update
      $db = $tableLike->getAdapter();
      $db->beginTransaction();
      try {
        $like = $tableLike->createRow();
        $like->poster_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $like->resource_type = 'album_photo';
        $like->resource_id = $photo_id;
        $like->poster_type = 'user';
        $like->save();

        $photo->update(array(
            'like_count' => new Zend_Db_Expr('like_count + 1'),
                ), array(
            'photo_id = ?' => $photo_id,
        ));
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
			//notification work
      $like_count = Engine_Api::_()->getItem('album_photo', $photo_id);
			$viewer = Engine_Api::_()->user()->getViewer();
			 $owner = $like_count->getOwner();
			 if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($owner, $viewer, $like_count, 'liked', array(
            'label' => $like_count->getShortType()
        ));
      }
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'like_count' => $like_count->like_count));die;
    }
  }
	//function to download photo from other modules lightbox
  public function downloadAction() {
    $filePath = $this->getRequest()->getParam('filePath');
    $file_id = $this->getRequest()->getParam('file_id');
    if ($filePath == '' || intval($file_id) == 0)
      return;
    $storageTable = Engine_Api::_()->getDbTable('files', 'storage');
    $select = $storageTable->select()->from($storageTable->info('name'), array('storage_path', 'name'))->where('file_id = ?', $file_id);
    $storageData = $storageTable->fetchRow($select);
    $storageData = (object) $storageData->toArray();
    if (empty($storageData->name) || $storageData->name == '' || empty($storageData->storage_path) || $storageData->storage_path == '')
      return;
    //Get base path
    $basePath = APPLICATION_PATH . '/' . $storageData->storage_path;
    @chmod($basePath, 0777);
    header("Content-Disposition: attachment; filename=" . urlencode(basename($storageData->name)), true);
    header("Content-Transfer-Encoding: Binary", true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    header("Content-Length: " . filesize($basePath), true);
    readfile("$basePath");
    exit();
    // for safety resason double check
    return;
  }
	//photo view function
  public function viewAction() {
		if(!Engine_Api::_()->core()->hasSubject()){
		 $album_id = 	$this->getRequest()->getParam('album_id');
		 $url = Engine_Api::_()->sesalbum()->getHref($album_id);
		 header('location:'.$url);
		 die;
		}
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
	if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.checkalbum'))
      return $this->_forward('notfound', 'error', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getAlbum();
    if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }
    /* Insert data for recently viewed widget */
    if ($viewer->getIdentity() != 0 && isset($photo->photo_id)) {
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $dbObject->query('INSERT INTO engine4_sesalbum_recentlyviewitems (resource_id, resource_type,owner_id,creation_date ) VALUES ("' . $photo->photo_id . '", "album_photo","' . $viewer->getIdentity() . '",NOW())	ON DUPLICATE KEY UPDATE	creation_date = NOW()');
    }
    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
        $message_view = true;
    }
    $this->view->message_view = $message_view;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid())
      return;
    if (!$message_view && !$this->_helper->requireAuth()->setAuthParams($photo, null, 'view')->isValid())
      return;
    $checkAlbum = Engine_Api::_()->getItem('album', $this->_getParam('album_id'));
    if (!($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() || $checkAlbum->album_id != $photo->album_id) {
      $this->_forward('requiresubject', 'error', 'core');
      return;
    }
		
    // Render
		if((Engine_Api::_()->getApi('core', 'sesbasic')->checkAdultContent(array('module'=>'sesalbum')) && $checkAlbum->adult) || $checkAlbum->owner_id == $viewer->getIdentity()) {
    	$this->_helper->content->setEnabled();
		} elseif($checkAlbum->adult) {
      $this->view->adultContent = true;
    } else {
			$this->_helper->content->setEnabled();
    }
  }
	//photo delete function
  public function deleteAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'delete')->isValid())
      return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject('album_photo');
    $album = $photo->getParent();
    $this->view->form = $form = new Sesalbum_Form_Photo_Delete();
    if (!$this->getRequest()->isPost())
      return;
    if (!$form->isValid($this->getRequest()->getPost()))
      return;
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      // delete files from server
      $filesDB = Engine_Api::_()->getDbtable('files', 'storage');
      $filePath = $filesDB->fetchRow($filesDB->select()->where('file_id = ?', $photo->file_id))->storage_path;
      unlink($filePath);
      $thumbPath = $filesDB->fetchRow($filesDB->select()->where('parent_file_id = ?', $photo->file_id))->storage_path;
      unlink($thumbPath);
      // Delete image and thumbnail
      $filesDB->delete(array('file_id = ?' => $photo->file_id));
      $filesDB->delete(array('parent_file_id = ?' => $photo->file_id));
      // Check activity actions
      $attachDB = Engine_Api::_()->getDbtable('attachments', 'activity');
      $actions = $attachDB->fetchAll($attachDB->select()->where('type = ?', 'album_photo')->where('id = ?', $photo->photo_id));
      $actionsDB = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actions as $action) {
        $action_id = $action->action_id;
        $attachDB->delete(array('type = ?' => 'album_photo', 'id = ?' => $photo->photo_id));
        $action = $actionsDB->fetchRow($actionsDB->select()->where('action_id = ?', $action_id));
        $count = $action->params['count'];
        if (!is_null($count) && ($count > 1)) {
          $action->params = array('count' => (integer) $count - 1);
          $action->save();
        } else {
          $action->delete();
        }
      }
      $photo->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    // get album_id 
    $ablum_id = (int) $this->_getParam('album_id', '0');
    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted successfully.')),
                'layout' => 'default-simple',
                'parentRedirect' => Engine_Api::_()->sesalbum()->getHref($ablum_id),
    ));
  }
	//location photo 
  public function locationAction() {
	$chanel_id = $this->_getParam('chanel_id',0);
	if(!$chanel_id){
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;
	}
		$this->view->type = $this->_getParam('type','photo');
    $this->view->photo_id = $photo_id = $this->_getParam('photo_id');
    $viewer = Engine_Api::_()->user()->getViewer();
	if(!$chanel_id){
    $photo = Engine_Api::_()->core()->getSubject('album_photo');
	}else{
		$photo =  Engine_Api::_()->getItem('sesvideo_chanelphoto', $photo_id);	
	}
    $this->view->photo = $photo;
    $this->view->form = $form = new Sesalbum_Form_Photo_Location();
    $form->populate($photo->toArray());
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $values = $form->getValues();
		//update location data in sesbasic location table
    if (isset($_POST['lat']) && isset($_POST['lng']) && $_POST['lat'] != '' && $_POST['lng'] != '') {
      $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
      $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $this->_getParam('photo_id') . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesalbum_photo")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
    }
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setFromArray($values);
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your location have been saved.')),
                'layout' => 'default-simple',
                'parentRefresh' => false,
								'smoothboxClose' => true,
    ));
  }
	//edit photo function
  public function editAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject('album_photo');
    $this->view->form = $form = new Sesalbum_Form_Photo_Edit();
    $form->populate($photo->toArray());
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $values = $form->getValues();
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setFromArray($values);
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
    ));
  }
	//edit photo details from lightbox
  public function editDetailAction() {
    $status = true;
    $error = false;
    if (!$this->_helper->requireSubject('album_photo')->isValid()) {
      $status = false;
      $error = true;
    }
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) {
      $status = false;
      $error = true;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject('album_photo');
    if ($status && !$error) {
      $values['title'] = $_POST['title'];
      $values['description'] = $_POST['description'];
      $values['location'] = $_POST['location'];
			//update location data in sesbasic location table
      if ($_POST['lat'] != '' && $_POST['lng'] != '') {
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $_POST['photo_id'] . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesalbum_photo")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
      }
      $db = $photo->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $photo->setFromArray($values);
        $photo->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
    echo json_encode(array('status' => $status, 'error' => $error));die;
  }
	//rotate photo action from lightbox and photo view page
  public function rotateAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject('album_photo');
    $angle = (int) $this->_getParam('angle', 90);
    if (!$angle || !($angle % 360)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must not be empty');
      return;
    }
    if (!in_array((int) $angle, array(90, 270))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must be 90 or 270');
      return;
    }
    // Get file
	
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
		
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }
    // Pull photo to a temporary file
    $tmpFile = $file->temporary();
    // Operate on the file
    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->rotate($angle)
            ->write()
            ->destroy()
    ;
    // Set the photo
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setPhoto($tmpFile,false,true);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }
	//flip photo action function 
  public function flipAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject('album_photo');
    $direction = $this->_getParam('direction');
    if (!in_array($direction, array('vertical', 'horizontal'))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid direction');
      return;
    }
    // Get file
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }
    // Pull photo to a temporary file
    $tmpFile = $file->temporary();
    // Operate on the file
    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->flip($direction != 'vertical')
            ->write()
            ->destroy()
    ;
    // Set the photo
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setPhoto($tmpFile,false,'flip');
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }
	//crop photo action 
  public function cropAction() {
    if (!$this->_helper->requireSubject('album_photo')->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject('album_photo');
    $x = (int) $this->_getParam('x', 0);
    $y = (int) $this->_getParam('y', 0);
    $w = (int) $this->_getParam('w', 0);
    $h = (int) $this->_getParam('h', 0);
    // Get file
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }
    // Pull photo to a temporary file
    $tmpFile = $file->temporary();
    // Open the file
    $image = Engine_Image::factory();
    $image->open($tmpFile);
    $curH = $image->getHeight();
    $curW = $image->getWidth();
    // Check the parameters
    if ($x < 0 ||
            $y < 0 ||
            $w < 0 ||
            $h < 0 ||
            $x + $w > $curW ||
            $y + $h > $curH) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid size');
      return;
    }
    $image->open($tmpFile)
            ->crop($x, $y, $w, $h)
            ->write()
            ->destroy()
    ;
    // Set the photo
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setPhoto($tmpFile,false,'crop');
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }
}
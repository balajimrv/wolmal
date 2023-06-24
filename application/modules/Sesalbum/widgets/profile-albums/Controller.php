<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Widget_ProfileAlbumsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  public function indexAction()
  {
		if(empty($_POST['is_ajax'])){
			// Don't render this if not authorized
			$viewer = Engine_Api::_()->user()->getViewer();
			if( !Engine_Api::_()->core()->hasSubject() ) {
				return $this->setNoRender();
			}
			// Get subject and check auth
			$subject = Engine_Api::_()->core()->getSubject();
			if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
				return $this->setNoRender();
			}
		}
		if(isset($_POST['params'])){
			$params = json_decode($_POST['params'],true);
		}
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.albumche'))
		  return $this->setNoRender();
		$this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
		$page = isset($_POST['page']) ? $_POST['page'] : 1 ;
		$this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
		$this->view->defaultOptionsArray = $defaultOptionsArray = $this->_getParam('search_type');
		$this->view->load_content = $load_content = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
		$this->view->height = $defaultHeight =isset($params['height']) ? $params['height'] : $this->_getParam('height', '250');
		$this->view->height_masonry = $defaultHeightMasonry =isset($params['height_masonry']) ? $params['height_masonry'] : $this->_getParam('height_masonry', '250');
		$this->view->width = $defaultWidth= isset($params['width']) ? $params['width'] :$this->_getParam('width', '200');
		$this->view->limit_data = $value['limit_data'] = $limit_data = isset($params['limit_data']) ? $params['limit_data'] :$this->_getParam('limit_data', '20');
 	  $this->view->limit = ($page-1)*$limit_data;
		$this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] :$this->_getParam('title_truncation', '45');
		$this->view->show_limited_data = $show_limited_data = isset($params['show_limited_data']) ? $params['show_limited_data'] :$this->_getParam('show_limited_data', 'no');
		$this->view->view_type = $view_type = isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', 'masonry');
		$show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','socialSharing','view','photoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','downloadCount'));
		$this->view->fixHover = $fixHover = isset($params['fixHover']) ? $params['fixHover'] :$this->_getParam('fixHover', 'fix');
		$this->view->insideOutside =  $insideOutside = isset($params['insideOutside']) ? $params['insideOutside'] : $this->_getParam('insideOutside', 'inside');
		$this->view->fixHover_profileAlbums = $fixHover_profileAlbums = isset($params['fixHover_profileAlbums']) ? $params['fixHover_profileAlbums'] :$this->_getParam('fixHover_profileAlbums', 'fix');
		$this->view->insideOutside_profileAlbums =  $insideOutside_profileAlbums = isset($params['insideOutside_profileAlbums']) ? $params['insideOutside_profileAlbums'] : $this->_getParam('insideOutside_profileAlbums', 'inside');
	if(count($show_criterias)){
		foreach($show_criterias as $show_criteria)
			$this->view->$show_criteria = $show_criteria;
	}
		if(!$is_ajax){
			if(count($defaultOptionsArray) == 0)
					return $this->setNoRender();
			$defaultOptions = $arrayOptions = array();
			foreach($defaultOptionsArray as $key=>$defaultValue){
				if($defaultValue == 'albumCreate' && $viewer->getIdentity() == 0)
					continue;
				if( $this->_getParam($defaultValue.'_order'))
					$order = $this->_getParam($defaultValue.'_order').'||'.$defaultValue;
				else
					$order = (999+$key).'||'.$defaultValue;
				if( $this->_getParam($defaultValue.'_label'))
						$valueLabel = $this->_getParam($defaultValue.'_label');
				else{
					if($defaultValue == 'taggedPhoto')
						$valueLabel ='Tagged Photo';
					else if($defaultValue == 'photoofyou')
						$valueLabel = 'Photo Of You';
					else if($defaultValue == 'profileAlbums')
						$valueLabel = 'Profile Albums';
					else if($defaultValue == 'albumCreate')
						$valueLabel = 'Add Album';
				}
				$arrayOptions[$order] = $valueLabel;
			}
			
			ksort($arrayOptions);
			$counter = 0;
			foreach($arrayOptions as $key => $valueOption){
				$key = explode('||',$key);
			if($counter == 0)
				$this->view->defaultOpenTab = $defaultOpenTab = $key[1];
				$defaultOptions[$key[1]]=$valueOption;
				$counter++;
			}				
			$this->view->defaultOptions = $defaultOptions;
		}
		if(isset($_GET['openTab']) || $is_ajax){
		 $this->view->defaultOpenTab = $defaultOpenTab = (isset($_GET['openTab']) ? str_replace('_','SP',$_GET['openTab']) : ($this->_getParam('openTab') != NULL ? $this->_getParam('openTab') : (isset($params['openTab']) ? $params['openTab'] : '' )));
		}
		$type = '';
		switch($defaultOpenTab){
			case 'taggedPhoto':
				$type = 'tagged_photo';
				$this->view->albumPhotoOption = 'photo';
			break;
			case 'photoofyou':
				$type = 'photoofyou';
				$this->view->albumPhotoOption = 'photo';
			break;
			case 'profileAlbums':
				$type = 'profileAlbums';
				$this->view->albumPhotoOption = 'album';
			break;
			default:
				return $this->setNoRender();
			break;
		}
		$defaultOptions =isset($params['defaultOptions']) ? $params['defaultOptions'] : $defaultOptions;
		$params = $this->view->params = array('height'=>$defaultHeight,'width' => $defaultWidth,'limit_data' => $limit_data,'openTab'=>$defaultOpenTab,'pagging'=>$load_content,'show_criterias'=>$show_criterias,'view_type'=>$view_type,'title_truncation' =>$title_truncation,'insideOutside' =>$insideOutside,'fixHover'=>$fixHover,'defaultOptions'=>$defaultOptions,'height_masonry'=>$defaultHeightMasonry,'show_limited_data'=>$show_limited_data,'insideOutside_profileAlbums' =>$insideOutside_profileAlbums,'fixHover_profileAlbums'=>$fixHover_profileAlbums);		
		$this->view->loadMoreLink = $this->_getParam('openTab') != NULL ? true : false;
		$this->view->type = $type;
		if(empty($_POST['is_ajax'])){
			if($subject->user_id != $viewer->getIdentity()){
				$userObject = Engine_Api::_()->getItem('user', $subject->user_id);
				$profile = 'other';
				$userId = $subject->user_id;
			}else{
				$userObject = Engine_Api::_()->getItem('user', $viewer->getIdentity());
				$profile = 'own';
				$userId = $viewer->getIdentity();
			}
		}else
			$userId = $_POST['identityObject'];
		$this->view->identityObject = $value['userId'] = $userId ;
		$value['allowSpecialAlbums'] = true;
		if($type == 'profileAlbums'){
			$paginator = Engine_Api::_()->getDbTable('albums', 'sesalbum')->profileAlbums($value);
		}else if($type == 'tagged_photo'){
		$paginator = Engine_Api::_()->sesalbum()->taggedPhoto($value);
		 $this->view->makeObjectOfPhoto = true;
		}else if($type == 'photoofyou'){
			$paginator = Engine_Api::_()->getDbTable('photos', 'sesalbum')->photoOfYou($value);
		}
		if(empty($_POST['is_ajax'])){
			// owner type
			if($profile == 'own'){
				$this->view->profile = 'own';	
			}else{
				$name = explode(' ',$userObject->displayname);
				if(isset($name[0]))
					$name = ucfirst($name[0]);
				else
					$name = ucfirst($name[1]);
				$this->view->profile = $name;	
			}
		}
		$this->view->itemOrigTitle = isset($defaultOptions[$defaultOpenTab]) ? $defaultOptions[$defaultOpenTab] : 'items';
    $this->view->paginator = $paginator ;
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($limit_data);
		$this->view->page = $page ;
    $paginator->setCurrentPageNumber($page);
		if($is_ajax)
			$this->getElement()->removeDecorator('Container');

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      //$this->_childCount = $paginator->getTotalItemCount();
    }
  }
  public function getChildCount()
  {
    return $this->_childCount;
  }
}
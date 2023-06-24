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
class Sesalbum_Widget_BrowseAlbumsController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		// Default param options
		if(isset($_POST['params'])){
			$params = json_decode($_POST['params'],true);
		}
		if(isset($_POST['searchParams']) && $_POST['searchParams'])
			parse_str($_POST['searchParams'], $searchArray);
		$this->view->is_ajax = $value['is_ajax'] = isset($_POST['is_ajax']) ? true : false;
		$value['page'] = isset($_POST['page']) ? $_POST['page'] : 1 ;
		$value['identityForWidget'] = $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
	  $this->view->view_type =$view_type =isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', '1');
		$this->view->height = $value['defaultHeight'] =isset($params['height']) ? $params['height'] : $this->_getParam('height', '200');
		$this->view->width = $value['defaultWidth'] =isset($params['width']) ? $params['width'] : $this->_getParam('width', '200');
		$this->view->limit_data = $value['limit_data'] = isset($params['limit_data']) ? $params['limit_data'] : $this->_getParam('limit_data', '20');		
		$this->view->load_content = $value['load_content'] = isset($params['load_content']) ? $params['load_content'] : $this->_getParam('load_content', 'auto_load');	
		$value['category_id'] = isset($searchArray['category_id']) ? $searchArray['category_id'] :  (isset($_GET['category_id']) ? $_GET['category_id'] : (isset($params['category_id']) ?  $params['category_id'] : '')) ;
		$value['subcat_id'] = isset($searchArray['subcat_id']) ? $searchArray['subcat_id'] :  (isset($_GET['subcat_id']) ? $_GET['subcat_id'] : (isset($params['subcat_id']) ?  $params['subcat_id'] : '')) ;
		$value['subsubcat_id'] = isset($searchArray['subsubcat_id']) ? $searchArray['subsubcat_id'] :  (isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : (isset($params['subsubcat_id']) ?  $params['subsubcat_id'] : '')) ;
		$value['sort'] = isset($searchArray['sort']) ? $searchArray['sort'] :  (isset($_GET['sort']) ? $_GET['sort'] : (isset($params['sort']) ?  $params['sort'] : $this->_getParam('sort', 'mostSPliked'))) ;
		$value['search'] = isset($searchArray['search']) ? $searchArray['search'] :  (isset($_GET['search']) ? $_GET['search'] : (isset($params['search']) ?  $params['search'] : '')) ;
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.albumche'))
		  return $this->setNoRender();
		$value['tag_id'] = isset($_GET['tag_id']) ? $_GET['tag_id'] : (isset($params['tag_id']) ?  $params['tag_id'] : '') ;
		$value['location'] = isset($searchArray['location']) ? $searchArray['location'] :  (isset($_GET['location']) ? $_GET['location'] : (isset($params['location']) ?  $params['location'] : ''));
		$value['lat'] = isset($searchArray['lat']) ? $searchArray['lat'] :  (isset($_GET['lat']) ? $_GET['lat'] : (isset($params['lat']) ?  $params['lat'] : ''));
		$value['show'] = isset($searchArray['show']) ? $searchArray['show'] :  (isset($_GET['show']) ? $_GET['show'] : (isset($params['show']) ?  $params['show'] : ''));
		$value['lng'] = isset($searchArray['lng']) ? $searchArray['lng'] :  (isset($_GET['lng']) ? $_GET['lng'] : (isset($params['lng']) ?  $params['lng'] : ''));
		$value['miles'] = isset($searchArray['miles']) ? $searchArray['miles'] :  (isset($_GET['miles']) ? $_GET['miles'] : (isset($params['miles']) ?  $params['miles'] : ''));
		$value['user_id'] = isset($_GET['user_id']) ? $_GET['user_id'] : (isset($params['user_id']) ?  $params['user_id'] : '');
		$this->view->title_truncation = $value['title_truncation'] = isset($params['title_truncation']) ? $params['title_truncation'] : $this->_getParam('title_truncation', '45');
		$value['show_criterias'] = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','socialSharing','view','photoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','downloadCount'));
		$this->view->fixHover = $fixHover = isset($params['fixHover']) ? $params['fixHover'] :$this->_getParam('fixHover', 'fix');
		$this->view->insideOutside =  $insideOutside = isset($params['insideOutside']) ? $params['insideOutside'] : $this->_getParam('insideOutside', 'inside');
		foreach($value['show_criterias'] as $show_criteria)
			$this->view->$show_criteria = $show_criteria;
		if(isset($value['sort']) && $value['sort'] != ''){
			$value['getParamSort'] = str_replace('SP','_',$value['sort']);
		}else
			$value['getParamSort'] = 'creation_date';
		switch($value['getParamSort']) {
      case 'most_viewed':
        $value['order'] = 'view_count';
        break;
			case 'most_favourite':
        $value['order'] = 'favourite_count';
        break;
			case 'most_liked':
				$value['order'] = 'like_count';
				break;
			case 'most_commented':
				$value['order'] = 'comment_count';
				break;
			case 'featured':
				$value['order'] = 'is_featured';			
				break;
			case 'sponsored':
				$value['order'] = 'is_sponsored';
				break;
			case 'most_rated':
				$value['order'] = 'rating';
				break;
      case 'creation_date':
      default:
        $value['order'] = 'creation_date';
        break;
    }
		$this->view->viewer = Engine_Api::_()->user()->getViewer();
		$params = $this->view->params = array('width'=>$value['defaultWidth'],'height'=>$value['defaultHeight'],'limit_data' => $value['limit_data'],'sort'=>$value['sort'],'search'=>$value['search'],'category_id'=>$value['category_id'],'subcat_id'=>$value['subcat_id'],'subsubcat_id'=>$value['subsubcat_id'],'tag_id'=>$value['tag_id'],'load_content'=>$value['load_content'],'show_criterias'=>$value['show_criterias'],'title_truncation' =>$value['title_truncation'],'lat'=>$value['lat'],'lng'=>$value['lng'],'location'=>$value['location'],'miles'=>$value['miles'],'insideOutside' =>$insideOutside,'fixHover'=>$fixHover,'user_id'=>$value['user_id'],'view_type'=>$view_type,'show'=>$value['show']);
		$this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'sesalbum')->getAlbumSelect($value);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($value['limit_data']);
		$this->view->page = $value['page'] ;
    $paginator->setCurrentPageNumber($value['page']);
		if($value['is_ajax'])
			$this->getElement()->removeDecorator('Container');  	
			else{
			// Do not render if nothing to show
			/*if( $paginator->getTotalItemCount() <= 0 ) {
					return $this->setNoRender();
			}*/
		}
	}
}
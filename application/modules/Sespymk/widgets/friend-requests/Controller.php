<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sespymk
 * @package    Sespymk
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2017-03-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sespymk_Widget_FriendRequestsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);
  
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    if(empty($viewer_id))
      return $this->setNoRender();
    
    $enable_type = array();
    foreach (Engine_Api::_()->getDbtable('NotificationTypes', 'activity')->getNotificationTypes() as $type) {
      $enable_type[] = $type->type;
    }
    
    $this->view->viewmore = $this->_getParam('viewmore', 0);
    if ($this->view->viewmore)
      $this->getElement()->removeDecorator('Container');
      
      
    $this->view->paginationType = $paginationType = isset($params['paginationType']) ? $params['paginationType'] : $this->_getParam('paginationType', 1);
  
    $this->view->linktopage = $linktopage = isset($params['linktopage']) ? $params['linktopage'] : $this->_getParam('linktopage', 1);
    
    $itemCount = isset($params['itemCount']) ? $params['itemCount'] : $this->_getParam('itemCount', 5);
    
    $this->view->all_params = array('itemCount' => $itemCount, 'paginationType' => $paginationType);

    $select = Engine_Api::_()->getDbtable('notifications', 'activity')->select()
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('type IN(?)', $enable_type)
            ->where('type = ?', 'friend_request')
            ->where('mitigated = ?', 0)
            ->order('date DESC')
            ->group('subject_id'); //echo $select;die;
    $this->view->friendRequests = $newFriendRequests = Zend_Paginator::factory($select);
    //$newFriendRequests->setCurrentPageNumber($this->_getParam('page'));
    $newFriendRequests->setItemCountPerPage($itemCount);
    $newFriendRequests->setCurrentPageNumber($this->_getParam('page'));

  }
}

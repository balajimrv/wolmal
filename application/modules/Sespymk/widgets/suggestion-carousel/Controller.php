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

class Sespymk_Widget_SuggestionCarouselController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $coreApi = Engine_Api::_()->core();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();
    
    $this->view->width = $this->_getParam('width', '200');

    $this->view->viewType = $this->_getParam('viewType', 'horizontal');
    $this->view->height = $this->_getParam('height', '200');
    
    //Advanced Activity Feed Plugin integration
    $this->view->anfheader = $this->_getParam('anfheader', 0);
    if($this->view->anfheader)
      $this->getElement()->removeDecorator('Container');
      
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_level_id = $viewer->level_id;
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

		$this->view->heightphoto = $this->_getParam('heightphoto', '200');
    $itemCount = $this->_getParam('itemCount', 10);
    $this->view->showdetails = $this->_getParam('showdetails', array('friends', 'mutualfriends'));
    $onlyphotousers = $this->_getParam('onlyphotousers', 0);
    
    $anffeed = $this->_getParam('anffeed', 0);
    
    $this->view->memberEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesmember');

    //People You May Know work
    $userIDS = $viewer->membership()->getMembershipsOfIds();
    $userMembershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $userMembershipTableName = $userMembershipTable->info('name');
    $select_membership = $userMembershipTable->select()
            ->where('resource_id = ?', $viewer->getIdentity());
    $member_results = $userMembershipTable->fetchAll($select_membership);
    foreach($member_results as $member_result) {
      $membershipIDS[] = $member_result->user_id;
    }
    
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');
    $select = $userTable->select()
                        ->where('user_id <> ?', $viewer->getIdentity());
    if($onlyphotousers)
      $select->where('photo_id <> ?', 0);
    if($membershipIDS) {
      $select->where('user_id NOT IN (?)', $membershipIDS);
    }
    
    $select->order('rand()');
    $this->view->peopleyoumayknow = $peopleyoumayknow = Zend_Paginator::factory($select);
    $peopleyoumayknow->setItemCountPerPage($itemCount);
    $peopleyoumayknow->setCurrentPageNumber($this->_getParam('page'));
    if(!empty($anffeed) && $peopleyoumayknow->getTotalItemCount() < 7)
      return $this->setNoRender();

    //People You may know work
     if ($peopleyoumayknow->getTotalItemCount() == 0)
       return $this->setNoRender();
      

  }

}
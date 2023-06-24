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

class Sespymk_Widget_SuggestionFriendsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_level_id = $viewer->level_id;
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    
    $this->view->horiwidth = $this->_getParam('horiwidth', '150');
		$this->view->horiheight = $this->_getParam('horiheight', '150');
    $itemCount = $this->_getParam('itemCount', 5);
    $this->view->showType = $this->_getParam('showType', 1);
    $this->view->showdetails = $this->_getParam('showdetails', array('friends', 'mutualfriends'));
    $onlyphotousers = $this->_getParam('onlyphotousers', 0);
    
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

    //People You may know work
     if ($peopleyoumayknow->getTotalItemCount() == 0)
       return $this->setNoRender();
  }

}

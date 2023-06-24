<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: groupMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_groupMenus {

  public function onMenuInitialize_GroupProfileAddPhoto() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $album = $subject->getSingletonAlbum();

    // Must be able to view groups
    if (!Engine_Api::_()->authorization()->isAllowed('group', $viewer, 'view')) {
      return false;
    }

    // Must be able to view groups
    if (!$subject->authorization()->isAllowed(null, 'photo')) {
      return false;
    }

    return array(
        'label' => 'Upload Photos',
        'data-icon' => 'picture',
        'route' => 'group_extended',
        'params' => array(
            'controller' => 'photo',
            'action' => 'upload',
            'subject' => $subject->getGuid()
        )
    );
  }

  public function onMenuInitialize_GroupTopicWatch() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $group = $subject->getParentGroup();
    $isWatching = null;

    $canPost = $group->authorization()->isAllowed($viewer, 'comment');

    if(!$canPost && !$viewer->getIdentity())
      return false;

		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
		$isWatching = $topicWatchesTable
						->select()
						->from($topicWatchesTable->info('name'), 'watch')
						->where('resource_id = ?', $group->getIdentity())
						->where('topic_id = ?', $subject->getIdentity())
						->where('user_id = ?', $viewer->getIdentity())
						->limit(1)
						->query()
						->fetchColumn(0)
		;

		if (false === $isWatching) {
			$isWatching = null;
		} else {
			$isWatching = (bool) $isWatching;
		}

    if(!$isWatching) {
      return array(
        'label' => 'Watch Topic',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'group',
            'controller' => 'topic',
            'action' => 'watch',
            'watch' => 1,
            'topic_id' => $subject->getIdentity()
        )
			);
    } else {
			return array(
        'label' => 'Stop Watching Topic',
        'route' => 'default',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'params' => array(
            'module' => 'group',
            'controller' => 'topic',
            'action' => 'watch',
            'watch' => 0
        )
			);
    }

	}

  public function onMenuInitialize_GroupTopicRename() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $group = $subject->getParentGroup();
    $canEdit = $group->authorization()->isAllowed($viewer, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

		return array(
			'label' => 'Rename',
			'route' => 'default',
      'class' => 'smoothbox ui-btn-default ui-btn-action',
			'params' => array(
					'module' => 'group',
					'controller' => 'topic',
					'action' => 'rename',
					'topic_id' => $subject->getIdentity()
			)
		);

	}

  public function onMenuInitialize_GroupTopicDelete() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $group = $subject->getParentGroup();
    $canEdit = $group->authorization()->isAllowed($viewer, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

		return array(
			'label' => 'Delete Topic',
			'route' => 'default',
      'class' => 'smoothbox ui-btn-default ui-btn-danger',
			'params' => array(
					'module' => 'group',
					'controller' => 'topic',
					'action' => 'delete',
					'topic_id' => $subject->getIdentity()
			)
		);

	}

  public function onMenuInitialize_GroupTopicOpen() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $group = $subject->getParentGroup();
    $canEdit = $group->authorization()->isAllowed($viewer, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

    if(!$subject->closed) {
			return array(
				'label' => 'Close',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'group',
						'controller' => 'topic',
						'action' => 'close',
						'topic_id' => $subject->getIdentity(),
            'closed'=> 1
				)
			);
    } else {
			return array(
				'label' => 'Open',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'group',
						'controller' => 'topic',
						'action' => 'close',
						'topic_id' => $subject->getIdentity(),
            'closed'=> 0
				)
			);
    }

	}

  public function onMenuInitialize_GroupTopicSticky() {

		$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $group = $subject->getParentGroup();
    $canEdit = $group->authorization()->isAllowed($viewer, 'edit');

    if(!$canEdit && !$viewer->getIdentity())
      return false;

    if(!$subject->sticky) {
			return array(
				'label' => 'Make Sticky',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'group',
						'controller' => 'topic',
						'action' => 'sticky',
						'topic_id' => $subject->getIdentity(),
            'sticky'=> 1
				)
			);
    } else {
			return array(
				'label' => 'Remove Sticky',
				'route' => 'default',
				'class' => 'smoothbox ui-btn-default ui-btn-action',
				'params' => array(
						'module' => 'group',
						'controller' => 'topic',
						'action' => 'sticky',
						'topic_id' => $subject->getIdentity(),
            'sticky'=> 0
				)
			);
    }

	}

  //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_GroupPhotoEdit($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if (!$subject->authorization()->isAllowed(null, 'edit') && ($this->subject->user_id != $viewer_id)) {
      return false;
    }

    return array(
        'label' => 'Edit',
        'route' => 'group_extended',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
           'controller' => 'photo',
           'action' => 'edit',
           'photo_id' => $subject->photo_id
        )
    );
  }

 //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_GroupPhotoDelete($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if (!$subject->authorization()->isAllowed(null, 'edit') && ($this->subject->user_id != $viewer_id)) {
      return false;
    }

    return array(
        'label' => 'Delete',
        'route' => 'group_extended',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
           'controller' => 'photo',
           'action' => 'delete',
           'photo_id' => $subject->photo_id
        )
    );
  }

  public function onMenuInitialize_GroupPhotoShare($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }
    return array(
        'label' => 'Share',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_GroupPhotoReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }
    return array(
        'label' => 'Report',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        )
    );
  }

  public function onMenuInitialize_GroupPhotoMakeProfilePhoto($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }

    return array(
        'label' => 'Make Profile Photo',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'route' => 'user_extended',
        'params' => array(
            'module' => 'user',
            'controller' => 'edit',
            'action' => 'external-photo',
            'photo' => $subject->getGuid(),
        )
    );
  }
  
  public function onMenuInitialize_GroupProfileMember()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'group' )
    {
      throw new Group_Model_Exception('Whoops, not a group!');
    }

    if( !$viewer->getIdentity() )
    {
      return false;
    }

    $row = $subject->membership()->getRow($viewer);
   //IF THE MODE IS APP THEN WE WILL NOT SHOW SOME LINK IN HEADER OF GROUP PROFILE:
   
    $return = true;
    if(Engine_Api::_()->sitemobile()->isApp())
      $return = false;
   $link = ''; 
    // Not yet associated at all
    if( null === $row )
    {
      if( $subject->membership()->isResourceApprovalRequired() ) {
        $link =  array(
          'label' => 'Request Membership',
          'icon' => 'application/modules/Group/externals/images/member/join.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'request',
            'group_id' => $subject->getIdentity(),
          ),
        );
      } else { 
        $link =  array(
          'label' => 'Join Group',
          'icon' => 'application/modules/Group/externals/images/member/join.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'join',
            'group_id' => $subject->getIdentity()
          ),
        );
      }
    }

    // Full member
    // @todo consider owner
    else if( $row->active )
    {
      if( !$subject->isOwner($viewer) ) {
        $link =  array(
          'label' => 'Leave Group',
          'icon' => 'application/modules/Group/externals/images/member/leave.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'leave',
            'group_id' => $subject->getIdentity()
          ),
        );
      } else {  
        $return = true;
        $link =  array(
          'label' => 'Delete Group',
          'icon' => 'application/modules/Group/externals/images/delete.png',
          'class' => 'smoothbox',
          'route' => 'group_specific',
          'params' => array(
            'action' => 'delete',
            'group_id' => $subject->getIdentity()
          ),
        );
      }
    }

    else if( !$row->resource_approved && $row->user_approved )
    {
      $link = array(
        'label' => 'Cancel Membership Request',
        'icon' => 'application/modules/Group/externals/images/member/cancel.png',
        'class' => 'smoothbox',
        'route' => 'group_extended',
        'params' => array(
          'controller' => 'member',
          'action' => 'cancel',
          'group_id' => $subject->getIdentity()
        ),
      );
    }

    else if( !$row->user_approved && $row->resource_approved )
    {
      $link =  array(
        array(
          'label' => 'Accept Membership Request',
          'icon' => 'application/modules/Group/externals/images/member/accept.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'accept',
            'group_id' => $subject->getIdentity()
          ),
        ), array(
          'label' => 'Ignore Membership Request',
          'icon' => 'application/modules/Group/externals/images/member/reject.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'reject',
            'group_id' => $subject->getIdentity()
          ),
        )
      );
    }

    else
    {
      throw new Group_Model_Exception('Wow, something really strange happened.');
    }

    if($return) return $link;
    return false;
  }
  
  public function onMenuInitialize_GroupProfileShare()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'group' )
    {
      throw new Group_Model_Exception('Whoops, not a group!');
    }

    if( !$viewer->getIdentity() )
    {
      return false;
    }
    if( Engine_Api::_()->sitemobile()->isApp())
      return false;
    return array(
      'label' => 'Share Group',
      'icon' => 'application/modules/Group/externals/images/share.png',
      'class' => 'smoothbox',
      'route' => 'default',
      'params' => array(
        'module' => 'activity',
        'controller' => 'index',
        'action' => 'share',
        'type' => $subject->getType(),
        'id' => $subject->getIdentity(),
        'format' => 'smoothbox',
      ),
    );
  }
  
  public function onMenuInitialize_GroupProfileInvite()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if( $subject->getType() !== 'group' ) {
      throw new Group_Model_Exception('Whoops, not a group!');
    }

    if( !$subject->authorization()->isAllowed($viewer, 'invite') ) {
      return false;
    }
   if( Engine_Api::_()->sitemobile()->isApp())
      return false;
    return array(
      'label' => 'Invite Members',
      'icon' => 'application/modules/Group/externals/images/member/invite.png',
      'class' => 'smoothbox',
      'route' => 'group_extended',
      'params' => array(
        //'module' => 'group',
        'controller' => 'member',
        'action' => 'invite',
        'group_id' => $subject->getIdentity(),
        'format' => 'smoothbox',
      ),
    );
  }




}
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Photo_Album extends Engine_Form {

  public function init() {

    $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id');
    $albumvalues['album_id'] = Engine_Api::_()->getItemTable('sitestore_album')->getDefaultAlbum($store_id)->album_id;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK

    if ($sitestore->owner_id == $viewer_id || $can_edit == 1) {
      $defaultalbum_id = 0;
    } else {
      $defaultalbum_id = $albumvalues['album_id'];
    }

    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $user = Engine_Api::_()->user()->getViewer();

    $this
            ->setTitle('Add New Photos')
            ->setDescription('Browse and choose photos on your system to add to this album.')
            ->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'albums_create')
            ->setAttrib('class', 'global_form sitestore_form_upload')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $this->addElement('Select', 'album', array(
        'label' => 'Choose Album',
        'multiOptions' => array('0' => 'Create A New Album'),
        'onchange' => "updateTextFields()",
    ));

    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['store_id'] = $store_id;
    $paramsAlbum['getSpecialField'] = 1;

    $all_albums = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($paramsAlbum);
    $album_options = Array();
    foreach ($all_albums as $all_album) {
      $album_options[$all_album->album_id] = htmlspecialchars_decode($all_album->getTitle());
    }

    $this->album->addMultiOptions($album_options);

    $this->addElement('Text', 'title', array(
        'label' => 'Album Title',
        'maxlength' => '256',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '256')),
        )
    ));

    $this->addElement('Hidden', 'store_id', array(
        'value' => $store_id,
        'order' => 333
    ));

    $this->addElement('Hidden', 'default_album_id', array(
        'value' => $defaultalbum_id,
        'order' => 334
    ));

    // Privacy
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1))
      $ownerTitle = "Store Admins";
    else
      $ownerTitle = "Just Me";

    $user = Engine_Api::_()->user()->getViewer();
    
//     $availableLabels = array(
//         'registered' => 'All Registered Members',
//         'owner_network' => 'Friends and Networks',
//         'owner_member_member' => 'Friends of Friends',
//         'owner_member' => 'Friends Only',
//         'owner' => $ownerTitle
//     );

    $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($user_level, 'sitestore_store', 'smecreate');
    $allowMemberInthisPackage = false;
    $allowMemberInthisPackage = Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoremember");
    $sitestoreMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
    
		$availableLabels = array(
			'registered' => 'All Registered Members',
			'owner_network' => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member' => 'Friends Only',
			'like_member' => 'Who Liked This Store',
		);
		if (!empty($sitestoreMemberEnabled) && $allowMemberInthisPackage) {
			$availableLabels['member'] = 'Store Members Only';
		} elseif( !empty($sitestoreMemberEnabled) && $allowMemberInLevel) {
			$availableLabels['member'] = 'Store Members Only';
		}
		$availableLabels['owner'] = $ownerTitle;
    
    
    
    $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_album', $user, 'auth_tag');

    $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));
    if (!empty($tagOptions) && count($tagOptions) >= 1) {
      if (count($tagOptions) == 1) {
        $this->addElement('hidden', 'auth_tag', array('value' => key($tagOptions)));
      } else {
        $this->addElement('Select', 'auth_tag', array(
            'label' => 'Tag Post Privacy',
            'description' => 'Who may tag photos in this album?',
            'multiOptions' => $tagOptions,
            'value' => key($tagOptions),
        ));
        $this->auth_tag->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Init search
    $this->addElement('Checkbox', 'search', array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Show this album in search results"),
        'value' => 1,
        'disableTranslator' => true
    ));

    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
            ->addDecorator('FormFancyUpload')
            ->addDecorator('viewScript', array(
                'viewScript' => '_FancyUpload.tpl',
                'placement' => '',
            ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Hidden', 'fancyuploadfileids');
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Photos',
        'type' => 'submit',
    ));
  }

  public function clearAlbum() {

    $this->getElement('album')->setValue(0);
  }

  public function saveValues() {

    $set_cover = false;
    $values = $this->getValues();
    $viewer = Engine_Api::_()->user()->getViewer();

    $params = Array();
    $getPackageAlbum = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestorealbum');
    if ((empty($values['owner_id']))) {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
    } else {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      throw new Zend_Exception("Non-user album owners not yet implemented");
    }
    if ((empty($values['album'])) && (empty($values['default_album_id']))) {
      $params['title'] = $values['title'];
      if (empty($params['title'])) {
        $params['title'] = "Untitled Album";
      }
      $params['search'] = $values['search'];

      $default_album_id = Engine_Api::_()->getItemTable('sitestore_album')->getDefaultAlbum($values['store_id'])->album_id;

      if (empty($default_album_id)) {
        $params['default_value'] = 1;
      } else {
        $params['default_value'] = 0;
      }

      $params['store_id'] = $values['store_id'];
      $album = Engine_Api::_()->getDbtable('albums', 'sitestore')->createRow();
      $album->setFromArray($params);
      $album->view_count = 1;
      $album->save();
      $set_cover = true;

      $auth = Engine_Api::_()->authorization()->context;
      //$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');

			$sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
			if (!empty($sitestorememberEnabled)) {
				$roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
			} else {
				$roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 	'registered', 'everyone');
			}

      if (empty($values['auth_tag'])) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = 'registered';
        }
      }

      $tagMax = array_search($values['auth_tag'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
      }

      //COMMENT PRIVACY
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
      }
    } else {
      if (!empty($values['album'])) {
        $album = Engine_Api::_()->getItem('sitestore_album', $values['album']);
      } else {
        $album = Engine_Api::_()->getItem('sitestore_album', $values['default_album_id']);
      }
    }
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $album->store_id, $layout);
    $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
   
    if (count($values['file'] > 0)) {
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $sendFB_Activity = 0;
      $activityFeedType = null;
      if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable()) {
        $activityFeedType = 'sitestorealbum_admin_photo_new';
        $sendFB_Activity = 1;
      } elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore)) {
        $activityFeedType = 'sitestorealbum_photo_new';
        $sendFB_Activity = 1;
      }

      if ($activityFeedType) {
        $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $sitestore, $activityFeedType, null, array('child_id' => $album->getIdentity(),'count' => count($values['file'])));
        Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
      }

      //STORE ALBUMS CREATE NOTIFICATION AND EMAIL WORK
			$sitestoreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitestore')->version;
			if ($sitestoreVersion >= '4.2.9p3') {
				Engine_Api::_()->sitestore()->sendNotificationEmail($album, $action, 'sitestorealbum_create', 'SITESTOREALBUM_CREATENOTIFICATION_EMAIL');
				
				$isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $album->store_id);
				if (!empty($isStoreAdmins)) {
					//NOTIFICATION FOR ALL FOLLWERS.
					Engine_Api::_()->sitestore()->sendNotificationToFollowers($album, $action, 'sitestorealbum_create');
				}
      }

      if ($sendFB_Activity == 1) {
        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {
          $facebooksefeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebooksefeed');
          $facebooksefeedmoduleversion = $facebooksefeedmodule->version;
          if ($facebooksefeedmoduleversion > '4.1.6p2') {
            $album_array = array();
            $album_array['type'] = 'sitestorealbum_photo_new';
            $album_array['object'] = $album;
            $album_array['file_ids'] = $this->getValues();
            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($album_array);
          }
        }
      }
      
    }

    $count = 0;
    foreach ($values['file'] as $photo_id) {
      $photo = Engine_Api::_()->getItem("sitestore_photo", $photo_id);
      if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
        continue;
      if ($set_cover) {
        $album->photo_id = $photo_id;
        $album->save();
        $set_cover = false;
      }
      $photo->album_id = $album->album_id;
      $photo->collection_id = $album->album_id;
      $photo->save();

      if ($action instanceof Activity_Model_Action && $count < 8) {
        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
      $count++;
    }
    return $album;
  }

}
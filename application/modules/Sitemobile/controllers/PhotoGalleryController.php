<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoGalleryController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemobile_PhotoGalleryController extends Core_Controller_Action_Standard {

  protected $_module_name;
  protected $_resource_type;

  public function init() {
    $albumPhotoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.phototype', null);
    $this->_module_name = $this->view->module_name = $this->_getParam('module_name');
    $resource_type = $this->_getParam('resource_type');
    $this->view->tab = $this->_getParam('tab');

    if (empty($resource_type)) { 
      if($this->_module_name == 'sitealbum'){
        $this->_module_name = 'album';
      }
      $this->_resource_type = $this->view->resource_type = $this->_module_name . "_photo";
    } else {
      $this->_resource_type = $this->view->resource_type = $this->_getParam('resource_type');
    }

//    if (empty($albumPhotoType))
//      return;
    //SETTING THE PHOTO SUBJECT
    if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
            null !== ($photo = Engine_Api::_()->getItem($this->_resource_type, $photo_id))) {
      Engine_Api::_()->core()->setSubject($photo);
    }
  }

  public function viewAction() { 

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    if ($this->_module_name == 'album' && !Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->isLessThan417AlbumModule()) {
      $this->view->album = $album = $photo->getAlbum();
    } else {
      $this->view->album = $album = $photo->getCollection();
    }
    $this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');

    $params = array();
    $viewPermission = $photo->authorization()->isAllowed($viewer, 'view');
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    $this->view->canComment = 0;
    $this->view->settings_optional_array = $settings_array = Engine_Api::_()->getApi('settings', 'sitemobile')->getSetting('sitemobile.lightbox.options', array('fullView', 'comments', 'tags', 'slideshow'));


//     if (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
// 
//     } elseif (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
//       $settings_array = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.lightbox.tablet.options', array('fullView', 'comments', 'tags', 'slideshow'));
//     }

    $this->view->canTag = 0;

    switch ($this->_module_name) {
      //GROUP PHOTOS IN THE LIGHTBOX
      case "advgroup":
      case "group":

        $viewPermission = $photo->getGroup()->authorization()->isAllowed($viewer, 'view');
        //GET COMMENT
        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        }
        if (in_array('tags', $settings_array)) {
          $this->view->canTag = $photo->canEdit(Engine_Api::_()->user()->getViewer());
        }
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        break;

      //ALBUM PHOTOS IN THE LIGHTBOX           
      case "album":
      case "advalbum":
      case "sitealbum":
        //CHECKING THE PRIVACY IF ALBUM HAVE PRIVACY THEN PHOTOS WILL BE SHO IN THE LIGHTBOX
        if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid()) {
          return;
        }
        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        }
        if (in_array('tags', $settings_array)) {
          $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag');
        }
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
            'album' => $album,
        ));

        $order = $photoTable->select()->from($photoTable->info('name'), 'order')->order('order DESC')->query()->fetchColumn();
        $photo_ids = $photoTable->select()->from($photoTable->info('name'), 'photo_id')->where('album_id =?', $album->getIdentity())->where("'order' =?", 0)->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        if ($photo_ids) {
          foreach ($photo_ids as $photo) {
            $photoTable->update(array('order' => ++$order), array('photo_id = ?' => $photo));
          }
        }
        break;

      //EVENT PHOTOS IN THE LIGHTBOX    
      case "ynevent":
      case "event":

        $viewPermission = $photo->getEvent()->authorization()->isAllowed($viewer, 'view');

        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        }
        if (in_array('tags', $settings_array)) {
          $this->view->canTag = $photo->canEdit(Engine_Api::_()->user()->getViewer());
        }
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        break;
      case "list":
        $viewPermission = $photo->authorization()->isAllowed(null, 'view');
        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        }
        break;
      //RECIPE PHOTOS IN THE LIGHTBOX  
      case "recipe":
        //CHECKING THE PRIVACY IF RECIPE HAVE PRIVACY THEN PHOTOS WILL BE SHOW IN THE LIGHTBOX
//         if (!$this->_helper->requireAuth()->setAuthParams('recipe', null, 'view')->isValid())
//           return;
        $viewPermission = $photo->authorization()->isAllowed(null, 'view');
        //GET TAG,UNTAG,EDIT PRIVACY
        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        }
        break;
      case "sitepage":
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $album->page_id);
        if (!empty($sitepage)) {
          $viewPermission = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
          //GET COMMENT PRIVACY
          if (in_array('comments', $settings_array)) {
            $this->view->canComment = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
          }
        }
        if (in_array('tags', $settings_array)) {
          $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag') || Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        }
        //Add Smoothbox 
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        break;
      //LISTING PHOTOS IN THE LIGHTBOX  
      case "sitepageevent":
        //GET NOTE ITEM
        $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $photo->event_id);
        //GET SITEPAGE ITEM
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);
        //GET MANAGE ADMIN
        $viewPermission = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');

        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
        }
        if (in_array('tags', $settings_array)) {
          $this->view->canTag = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        }
        //Add Smoothbox
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        break;
      //SITEPAGENOTE PHOTOS IN THE LIGHTBOX  
      case "sitepagenote":
        //GET NOTE ITEM
        $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $photo->note_id);
        //GET SITEPAGE ITEM
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);
        //GET MANAGE ADMIN
        $viewPermission = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');

        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
        }
        if (in_array('tags', $settings_array)) {
          $this->view->canTag = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        }
        //Add Smoothbox
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        break;
      //SITEPAGE PHOTOS IN THE LIGHTBOX    
      case "sitebusiness":

        $this->view->sitebusiness = $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $album->business_id);
        if (!empty($sitepage)) {
          $viewPermission = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'view');
          //GET COMMENT PRIVACY
          if (in_array('comments', $settings_array)) {
            $this->view->canComment = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'comment');
          }
          if (in_array('tags', $settings_array)) {
            $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag') || Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
          }
        }
        //Add Smoothbox 
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        break;
      //SITEPAGE PHOTOS IN THE LIGHTBOX    
      case "sitestore":

        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $album->store_id);
        if (!empty($sitepage)) {
          $viewPermission = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
          //GET COMMENT PRIVACY
          if (in_array('comments', $settings_array)) {
            $this->view->canComment = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'comment');
          }
          if (in_array('tags', $settings_array)) {
            $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag') || Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
          }
        }
        //Add Smoothbox 
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        break;
      case "sitebusinessevent":
        //GET NOTE ITEM
        $sitebusinessevent = Engine_Api::_()->getItem('sitebusinessevent_event', $photo->event_id);
        //GET SITEPAGE ITEM
        $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $sitebusinessevent->business_id);
        //GET MANAGE ADMIN
        $viewPermission = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'view');

        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'comment');
        }
        if (in_array('tags', $settings_array)) {
          $this->view->canTag = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
        }
        //Add Smoothbox
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        break;
      case "sitebusinessnote":
        //GET NOTE ITEM
        $sitebusinessnote = Engine_Api::_()->getItem('sitebusinessnote_note', $photo->note_id);
        //GET SITEBUSINESS ITEM
        $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $sitebusinessnote->business_id);
        //GET MANAGE ADMIN
        $viewPermission = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'view');

        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'comment');
        }

        if (in_array('tags', $settings_array)) {
          $this->view->canTag = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
        }
        //Add Smoothbox
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        break;
      case "sitegroup":

        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $album->group_id);
        if (!empty($sitegroup)) {
          $viewPermission = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
          //GET COMMENT PRIVACY
          if (in_array('comments', $settings_array)) {
            $this->view->canComment = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'comment');
          }

          if (in_array('tags', $settings_array)) {
            $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag') || Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
          }
        }
        //Add Smoothbox 
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        break;
      case "sitegroupevent":
        //GET NOTE ITEM
        $sitegroupevent = Engine_Api::_()->getItem('sitegroupevent_event', $photo->event_id);
        //GET SITEGROUP ITEM
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $sitegroupevent->group_id);
        //GET MANAGE ADMIN
        $viewPermission = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');

        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'comment');
        }
        if (in_array('tags', $settings_array)) {
          $this->view->canTag = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        }
        //Add Smoothbox
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        break;
      case "sitegroupnote":
        //GET NOTE ITEM
        $sitegroupnote = Engine_Api::_()->getItem('sitegroupnote_note', $photo->note_id);
        //GET SITEGROUP ITEM
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $sitegroupnote->group_id);
        //GET MANAGE ADMIN
        $viewPermission = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');

        //GET COMMENT PRIVACY
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'comment');
        }
        if (in_array('tags', $settings_array)) {
          $this->view->canTag = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        }
        //Add Smoothbox
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        break;
      //END MANAGE-ADMIN CHECK  
      case "sitereview":
        $listingtype_id = $this->_getParam('listingtype_id', null);
        Engine_Api::_()->sitereview()->setListingTypeInRegistry($listingtype_id);
        $this->_listingType = Zend_Registry::get('listingtypeArray' . $listingtype_id);
        $this->view->paginator = $album->getCollectiblesPaginator();

        if (in_array('tags', $settings_array)) {
          $this->view->canTag = $photo->authorization()->isAllowed(null, "edit_listtype_$listingtype_id") || $photo->user_id == $viewer_id;
        }
        
         if (in_array('comments', $settings_array)) {
          $this->view->canComment = $photo->authorization()->isAllowed($viewer, 'comment');
        }
        
        //GET LISTING TYPE ID
        $this->view->listingtype_id = $listingtype_id = $this->_listingType->listingtype_id;
        $viewPermission = $photo->authorization()->isAllowed(null, "view_listtype_$listingtype_id");

        break;
      case "sitestoreproduct":
        $this->view->paginator = $album->getCollectiblesPaginator();

        if (in_array('tags', $settings_array)) {
          $this->view->canTag = $photo->authorization()->isAllowed(null, "edit") || $photo->user_id == $viewer_id;
        }
        $viewPermission = $photo->authorization()->isAllowed(null, "view");
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        }
        break;
      case "siteevent":
        $this->view->paginator = $album->getCollectiblesPaginator();

        if (in_array('tags', $settings_array)) {
          $this->view->canTag = $photo->authorization()->isAllowed(null, "edit") || $photo->user_id == $viewer_id;
        }
        $viewPermission = $photo->authorization()->isAllowed(null, "view");
        if (in_array('comments', $settings_array)) {
          $this->view->canComment = $album->authorization()->isAllowed($viewer, 'comment');
        }
        break;
    }

    if ($this->view->paginator->getTotalItemCount() > 0) {
      $this->view->paginator->setItemCountPerPage(100000000);
    }
    
    $this->view->totalImageCount = $this->view->paginator->getTotalItemCount(); 
    $this->view->canRate = 0;
    $sitealbumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum')->enabled;
        if ($sitealbumModule && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) {
            if (!empty($viewer_id)) {
                $this->view->level_id = $viewer->level_id;
            } else {
                $this->view->level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            }

            $allowRating = Engine_Api::_()->authorization()->getPermission($this->view->level_id, 'album', 'rate');
            $this->view->canSeeRating = 1;
            if (!empty($viewer_id) && !empty($allowRating))
                $this->view->canRate = 1;
            else
                $this->view->canRate = 0;
        } else {
            $this->view->canSeeRating = 0;
        }
      

    $this->view->slideshow = 0;
    if (in_array('slideshow', $settings_array)) {
      $this->view->slideshow = 1;
    }
    $this->view->fullView = 0;
    if (in_array('fullView', $settings_array)) {
      $this->view->fullView = 1;
    }
     if (Engine_API::_()->sitemobile()->isApp()) {
    $this->view->fullView = 0;

    $this->view->photoDescription = 0;

    if (Engine_Api::_()->getApi('settings', 'sitemobile')->getSetting('sitemobile.lightbox.caption')) {
      $this->view->photoDescription = 1;
    }
    
    $this->view->canShare = 0;
    if (in_array('share', $settings_array) && !empty($viewer_id)) {
      $this->view->canShare = 1;
    }
     $this->view->socialShare = 0;
    if (in_array('socialShare', $settings_array)) {
      $this->view->socialShare = 1;
    }
     $this->view->savePhoto = 0;
    if (in_array('savePhoto', $settings_array)) {
      $this->view->savePhoto = 1;
    }
    $this->view->viewAlbum = 0;
    if (in_array('viewAlbum', $settings_array)) {
      $this->view->viewAlbum = 1;
    }
    $this->view->reportPhoto = 0;
     if (in_array('reportPhoto', $settings_array) && !empty($viewer_id)) {
      $this->view->reportPhoto = 1;
    }
     }
    if (empty($viewPermission)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
  }

  public function suggestAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->clear_cache = true;
    if (!$viewer->getIdentity()) {
      $data = null;
    } else {
      $data = array();
      $table = Engine_Api::_()->getItemTable('user');
      $select = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();

      if ($this->_getParam('includeSelf', false)) {
        $data[] = array(
            'type' => 'user',
            'id' => $viewer->getIdentity(),
            'guid' => $viewer->getGuid(),
            'label' => $viewer->getTitle() . ' ' . $this->view->translate('(you)'),
            'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
            'url' => $viewer->getHref(),
        );
      }

      if (0 < ($limit = (int) $this->_getParam('limit', 10))) {
        $select->limit($limit);
      }

      if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
        $select->where('`' . $table->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
      }

      $ids = array();
      foreach ($select->getTable()->fetchAll($select) as $friend) {
        $data[] = array(
            'type' => 'user',
            'id' => $friend->getIdentity(),
            'guid' => $friend->getGuid(),
            'label' => $friend->getTitle(),
            'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
            'url' => $friend->getHref(),
        );
        $ids[] = $friend->getIdentity();
        //  $friend_data[$friend->getIdentity()] = $friend->getTitle();
      }
      $onlylist = $this->_getParam('onlylist', false);
      if (empty($onlylist)) {
        $friendList = '<ul data-role="listview" data-filter="true" class="tags-list">
      <li data-ajax="true" contenteditable="true" placeholder="' . Zend_Registry::get('Zend_Translate')->_('Start typing a name...') . '" class="text"></li>';
      } else {
        $friendList = '';
      }
      foreach ($data as $list) {
        $friendList .='<li class="friend selected" data-title="' . $list['label'] . '" data-user_id="' . $list['id'] . '">' . $list['label'] . '</li>';
      }
      if (empty($onlylist)) {
        $friendList .="</ul>";
      }
      $data['friendList'] = $friendList;
    }
    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
    //  $this->getResponse()->setBody($data);
  }
  
  public function rateAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    $allowRating = Engine_Api::_()->authorization()->getPermission($level_id, 'album', 'rate');
    if (empty($viewer_id) || empty($allowRating))
      return;

    $rating = $this->_getParam('rating');
    $subject_id = $this->_getParam('subject_id');
    $subject_type = $this->_getParam('subject_type');

    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitealbum');

    $db = $ratingTable->getAdapter();
    $db->beginTransaction();

    try {
      $ratingTable->setRating($subject_id, $subject_type, $rating);

      $subject = Engine_Api::_()->getItem($subject_type, $subject_id);
      $subject->rating = $ratingTable->getRating($subject_id, $subject_type);
      $subject->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $total = $ratingTable->ratingCount(array('resource_id' => $subject_id, 'resource_type' => $subject_type));

    $data = array();
    $data[] = array(
        'total' => $total,
        'rating' => $rating,
    );
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }


}
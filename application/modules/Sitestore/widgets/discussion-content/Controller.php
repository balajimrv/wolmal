<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_DiscussionContentController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE STORES
  public function indexAction() { 	
  	
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject('sitestore_topic')) {
      return $this->setNoRender();
    }

    //SEND THE VIEWER TO THE TPL   
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //SEND THE TOPIC SUBJECT TO THE TPL
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET THE SITESTORE ITEM 
    $this->view->sitestore = $sitestore = $topic->getParentSitestore();

    //SEND THE TAB ID TO THE TPL
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //GET COMMENT PRIVACY
    $this->view->canPost = $canPost = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdicreate');

    //GET EDIT PRIVACY
    $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

    //INCREMENT IN VIEWS
    if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
      $topic->view_count = new Zend_Db_Expr('view_count + 1');
      $topic->save();
    }

    //CHECK WAITING
    $isWatching = null;
    if ($viewer->getIdentity()) {
      $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitestore');
      $isWatching = $topicWatchesTable->isWatching($sitestore->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());
      if (false === $isWatching) {
        $isWatching = null;
      } else {
        $isWatching = (bool) $isWatching;
      }
    }
    $this->view->isWatching = $isWatching;

    //@TODO IMPLEMENT SCAN TO POST
    $this->view->post_id = $post_id = (int) $this->_getParam('post');

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('posts', 'sitestore')->getPost($sitestore->getIdentity(), $topic->getIdentity());
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber(Zend_Controller_Front::getInstance()->getRequest()->getParam('page'));    

//    //SKIP TO STORE OF SPECIFIED POST
//    if (0 !== ($post_id = (int) $this->_getParam('post_id')) &&
//            null !== ($post = Engine_Api::_()->getItem('sitestore_post', $post_id))) {
//      $icpp = $paginator->getItemCountPerPage();
//      $store = ceil(($post->getPostIndex() + 1) / $icpp);
//      $paginator->setCurrentPageNumber($store);
//    }
//
//    //USE SPECIFIED STORE
//    else if (0 !== ($store = (int) $this->_getParam('store'))) {
//      $paginator->setCurrentPageNumber($this->_getParam('store'));
//    }

    if ($canPost && !$topic->closed) {
      $this->view->form = $form = new Sitestore_Form_Post_Create();
      $form->populate(array(
          'topic_id' => $topic->getIdentity(),
          'ref' => $topic->getHref(),
          'watch' => ( false === $isWatching ? '0' : '1' ),
      ));
    }
  }

}
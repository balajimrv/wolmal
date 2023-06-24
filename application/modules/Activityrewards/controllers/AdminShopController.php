<?php

class Activityrewards_AdminShopController extends Core_Controller_Action_Admin
{
  
  public function indexAction()
  {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activityrewards_admin_main_shop');
      
    $r = $this->getRequest();
    $task = $r->get('task','');
    $item_id = $r->get('item_id', 0);
    
    if($task == "addnew") {
      $offer_type_id = intval($r->get('offer_type'));
      $offer_name = Engine_Api::_()->getDbTable('spendertype','activitypoints')->getTypename($offer_type_id);
      if($offer_name) {
        return $this->_helper->_redirector->gotoRoute(array('controller' => $offer_name), 'admin_default');
      }
    }
    
    
    if($task == "edit") {
    
      $upspender = Engine_Api::_()->getDbTable('spender','activityrewards')->fetchRow(array("userpointspender_id=?"=>$item_id));

      if($upspender) {
        $offer_name = $upspender->userpointspendertype_name;
        return $this->_helper->_redirector->gotoRoute(array('controller' => $offer_name, 'item_id'  => $item_id), 'admin_default');
      }
    
    }
    
    
    if($task == "delete") {

      $upspender = Engine_Api::_()->getDbTable('spender','activityrewards')->fetchRow(array("userpointspender_id=?"=>$item_id));
      if($upspender) {
        $upspender->delete();
      }
      
      return $this->_helper->_redirector->gotoRoute(array('controller' => 'shop'), 'admin_default');
    }
    
    
    if($task == "enable") {

      $enable = (int)$r->get('enable',0);
      $upspender = Engine_Api::_()->getDbTable('spender','activityrewards')->fetchRow(array("userpointspender_id=?"=>$item_id));
      
      if($upspender) {
        $upspender->userpointspender_enabled = $enable;
        $upspender->save();
      }
      return $this->_helper->_redirector->gotoRoute(array('controller' => 'shop'), 'admin_default');
    }
    

    $page=$this->_getParam('page',1);
    $this->view->paginator = Engine_Api::_()->activityrewards()->getItemsPaginator(array(
      'orderby' => 'userpointspender_id',
      'type'  => 'spender'
    ));
    $this->view->paginator->setItemCountPerPage(25);
    $this->view->paginator->setCurrentPageNumber($page);
    
    $this->view->offer_types = Engine_Api::_()->getDbTable('spendertype','activitypoints')->fetchAll("userpointspendertype_type >= 100");
    
  }

  
  public function editAction() {

    $this->_helper->requireAdmin()->isValid();

    $viewer = Engine_Api::_()->user()->getViewer();

    $r = $this->getRequest();
    
    $item_id = $r->get('item_id', 0);

    $newitem = $r->get('newitem', 0);
    $offer_type = $r->get('offer_type', 0);
    
    if(!$newitem) {

      $upspender = Engine_Api::_()->getDbTable('spender','activityrewards')->fetchRow(array("userpointspender_id=?"=>$item_id));
  
      if(!$upspender) {
        return $this->_helper->_redirector->gotoRoute(array('controller' => 'shop'), 'admin_default');
      }
  
      $offer_name = $upspender->getItemType();
      
    } else {

      $offer_name = Engine_Api::_()->getDbTable('spendertype','activityrewards')->getTypename($offer_type);

      if(!$offer_name) {
        return $this->_helper->_redirector->gotoRoute(array('controller' => 'shop'), 'admin_default');
      }
      
    }
    
    if(!$newitem) {

      $this->setupNavigation('activityrewards_admin_edit_item', array('item_id' => $item_id) );
      
    }

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    $spenderType = Engine_Api::_()->getDbTable('spendertype','activityrewards')->fetchRow(array("userpointspendertype_typename = ?" => $offer_name));

    if($spenderType && !empty($spenderType->form)) {
      
      $form_class = $spenderType->form;
      
    } else {

      $form_class = 'Activityrewards_Form_Admin_Spender_'.ucwords($offer_name);
      
    }

    $this->view->form = $form = new $form_class();

    $form->offer_type->setValue($offer_type);
    $form->newitem->setValue($newitem);
    

    // set values
    if(!$newitem) {
      $form->setItem($upspender);
    }
    
    if(!$this->getRequest()->isPost())
    {
      
      if(!$newitem) {

        // prepare tags
        $tags = $upspender->tags()->getTagMaps();
        
        $tagString = '';
        foreach( $tags as $tagmap )
        {
          if( $tagString !== '' ) $tagString .= ', ';
          $tagString .= $tagmap->getTag()->getTitle();
        }
  
        $this->view->tagNamePrepared = $tagString;
        $form->tags->setValue($tagString);
        
      }
      
      $item_levels = $newitem ? array() : (empty($upspender->userpointspender_levels) ? array() : explode(',',$upspender->userpointspender_levels));
      
      $rows = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
      $levels = array();
      
      foreach( $rows as $row ) {
        if(empty($item_levels) || in_array($row->level_id, $item_levels)) {
          $levels[] = $row->level_id;
        }
      }
      
      $form->levels->setValue($levels);
      
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }
    



    // process 
    
    $values = $form->getValues();
    
    $tags = preg_split('/[,]+/', $values['tags']);
    $tags = array_filter(array_map("trim", $tags));
    
    $levels = $values['levels'];
    $rows = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    foreach( $rows as $row ) {
      $levels_array[] = $row->level_id;
    }
    
    if(empty($levels) || (array_diff($levels_array, $levels)) == array()) {
      $levels = '';
    }
    
    $upspender->userpointspender_levels = !empty($levels) ? implode(',',$levels) : '';
    
    if($newitem) {
      $upspender = Engine_Api::_()->getDbTable('spender','activityrewards')->createRow();
      $upspender->userpointspender_type = Engine_Api::_()->getDbTable('spendertype','activityrewards')->getType($offer_type);;
      $upspender->userpointspender_name = $offer_name;
      $upspender->owner_id = $viewer->getIdentity();
      $upspender->userpointspender_date = time();
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {

      $form->save($upspender);
      $upspender->save();
      
      if($newitem) {
        $upspender->tags()->addTagMaps($viewer, $tags);
      } else {
        $upspender->tags()->setTagMaps($viewer, $tags);
      }
      
      if($upspender->userpointspender_max_acts > 0) {
        Engine_Api::_()->getDbTable('actionpoints','activitypoints')
        ->updateActionPoints('upspender_' . $upspender->userpointspender_id,
                             array('points'         => $upspender->userpointspender_cost,
                                   'pointsmax'      => $upspender->userpointspender_cost * $upspender->userpointspender_max_acts,
                                   'rolloverperiod' => $upspender->userpointspender_rolloverperiod * 86400, // in seconds
                                   'group'          => -1 // non displayable
                                   )
                            );
      } else {
        Engine_Api::_()->getDbTable('actionpoints','activitypoints')
        ->removeAction('upspender_' . $upspender->userpointspender_id);
      }


      // AUTH 
      $auth = Engine_Api::_()->authorization()->context;
      
      $auth->setAllowed($upspender, 'everyone', 'comment', ($values['allow_comments'] == 1));

      $db->commit();


      return $this->_redirect('admin/activityrewards/shop');
      
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    

  }


  public function editphotoAction() {

    $r = $this->getRequest();
    
    $item_id = $r->get('item_id', 0);
    
    $upspender = Engine_Api::_()->getDbTable('spender','activityrewards')->fetchRow(array("userpointspender_id=?"=>$item_id));

    if(!$upspender) {
      return $this->_helper->_redirector->gotoRoute(array('controller' => 'shop'), 'admin_default');
    }

    $task = $r->get('task','');
    
    if($task == "remove") {
      $upspender->removePhoto();
      return $this->_helper->_redirector->gotoRoute(array('module'  => 'activityrewards', 'controller' => 'shop', 'action' => 'editphoto', 'item_id' => $item_id), 'admin_default', true);
    }

    $this->setupNavigation('activityrewards_admin_edit_photo', array('item_id' => $item_id) );
    
    $this->view->form = $form = new Activityrewards_Form_Admin_ItemPhoto();
    
    $this->view->item = $upspender;
    
    $form->item_id->setValue($upspender->userpointspender_id);
    
    if(!$this->getRequest()->isPost())
    {
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {
      
      $values = $form->getValues();

      if( !empty($values['photo']) ) {
        $upspender->setPhoto($form->photo);
      }

      $db->commit();
      
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    

  }



   public function getNavigation($activeItem, $params = array()) {

    $links = array(
                array(
                      'label'      => 'Edit Offer',
                      'route'      => 'admin_default',
                      'action'     => 'edit',
                      'controller' => 'shop',
                      'module'     => 'activityrewards',
                      'active'    => $activeItem == 'activityrewards_admin_edit_item' ? true : false,
                      'params'    => $params,
                    ),
                array(
                      'label'      => 'Edit Photo',
                      'route'      => 'admin_default',
                      'action'     => 'editphoto',
                      'controller' => 'shop',
                      'module'     => 'activityrewards',
                      'active'    => $activeItem == 'activityrewards_admin_edit_photo' ? true : false,
                      'params'    => $params,
                    ),
                );

    return $links;

  }


  public function setupNavigation($activeItem, $params = array()) {

    $links = $this->getNavigation($activeItem, $params);

    $this->view->navigation = new Zend_Navigation();
    $this->view->navigation->addPages($links);


  }

}
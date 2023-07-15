<?php

class Activityrewards_AdminOffersController extends Core_Controller_Action_Admin
{
  
  public function indexAction()
  {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activityrewards_admin_main_offers');
      
    $r = $this->getRequest();
    $task = $r->get('task','');
    $item_id = $r->get('item_id', 0);
    
    if($task == "addnew") {
      $offer_type_id = intval($r->get('offer_type'));
      $offer_name = Engine_Api::_()->getDbTable('earnertype','activitypoints')->getTypename($offer_type_id);
      if($offer_name) {
        return $this->_helper->_redirector->gotoRoute(array('controller' => $offer_name), 'admin_default');
      }
    }
    
    
    if($task == "edit") {
    
      $upearner = Engine_Api::_()->getDbTable('earner','activityrewards')->fetchRow(array("userpointearner_id=?"=>$item_id));

      if($upearner) {
        $offer_name = $upearner->userpointearnertype_name;
        return $this->_helper->_redirector->gotoRoute(array('controller' => $offer_name, 'item_id'  => $item_id), 'admin_default');
      }
    
    }
    
    
    if($task == "delete") {

      $upearner = Engine_Api::_()->getDbTable('earner','activityrewards')->fetchRow(array("userpointearner_id=?"=>$item_id));
      if($upearner) {
        $upearner->delete();
      }
      
      return $this->_helper->_redirector->gotoRoute(array('controller' => 'offers'), 'admin_default');
    }
    
    
    if($task == "enable") {

      $enable = (int)$r->get('enable',0);
      $upearner = Engine_Api::_()->getDbTable('earner','activityrewards')->fetchRow(array("userpointearner_id=?"=>$item_id));
      
      if($upearner) {
        $upearner->userpointearner_enabled = $enable;
        $upearner->save();
      }
      return $this->_helper->_redirector->gotoRoute(array('controller' => 'offers'), 'admin_default');
    }
    

    $page=$this->_getParam('page',1);
    $this->view->paginator = Engine_Api::_()->activityrewards()->getItemsPaginator(array(
      'orderby' => 'userpointearner_id',
      'type'  => 'earner'
    ));
    $this->view->paginator->setItemCountPerPage(25);
    $this->view->paginator->setCurrentPageNumber($page);
    
    $this->view->offer_types = Engine_Api::_()->getDbTable('earnertype','activitypoints')->fetchAll();
    
  }

  
  public function editAction() {

    $this->_helper->requireAdmin()->isValid();

    $viewer = Engine_Api::_()->user()->getViewer();

    $r = $this->getRequest();
    
    $item_id = $r->get('item_id', 0);

    $newitem = $r->get('newitem', 0);
    $offer_type = $r->get('offer_type', 0);
    
    if(!$newitem) {

      $upearner = Engine_Api::_()->getDbTable('earner','activityrewards')->fetchRow(array("userpointearner_id=?"=>$item_id));
  
      if(!$upearner) {
        return $this->_helper->_redirector->gotoRoute(array('controller' => 'offers'), 'admin_default');
      }
  
      $offer_name = $upearner->getItemType();
      
    } else {

      $offer_name = Engine_Api::_()->getDbTable('earnertype','activityrewards')->getTypename($offer_type);

      if(!$offer_name) {
        return $this->_helper->_redirector->gotoRoute(array('controller' => 'offers'), 'admin_default');
      }
      
    }
    
    if(!$newitem) {

      $this->setupNavigation('activityrewards_admin_edit_item', array('item_id' => $item_id) );
      
    }

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    $earnerType = Engine_Api::_()->getDbTable('earnertype','activityrewards')->fetchRow(array("userpointearnertype_typename = ?" => $offer_name));

    if($earnerType && !empty($earnerType->form)) {
      
      $form_class = $earnerType->form;
      
    } else {

      $form_class = 'Activityrewards_Form_Admin_Offer_'.ucwords($offer_name);
      
    }

    $this->view->form = $form = new $form_class();

    $form->offer_type->setValue($offer_type);
    $form->newitem->setValue($newitem);
    

    // set values
    if(!$newitem) {
      $form->setItem($upearner);
    }
    
    if(!$this->getRequest()->isPost())
    {
      
      if(!$newitem) {

        // prepare tags
        $tags = $upearner->tags()->getTagMaps();
        
        $tagString = '';
        foreach( $tags as $tagmap )
        {
          if( $tagString !== '' ) $tagString .= ', ';
          $tagString .= $tagmap->getTag()->getTitle();
        }
  
        $this->view->tagNamePrepared = $tagString;
        $form->tags->setValue($tagString);
        
      }
      
      $item_levels = $newitem ? array() : (empty($upearner->userpointearner_levels) ? array() : explode(',',$upearner->userpointearner_levels));
      
      $rows = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll($level_tbl->select()->order('level_order ASC'));
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
	$level_tbl = Engine_Api::_()->getDbtable('levels', 'authorization');
	$rows = $level_tbl->fetchAll($level_tbl->select()->order('level_order ASC'));
	
    foreach( $rows as $row ) {
      $levels_array[] = $row->level_id;
    }
    
    if(empty($levels) || (array_diff($levels_array, $levels)) == array()) {
      $levels = '';
    }
    
    $upearner->userpointearner_levels = !empty($levels) ? implode(',',$levels) : '';
    
    if($newitem) {
      $upearner = Engine_Api::_()->getDbTable('earner','activityrewards')->createRow();
      $upearner->userpointearner_type = Engine_Api::_()->getDbTable('earnertype','activityrewards')->getType($offer_type);;
      $upearner->userpointearner_name = $offer_name;
      $upearner->owner_id = $viewer->getIdentity();
      $upearner->userpointearner_date = time();
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {

      $form->save($upearner);
      $upearner->save();
      
      if($newitem) {
        $upearner->tags()->addTagMaps($viewer, $tags);
      } else {
        $upearner->tags()->setTagMaps($viewer, $tags);
      }
      
      if($upearner->userpointearner_max_acts > 0) {
        Engine_Api::_()->getDbTable('actionpoints','activitypoints')
        ->updateActionPoints('upearner_' . $upearner->userpointearner_id,
                             array('points'         => $upearner->userpointearner_cost,
                                   'pointsmax'      => $upearner->userpointearner_cost * $upearner->userpointearner_max_acts,
                                   'rolloverperiod' => $upearner->userpointearner_rolloverperiod * 86400, // in seconds
                                   'group'          => -1 // non displayable
                                   )
                            );
      } else {
        Engine_Api::_()->getDbTable('actionpoints','activitypoints')
        ->removeAction('upearner_' . $upearner->userpointearner_id);
      }


      // AUTH 
      $auth = Engine_Api::_()->authorization()->context;
      
      $auth->setAllowed($upearner, 'everyone', 'comment', ($values['allow_comments'] == 1));

      $db->commit();


      return $this->_redirect('admin/activityrewards/offers');
      
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
    
    $upearner = Engine_Api::_()->getDbTable('earner','activityrewards')->fetchRow(array("userpointearner_id=?"=>$item_id));

    if(!$upearner) {
      return $this->_helper->_redirector->gotoRoute(array('controller' => 'offers'), 'admin_default');
    }

    $task = $r->get('task','');
    
    if($task == "remove") {
      $upearner->removePhoto();
      return $this->_helper->_redirector->gotoRoute(array('module'  => 'activityrewards', 'controller' => 'offers', 'action' => 'editphoto', 'item_id' => $item_id), 'admin_default', true);
    }

    $this->setupNavigation('activityrewards_admin_edit_photo', array('item_id' => $item_id) );
    
    $this->view->form = $form = new Activityrewards_Form_Admin_ItemPhoto();
    
    $this->view->item = $upearner;
    
    $form->item_id->setValue($upearner->userpointearner_id);
    
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
        $upearner->setPhoto($form->photo);
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
                      'controller' => 'offers',
                      'module'     => 'activityrewards',
                      'active'    => $activeItem == 'activityrewards_admin_edit_item' ? true : false,
                      'params'    => $params,
                    ),
                array(
                      'label'      => 'Edit Photo',
                      'route'      => 'admin_default',
                      'action'     => 'editphoto',
                      'controller' => 'offers',
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
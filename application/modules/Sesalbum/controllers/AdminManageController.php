<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManageController.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction() {
    $this->view->navigation  = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sesalbum_admin_main', array(), 'sesalbum_admin_main_manage');
		$this->view->formFilter = $formFilter = new Sesalbum_Form_Admin_Manage_Filter();
		$this->view->category_id=isset($_GET['category_id']) ?  $_GET['category_id'] : 0;
		$this->view->subcat_id=isset($_GET['subcat_id']) ?  $_GET['subcat_id'] : 0;
		$this->view->subsubcat_id=isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : 0;
		// Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    foreach( $_GET as $key => $value ) {
      if( '' === $value ) {
        unset($_GET[$key]);
      }else
				$values[$key]=$value;
    }
    if( $this->getRequest()->isPost() ) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $album = Engine_Api::_()->getItem('album', $value);
          $album->delete();
        }
      }
    }
		$tableAlbum = Engine_Api::_()->getDbtable('albums', 'sesalbum');
		$tableAlbumName = $tableAlbum->info('name');
		$tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
    $select = $tableAlbum->select()
													->from($tableAlbumName)
												 ->setIntegrityCheck(false)
												 ->joinLeft($tableUserName, "$tableUserName.user_id = $tableAlbumName.owner_id", 'username');
		$select->order('album_id DESC'); 
		// Set up select info
		if( isset($_GET['category_id']) && $_GET['category_id'] != 0)
      $select->where('category_id = ?', $values['category_id'] );
    
		if( isset($_GET['subcat_id']) && $_GET['subcat_id'] != 0) 
      $select->where('subcat_id = ?',  $values['subcat_id']);
    
		if( isset($_GET['subsubcat_id']) && $_GET['subsubcat_id'] != 0) 
      $select->where('subsubcat_id = ?', $values['subsubcat_id']);
    
    if( !empty($_GET['title']) ) 
      $select->where('title LIKE ?', '%' . $values['title'] . '%');
    
    if( isset($_GET['is_featured']) && $_GET['is_featured'] != '') 
      $select->where('is_featured = ?', $values['is_featured']);
    
    if( isset($_GET['is_sponsored']) && $_GET['is_sponsored'] != '') 
      $select->where('is_sponsored = ?', $values['is_sponsored'] );
    
    if( !empty($values['creation_date']) ) 
      $select->where('date(creation_date) = ?', $values['creation_date'] );
    
		 if( isset($_GET['location']) && $_GET['location'] != '')
      $select->where('location != ?', '' );
		
		if (!empty($_GET['owner_name']))			
      $select->where($tableUserName . '.displayname LIKE ?', '%' . $_GET['owner_name'] . '%');
		
		if( isset($_GET['offtheday']) && $_GET['offtheday'] != '')
			$select->where($tableAlbumName.'.offtheday =?',$values['offtheday']);	
		
   	if ( isset($_GET['rating']) && $_GET['rating'] != '') {
      if ($_GET['rating'] == 1):
        $select->where('rating != ?', 0);
      elseif ($_GET['rating'] == 0 && $_GET['rating'] != ''):
        $select->where('rating =?', 0);
      endif;
    }
    $page = $this->_getParam('page', 1);
		
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber( $page );
  }
  public function viewAction() {
    $this->view->type = $type = $this->_getParam('type', 1);
    $id = $this->_getParam('id', 1);
    if($type == 'album')
      $item = Engine_Api::_()->getItem('album', $id);
    else
      $item = Engine_Api::_()->getItem('album_photo', $id);
    $this->view->item = $item;
  }
	public function ofthedayAction() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $type = $this->_getParam('type');
    $param = $this->_getParam('param');
    $this->view->form = $form = new Sesalbum_Form_Admin_Oftheday();
    if ($type == 'album') {
      $item = Engine_Api::_()->getItem('album', $id);
      $form->setTitle("Album of the Day");
      $form->setDescription('Here, choose the start date and end date for this  album to be displayed as "Album of the Day".');
      if (!$param)
        $form->remove->setLabel("Remove as  Album of the Day");
      $table = 'engine4_album_albums';
      $item_id = 'album_id';
    } elseif ($type == 'album_photo') {
      $item = Engine_Api::_()->getItem('album_photo', $id);
      $form->setTitle("Photo of the Day");
      if (!$param)
        $form->remove->setLabel("Remove as Photo of the Day");
      $form->setDescription('Here, choose the start date and end date for this photo to be displayed as "Photo of the Day".');
      $table = 'engine4_album_photos';
      $item_id = 'photo_id';
    }
    if (!empty($id))
      $form->populate($item->toArray());
    if ($this->getRequest()->isPost()) {
      if (!$form->isValid($this->getRequest()->getPost())) 
        return;
      $values = $form->getValues();
      $values['starttime'] = date('Y-m-d',  strtotime($values['starttime']));
      $values['endtime'] = date('Y-m-d', strtotime($values['endtime']));
      $db->update($table, array('starttime' => $values['starttime'], 'endtime' => $values['endtime']), array("$item_id = ?" => $id));
      if ($values['remove']) {
        $db->update($table, array('offtheday' => 0), array("$item_id = ?" => $id));
      } else {
        $db->update($table, array('offtheday' => 1), array("$item_id = ?" => $id));
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Successfully updated the item.')
      ));
    }
  }

	public function photosAction() {
    $this->view->navigation  = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sesalbum_admin_main', array(), 'sesalbum_admin_main_photos');
    
		$this->view->formFilter = $formFilter = new Sesalbum_Form_Admin_Manage_Filter(array('albumTitle' =>'yes'));
		// Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    foreach( $_GET as $key => $value ) {
      if( '' === $value ) {
        unset($_GET[$key]);
      }else
				$values[$key]=$value;
    }
		if( $this->getRequest()->isPost() ) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $photo = Engine_Api::_()->getItem('photo', $value);
          $photo->delete();
        }
      }
    }
				
		$tablePhoto = Engine_Api::_()->getDbtable('photos', 'sesalbum');
		$tablePhotoName = $tablePhoto->info('name');
		$tableAlbum = Engine_Api::_()->getDbtable('albums', 'sesalbum');
		$tableAlbumName = $tableAlbum->info('name');
		$tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
    $select =  $tablePhoto->select()
													->from($tablePhotoName)
													->setIntegrityCheck(false)
													->joinLeft($tableAlbumName, "$tableAlbumName.album_id = $tablePhotoName.album_id",NULL)
													->joinLeft($tableUserName, "$tableUserName.user_id = $tablePhotoName.owner_id", 'username');
												 
		 // Set up select info
    if( !empty($values['title']) ) 
      $select->where($tablePhotoName.'.title LIKE ?',$values['title'] );
    if( !empty($values['album_title']) ) 
      $select->where($tableAlbumName.'.title LIKE ?', $values['album_title']);
    
		if( isset($_GET['is_featured']) && $_GET['is_featured'] != '') 
      $select->where($tablePhotoName.'.is_featured  =?',  $values['is_featured'] );
    
    if( isset($_GET['is_sponsored']) && $_GET['is_sponsored'] != '') 
      $select->where($tablePhotoName.'.is_sponsored  =?',  $values['is_sponsored'] );
    
    if( !empty($values['creation_date']) ) 
      $select->where($tablePhotoName.'.date(creation_date) = ?', $values['creation_date'] );
    
   	 if( isset($_GET['location']) && $_GET['location'] != '') 
      $select->where($tablePhotoName.'.location = ?', $values['location'] );
    
		if( isset($_GET['offtheday']) && $_GET['offtheday'] != '')
			$select->where($tablePhotoName.'.offtheday =?',$values['offtheday']);	
		
		if (!empty($values['owner_name']))			
      $select->where($tableUserName . '.displayname LIKE ?', '%' . $_GET['owner_name'] . '%');
   	if ( isset($_GET['rating']) && $_GET['rating'] != '') {
      if ($_GET['rating'] == 1):
        $select->where($tablePhotoName.'.rating <> ?', 0);
      elseif ($_GET['rating'] == 0 && $_GET['rating'] != ''):
        $select->where($tablePhotoName.'.rating = ?', $_GET['rating']);
      endif;
    }
		$select->where($tableAlbumName.'.album_id != ?',0);
		$select->where($tablePhotoName.'.album_id != ?',0);
		$select->order($tablePhotoName.'.photo_id DESC');
		
    $page = $this->_getParam('page', 1);
		// Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber( $page );
  }
	public function featureSponsoredAction(){
		$this->view->album_id = $id = $this->_getParam('id');
		$this->view->status = $status = $this->_getParam('status');
		$this->view->category = $category = $this->_getParam('category');
		$this->view->params = $params = $this->_getParam('param');
		if($status == 1)
			$statusChange = ' '.$category;
		else
			$statusChange = 'un'.$category;
		//$this->view->statusChange = $statusChange;
		 // Check post
    //if( $this->getRequest()->isPost())
   // {
			if($params == 'photos')
				$col = 'photo_id';
			else
				$col = 'album_id';
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
				Engine_Api::_()->getDbtable($params, 'sesalbum')->update(array(
        'is_'.$category => $status,
      ), array(
        "$col = ?" => $id,
      ));
       $db->commit();
			}
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
			header('location:'.$_SERVER['HTTP_REFERER']);
	}
	public function deletePhotoAction(){
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->album_id = $id = $this->_getParam('id');
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $photo = Engine_Api::_()->getItem('photo', $id);
        // delete the photo in the database
        $photo->delete();
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Photo deleted successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete-photo.tpl');
	}
  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->album_id = $id = $this->_getParam('id');
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $album = Engine_Api::_()->getItem('album', $id);
        // delete the album in the database
        $album->delete();
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Album deleted successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete.tpl');
  }
	public function getalbumsAction(){
    $sesdata = array();
    $album_table = Engine_Api::_()->getDbtable('albums', 'sesalbum');
		$offtheday_table = Engine_Api::_()->getDbtable('offthedays', 'sesalbum');
		$offtheday_tableName = $offtheday_table->info('name');
		$sub_select = $offtheday_table->select()
                  ->from($offtheday_tableName, array("resource_id AS id"))
									->where('resource_type = ?','album');
    $select = $album_table->select()
                    ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
										->where("album_id NOT IN ?", $sub_select)
                    ->order('album_id ASC')->limit('25');
    $albums = $album_table->fetchAll($select);
    foreach ($albums as $album) {
      $album_icon_photo = $this->view->itemPhoto($album, 'thumb.icon');
      $sesdata[] = array(
          'id' => $album->album_id,
          'label' => $album->title,
          'photo' => $album_icon_photo
      );
    }
    return $this->_helper->json($sesdata);
	}
	public function getphotosAction(){
    $sesdata = array();
    $album_table = Engine_Api::_()->getDbtable('photos', 'sesalbum');
		$offtheday_table = Engine_Api::_()->getDbtable('offthedays', 'sesalbum');
		$offtheday_tableName = $offtheday_table->info('name');
		$sub_select = $offtheday_table->select()
                  ->from($offtheday_tableName, array("resource_id AS id"))
									->where('resource_type = ?','album_photo');
    $select = $album_table->select()
                    ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
										->where("photo_id NOT IN ?", $sub_select)
                    ->order('photo_id ASC')->limit('25');
    $albums = $album_table->fetchAll($select);
    foreach ($albums as $album) {
      $album_icon_photo = $this->view->itemPhoto($album, 'thumb.icon');
      $sesdata[] = array(
          'id' => $album->photo_id,
          'label' => $album->title,
          'photo' => $album_icon_photo
      );
    }
    return $this->_helper->json($sesdata);
	}	
}
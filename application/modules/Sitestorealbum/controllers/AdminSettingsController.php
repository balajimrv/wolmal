<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {  	
		//GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorealbum');   
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorealbum_admin_main', array(), 'sitestorealbum_admin_main_settings');
    
    $this->view->form = $form = new Sitestorealbum_Form_Admin_Global();
    
		if( $this->getRequest()->isPost() ) {
			$sitestoreKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', null);
			if( !empty($sitestoreKeyVeri) ) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.lsettings', trim($sitestoreKeyVeri));
			}
			if( $_POST['sitestorealbum_lsettings'] ) {
				$_POST['sitestorealbum_lsettings'] = trim($_POST['sitestorealbum_lsettings']);
			}
		}
    
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

    // It is only for installtion time use after it remove
    if (Engine_Api::_()->sitestore()->hasPackageEnable() && isset($values['include_in_package']) && !empty($values['include_in_package'])){
        Engine_Api::_()->sitestore()->oninstallPackageEnableSubMOdules('sitestorealbum');
      }

      // It is only for installtion time use after it remove
      if(array_key_exists('sitestorealbum_photolightbox_show', $values)) {
        unset($values['sitestorealbum_photolightbox_show']);
      }
      foreach ($values as $key => $value) {
        if ($key != 'submit') {
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }   
      }
    }
  }

  public function addFeaturedAction() {
    
		//GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorealbum');     

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorealbum_admin_main', array(), 'sitestorealbum_admin_widget_settings');

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitestorealbum_Form_Admin_FeaturedAlbum();
    $form->setTitle('Add an Photo as Featured')
            ->setDescription('Using the auto-suggest field below, choose the photo to be made featured.');
    $form->getElement('title')->setLabel('Photo Title');
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();
      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $photo = Engine_Api::_()->getItem('sitestore_photo', $values['resource_id']);
        $photo->featured = !$photo->featured;
        $photo->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The make featured photo has been added successfully.'))
              ));
    }
  }

  //ACTION FOR PHOTO SUGGESTION DROP-DOWN
  public function getPhotoAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $featured = $this->_getParam('featured', 0);
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitestore');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitestore');
    $photoName = $photoTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $data = array();
    $select = $photoTable->select()
													->setIntegrityCheck(false)
													->from($photoName)
                          ->join($albumName, $albumName . '.album_id = ' . $photoName . '.album_id', array())
													->join($storeTableName, $storeTableName . '.store_id = '. $albumName . '.store_id',array('title AS store_title', 'photo_id as store_photo_id'))
													->join($allowName, $allowName . '.resource_id = '. $storeTableName . '.store_id', array('resource_type','role'))
													->where($allowName.'.resource_type = ?', 'sitestore_store')
													->where($allowName.'.role = ?', 'registered')
													->where($allowName.'.action = ?', 'view')
													->where($albumName.'.search = ?', 1)
                          ->where($photoName . '.title  LIKE ? ', '%' . $title . '%')
													->limit($limit)
													->order($photoName . '.title')
													->limit($limit);
		$select = $select
						->where($storeTableName . '.closed = ?', '0')
						->where($storeTableName . '.approved = ?', '1')
						->where($storeTableName . '.declined = ?', '0')
						->where($storeTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($storeTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    
    if (!empty($featured))
      $select->where($photoName . ".featured = ?", 0);

    $photos = $photoTable->fetchAll($select);

    foreach ($photos as $photo) {
      $content_photo = $this->view->itemPhoto($photo, 'thumb.normal');
      $data[] = array(
          'id' => $photo->photo_id,
          'label' => $photo->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  public function featuredAction() {
    
		//GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorealbum');     
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorealbum_admin_main', array(), 'sitestorealbum_admin_main_photo_featured');

    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitestore');
    $albumName = $albumTable->info('name');
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitestore');
    $photoName = $photoTable->info('name');
    $data = array();
    $select = $photoTable->select()
            ->setIntegrityCheck(false)
            ->from($photoName);
    //if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $select->join($albumName, $albumName . '.album_id = ' . $photoName . '.album_id', array());
   // } 
    $select->where($photoName . ".featured = ?", 1)
            ->order($photoName . '.creation_date DESC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    // Set item count per store and current store number
    $paginator->setItemCountPerPage(50);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->isAlbum = true;
  }

  public function removeFeaturedAction() {

    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $photo = Engine_Api::_()->getItem('sitestore_photo', $this->_getParam('id'));
        $photo->featured = !$photo->featured;
        $photo->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin-settings/un-featured.tpl');
  }
}

?>
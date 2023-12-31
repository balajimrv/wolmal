<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_Widget_ListAlbumsTabsViewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    $this->view->showViewMore = $this->_getParam('showViewMore', 1);
    $this->view->category_id = $category_id = $this->_getParam('category_id',0);
    if (empty($is_ajax)) {
      $this->view->tabs = $tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitestorealbum', 'type' => 'albums', 'enabled' => 1));
      $count_tabs = count($tabs);
      if (empty($count_tabs)) {
        return $this->setNoRender();
      }
      $activeTabName = $tabs[0]['name'];
    }
    $this->view->marginPhoto = $this->_getParam('margin_photo', 12);
    $table = Engine_Api::_()->getItemTable('sitestore_album');
    $tableName = $table->info('name');
    $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore'); 
    $tableStoreName = $tableStore->info('name');
    $select = $table->select()
										->setIntegrityCheck(false)
                    ->from($tableName)
                    ->joinLeft($tableStoreName, "$tableStoreName.store_id = $tableName.store_id", array('title AS store_title', 'photo_id as store_photo_id'))
            ->where($tableName . '.search = ?', '1');;
 
    $select = $select
              ->where($tableStoreName . '.closed = ?', '0')
              ->where($tableStoreName . '.approved = ?', '1')
              ->where($tableStoreName . '.declined = ?', '0')
              ->where($tableStoreName . '.draft = ?', '1');

    if(!empty($category_id)) {
      $select->where($tableStoreName . '.	category_id =?', $category_id);
    }
    
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($tableStoreName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    } 
    
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.hide.autogenerated', 1) ) {
			$select->where($tableName. '.default_value'.'= ?', 0);
			$select->where($tableName . ".type is Null");
    }     
            
    $paramTabName = $this->_getParam('tabName', '');

    if (!empty($paramTabName))
      $activeTabName = $paramTabName;

    $activeTab = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitestorealbum', 'type' => 'albums', 'enabled' => 1, 'name' => $activeTabName));
    $this->view->activTab = $activTab = $activeTab['0'];

    switch ($activTab->name) {
      case 'recent_storealbums':
        break;
      case 'liked_storealbums':
        $select->order($tableName .'.like_count DESC');
        break;
      case 'viewed_storealbums':
        $select->order($tableName .'.view_count DESC');
        break;
      case 'commented_storealbums':
        $select->order($tableName .'.comment_count DESC');
        break;
      case 'featured_storealbums':
        $select->where($tableName .'.featured = ?', 1);
        $select->order('Rand()');
        break;
      case 'random_storealbums':
        $select->order('Rand()');
        break;
    }
 
    if ($activTab->name != 'featured_storealbums' && $activTab->name != 'random_storealbums') {
      $select->order('creation_date DESC');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($activTab->limit);
    $paginator->setCurrentPageNumber($this->_getParam('store', 1));
    $this->view->count = $paginator->getTotalItemCount();
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
  }

}

?>

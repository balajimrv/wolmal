<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Widget_ListVideosTabsViewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    if ($this->view->is_ajax) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $this->view->category_id = $category_id = $this->_getParam('category_id', 0);
    $this->view->showViewMore = $this->_getParam('showViewMore', 1);
    if (empty($is_ajax)) {
      $this->view->tabs = $tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitestorevideo', 'type' => 'videos', 'enabled' => 1));
      $count_tabs = count($tabs);
      if (empty($count_tabs)) {
        return $this->setNoRender();
      }
      $activeTabName = $tabs[0]['name'];
    }
    $this->view->marginPhoto = $this->_getParam('margin_photo', 12);
    $table = Engine_Api::_()->getItemTable('sitestorevideo_video');
    $tableName = $table->info('name');
    $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $tableStoreName = $tableStore->info('name');
    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($tableName)
            ->joinLeft($tableStoreName, "$tableStoreName.store_id = $tableName.store_id", array('title AS store_title', 'photo_id as store_photo_id'));

    $select = $select
            ->where($tableStoreName . '.closed = ?', '0')
            ->where($tableStoreName . '.approved = ?', '1')
            ->where($tableStoreName . '.declined = ?', '0')
            ->where($tableStoreName . '.search = ?', '1')
            ->where($tableStoreName . '.draft = ?', '1');
    if (!empty($category_id)) {
      $select = $select->where($tableStoreName . '.	category_id =?', $category_id);
    }
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($tableStoreName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }

    $paramTabName = $this->_getParam('tabName', '');

    if (!empty($paramTabName))
      $activeTabName = $paramTabName;

    $activeTab = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitestorevideo', 'type' => 'videos', 'enabled' => 1, 'name' => $activeTabName));
    $this->view->activTab = $activTab = $activeTab['0'];

    switch ($activTab->name) {
      case 'recent_storevideos':
        break;
      case 'liked_storevideos':
        $select->order($tableName . '.like_count DESC');
        break;
      case 'viewed_storevideos':
        $select->order($tableName . '.view_count DESC');
        break;
      case 'commented_storevideos':
        $select->order($tableName . '.comment_count DESC');
        break;
      case 'featured_storevideos':
        $select->where($tableName . '.featured = ?', 1);
        $select->order('Rand()');
        break;
      case 'random_storevideos':
        $select->order('Rand()');
        break;
    }

    if ($activTab->name != 'featured_storevideos' && $activTab->name != 'random_storevideos') {
      $select->order('creation_date DESC');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($activTab->limit);
    $paginator->setCurrentPageNumber($this->_getParam('store', 1));
    $this->view->count = $paginator->getTotalItemCount();
  }

}

?>

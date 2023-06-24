<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Editors.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Editors extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Editor';

  public function getSimilarEditors($params = array()) {

    //GET EDITOR TABLE NAME
    $editorTableName = $this->info('name');

    //GET USER TABLE NAME
    $userTable = Engine_Api::_()->getItemtable('user');
    $userTableName = $userTable->info('name');

    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($userTableName, array('user_id', 'username', 'displayname', 'photo_id'))
            ->join($editorTableName, "$userTableName.user_id = $editorTableName.user_id", array('editor_id', 'designation'))
            ->group("$editorTableName.user_id");

    if (isset($params['user_id']) && !empty($params['user_id'])) {
      $select->where($editorTableName . ".user_id != ?", $params['user_id']);
    }
    
    if (isset($params['super_editor_user_id']) && !empty($params['super_editor_user_id'])) {
      $select->where($editorTableName . ".user_id != ?", $params['super_editor_user_id']);
    }    

    $select->order("RAND()");

    if (isset($params['limit']) && !empty($params['limit'])) {
      $select->limit($params['limit']);
    }

    return $this->fetchAll($select);
  }

  public function getEditorsCount() {

    //MAKE QUERY
    $select = $this->select()->from($this->info('name'), array('COUNT(DISTINCT user_id) as total_editors'));

    $editorsCount = $select->query()->fetchColumn();

    return $editorsCount;
  }

  public function getEditorsProduct($params = null) {

    //GET EDITOR TABLE NAME
    $editorTableName = $this->info('name');

    //GET USER TABLE NAME
    $userTable = Engine_Api::_()->getItemtable('user');
    $userTableName = $userTable->info('name');

    //GET REVIEW TABLE NAME
    $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
    $reviewTableName = $reviewTable->info('name');

    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($userTableName, array('user_id', 'email', 'username', 'displayname', 'photo_id'))
            ->join($editorTableName, "$userTableName.user_id = $editorTableName.user_id", array('editor_id', 'designation'))
            ->joinLeft($reviewTableName, "($reviewTableName.owner_id = $editorTableName.user_id and $reviewTableName.type = 'editor')", array("COUNT(review_id) as total_reviews"))
            ->group("$editorTableName.user_id")
            ->order("total_reviews DESC")
    ;

    if (isset($params['user_id']) && !empty($params['user_id'])) {
      $select->where("$editorTableName.user_id != ?", $params['user_id']);
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $select->limit($params['limit']);
    }

    return $this->fetchAll($select);
  }

  public function isEditor($user_id = 0) {

    $select = $this->select()
            ->from($this->info('name'), "editor_id");

    $isEditor = $select
            ->where('user_id = ?', $user_id)
            ->query()
            ->fetchColumn();

    return $isEditor;
  }

  public function getColumnValue($user_id = 0, $column_name = 'designation') {

    $select = $this->select()
            ->from($this->info('name'), array("$column_name"));
    if (!empty($user_id)) {
      $select->where('user_id = ?', $user_id);
    }

    return $select->limit(1)->query()->fetchColumn();
  }

  /**
   * Return users lists whose pages can be claimed
   *
   * @param int $text
   * @param int $limit
   * @return user lists
   */
  public function getMembers($text, $limit = 40, $featured_editor = 0) {

    //MAKE QUERY
    $select = $this->select()->from($this->info('name'), array('user_id'));

    $select->group('user_id');

    $userDatas = $this->fetchAll($select);

    //MAKING USER ID ARRAY
    $userIds = '0,';
    if (!empty($userDatas)) {
      foreach ($userDatas as $user) {
        $userIds .= "$user->user_id,";
      }
    }

    $userIds = trim($userIds, ',');

    //GET USER TABLE
    $tableUser = Engine_Api::_()->getDbtable('users', 'user');

    //SELECT
    $selectUsers = $tableUser->select()
            ->from($tableUser->info('name'), array('user_id', 'displayname', 'username', 'photo_id'))
            ->where('displayname  LIKE ? OR username LIKE ?', '%' . $text . '%');

    if (!empty($featured_editor)) {
      $selectUsers->where($tableUser->info('name') . '.user_id IN (' . $userIds . ')');
    } else {
      $selectUsers->where($tableUser->info('name') . '.user_id NOT IN (' . $userIds . ')');
    }

    $selectUsers->where('approved = ?', 1)
            ->where('verified = ?', 1)
            ->where('enabled = ?', 1)
            ->order('displayname ASC')
            ->limit($limit);

    //FETCH
    return $tableUser->fetchAll($selectUsers);
  }

  public function getEditor($user_id) {

    $select = $this->select()
            ->where('user_id = ?', $user_id);

    return $this->fetchRow($select);
  }

  public function getEditorDetails($user_id = 0, $params = array()) {

    //GET EDITOR TABLE NAME
    $editorTableName = $this->info('name');

    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($editorTableName, array('details'))
            ->where("$editorTableName.user_id = ?", $user_id);
    
    if (isset($params['editorReviewAllow']) && !empty($params['editorReviewAllow'])) {
      $select->where("$productTypeTableName.reviews = 1 OR $productTypeTableName.reviews = 3");
    }    

    return $this->fetchAll($select);
  }

  /**
   * Return Editors Ids
   *
   * @param int $user_id
   * @return Editors Ids
   */
  public function getAllEditors($user_id = 0) {

    //MAKE QUERY
    $select = $this->select()->from($this->info('name'), array('user_id'));

    if (!empty($user_id)) {
      $select->where('user_id != ?', $user_id);
    }

    //FETCH
    return $this->fetchAll($select);
  }

  public function getSuperEditor($column_name = 'editor_id') {

    $column_value = $this->select()
            ->from($this->info('name'), $column_name)
            ->where('super_editor = ?', 1)
            ->limit(1)
            ->query()
            ->fetchColumn();

    return $column_value;
  }

  public function getHighestLevelEditorId() {

    $userTable = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTable->info('name');

    $editorTableName = $this->info('name');

    $editor_id = $this->select()
            ->setIntegrityCheck(false)
            ->from($editorTableName, 'editor_id')
            ->joinInner($userTableName, "$userTableName.user_id = $editorTableName.user_id", array(''))
            ->group($editorTableName . '.user_id')
            ->order('level_id ASC')
            ->limit(1)
            ->query()
            ->fetchColumn();

    return $editor_id;
  }

}
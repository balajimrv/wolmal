<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Post.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_Post extends Core_Model_Item_Abstract {

  protected $_parent_type = 'sitestore_topic';
  protected $_owner_type = 'user';

  /**
   * Gets an absolute URL to the album to view this item
   *
   * @param array $params 
   * @return string
   */  
  public function getHref($params = array()) {

    $params = array_merge(array(
        'route' => 'sitestore_extended',
        'controller' => 'topic',
        'action' => 'view',
        'store_id' => $this->store_id,
        'topic_id' => $this->getParentTopic()->getIdentity(),
        'post_id' => $this->getIdentity(),
        'tab' => Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.discussion-sitestore', $this->store_id, Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0)),
            ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  /**
   * Gets description
   *
   * @return description
   * */  
  public function getDescription() {
    // strip HTML and BBcode
    $content = strip_tags($this->body);
    $content = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $content);
    return $content;
  }

  /**
   * Gets count
   *
   * @return how many post ids related to topic
   * */  
  public function getPostIndex() {

    $table = $this->getTable();
    $select = new Zend_Db_Select($table->getAdapter());
    $select
            ->from($table->info('name'), new Zend_Db_Expr('COUNT(post_id) as count'))
            ->where('topic_id = ?', $this->topic_id)
            ->where('post_id < ?', $this->getIdentity())
            ->order('post_id ASC');
    $data = $select->query()->fetch();

    return (int) $data['count'];
  }

  /**
   * Gets sitestore item
   *
   * @return sitestore item
   * */    
  public function getParentsitestore() {

    return Engine_Api::_()->getItem('sitestore_store', $this->store_id);
  }

  /**
   * Gets discussion item
   *
   * @return discussion item
   * */ 
  public function getParentTopic() {

    return Engine_Api::_()->getItem('sitestore_topic', $this->topic_id);
  }

  /**
   * Inserts topic in 'engine4_sitestorediscussion_topics table'
   *
   * @return inserted topic
   * */  
  protected function _insert() {

    if ($this->_disableHooks)
      return;

    if (!$this->store_id) {
      $errormsg1 = Zend_Registry::get('Zend_Translate')->_('Cannot create post without store_id');
      throw new Exception($errormsg1);
    }

    if (!$this->topic_id) {
      $errormsg2 = Zend_Registry::get('Zend_Translate')->_('Cannot create post without topic_id');
      throw new Exception($errormsg2);
    }

    $table = Engine_Api::_()->getDbtable('topics', 'sitestore');
    $select = $table->select()->where('topic_id = ?', $this->topic_id)->limit(1);
    $topic = $table->fetchRow($select);
    $topic->lastpost_id = $this->post_id;
    $topic->lastposter_id = $this->user_id;
    $topic->modified_date = date('Y-m-d H:i:s');
    $topic->post_count++;
    $topic->save();
    parent::_insert();
  }

  /**
   * Delete topics
   * */  
  protected function _delete() {

    if ($this->_disableHooks)
      return;

    $topic = Engine_Api::_()->getDbtable('topics', 'sitestore')->fetchRow(Engine_Api::_()->getDbtable('topics', 'sitestore')->select()->where('topic_id = ?', $this->topic_id)->limit(1));
    $topic->post_count--;
    if ($topic->post_count == 0) {
      $topic->delete();
    } else {
      $topic->save();
    }
    // Delete create activity feed of note before delete note
		Engine_Api::_()->getApi('subCore', 'sitestore')->deleteCreateActivityOfExtensionsItem($this, array('sitestore_topic_reply', 'sitestore_admin_topic_reply'));

    parent::_delete();
  }

}

?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topic.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_Topic extends Core_Model_Item_Abstract {

  protected $_parent_type = 'sitestore_store';
  protected $_owner_type = 'user';
  protected $_children_types = array('sitestore_post');

  /**
   * Gets an absolute URL to the album to view this item
   *
   * @param array $params 
   * @return string
   */
  public function getHref($params = array()) {
 
    $tab_id='';
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.sitemobile-discussion-sitestore', $this->store_id, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.discussion-sitestore', $this->store_id, $layout);
		}
    $params = array_merge(array(
        'route' => 'sitestore_extended',
        'controller' => 'topic',
        'action' => 'view',
        'store_id' => $this->store_id,
        'topic_id' => $this->getIdentity(),
        'tab' => $tab_id,
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
  public function getDescription()
  {
    if( !isset($this->store()->firstPost) ) {
      $postTable = Engine_Api::_()->getDbtable('posts', 'sitestore');
      $postSelect = $postTable->select()
        ->where('topic_id = ?', $this->getIdentity())
        ->where('store_id = ?', $this->store_id)
        ->order('post_id ASC')
        ->limit(1);
      $this->store()->firstPost = $postTable->fetchRow($postSelect);
    }
    if( isset($this->store()->firstPost) ) {
      // strip HTML and BBcode
      $content = $this->store()->firstPost->body;
      $content = strip_tags($content);
      $content = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $content);
      return $content;
    }
    return '';
  }

  /**
   * Gets sitestore item
   *
   * @return sitestore item
   * */
  public function getParentSitestore() {

    return Engine_Api::_()->getItem('sitestore_store', $this->store_id);
  }

  public function getResource() {
    if ($this->resource_type && $this->resource_id)
      return Engine_Api::_()->getItem($this->resource_type, $this->resource_id);
  }

  /**
   * Gets first post
   *
   * @return first post
   * */
  public function getFirstPost() {

    return Engine_Api::_()->getDbtable('posts', 'sitestore')->fetchRow(Engine_Api::_()->getDbtable('posts', 'sitestore')->select()
                            ->where('topic_id = ?', $this->getIdentity())
                            ->order('post_id ASC')
                            ->limit(1));
  }

  /**
   * Gets last post
   *
   * @return last post
   * */
  public function getLastPost() {

    return Engine_Api::_()->getItemTable('sitestore_post')->fetchRow(Engine_Api::_()->getItemTable('sitestore_post')->select()
                            ->where('topic_id = ?', $this->getIdentity())
                            ->order('post_id DESC')
                            ->limit(1));
  }

  /**
   * Gets last poster information
   *
   * @return last poster item
   * */
  public function getLastPoster() {

    return Engine_Api::_()->getItem('user', $this->lastposter_id);
  }

  /**
   * Inserts topic
   * */
  protected function _insert() {

    if ($this->_disableHooks)
      return;

    if (!$this->store_id) {
      $error_msg = Zend_Registry::get('Zend_Translate')->_('Cannot create topic without store_id');
      throw new Exception($error_msg);
    }

    parent::_insert();
  }

  /**
   * Delete posts
   * */
  protected function _delete() {

    if ($this->_disableHooks)
      return;

    $postTable = Engine_Api::_()->getItemTable('sitestore_post');
    $postSelect = $postTable->select()->where('topic_id = ?', $this->getIdentity());
    foreach ($postTable->fetchAll($postSelect) as $sitestorePost) {
      $sitestorePost->disableHooks()->delete();
    }

    // Delete create activity feed of note before delete note 
    Engine_Api::_()->getApi('subCore', 'sitestore')->deleteCreateActivityOfExtensionsItem($this, array('sitestore_topic_create', 'sitestore_admin_topic_create'));

    parent::_delete();
  }

}

?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topic.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Topic extends Core_Model_Item_Abstract {

  protected $_parent_type = 'sitestoreproduct_product';
  protected $_owner_type = 'user';
  protected $_children_types = array('sitestoreproduct_post');

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    $params = array_merge(array(
        'route' => "sitestoreproduct_extended",
        'controller' => 'topic',
        'action' => 'view',
        'product_id' => $this->product_id,
        'topic_id' => $this->getIdentity(),
        'tab' => $tab_id
            ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getDescription() {
    
    $firstPost = $this->getFirstPost();
    return ( null != $firstPost ? Engine_String::substr($firstPost->body, 0, 255) : '' );
  }

  public function getFirstPost() {
    
    $table = Engine_Api::_()->getDbtable('posts', 'sitestoreproduct');
    $select = $table->select()
            ->where('topic_id = ?', $this->getIdentity())
            ->order('post_id ASC')
            ->limit(1);
    return $table->fetchRow($select);
  }

  public function getLastPost() {
    
    $table = Engine_Api::_()->getItemTable('sitestoreproduct_post');
    $select = $table->select()
            ->where('topic_id = ?', $this->getIdentity())
            ->order('post_id DESC')
            ->limit(1);

    return $table->fetchRow($select);
  }

  public function getAuthorizationItem() {
    
    return $this->getParent('sitestoreproduct_product');
  }

  protected function _insert() {
    if ($this->_disableHooks)
      return;

    if (!$this->product_id) {
      throw new Exception('Cannot create topic without product_id');
    }

    parent::_insert();
  }

  protected function _delete() {
    
    if ($this->_disableHooks)
      return;

    //DELETE ALL CHIELD POST
    $postTable = Engine_Api::_()->getItemTable('sitestoreproduct_post');
    $postSelect = $postTable->select()->where('topic_id = ?', $this->getIdentity());
    foreach ($postTable->fetchAll($postSelect) as $sitestoreproductPost) {
      $sitestoreproductPost->disableHooks()->delete();
    }

    parent::_delete();
  }
  
    /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

}
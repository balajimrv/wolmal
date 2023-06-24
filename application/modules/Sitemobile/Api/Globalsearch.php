<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Globalsearch.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Api_Globalsearch extends Core_Api_Abstract {

  protected $_types;
  protected $_itemTypes;

  public function index(Core_Model_Item_Abstract $item) {
    // Check if not search allowed
    if (isset($item->search) && !$item->search) {
      return false;
    }

    // Get info
    $type = $item->getType();
    $id = $item->getIdentity();
    $title = substr(trim($item->getTitle()), 0, 255);
    $description = substr(trim($item->getDescription()), 0, 255);
    $keywords = substr(trim($item->getKeywords()), 0, 255);
    $hiddenText = substr(trim($item->getHiddenSearchData()), 0, 255);

    // Ignore if no title and no description
    if (!$title && !$description) {
      return false;
    }

    // Check if already indexed
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $select = $table->select()
            ->where('type = ?', $type)
            ->where('id = ?', $id)
            ->limit(1);

    $row = $table->fetchRow($select);

    if (null === $row) {
      $row = $table->createRow();
      $row->type = $type;
      $row->id = $id;
    }

    $row->title = $title;
    $row->description = $description;
    $row->keywords = $keywords;
    $row->hidden = $hiddenText;
    $row->save();
  }

  public function unindex(Core_Model_Item_Abstract $item) {
    $table = Engine_Api::_()->getDbtable('search', 'core');

    $table->delete(array(
        'type = ?' => $item->getType(),
        'id = ?' => $item->getIdentity(),
    ));

    return $this;
  }

  public function getPaginator($text, $type = null) {
    return Zend_Paginator::factory($this->getSelect($text, $type));
  }

  public function getSelect($text, $type = null) {
    // Build base query
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $db = $table->getAdapter();
    $select = $table->select()
            ->where(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (? IN BOOLEAN MODE)', $text)))
            ->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)));

    // Filter by item types
    $availableTypes = $this->getItemTypes();
    if ($type && in_array($type, $availableTypes)) {
      $select->where('type = ?', $type);
    } else {
      $select->where('type IN(?)', $availableTypes);
    }

    return $select;
  }

  public function getAvailableTypes() {
    if (null === $this->_types) {
      $this->_types = Engine_Api::_()->getDbtable('search', 'core')->getAdapter()
              ->query('SELECT DISTINCT `type` FROM `engine4_core_search`')
              ->fetchAll(Zend_Db::FETCH_COLUMN);
      $this->_types = array_intersect($this->_types, $this->getItemTypes());
    }
    return $this->_types;
  }

  /**
   * Get all item types
   *
   * @return array
   */
  public function getItemTypes()
  {
    $this->_loadItemInfo();
    return array_keys($this->_itemTypes);
  }
  /**
   * Load item info from manifest
   */
  protected function _loadItemInfo()
  {
    if( null === $this->_itemTypes ) {
      $manifest = Zend_Registry::get('Engine_Manifest');
      if( null === $manifest ) {
        throw new Engine_Api_Exception('Manifest data not loaded!');
      }
      $this->_itemTypes = array();
      foreach( $manifest as $module => $config ) {
        if(!Engine_Api::_()->sitemobile()->isSupportedModule($module)|| !isset($config['items']) ) continue;
        foreach( $config['items'] as $key => $value ) {
          if( is_numeric($key) ) {
            $this->_itemTypes[$value] = array(
              'module' => $module
            );
          } else {
            $this->_itemTypes[$key] = $value;
            $this->_itemTypes[$key]['module'] = $module;
         //   $this->_itemTypes[$key]['moduleInflected'] = self::inflect($module);
          }
        }
      }
    }
  }
}
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Tabletpages.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Model_DbTable_Tabletpages extends Engine_Db_Table implements Engine_Content_Storage_Interface {

  protected $_rowClass = 'Sitemobile_Model_Tabletpage';
  protected $_name = 'sitemobile_tablet_pages';
  protected $_primary = array('page_id');

  public function loadMetaData(Engine_Content $contentAdapter, $name) {
    $select = $this->select()->where('name = ?', $name)->orWhere('page_id = ?', $name);
    $page = $this->fetchRow($select);

    if (!is_object($page)) {
      //throw?
      return null;
    }

    return $page->toArray();
  }

  public function loadContent(Engine_Content $contentAdapter, $name) {
    if (is_array($name)) {
      $name = join('_', $name);
    }
    if (!is_string($name) && !is_numeric($name)) {
      throw new Exception('not string');
    }

    $select = $this->select()->where('name = ?', $name)->orWhere('page_id = ?', $name);
    $page = $this->fetchRow($select);

    if (!is_object($page)) {
      //throw?
      return null;
    }

    //Get all content
    $contentTable = Engine_Api::_()->getDbtable('tabletcontent', 'Sitemobile');
    $select = $contentTable->select()
            ->where('page_id = ?', $page->page_id)
            ->order('order ASC')
    ;
    $content = $contentTable->fetchAll($select);

    //Create structure
    $structure = $this->prepareContentArea($content);

    //Create element (with structure)
    $element = new Engine_Content_Element_Container(array(
                'class' => 'layout_page_' . $page->name,
                'elements' => $structure
            ));

    return $element;
  }

  public function prepareContentArea($content, $current = null) {
    //Get parent content id
    $parent_content_id = null;
    if (null !== $current) {
      $parent_content_id = $current->content_id;
    }

    //Get children
    $children = $content->getRowsMatching('parent_content_id', $parent_content_id);
    if (empty($children) && null === $parent_content_id) {
      $children = $content->getRowsMatching('parent_content_id', 0);
    }

    //Get struct
    $struct = array();
    foreach ($children as $child) {
      $elStruct = $this->createElementParams($child);
      if ($elStruct) {
        $elStruct['elements'] = $this->prepareContentArea($content, $child);
        $struct[] = $elStruct;
      }
    }

    return $struct;
  }

  public function createElementParams($row) {

    if ($row->type === 'widget') {
      if (empty($row->module)) {
        $nameArray = explode('.', $row->name);
        $moduleName = $nameArray[0];
        if ($moduleName == 'sitemobile') {
          $modulesNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getAllMobileEnabledModuleNames();
          $widgetNameArray = explode('-', $nameArray[1]);
          if (in_array($widgetNameArray[0], $modulesNames))
            $moduleName = $widgetNameArray[0];
        }
        $row->module = $moduleName;
        $row->save();
      }
      if (!Engine_Api::_()->sitemobile()->isSupportedModule($row->module))
        return false;
    }
    $data = array(
        'identity' => $row->content_id,
        'type' => $row->type,
        'name' => $row->name,
        'order' => $row->order,
    );
    $params = (array) $row->params;
    if (isset($params['title'])) {
      $data['title'] = $params['title'];
    }
    $data['params'] = $params;



    return $data;
  }

  public function deletePage(Sitemobile_Model_Tabletpage $page) {
    $contentTable = Engine_Api::_()->getDbtable('tabletcontent', 'Sitemobile');
    $contentTable->delete(array(
        'page_id = ?' => $page->page_id,
    ));

    $page->delete();

    return $this;
  }

  public function getPageName($page_id) {

    return $this->select()->from($this->info('name'), array('name'))->where('page_id =?', $page_id)->query()->fetchColumn();
  }

}
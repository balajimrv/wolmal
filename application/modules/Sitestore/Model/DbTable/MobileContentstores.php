<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contentstores.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_MobileContentstores extends Engine_Db_Table implements Engine_Content_Storage_Interface {

  protected $_rowClass = "Sitestore_Model_MobileContentstore";

  public function loadMetaData(Engine_Content $contentAdapter, $name) {
  	
    $select = $this->select()->where('name = ?', $name)->orWhere('mobilecontentstore_id = ?', $name);
    $store = $this->fetchRow($select);

    if (!is_object($store)) {
      //throw?
      return null;
    }

    return $store->toArray();
  }

  public function loadContent(Engine_Content $contentAdapter, $name) {
  	
    $sitestore_id = Engine_Api::_()->core()->getSubject()->getIdentity();

    if (is_array($name)) {
      $name = join('_', $name);
    }
    if (!is_string($name) && !is_numeric($name)) {
      throw new Exception('not string');
    }
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()->where('store_id = ?', $sitestore_id)->where('name = ?', $name)->orWhere('mobilecontentstore_id = ?', $name);
    $store = $this->fetchRow($select);

    if (is_object($store)) {
			$contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitestore');
			$select = $contentTable->select()
							->where('mobilecontentstore_id = ?', $store->mobilecontentstore_id)
							->order('order ASC');
			$content = $contentTable->fetchAll($select);

			$structure = $this->prepareContentArea($content);
			$element = new Engine_Content_Element_Container(array(
									'class' => 'layout_store_' . $store->name,
									'elements' => $structure
							));
    } else {
      $store_id = Engine_Api::_()->sitestore()->getMobileWidgetizedStore()->page_id;
			$contentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitestore');
			$select = $contentTable->select()
							->where('store_id = ?', $store_id)
							->order('order ASC');
			$content = $contentTable->fetchAll($select);
			$structure = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitestore')->prepareContentArea($content);
			$element = new Engine_Content_Element_Container(array(
									'class' => 'layout_store_sitestore_index_view',
									'elements' => $structure
							));
    }
    


    return $element;
  }

  public function prepareContentArea($content, $current = null) {

    $parent_content_id = null;
    if (null !== $current) {
      $parent_content_id = $current->mobilecontent_id;
    }

    $children = $content->getRowsMatching('parent_content_id', $parent_content_id);
    if (empty($children) && null === $parent_content_id) {
      $children = $content->getRowsMatching('parent_content_id', 0);
    }

    $struct = array();
    foreach ($children as $child) {
      $elStruct = $this->createElementParams($child);
      $elStruct['elements'] = $this->prepareContentArea($content, $child);
      $struct[] = $elStruct;
    }
    $Modules = array("offer" => "sitestoreoffer", "form" => "sitestoreform", "invite" => "sitestoreinvite", "sdcreate" => "sitestoredocument", "sncreate" => "sitestorenote", "splcreate" => "sitestorepoll", "secreate" => "sitestoreevent", "svcreate" => "sitestorevideo", "spcreate" => "sitestorealbum", "sdicreate" => "sitestorediscussion", "smcreate" => "sitestoremusic");
    $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
    foreach ($struct as $keys => $valuess) {
      $unsetFlage = false;
      $explode_modulename_array = explode('.', $valuess['name']);
      $explode_modulename = $explode_modulename_array[0];
      $search_Key = "";
      $search_Key = array_search($explode_modulename, $Modules);
      if ($explode_modulename == 'sitestore') {
        if ($valuess['name'] == 'sitestore.photos-sitestore' || $valuess['name'] == 'sitestore.photorecent-sitestore' || $valuess['name'] == 'sitestore.albums-sitestore') {
          $explode_modulename = 'sitestorealbum';
          $search_Key = 'spcreate';
        }
        if ($valuess['name'] == 'sitestore.discussion-sitestore') {
          $explode_modulename = 'sitestorediscussion';
          $search_Key = 'sdicreate';
        }
        if ($valuess['name'] == 'sitestore.overview-sitestore') {
          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'overview');
          if (empty($isManageAdmin)) {
            $unsetFlage = true;
          }
        }
        if ($valuess['name'] == 'sitestore.location-sitestore') {
          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'map');
          if (empty($isManageAdmin)) {
            $unsetFlage = true;
          }
        }
      }
      if (!empty($search_Key)) {
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", $explode_modulename)) {
            $unsetFlage = true;
          }
        } else {
          $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, $search_Key);
          if (empty($isStoreOwnerAllow)) {
            $unsetFlage = true;
          }
        }
      }
      if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
        if ($valuess['name'] == 'sitestore.thumbphoto-sitestore') {
          unset($struct[$keys]);
        }
      }

      if ($unsetFlage)
        unset($struct[$keys]);
    }
    return $struct;
  }

  public function createElementParams($row) {
  	
    $data = array(
        'identity' => $row->mobilecontent_id,
        'type' => $row->type,
        'name' => $row->name,
        'order' => $row->order,
        'widget_admin' => $row->widget_admin,
    );
    $params = (array) $row->params;
    if (isset($params['title']))
      $data['title'] = $params['title'];
    $data['params'] = $params;

    return $data;
  }

  public function deleteStore(Sitestore_Model_Store $store) {

    Engine_Api::_()->getDbtable('mobileContent', 'sitestore')->delete(array('mobilecontentstore_id = ?' => $store->mobilecontentstore_id));    
    $store->delete();
    return $this;
  }
  
  /**
   * Gets contentstore_id,description,keywords
   *
   * @param int $store_id
   * @return contentstore_id,description,keywords
   */     
  public function getContentStoreId($store_id) {
  	
  	$selectStoreAdmin = $this->select()            
            ->from($this->info('name'), array('mobilecontentstore_id', 'description', 'keywords'))  
            ->where('name = ?', 'sitestore_index_view')           
            ->where('store_id =?', $store_id)
            ->limit(1);
    return $this->fetchRow($selectStoreAdmin);
  }

}

?>
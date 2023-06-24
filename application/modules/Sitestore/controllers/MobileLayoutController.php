<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LayoutController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_MobileLayoutController extends Core_Controller_Action_Standard {

  //SET THE VALUE FOR ALL ACTION DEFAULT
  public function init() {

    //SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMESTORES
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) { 
      Engine_API::_()->sitemobile()->setupRequestError();
		}

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();

    $id = $this->_getParam('store_id', $this->_getParam('id', null));
    if ($id) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $id);
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);
        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

        if (empty($isManageAdmin)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
      }
    }
  }

  public function buildCategorizedContentAreas($contentAreas) {

    $categorized = array();
    foreach ($contentAreas as $config) {
      //CHECK SOME STUFF
      if (!empty($config['requireItemType'])) {
        if (is_string($config['requireItemType']) && !Engine_Api::_()->hasItemType($config['requireItemType'])) {
          $config['disabled'] = true;
        } else if (is_array($config['requireItemType'])) {
          $tmp = array_map(array(Engine_Api::_(), 'hasItemType'), $config['requireItemType']);
          $config['disabled'] = !(array_sum($tmp) == count($config['requireItemType']));
        }
      }

      //ADD TO CATEGORY
      $category = ( isset($config['category']) ? $config['category'] : 'Uncategorized' );
      $categorized[$category][] = $config;
    }

    //SORT CATEGORIES
    uksort($categorized, array($this, '_sortCategories'));

    //SORT ITEMS IN CATEGORIES
    foreach ($categorized as $category => &$items) {
      usort($items, array($this, '_sortCategoryItems'));
    }

    return $categorized;
  }

  protected function _sortCategories($a, $b) {

    if ($a == 'Core')
      return -1;
    if ($b == 'Core')
      return 1;
    return strcmp($a, $b);
  }

  protected function _sortCategoryItems($a, $b) {

    if (!empty($a['special']))
      return -1;
    if (!empty($b['special']))
      return 1;
    return strcmp($a['title'], $b['title']);
  }

  protected function _reorderContentStructure($a, $b) {

    $sample = array('left', 'middle', 'right');
    $av = $a['name'];
    $bv = $b['name'];
    $ai = array_search($av, $sample);
    $bi = array_search($bv, $sample);
    if ($ai === false && $bi === false)
      return 0;
    if ($ai === false)
      return -1;
    if ($bi === false)
      return 1;
    $r = ( $ai == $bi ? 0 : ($ai < $bi ? -1 : 1) );
    return $r;
  }

  public function setUserDrivenLayoutAction() {
    $this->view->store_id = $this->_getParam('store_id', null);
		$this->_helper->layout->setLayout('default-simple');
  }

  public function saveUserDrivenLayoutAction() {
    $store_id = $this->_getParam('store_id', null);
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW STORE.
		$storeAdminTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
		$storeAdminTableName = $storeAdminTable->info('name');
		$selectStoreAdmin = $storeAdminTable->select()
						->setIntegrityCheck(false)
						->from($storeAdminTableName)
						->where('name = ?', 'sitestore_index_view');
		$storeAdminresult = $storeAdminTable->fetchRow($selectStoreAdmin);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
		//NOW INSERTING THE ROW IN STORE TABLE
		$storeTable = Engine_Api::_()->getDbtable('mobileContentstores', 'sitestore');
     
		//CREATE NEW STORE
		$storeObject = $storeTable->createRow();
		$storeObject->displayname = $sitestore->title;
		$storeObject->title = $sitestore->title;
		$storeObject->description = $sitestore->body;
		$storeObject->name = "sitestore_index_view";
		$storeObject->url = $storeAdminresult->url;
		$storeObject->custom = $storeAdminresult->custom;
		$storeObject->fragment = $storeAdminresult->fragment;
		$storeObject->keywords = $storeAdminresult->keywords;
		$storeObject->layout = $storeAdminresult->layout;
		$storeObject->view_count = $storeAdminresult->view_count;
		$storeObject->user_id = $viewer_id;
		$storeObject->store_id = $store_id;
		$contentStoreId = $storeObject->save();

		//NOW FETCHING STORE CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS STORE.
		//NOW INSERTING DEFAULT STORE CONTENT SETTINGS IN OUR CONTENT TABLE
		$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
		$sitestore_layout_cover_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layout.cover.photo', 1);
		if (!$layout) {
			Engine_Api::_()->getDbtable('mobileContent', 'sitestore')->setContentDefault($contentStoreId, $sitestore_layout_cover_photo);
		} else {
			Engine_Api::_()->getApi('mobilelayoutcore', 'sitestore')->setContentDefaultLayout($contentStoreId, $sitestore_layout_cover_photo);
		}

		$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefreshTime' => '60',
				'parentRefresh' => 'true',
				'format' => 'smoothbox',
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Layout for this store has been changed successfully.'))
		));

	}

  public function layoutAction() {

//     $cont = Engine_Content::getInstance();
//     $storage = Engine_Api::_()->getDbtable('pages', 'sitemobile');
//     $cont->setStorage($storage);

    if (!Engine_Api::_()->core()->hasSubject('sitestore_store')) {
      return false;
    }

		$edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    if (empty($edit_layout_setting)) {
      $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');  
		
    //FINDING THE LAYOUT ID OF THIS STORE
    $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitestore');
    $contentTableName = $contentTable->info('name');
    $contentStoreTable = Engine_Api::_()->getDbtable('mobileContentstores', 'sitestore');
    $contentStoreTableName = $contentStoreTable->info('name');
    $mobilecontentstore_id = $contentStoreTable->select()
            ->from($contentStoreTableName, array('mobilecontentstore_id'))
            ->where('store_id =?', $sitestore->store_id)
            ->query()
            ->fetchColumn();
    //GET STORE PARAM
    $store = $mobilecontentstore_id;
    $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitestore');
    $this->view->adminDriven = 0;
    if(empty($store)) {
      $corestoreinfo = Engine_Api::_()->sitestore()->getMobileWidgetizedStore();
      $coreStoresTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
      $coreStoresTableName = $coreStoresTable->info('name');
      $contentCoreTable = Engine_Api::_()->getDbtable('mobileContent', 'sitestore');
      $contentCoreTableName = $contentCoreTable->info('name');
			$adminContentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitestore');
			$adminContentTableName = $adminContentTable->info('name');
			$contentstore_id = $adminContentTable->select()
							->from($adminContentTableName, array('store_id'))
							->where('store_id =?', $corestoreinfo->page_id)
							->query()
							->fetchColumn();
      //GET CURRENT STORE
			$this->view->storeObject = $storeObject = $coreStoresTable->fetchRow($coreStoresTable->select()->where('name = ?', 'sitestore_index_view')->orWhere('page_id = ?', $mobilecontentstore_id));
			if (null === $storeObject) {
				$store = 'core_index_index';
				$storeObject = $coreStoresTable->fetchRow($coreStoresTable->select()->where('name = ?', 'core_index_index'));
			}

			//GET REGISTERED CONTENT AREAS
			if (!empty($storeObject)) {
				$contentRowset = $adminContentTable->fetchAll($adminContentTable->select()->where('store_id = ?', $storeObject->page_id)->order('order ASC'));
				$contentStructure = $adminContentTable->prepareContentArea($contentRowset);
			}
      $this->view->adminDriven = 1;
    } else {
			//GET CURRENT STORE
			$this->view->storeObject = $storeObject = $contentStoreTable->fetchRow($contentStoreTable->select()->where('mobilecontentstore_id = ?', $store));
			//GET REGISTERED CONTENT AREAS
			if (!empty($storeObject)) {
				$contentRowset = $contentTable->fetchAll($contentTable->select()->where('mobilecontentstore_id = ?', $storeObject->mobilecontentstore_id)->order('order ASC'));
				$contentStructure = $contentStoreTable->prepareContentArea($contentRowset);

			}
    }
		$this->view->store = $store;
		$this->view->storeObject = $storeObject;

    //GET AVAILABLE CONTENT BLOCKS
    $this->view->contentAreas = $contentAreas = $this->buildCategorizedContentAreas($this->getContentAreas());
  
    $rows = Engine_Api::_()->getDbtable('hideprofilewidgets', 'sitestore')->hideWidgets();
    $hideWidgets = array();
    foreach ($rows as $value)
      $hideWidgets[] = $value->widgetname;
    $this->view->hideWidgets = $hideWidgets;
    $contentByName = array();
    foreach ($contentAreas as $category => $categoryAreas) {
      foreach ($categoryAreas as $info) {
        $contentByName[$info['name']] = $info;
      }
    }
    $this->view->contentByName = $contentByName;

    //MAKE STORE FORM
    $this->view->storeForm = $storeForm = new Sitestore_Form_Layout_Content_MobileStore();
    if (!empty($storeObject)) {
      $storeForm->populate($storeObject->toArray());
    } else {
      //return;
    }

    //VALIDATE STRUCTURE
    //NOTE: DO NOT VALIDATE FOR HEADER OR FOOTER
    $error = false;
    if ($storeObject->name !== 'header' && $storeObject->name !== 'footer') {
      foreach ($contentStructure as &$info1) {
        if (!in_array($info1['name'], array('top', 'bottom', 'main')) || $info1['type'] != 'container') {
          $error = true;
          break;
        }
        foreach ($info1['elements'] as &$info2) {
          if (!in_array($info2['name'], array('left', 'middle', 'right')) || $info1['type'] != 'container') {
            $error = true;
            break;
          }
        }
        //RE ORDER SECOND-LEVEL ELEMENTS
        usort($info1['elements'], array($this, '_reorderContentStructure'));
      }
    }

    if ($error) {
      $error_msg = Zend_Registry::get('Zend_Translate')->_('store failed validation check');
      throw new Exception($error_msg);
    }

    $this->view->showeditinwidget = array('seaocore.feed', 'activity.feed', 'sitestore.info-sitestore', 'sitestore.overview-sitestore', 'sitestore.location-sitestore', 'core.profile-links', 'sitestore.discussion-sitestore', 'sitestorepoll.profile-sitestorepolls', 'sitestoreevent.profile-sitestoreevents', 'sitestoreoffer.profile-sitestoreoffers', 'sitestoredocument.profile-sitestoredocuments', 'sitestoreform.sitestore-viewform', 'sitestorereview.profile-sitestorereviews', 'sitestorenote.profile-sitestorenotes', 'sitestorevideo.profile-sitestorevideos', 'sitestore.photos-sitestore', 'sitestoremusic.profile-sitestoremusic', 'sitestoreintegration.profile-items', 'sitestoretwitter.feeds-sitestoretwitter', 'advancedactivity.home-feeds', 'sitestoremember.profile-sitestoremembers', 'siteevent-contenttype-events', 'sitevideo-contenttype-videos', 'document-contenttype-documents');

    //ASSIGN STRUCTURE
    $this->view->contentRowset = $contentRowset;
    $this->view->contentStructure = $contentStructure;

		$isSupport = null;
		$coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
		/*
			return < 0 : when running version is lessthen 4.2.1
			return 0 : If running version is equal to 4.2.1
			return > 0 : when running version is greaterthen 4.2.1
		*/
		if( !empty($coreVersion) ) {
			$coreVersion = $coreVersion->version;
			$isPluginSupport = strcasecmp($coreVersion, '4.2.1');
			if( $isPluginSupport >= 0 ) {
				$isSupport = 1;
			}
		}
    if (!empty($isSupport)) {
      $this->renderScript('mobile-layout/layout.tpl');
    } else {
      $this->renderScript('mobile-layout/layout_default.tpl');
    }
  }

  public function getContentAreas() {

    $contentAreas = array();
    // From modules
    $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();
    foreach ($modules as $module => $path) {
      $contentManifestFile = dirname($path) . '/settings/sitemobile/content.php';
      if (!file_exists($contentManifestFile)) {
        $contentManifestFile = dirname($path) . '/settings/sitemobile_content.php';
        if (!file_exists($contentManifestFile))
          continue;
      }
      $ret = include $contentManifestFile;
      if (!is_array($ret))
        continue;
      for ($i = 0; $i < count($ret); $i++) {
        if (!isset($ret[$i]['module']))
          $ret[$i]['module'] = $module;
      }
      $contentAreas = array_merge($contentAreas, (array) $ret);
    }

    foreach ($modules as $module => $path) {
      $explodePath = explode(DS, dirname($path));
      $moduleName = '/' . end($explodePath);
      $path = str_replace($moduleName, '/Sitemobile/modules' . $moduleName, implode('/', $explodePath));
      $contentManifestFile = $path . '/settings/sitemobile_content.php';

      if (!file_exists($contentManifestFile))
        continue;
      $ret = include $contentManifestFile;
      if (!is_array($ret))
        continue;
      for ($i = 0; $i < count($ret); $i++) {
        if (!isset($ret[$i]['module']))
          $ret[$i]['module'] = $module;
      }
      $contentAreas = array_merge($contentAreas, (array) $ret);
    }
    return $contentAreas;
  }

  public function updateAction() {

    $storeTable = Engine_Api::_()->getDbtable('mobileContentstores', 'sitestore');
    $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitestore');
    $db = $storeTable->getAdapter();
    $db->beginTransaction();

    try {
      //GET STORE
      $store = $this->_getParam('store');
      $storeObject = $storeTable->fetchRow($storeTable->select()->where('name = ?', $store)->orWhere('mobilecontentstore_id = ?', $store));
      if (null === $storeObject) {
        $message1 = Zend_Registry::get('Zend_Translate')->_('Store is missing');
        throw new Engine_Exception($message1);
      }

      //UPDATE LAYOUT
      if (null !== ($newLayout = $this->_getParam('layout'))) {
        $storeObject->layout = $newLayout;
        $storeObject->save();
      }

      //GET REGISTERED CONTENT AREAS
      $contentRowset = $contentTable->fetchAll($contentTable->select()->where('mobilecontentstore_id = ?', $storeObject->mobilecontentstore_id));

      //GET STRUCTURE
      $structure = Zend_Json::decode($this->_getParam('structure'));

      //DIFF
      $orderIndex = 1;
      $newRowsByTmpId = array();
      $existingRowsByContentId = array();

      foreach ($structure as $element) {
        //GET INFO
        $content_id = @$element['identity'];
        $tmp_content_id = @$element['tmp_identity'];
        $parent_id = @$element['parent_identity'];
        $tmp_parent_id = @$element['parent_tmp_identity'];

        $newOrder = $orderIndex++;

        //SANITY
        if (empty($content_id) && empty($tmp_content_id)) {
          $message2 = Zend_Registry::get('Zend_Translate')->_('content id and tmp content id both empty');
          throw new Exception($message2);
        }

        //GET EXISTING CONTENT ROW (IF ANY)
        $contentRow = null;
        if (!empty($content_id)) {
          $contentRow = $contentRowset->getRowMatching('mobilecontent_id', $content_id);
          if (null === $contentRow) {
            $message3 = Zend_Registry::get('Zend_Translate')->_('content row missing');
            throw new Exception($message3);
          }
        }

        //GET EXISTING PARENT ROW (IF ANY)
        $parentContentRow = null;
        if (!empty($parent_id)) {
          $parentContentRow = $contentRowset->getRowMatching('mobilecontent_id', $parent_id);
        } else if (!empty($tmp_parent_id)) {
          $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
        }

        //EXISTING ROW
        if (!empty($contentRow) && is_object($contentRow)) {
          $existingRowsByContentId[$content_id] = $contentRow;

          //UPDATE ROW
          if (!empty($parentContentRow)) {
            $contentRow->parent_content_id = $parentContentRow->mobilecontent_id;
          }
          if (empty($contentRow->parent_content_id)) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }
					$session = new Zend_Session_Namespace();

          //SET PARAMS
          if (isset($session->setSomething) && in_array($element['name'], $session->setSomething)) {
            $contentRow->params = json_encode($element['params']);
            $contentRow->widget_admin = 0;
					}
          if ($contentRow->type == 'container') {
            $newOrder = array_search($contentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow->order = $newOrder;
          $contentRow->save();
        }
        //NEW ROW
        else {
          if (empty($element['type']) || empty($element['name'])) {
            $message4 = Zend_Registry::get('Zend_Translate')->_('missing name and/or type info');
            throw new Exception($message4);
          }

          if ($element['type'] == 'container') {
            $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow = $contentTable->createRow();
          $contentRow->mobilecontentstore_id = $storeObject->mobilecontentstore_id;
          $contentRow->order = $newOrder;
          $contentRow->type = $element['type'];
          $contentRow->name = $element['name'];
          $contentRow->widget_admin = 0;

          //SET PARENT CONTENT
          if (!empty($parentContentRow)) {
            $contentRow->parent_content_id = $parentContentRow->mobilecontent_id;
          }
          if (empty($contentRow->parent_content_id)) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }

					$contentRow->params = json_encode($element['params']);
					$contentRow->save();
					$newRowsByTmpId[$tmp_content_id] = $contentRow;
				}
			}
      //DELETE ROWS THAT WERE NOT PRESENT IN DATA SENT BACK
      $deletedRowIds = array();
      foreach ($contentRowset as $contentRow) {
        if (empty($existingRowsByContentId[$contentRow->mobilecontent_id])) {
          $deletedRowIds[] = $contentRow->mobilecontent_id;
          $contentRow->delete();
        }
      }
      $this->view->deleted = $deletedRowIds;

      //SEND BACK NEW CONTENT INFO
      $newData = array();
      foreach ($newRowsByTmpId as $tmp_id => $newRow) {
        $newData[$tmp_id] = $storeTable->createElementParams($newRow);
      }
      $this->view->newIds = $newData;

      $this->view->status = true;
      $this->view->error = false;

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }
    if (isset($session->setSomething))
      unset($session->setSomething);
  }

  public function widgetAction() {

    $store_id = $this->_getParam('store_id');
    $mod = $this->_getParam('mod');
    //RENDER BY WIDGET NAME    
    $name = $this->_getParam('name');
    if (null === $name) {
      $message5 = Zend_Registry::get('Zend_Translate')->_('no widget found with name:');
      throw new Exception($message5 . $name);
    }
    if (null !== $mod) {
      $name = $mod . '.' . $name;
    }

    $contentInfoRaw = $this->getContentAreas();
    $contentInfo = array();
    foreach ($contentInfoRaw as $info) {
      $contentInfo[$info['name']] = $info;
    }

    //IT HAS A FORM SPECIFIED IN CONTENT MANIFEST
    if (!empty($contentInfo[$name]['adminForm'])) {
      if (is_string($contentInfo[$name]['adminForm'])) {
        $formClass = $contentInfo[$name]['adminForm'];
        Engine_Loader::loadClass($formClass);
        $this->view->form = $form = new $formClass();
      } else if (is_array($contentInfo[$name]['adminForm'])) {
        $this->view->form = $form = new Engine_Form($contentInfo[$name]['adminForm']);
      } else {
        throw new Core_Model_Exception('Unable to load admin form class');
      }

      //TRY TO SET TITLE IF MISSING
      if (!$form->getTitle()) {
        $form->setTitle('Editing: ' . $contentInfo[$name]['title']);
      }

      //TRY TO SET DESCRIPTION IF MISSING
      if (!$form->getDescription()) {
        $form->setDescription('placeholder');
      }

      $form->setAttrib('class', 'global_form_popup ' . $form->getAttrib('class'));

      //ADD TITLE ELEMENT
      if (!$form->getElement('title')) {
        $form->addElement('Text', 'title', array(
            'label' => 'Title',
            'order' => -100,
        ));
      }
      //ADD SUBMIT BUTTON
      if (!$form->getElement('submit') && !$form->getElement('execute')) {
        $form->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
      }

      //ADD NAME
      $form->addElement('Hidden', 'name', array(
          'value' => $name
      ));

      if (!$form->getElement('cancel')) {
        $form->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
      }

      if (!$form->getDisplayGroup('buttons')) {
        $submitName = ( $form->getElement('execute') ? 'execute' : 'submit' );
        $form->addDisplayGroup(array(
            $submitName,
            'cancel',
                ), 'buttons', array(
        ));
      }

      //FORCE METHOD AND ACTION
      $form->setMethod('post')
              ->setAction($_SERVER['REQUEST_URI']);

      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
        $this->view->values = $form->getValues();
        $this->view->form = null;
				$session = new Zend_Session_Namespace();
				if (isset($session->setSomething))
					unset($session->setSomething);

        $session = new Zend_Session_Namespace();
				$session->setSomething[] = $name;

      }

      return;
    }

    //TRY TO RENDER ADMIN STORE
    if (!empty($contentInfo[$name])) {
      try {
        $structure = array(
            'type' => 'widget',
            'name' => $name,
            'request' => $this->getRequest(),
            'action' => 'admin',
            'throwExceptions' => true,
        );

        //CREATE ELEMENT (WITH STRUCTURE)
        $element = new Engine_Content_Element_Container(array(
                    'elements' => array($structure),
                    'decorators' => array(
                        'Children'
                    )
                ));

        $content = $element->render();
        $this->getResponse()->setBody($content);
        $this->_helper->viewRenderer->setNoRender(true);
        return;
      } catch (Exception $e) {
        
      }
    }

    //JUST RENDER DEFAULT EDITING FORM
    $this->view->form = $form = new Engine_Form(array(
                'title' => $contentInfo[$name]['title'],
                'description' => 'placeholder',
                'method' => 'post',
                'action' => $_SERVER['REQUEST_URI'],
                'class' => 'global_form_popup',
                'elements' => array(
                    array(
                        'Text',
                        'title',
                        array(
                            'label' => 'Title',
                        )
                    ),
                    array(
                        'Button',
                        'submit',
                        array(
                            'label' => 'Save',
                            'type' => 'submit',
                            'decorators' => array('ViewHelper'),
                            'ignore' => true,
                            'order' => 1501,
                        )
                    ),
                    array(
                        'Hidden',
                        'name',
                        array(
                            'value' => $name,
                        )
                    ),
                    array(
                        'Cancel',
                        'cancel',
                        array(
                            'label' => 'cancel',
                            'link' => true,
                            'prependText' => ' or ',
                            'onclick' => 'parent.Smoothbox.close();',
                            'ignore' => true,
                            'decorators' => array('ViewHelper'),
                            'order' => 1502,
                        )
                    )
                ),
                'displaygroups' => array(
                    'buttons' => array(
                        'name' => 'buttons',
                        'elements' => array(
                            'submit',
                            'cancel',
                        ),
                        'options' => array(
                            'order' => 1500,
                        )
                    )
                )
            ));

    if (!empty($contentInfo[$name]['isPaginated'])) {
      $form->addElement('Text', 'itemCountPerStore', array(
          'label' => 'Count',
          'description' => 'Number of items to show.',
          'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
          ),
          'order' => 1000000 - 1,
      ));
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->view->values = $form->getValues();
      $this->view->form = null;
      $session = new Zend_Session_Namespace();
      if (isset($session->setSomething))
        unset($session->setSomething);
      $session->setSomething[] = $name;
    } else {
      $form->populate($this->_getAllParams());
    }
  }

  public function saveAction() {

    $form = new Sitestore_Form_Layout_Content_MobileStore();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $store_id = $values['mobilecontentstore_id'];
      unset($values['mobilecontentstore_id']);

      if (empty($values['url'])) {
        $values['url'] = new Zend_Db_Expr('NULL');
      }

      $storeTable = Engine_Api::_()->getDbtable('mobileContentstores', 'sitestore');
      $storeObject = $storeTable->fetchRow($storeTable->select()->where('name = ?', $store_id)->orWhere('mobilecontentstore_id = ?', $store_id));
      $storeObject->setFromArray($values)->save();
      $form->addNotice($this->view->translate('Your changes have been saved.'));
    }

    $this->getResponse()->setBody($form->render($this->view));
    $this->_helper->layout->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }

  public function deleteAction() {

    $store_id = $this->_getParam('store');
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    $store = Engine_Api::_()->getDbtable('mobilecontentstores', 'sitestore')->find($store_id)->current();
    if (null === $store) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Store not found');
      return;
    }

    if (!$store->custom) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Cannot delete non-custom stores');
      return;
    }

    $storeTable->deleteStore($store);
    $this->view->status = true;
  }
}

?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Abstract.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_Controller_Abstract extends Seaocore_Controller_Action_Standard {

  protected $_fieldType;
  protected $_requireProfileType = false;
  protected $_moduleName;

  public function init() {

    //PARSE MODULE NAME FROM CLASS
    if (!$this->_moduleName) {
      $this->_moduleName = substr(get_class($this), 0, strpos(get_class($this), '_'));
    }

    //TRY TO SET ITEM TYPE TO MODULE NAME (USUALLY AN ITEM TYPE)
    if (!$this->_fieldType) {
      $this->_fieldType = Engine_Api::deflect($this->_moduleName);
    }

    //SEND FILE TYPE TO TPL
    $this->view->fieldType = $this->_fieldType;

    //HACK UP THE VIEW PATS
    $this->view->addHelperPath(dirname(dirname(__FILE__)) . '/views/helpers', 'Fields_View_Helper');
    $this->view->addScriptPath(dirname(dirname(__FILE__)) . '/views/scripts');

    $this->view->addHelperPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/helpers', $this->_moduleName . '_View_Helper');
    $this->view->addScriptPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/scripts');
  }

  //ACTION FOR MANAGING FORMS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitestoreproduct_main');

    //CHECK USER ATHORIZATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET PACKAGE ID, STORE ID AND STORE OBJECT
    $this->view->package_id = $this->_getParam('package_id');
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreform")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'form');
      if (empty($isStoreOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'form');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET THE OPTION ID FROM THE URL
    $this->view->option_id = $option_id = $this->_getParam('option_id');

    //GET THE STORE NAME FROM THE URL 
    $this->view->storename = $store_name = $this->_getParam('pagename');

    //FORM GENERATION
    $this->view->createform = $createform = new Sitestoreform_Form_Create();

     //GET FORM DATA
    $formSelectData = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform')->getFormData($store_id);
    $this->view->formSelectData = $formSelectData;
    
    $formSelectDataValues = $formSelectData->toArray();
    $formSelectDataValues['title'] = Zend_Registry::get('Zend_Translate')->_($formSelectDataValues['title']);
    $formSelectDataValues['description'] = Zend_Registry::get('Zend_Translate')->_($formSelectDataValues['description']);
    
    if (!$this->getRequest()->isPost()) {
      $createform->populate($formSelectDataValues);
    }

    $values = array();
    if ($this->getRequest()->getPost()) {
      $createform->isValid($this->_getAllParams());
    }

    //GET FORM DATA
    $values = $_POST;
    $sitestoreform = Engine_Api::_()->getItem('sitestoreform', $formSelectData->sitestoreform_id);
    $sitestoreform->setFromArray($values);
    $createform->populate($values);
    $sitestoreform->save();

    //TO GET THE OBJECT OF MAP TABLE
    $formMapTable = Engine_Api::_()->fields()->getTable('sitestoreform', 'maps');
    $formMapSelect = $formMapTable->select()->where('option_id =?', $option_id)->orWhere('option_id =?', '0')->order('order ASC');
    $mapData = $formMapTable->fetchAll($formMapSelect);

    //GET THE OBJECT OF META TABLE
    $metaData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMeta($this->_fieldType);

    //GET THE OBJECT OF OPTION TABLE
    $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions($this->_fieldType);

    //GET TOP LEVEL FIELDS
    $topLevelMaps = $mapData->getRowsMatching(array('field_id' => 0, 'option_id' => 0));
    $topLevelFields = array();
    foreach ($topLevelMaps as $map) {
      $field = $map->getChild();
      $topLevelFields[$field->field_id] = $field;
    }
    $this->view->topLevelMaps = $topLevelMaps;
    $this->view->topLevelFields = $topLevelFields;

    if (!$this->_requireProfileType) {
      $this->topLevelOptionId = '0';
      $this->topLevelFieldId = '0';
    } else {
      $topLevelField = array_shift($topLevelFields);
      $this->view->topLevelField = $topLevelField;
      $this->view->topLevelFieldId = $topLevelField->field_id;

      //GET TOP LEVEL OPTIONS
      $topLevelOptions = array();
      foreach ($optionsData->getRowsMatching('field_id', $topLevelField->field_id) as $option) {
        $topLevelOptions[$option->option_id] = $option->label;
      }

      //GET SELECTED OPTION
      if (empty($option_id) || empty($topLevelOptions[$option_id])) {
        $option_id = current(array_keys($topLevelOptions));
      }

      //$topLevelOption = $optionsData->getRowMatching('option_id', $option_id);
      //$this->view->topLevelOption = $topLevelOption;
      $this->view->topLevelOptionId = $option_id;

      //GET SECOND LEVEL MAPS
      $secondLevelMaps = array();
      $secondLevelFields = array();
      if (!empty($option_id)) {
        $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
        $canAddquestions = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusinessform.add.question', 1);
        if(count($secondLevelMaps) || !empty($canAddquestions)) {
					$secondLevelMaps = $mapData->getRowsMatching('field_id', $field->field_id);
        }
        if (!empty($secondLevelMaps)) {
          foreach ($secondLevelMaps as $map) {
            $secondLevelFields[$map->child_id] = $map->getChild();
          }
        }
      }
      $this->view->secondLevelMaps = $secondLevelMaps;
      $this->view->secondLevelFields = $secondLevelFields;
    }
  }

  //ACTION FOR CREATING NEW FIELDS
  public function fieldCreateAction() {

    global $sitestoreform_fieldCreate;
    $getPackageFormQuestion = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreform');
    if ((!$this->_helper->requireUser()->isValid()) || empty($getPackageFormQuestion))
      return;

    if (empty($sitestoreform_fieldCreate)) {
      return;
    }

    //GET STORE ID AND STORE OBJECT
    $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //PACKAGE BASE PRIYACY START
    if (!empty($sitestore)) {
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreform")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'form');
        if (empty($isStoreOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'form');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    if ($this->_requireProfileType || $this->_getParam('option_id')) {
      $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    } else {
      $option = null;
    }

    $FormClass = 'Sitestoreform_Form_Field';

    //CREATE FORM
    $this->view->form = $form = new $FormClass();
    $form->setTitle('Add a Question');

    $formMapTable = Engine_Api::_()->fields()->getTable('sitestoreform', 'maps');
    $formMapSelect = $formMapTable->select()->where('option_id =?', $option->option_id)->order('order DESC');
    $mapData = $formMapTable->fetchRow($formMapSelect);

    $order = 0;
    if (!empty($mapData)) {
      $order = $mapData->order;
    }

    // GET FIELD DATA FOR AUTO SUGGESTION
    $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType);
    $fieldList = array();
    $fieldData = array();
    foreach (Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType) as $field) {
      if ($field->type == 'profile_type')
        continue;

      //IGNORE IN THE FIELD AS WE HAVE SELECTED
      foreach ($fieldMaps as $map) {
        if ((!$option || !$map->option_id || $option->option_id == $map->option_id ) && $field->field_id == $map->child_id) {
          continue 2;
        }
      }

      $fieldList[] = $field;
      $fieldData[$field->field_id] = $field->label;
    }

    $sitestoreModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    $this->view->fieldList = $fieldList;
    $this->view->fieldData = $fieldData;

    if (!$this->getRequest()->isPost()) {
      $form->populate($this->_getAllParams());
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //CREATE THE ROW IN THE META TABLE
    $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
                        'option_id' => ( is_object($option) ? $option->option_id : '0' ),
                            ), $this->getRequest()->getPost()));
    $field->option_id = $this->_getParam('option_id');
    $field->save();
    $option_id = $this->_getParam('option_id');
    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->option = is_object($option) ? $option->toArray() : array('option_id' => '0');
    $this->view->form = null;

    //RE-RNDER ALL MAPS THAT HAVE THIS FIELD AS A PARENT OR CHILD
    $maps = array_merge(
                    Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id), Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach ($maps as $map) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }

    $order++;
    $formMapSelect = $formMapTable->select()->where('option_id = ?', $option->option_id)->where($formMapTable->info('name') . '.order = ?', 9999);

    $Data = $formMapTable->fetchAll($formMapSelect);
    $mapData = $formMapTable->getMaps();
    foreach ($Data as $data => $ids) {
      $map = $mapData->getRowMatching(array(
                  'field_id' => $ids['field_id'],
                  'option_id' => $ids['option_id'],
                  'child_id' => $ids['child_id'],
              ));
      $map->order = $order;
      $map->save();
    }
    $mapData->getTable()->flushCache();
    //$formMapTable->update(array('order' => $order), array('option_id = ?' => $option->option_id, 'order = ?' => 9999 ));
    $this->view->htmlArr = $html;
  }

  //ACTION FOR FIELD EDITION
  public function fieldEditAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET STORE ID AND STORE OBJECT
    $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreform")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'form');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

    //CHECK TYPE PARAM AND GET FORM CLASS
    $cfType = $this->_getParam('type', $field->type);
    $adminFormClass = null;
    if (!empty($cfType)) {
      $adminFormClass = Engine_Api::_()->fields()->getFieldInfo($cfType, 'adminFormClass');
    }
    if (empty($adminFormClass) || !@class_exists($adminFormClass)) {
      $FormClass = 'Sitestoreform_Form_Field';
    }
    $FormClass = 'Sitestoreform_Form_Field';

    //CREATE FORM
    $this->view->form = $form = new $FormClass();
    $form->setTitle('Edit Question');

    //CHECK METHOD/DATA
    if (!$this->getRequest()->isPost()) {
      $form->populate($field->toArray());
      $form->populate($this->_getAllParams());
      if (is_array($field->config)) {
        $form->populate($field->config);
      }
      $this->view->search = $field->search;
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    Engine_Api::_()->fields()->editField($this->_fieldType, $field, $form->getValues());
    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->form = null;

    //RE-RENDER ALL MAPS THAT HAVE THIS FIELD AS A PARENT OR CHILD
    $maps = array_merge(
                    Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id), Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach ($maps as $map) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  //ACTION FOR FIELD DELETION
  public function fieldDeleteAction() {

    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);
    $this->view->form = $form = new Engine_Form(array(
                'method' => 'post',
                'action' => $_SERVER['REQUEST_URI'],
                'elements' => array(
                    array(
                        'type' => 'submit',
                        'name' => 'submit',
                    )
                )
            ));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $this->view->status = true;
    Engine_Api::_()->fields()->deleteField($this->_fieldType, $field);
  }

  //ACTON FOR MAP CREATION
  public function mapCreateAction() {

    //GET THE OPTIONS FROM THE OPTION TABLE
    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    $child_id = $this->_getParam('child_id');
    $label = $this->_getParam('label');
    $child = null;

    if ($child_id) {
      $child = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType)->getRowsMatching('field_id', $child_id);
    } else if ($label) {
      $child = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType)->getRowsMatching('label', $label);
      if (count($child) > 1) {
        throw new Fields_Model_Exception('Duplicate label');
      }
      $child = current($child);
    } else {
      throw new Fields_Model_Exception('No child field specified');
    }

    if (!($child instanceof Fields_Model_Meta)) {
      throw new Fields_Model_Exception('No child field found');
    }

    $fieldMap = Engine_Api::_()->fields()->createMap($child, $option);
    $this->view->fieldMap = $fieldMap->toArray();

    //RE-RENDER ALL MAPS THAT HAVE THIS FIELD AS A PARENT OR CHILD
    $maps = array_merge(
                    Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id), Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
    );
    $html = array();
    foreach ($maps as $map) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  //ACTON FOR MAP DELETION
  public function mapDeleteAction() {

    $map = Engine_Api::_()->fields()->getMap($this->_getParam('child_id'), $this->_getParam('option_id'), $this->_fieldType);
    Engine_Api::_()->fields()->deleteMap($map);
  }

  //ACTON FOR OPTION CREATION
  public function optionCreateAction() {

    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);
    $label = $this->_getParam('label');

    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CREATE NEW OPTION
    $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
                'label' => $label,
            ));
    $this->view->status = true;
    $this->view->option = $option->toArray();
    $this->view->field = $field->toArray();

    //RE-RENDER ALL MAPS THAT HAVE THIS OPTIONS'S FIELD AS A PARENT OR CHILD ID
    $maps = array_merge(
                    Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id), Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
    );
    $html = array();
    foreach ($maps as $map) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }

    $this->view->htmlArr = $html;
  }

  //ACTON FOR OPTION EDITION
  public function optionEditAction() {

    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    $field = Engine_Api::_()->fields()->getField($option->field_id, $this->_fieldType);

    //FORM CREATE
    $this->view->form = $form = new Fields_Form_Admin_Option();
    $form->submit->setLabel('Edit Choice');

    //CHECK METHOD/DATA
    if (!$this->getRequest()->isPost()) {
      $form->populate($option->toArray());
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    Engine_Api::_()->fields()->editOption($this->_fieldType, $option, $form->getValues());

    //PROCESS
    $this->view->status = true;
    $this->view->form = null;
    $this->view->option = $option->toArray();
    $this->view->field = $field->toArray();

    //RE-RENDER ALL MAPS THAT HAVE THIS OPTIONS FIELD AS A PARENT OR CHILD ID
    $maps = array_merge(
                    Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id), Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
    );

    $html = array();
    foreach ($maps as $map) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  //ACTION FOR DELETING THE OPTIONS
  public function optionDeleteAction() {

    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    //DELETE ALL VALUES
    $option_id = $option->option_id;
    Engine_Api::_()->fields()->deleteOption($this->_fieldType, $option);
  }

  //ACTION FOR ORDING THE FORM ELEMENTS
  public function orderAction() {

    if (!$this->getRequest()->isPost()) {
      return;
    }

    //GET PARAMS
    $fieldOrder = (array) $this->_getParam('fieldOrder');
    $optionOrder = (array) $this->_getParam('optionOrder');

    //SORT
    ksort($fieldOrder, SORT_NUMERIC);
    ksort($optionOrder, SORT_NUMERIC);

    //GET DATA
    $mapData = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType);
    $metaData = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType);
    $optionData = Engine_Api::_()->fields()->getFieldsOptions($this->_fieldType);

    //PARSE FIELDS (MAP)
    $i = 0;
    foreach ($fieldOrder as $index => $ids) {
      $map = $mapData->getRowMatching(array(
                  'field_id' => $ids['parent_id'],
                  'option_id' => $ids['option_id'],
                  'child_id' => $ids['child_id'],
              ));
      $map->order = ++$i;
      $map->save();
    }

    //PARSE OPTIONS
    $i = 0;
    foreach ($optionOrder as $index => $ids) {
      $option = $optionData->getRowMatching('option_id', $ids['suboption_id']);
      $option->order = ++$i;
      $option->save();
    }

    //FLUSH CASH
    $mapData->getTable()->flushCache();
    $metaData->getTable()->flushCache();
    $optionData->getTable()->flushCache();

    $this->view->status = true;
  }

}
?>
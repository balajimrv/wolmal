<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Abstract.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Controller_Abstract extends Core_Controller_Action_Standard {

    protected $_fieldType;
    protected $_requireProfileType = false;
    protected $_moduleName;

    public function init() {

        $this->_fieldType = 'sitestoreproduct_cartproduct';

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
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

        //CHECK USER ATHORIZATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SELECTED TAB
        $this->view->sitestores_view_menu = 3;

        //GET PACKAGE ID, PAGE ID AND PAGE OBJECT
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        //CHECK FOR CONFIGURABLE PRODUCT
        if ($sitestoreproduct->product_type != 'configurable' && $sitestoreproduct->product_type != 'virtual')
            return $this->_forward('notfound', 'error', 'core');
        //END CHECK FOR CONFIGURABLE PRODUCT
        //GET THE OPTION ID FROM THE URL
        $this->view->option_id = $option_id = $this->_getParam('option_id');

        $this->view->published = $sitestoreproduct->search;

        //TO GET THE OBJECT OF MAP TABLE
        $formMapTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'maps');
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

            $this->view->topLevelOptionId = $option_id;

            //GET SECOND LEVEL MAPS
            $secondLevelMaps = array();
            $secondLevelFields = array();
            if (!empty($option_id)) {
                $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
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

        if ((!$this->_helper->requireUser()->isValid()))
            return;

        //GET PAGE ID AND PAGE OBJECT
        $product_id = $this->_getParam('product_id', null);
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $optionId = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id);
        if ($this->_requireProfileType || $optionId) {
            $option = Engine_Api::_()->fields()->getFieldsOptions($this->_fieldType)->getRowMatching('option_id', $optionId);
            if (null === $option || !($option instanceof Fields_Model_Option)) {
                Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options')->getOptions()->getTable()->flushCache();
                Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'maps')->getMaps()->getTable()->flushCache();
                Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'meta')->getMeta()->getTable()->flushCache();
                $this->_helper->redirector->gotoRoute(array('route' => 'default'));

//        $table = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
//        $name = $table->info('name'); 
//        $select = $table->select()->where('option_id = ?', $optionId)->limit(1);
//        $option = $table->fetchRow($select);      
            } else {
                $option = Engine_Api::_()->fields()->getOption($optionId, $this->_fieldType);
            }
        } else {
            $option = null;
        }

        $FormClass = 'Sitestoreproduct_Form_Field';

        //CREATE FORM
        $this->view->form = $form = new $FormClass();
        $form->setTitle('Add Product Attribute');

        $formMapTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'maps');
        if (!empty($option)) {
            $tempOptionId = $option->option_id;
        }
//    if(!empty($option)) {
        $formMapSelect = $formMapTable->select()->where('option_id =?', $tempOptionId)->order('order DESC');
        $mapData = $formMapTable->fetchRow($formMapSelect);
//    }

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

        $this->view->fieldList = $fieldList;
        $this->view->fieldData = $fieldData;

        if (isset($_GET) && isset($_GET['type'])) {
            if ($_GET['type'] == 'checkbox') {
//        if(!empty($sitestoreproduct->stock_unlimited)){
//        $form->addElement('Radio', 'quantity_unlimited', array(
//          'label' => Zend_Registry::get('Zend_Translate')->_("Unlimited Quantity"),
//          'description' => Zend_Registry::get('Zend_Translate')->_("Do you have unlimited quantity of this attribute in your stock?"),
//          'multiOptions' => array(
//              "1" => "Yes", "0" => "No"
//          ),
//          'value' => 1,
//          'onclick' => 'showStock();',
//          'filters' => array(
//              'StripTags',
//              new Engine_Filter_Censor(),
//      )));
//      $form->addElement('Text', 'quantity', array(
//          'label' => Zend_Registry::get('Zend_Translate')->_('In Stock Quantity'),
//          'maxlength' => 5,
//          'description' => Zend_Registry::get('Zend_Translate')->_('You have unlimited quantites available for this product.'),
//          'validators' => array(
//              array('Int', false),
//              array('GreaterThan', true, array(-1))
//          ),
//          'filters' => array(
//              'StripTags',
//              new Engine_Filter_Censor(),
//      )));
//        }
//        else{
//          $form->addElement('Text', 'quantity', array(
//          'label' => Zend_Registry::get('Zend_Translate')->_('In Stock Quantity'),
//          'maxlength' => 5,
//          'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('You have %s total inventory stock available for this product.'), $sitestoreproduct->in_stock ),
//          'required' => true,
//          'allowedEmpty' => false,
//          'validators' => array(
//              array('Int', false),
//              array('GreaterThan', true, array(-1))
//          ),
//          'filters' => array(
//              'StripTags',
//              new Engine_Filter_Censor(),
//      )));
//        }
                $form->addElement('Radio', 'price_increment', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_("Increment / Decrement"),
                    'description' => Zend_Registry::get('Zend_Translate')->_('Do you want to increment or decrement the basic price of the product by the amount you will enter in the below "Price" field ?'),
                    'multiOptions' => array(
                        "1" => "Increment", "0" => "Decrement"
                    ),
                    'value' => 1,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                )));

                $priceAfterDiscount = Engine_Api::_()->sitestoreproduct()->getProductDiscount($sitestoreproduct, '', '', 1);
                $localeObject = Zend_Registry::get('Locale');
                $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
                $form->addElement('Text', 'price', array(
                    'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
                    'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("The basic price of this product is %s.  Enter the amount by which you want to increment or decrement the price of the product for this attribute."), $priceAfterDiscount),
                    'allowEmpty' => true,
                    'maxlength' => 12,
                    'value' => 0.00,
                    'validators' => array(
                        //array('NotEmpty', true),
                        array('GreaterThan', false, array(-1))
                    ),
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                )));
            }
        }
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
            'required' => 1,
            'display' => 1,), $this->getRequest()->getPost()));
        $field->option_id = $this->_getParam('option_id');
        $field->save();

        if ($field->type == 'checkbox' || $field->type == 'multi_checkbox' || $field->type == 'text' || $field->type == 'textarea') {
            $field->required = 0;
            $field->save();
        }

        if ($field->type == 'checkbox') {
            $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
            $optionRow = $formOptionTable->createRow();
            $optionRow->field_id = $field->field_id;
            $optionRow->label = $field->label;
//      $optionRow->quantity = $_POST['quantity'];
//      $optionRow->quantity_unlimited = $_POST['quantity_unlimited'];
            $optionRow->price = $_POST['price'];
            $optionRow->price_increment = $_POST['price_increment'];
            $optionRow->save();
        }

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
//    if(!empty($option)) {
        $formMapSelect = $formMapTable->select()->where('option_id = ?', $tempOptionId)->where($formMapTable->info('name') . '.order = ?', 9999);

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
//    }

        $this->view->htmlArr = $html;
    }

    //ACTION FOR FIELD EDITION
    public function fieldEditAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PAGE ID AND PAGE OBJECT
        $product_id = $this->_getParam('product_id', null);
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

        //CHECK TYPE PARAM AND GET FORM CLASS
        $cfType = $this->_getParam('type', $field->type);
        $adminFormClass = null;
        if (!empty($cfType)) {
            $adminFormClass = Engine_Api::_()->fields()->getFieldInfo($cfType, 'adminFormClass');
        }
        if (empty($adminFormClass) || !@class_exists($adminFormClass)) {
            $FormClass = 'Sitestoreproduct_Form_Field';
        }
        $FormClass = 'Sitestoreproduct_Form_Field';

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
        $maps = array_merge(Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id), Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
        );
        $html = array();
        foreach ($maps as $map) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
        }
        $this->view->htmlArr = $html;
    }

    //ACTON FOR MAP DELETION
    public function mapDeleteAction() {

        $child_id = $this->_getParam('child_id');

        // Delete the created combinations of the corresponding option
        if ($child_id != 0) {

            $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
            $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
            $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
            $combinationAttributes = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')
                            ->where('field_id =?', $child_id)
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

            if (!empty($combinationAttributes)) {
                foreach ($combinationAttributes as $attribute_id) {
                    $combination_ids = $combinationAttributeMapsTable->select()->from($combinationAttributeMapsTable->info('name'), 'combination_id')
                                    ->where("attribute_id = ?", $attribute_id)
                                    ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

                    foreach ($combination_ids as $combination_id) {

                        $combinationAttributeIds = $combinationAttributeMapsTable->select()->from($combinationAttributeMapsTable->info('name'), 'attribute_id')
                                        ->where('combination_id =?', $combination_id)
                                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

                        if (count($combinationAttributeIds) != 0) {
                            foreach ($combinationAttributeIds as $combination_attribute_id) {
                                $combinationAttributesTable->delete(array('attribute_id = ?' => $combination_attribute_id));
                            }
                        }

                        $combinationAttributeMapsTable->delete(array('combination_id = ?' => $combination_id));
                        $combinationsTable->delete(array('combination_id = ?' => $combination_id));
                    }
                }
            }
        }
        $map = Engine_Api::_()->fields()->getMap($child_id, $this->_getParam('option_id'), $this->_fieldType);
        Engine_Api::_()->fields()->deleteMap($map);
    }

    //ACTON FOR OPTION CREATION
    public function optionCreateAction() {

        $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);
        $field_id = $this->_getParam('field_id');
        $product_id = $this->_getParam('product_id');
        $total_quantity = 0;
        $label = $this->_getParam('label');


        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        //$formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
//    if(empty($product->stock_unlimited) && !empty($product->in_stock)){
//      $option_select = $formOptionTable->select()->from($formOptionTable->info('name'), 'quantity')
//                                ->where('field_id =?', $field_id);
//      $options = $formOptionTable->fetchAll($option_select);
//    
//      if($options){
//      foreach($options as $option){
//            $total_quantity +=  $option->quantity;
//      }
//      if($total_quantity >= $product->in_stock)
//      {
//        echo Zend_Json::encode(array('error_message' => 1));
//        exit();
//      }
//      }
//    }
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
        $maps = array_merge(Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id), Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
        );
        $html = array();
        foreach ($maps as $map) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
        }

        $this->view->htmlArr = $html;
        $this->view->field_type = $field->type;
        $this->view->allow_combinations = $allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);
    }

    //ACTON FOR OPTION EDITION
    public function optionEditAction() {

    // Flush cache when editing option (Fix crashing)
        Engine_Api::_()->fields()
                ->getTable(Engine_Api::_()->fields()->getFieldType($this->_fieldType), 'options')
                ->flushCache();

        $option_id = $this->_getParam('option_id');
        $option = Engine_Api::_()->fields()->getOption($option_id, $this->_fieldType);
        $field = Engine_Api::_()->fields()->getField($option->field_id, $this->_fieldType);
        $product_id = $this->_getParam('product_id');
        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $priceAfterDiscount = Engine_Api::_()->sitestoreproduct()->getProductDiscount($product, '', '', 1);
        //$quantityCheck = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.quantity', 0);
        $allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);

        //FORM CREATE
        if ($field->type == 'select' && !empty($allowCombinations)) {
            $this->view->form = $form = new Fields_Form_Admin_Option();
        } else {
            $this->view->form = $form = new Sitestoreproduct_Form_OptionDetail(array('inStock' => $product->in_stock, 'stockUnlimited' => $product->stock_unlimited, 'price' => $priceAfterDiscount));
            $form->addElement('Text', 'label', array(
                'label' => 'Choice Label',
                'required' => true,
                'allowEmpty' => false,
                'order' => 0,
            ));
        }
        $form->submit->setLabel('Edit Choice');

        //CHECK METHOD/DATA
        if (!$this->getRequest()->isPost()) {
            $form->populate($option->toArray());
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //Engine_Api::_()->fields()->editOption($this->_fieldType, $option, $form->getValues());

        $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');

        /* CHECK VALIDATION IF THE TOTAL QUANTITY IS GREATER THAN THE PRODUCT QUNATITY */
//    if (!empty($quantityCheck)) {
//      if (empty($_POST['quantity_unlimited']) && empty($_POST['quantity'])) {
//        $error = Zend_Registry::get('Zend_Translate')->_(' "*In Stock Quantity" Please complete this Field. Enter Value Other than Zero.');
//        $form->getDecorator('errors')->setOption('escape', false);
//        $form->addError($error);
//        return;
//      }
//      if (empty($product->stock_unlimited) && !empty($product->in_stock)) {
//        $option_select = $formOptionTable->select()->from($formOptionTable->info('name'), 'quantity')
//                ->where('field_id =?', $option->field_id)
//                ->where('option_id !=?', $option_id);
//        $options = $formOptionTable->fetchAll($option_select);
//
//        if ($options) {
//          foreach ($options as $option_qunatity) {
//            $total_quantity += $option_qunatity->quantity;
//          }
//        }
//        $total_quantity += $_POST['quantity'];
//
//        if ($total_quantity > $product->in_stock) {
//          $error = Zend_Registry::get('Zend_Translate')->_('Total quantity should be less than the original quantity of the product.');
//          $form->getDecorator('errors')->setOption('escape', false);
//          $form->addError($error);
//          return;
//        }
//      }
//    }
        /* END VALIDATION WORK */

//    if(!empty($quantityCheck))
//      $formOptionTable->update(array(
//                            'label' => $_POST['label'], 'quantity_unlimited' => $_POST['quantity_unlimited'], 'quantity' => $_POST['quantity'], 'price' => $_POST['price'], 'price_increment' => $_POST['price_increment']), array('option_id =?' => $option_id, 'field_id =?' => $option->field_id));
//    else
        if ($field->type == 'select' && !empty($allowCombinations))
            $formOptionTable->update(array(
                'label' => $_POST['label']), array('option_id =?' => $option_id, 'field_id =?' => $option->field_id));
        else
            $formOptionTable->update(array(
                'label' => $_POST['label'], 'price' => $_POST['price'], 'price_increment' => $_POST['price_increment']), array('option_id =?' => $option_id, 'field_id =?' => $option->field_id));


        $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
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

        // Delete the created combinations of the corresponding option
        if ($option != 0) {

            $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
            $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
            $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
            $combinationAttributes = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')
                            ->where('combination_attribute_id =?', $option_id)
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

            if (!empty($combinationAttributes)) {
                foreach ($combinationAttributes as $attribute_id) {
                    $combination_ids = $combinationAttributeMapsTable->select()->from($combinationAttributeMapsTable->info('name'), 'combination_id')
                                    ->where("attribute_id = ?", $attribute_id)
                                    ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

                    foreach ($combination_ids as $combination_id) {

                        $combinationAttributeIds = $combinationAttributeMapsTable->select()->from($combinationAttributeMapsTable->info('name'), 'attribute_id')
                                        ->where('combination_id =?', $combination_id)
                                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

                        if (count($combinationAttributeIds) != 0) {
                            foreach ($combinationAttributeIds as $combination_attribute_id) {
                                $selectedCombinationIds = $combinationAttributeMapsTable->select()->from($combinationAttributeMapsTable->info('name'), 'combination_id')
                                                ->where("attribute_id = ?", $combination_attribute_id)
                                                ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                                if ((count($selectedCombinationIds) == 1) ||
                                        (count(array_diff($combination_ids, $selectedCombinationIds)) == 0))
                                    $combinationAttributesTable->delete(array('attribute_id = ?' => $combination_attribute_id));
                                else
                                    continue;
                            }
                        }

                        $combinationAttributeMapsTable->delete(array('combination_id = ?' => $combination_id));
                        $combinationsTable->delete(array('combination_id = ?' => $combination_id));
                    }
                }
            }
        }

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

    public function makeOrderAction() {

        $combination_attribute_ids = array();

        //SMOOTHBOX
        if (null == $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        $product_id = $this->_getParam('product_id', 0);
        $product = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id);

        $this->view->form = $form = new Sitestoreproduct_Form_Custom_Standard(array(
            'item' => 'sitestoreproduct_cartproduct',
            'topLevelId' => 1,
            'topLevelValue' => $option_id,
            'productId' => $product_id,
            'hideSelect' => 1,
        ));

        if ($product->product_type == 'virtual' && !empty($_POST['is_calendar_allow'])) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
            $oldTz = date_default_timezone_get();
            if (!empty($viewer_id))
                date_default_timezone_set($viewer->timezone);
            $start = strtotime($_POST['starttime']['date']);
            $end = strtotime($_POST['endtime']['date']);
            date_default_timezone_set($oldTz);

            $starttime = new Engine_Form_Element_CalendarDateTime('starttime');
            $starttime->setValue(date('Y-m-d H:i:s', $start));
            $form->addElement($starttime);

            $endtime = new Engine_Form_Element_CalendarDateTime('endtime');
            $endtime->setValue(date('Y-m-d H:i:s', $end));
            $form->addElement($endtime);
        }

        $form->populate($_POST);
        $formValues = $form->getValues();
        unset($formValues['submit']);
        $formValues['quantity'] = !empty($_POST['quantity']) ? $_POST['quantity'] : 0;

        //CHECK POST
        if (!$this->getRequest()->isPost())
            return;

        /* CHECK QUANTITY IS AVAILABLE FOR THE SELECTED CONFIGURATION OR NOT */
        //$formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');

        foreach ($formValues as $key => $value) {

            $parts = explode('_', $key);
//      if (!((count($parts) == 2) && ($parts[0] == 'select')))
//        continue;
            if ((count($parts) == 3 || (count($parts) == 2 && $parts[0] == 'select'))) {

                if (count($parts) == 3) {
                    $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
                    if (!is_array($value)) {
                        $formMetaTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'meta');
                        $field_type = $formMetaTable->select()->from($formMetaTable->info('name'), 'type')
                                        ->where('field_id =?', $parts[2])
                                        ->query()->fetchColumn();
                        if ($field_type == 'checkbox' && !empty($value)) {

                            $value = $formOptionTable->select()->from($formOptionTable->info('name'), 'option_id')->where('field_id =?', $parts[2])->query()->fetchColumn();
                            //if (!empty($value))
                            $formValues[$key] = $value;
//            else
//              $value = $formValues[$key];
                        }
                    }
                } elseif (count($parts) == 2 && $parts[0] == 'select') {
                    if (!empty($value)) {
                        $attribute_id = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')->where('product_id =?', $product_id)->where('field_id =?', $parts[1])->where('combination_attribute_id =?', $value)->query()->fetchColumn();
                        $combination_attribute_ids[] = $attribute_id;
                    }
                }
            }
        }

        if (count($combination_attribute_ids) != 0) {
            $combination_quantity = Engine_Api::_()->sitestoreproduct()->getCombinationQuantity($combination_attribute_ids);
            if (empty($combination_quantity) || $combination_quantity < $formValues['quantity']) {
                $this->view->error_message = 1;
                return;
            }
        }

        /* END QUANTITY CHECK WORK HERE */

        if ($sitestoreproduct->product_type == 'virtual' && !empty($_POST) && !empty($_POST['is_calendar_allow'])) {
            $viewerSelectedDateTime = array('starttime' => $formValues['starttime'], 'endtime' => $formValues['endtime']);
            unset($formValues['is_calendar_allow']);
            unset($formValues['starttime']);
            unset($formValues['endtime']);
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                //BEGIN THE CART ENTRY WORK
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $cartTable = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct');
                $cartProductTable = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
                $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
                $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

                if (!empty($viewer_id)) {

                    $cart_id = $cartTable->getCartId($viewer_id);

                    if (!empty($cart_id) && !empty($directPayment) && !empty($isDownPaymentEnable)) {
                        $productIds = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCartProductIds($cart_id);
                        $product_ids = implode(",", $productIds);
                        $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
                        $selectedProductDownpaymentValue = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($product_id, 'downpayment_value');
                        if (empty($selectedProductDownpaymentValue) && !empty($cartProductPaymentType)) {
                            return $this->_helper->redirector->gotoRoute(array('action' => 'cart', 'cartproduct' => 1), "sitestoreproduct_product_general", false);
                        } else if (!empty($selectedProductDownpaymentValue) && empty($cartProductPaymentType)) {
                            return $this->_helper->redirector->gotoRoute(array('action' => 'cart', 'cartproduct' => 2), "sitestoreproduct_product_general", false);
                        }
                    }

                    if (empty($cart_id)) {
                        $cart = $cartTable->createRow();
                        $cart->owner_id = $viewer_id;
                        $cart->save();
                        $cart_id = $cart->cart_id;
                    }

                    $duplicateOrder = 0;

                    $cartProductIds = $cartProductTable->getConfiguration(array("cartproduct_id"), $product_id, $cart_id);
                    if (empty($cartProductIds)) {
                        $cartProduct = $cartProductTable->createRow();
                        $cartProduct->cart_id = $cart_id;
                        $cartProduct->product_id = $product_id;
                        $cartProduct->quantity = $formValues['quantity']; //$product->min_order_quantity;
                        if ($sitestoreproduct->product_type == 'virtual' && !empty($viewerSelectedDateTime)) {
                            $cartProduct->other_info = serialize($viewerSelectedDateTime);
                        }
                        $cartProduct->save();
                    } elseif (!empty($cartProductIds)) {

                        $duplicateOrder = 0;

                        //GET CART PRODUCT IDS
                        foreach ($cartProductIds as $cartProductId) {
                            $cartProduct = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProductId);
                            $values = Engine_Api::_()->fields()->getFieldsValues($cartProduct);
                            $valueRows = $values->getRowsMatching(array(
                                'item_id' => $cartProduct->getIdentity(),
                            ));

                            $valueRowsArray = array();

                            foreach ($valueRows as $key => $valueRow) {
//                $valueRow->field_id = "1_$option_id" . '_' . "$valueRow->field_id";
                                $valueRow->field_id = "select" . '_' . "$valueRow->field_id";
                                if (!array_key_exists($valueRow->field_id, $valueRowsArray)) {
                                    $valueRowsArray[$valueRow->field_id] = $valueRow->value;
                                } else {
                                    if (is_array($valueRowsArray[$valueRow->field_id])) {
                                        $newArray = $valueRowsArray[$valueRow->field_id];
                                        array_push($newArray, $valueRow->value);
                                        $valueRowsArray[$valueRow->field_id] = $newArray;
                                    } else {
                                        $newArray = array();
                                        $newArray[] = $valueRowsArray[$valueRow->field_id];
                                        array_push($newArray, $valueRow->value);
                                        $valueRowsArray[$valueRow->field_id] = $newArray;
                                    }
                                }
                            }

                            $array_diff_assoc = Engine_Api::_()->sitestoreproduct()->multidimensional_array_diff($formValues, $valueRowsArray);


                            if ($sitestoreproduct->product_type == 'virtual' && !empty($viewerSelectedDateTime)) {
                                $oldViewerSelectionDate = unserialize($cartProduct->other_info);
                                if ($oldViewerSelectionDate != $viewerSelectedDateTime)
                                    $array_diff_assoc = false;
                            }

                            if ($array_diff_assoc) {
                                $cartProduct->quantity = $cartProduct->quantity + $formValues['quantity'];
                                $cartProduct->save();
                                $duplicateOrder = 1;
                                break;
                            }
                        }

                        if (empty($duplicateOrder)) {
                            $cartProduct = $cartProductTable->createRow();
                            $cartProduct->cart_id = $cart_id;
                            $cartProduct->product_id = $product_id;
                            $cartProduct->quantity = $formValues['quantity'];
                            if ($sitestoreproduct->product_type == 'virtual' && !empty($viewerSelectedDateTime)) {
                                $cartProduct->other_info = serialize($viewerSelectedDateTime);
                            }
                            $cartProduct->save();
                        }
                    }

                    if (empty($duplicateOrder)) {
                        $form->setItem($cartProduct);
                        $form->saveValues();
                    }
                } else {
                    $tempUserCart = array();
                    $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');

                    if (!isset($session->sitestoreproduct_guest_user_cart))
                        $session->sitestoreproduct_guest_user_cart = '';
                    else
                        $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);

                    // CHECK PRODUCT PAYMENT TYPE => DOWNPAYMENT OR NOT
                    if (!empty($directPayment) && !empty($isDownPaymentEnable) && !empty($tempUserCart)) {
                        $productIds = array();
                        foreach ($tempUserCart as $cart_product_id => $values) {
                            $productIds[] = $cart_product_id;
                        }
                        $product_ids = implode(",", $productIds);
                        $selectedProductDownpaymentValue = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($product_id, 'downpayment_value');
                        $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
                        if (empty($selectedProductDownpaymentValue) && !empty($cartProductPaymentType)) {
                            return $this->_helper->redirector->gotoRoute(array('action' => 'cart', 'cartproduct' => 1), "sitestoreproduct_product_general", false);
                        } else if (!empty($selectedProductDownpaymentValue) && empty($cartProductPaymentType)) {
                            return $this->_helper->redirector->gotoRoute(array('action' => 'cart', 'cartproduct' => 2), "sitestoreproduct_product_general", false);
                        }
                    }

                    // PRODUCT IS ALREADY IN VIEWER CART OR NOT
                    if (is_array($tempUserCart) && array_key_exists($product_id, $tempUserCart)) {

                        $valueRowsArray = array();
                        $duplicateOrder = 0;
                        foreach ($tempUserCart[$product_id]['config'] as $key => $valueRow) {

                            if ($sitestoreproduct->product_type == 'virtual' && !empty($viewerSelectedDateTime)) {
                                $formValues = array_merge($formValues, $viewerSelectedDateTime);
                            }

                            $array_diff_assoc = Engine_Api::_()->sitestoreproduct()->multidimensional_array_diff($formValues, $valueRow);
                            //unset($array_diff_assoc['quantity']);

                            if (!empty($array_diff_assoc)) {
                                $tempUserCart[$product_id]['config'][$key]['quantity'] = $tempUserCart[$product_id]['config'][$key]['quantity'] + $formValues['quantity'];
                                $duplicateOrder = 1;
                                break;
                            }
                        }

                        if (empty($duplicateOrder)) {
                            //$formValues['quantity'] = $product->min_order_quantity;
                            if ($sitestoreproduct->product_type == 'virtual' && !empty($viewerSelectedDateTime)) {
                                $formValues = array_merge($formValues, $viewerSelectedDateTime);
                            }
                            $tempUserCart[$product_id]['config'][] = $formValues;
                        }
                    } else {
                        //$formValues['quantity'] = $product->min_order_quantity;
                        if ($sitestoreproduct->product_type == 'virtual' && !empty($viewerSelectedDateTime)) {
                            $formValues = array_merge($formValues, $viewerSelectedDateTime);
                        }
                        $tempUserCart[$product_id]['store_id'] = $product->store_id;
                        $tempUserCart[$product_id]['type'] = $product->product_type;
                        $tempUserCart[$product_id]['quantity'] = 0;
                        $tempUserCart[$product_id]['config'] = array();
                        $tempUserCart[$product_id]['config'][] = $formValues;
                    }

                    $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
            if (!empty($session->sitestoreproductCartCouponDetail)) {
                $session->sitestoreproductCartCouponDetail = null;
            }

            return $this->_helper->redirector->gotoRoute(array('action' => 'cart'), "sitestoreproduct_product_general", false);
        }
    }

    public function optionDetailAction() {
        $this->_helper->layout->setLayout('default-simple');
        $option_id = $this->_getParam('option_id', null);
        $field_id = $this->_getParam('field_id', null);
        $product_id = $this->_getParam('product_id', null);
        $total_quantity = 0;

        $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
        $formOptionSelect = $formOptionTable->select()->where('option_id =?', $option_id)->where('field_id =?', $field_id);
        $optionData = $formOptionTable->fetchRow($formOptionSelect);

        //$quantityCheck = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.quantity', 0);
        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $priceAfterDiscount = Engine_Api::_()->sitestoreproduct()->getProductDiscount($product, '', '', 1);

//    if (!empty($quantityCheck) && empty($product->stock_unlimited) && !empty($product->in_stock)) {
//      $option_select = $formOptionTable->select()->from($formOptionTable->info('name'), 'quantity')
//              ->where('field_id =?', $field_id);
//      $options = $formOptionTable->fetchAll($option_select);
//
//      if ($options) {
//        foreach ($options as $option) {
//          $total_quantity += $option->quantity;
//        }
//        if ($total_quantity >= $product->in_stock) {
//          $this->view->error_message = 'You can not add more options with quantities as the total quantity of all the options of this attribute has been exceed the Quantity of the product.';
//          return;
//        }
//      }
//    }
        $this->view->form = $form = new Sitestoreproduct_Form_OptionDetail(array('inStock' => $product->in_stock, 'stockUnlimited' => $product->stock_unlimited, 'price' => $priceAfterDiscount));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

//      if (!empty($quantityCheck)) {
//        if (empty($_POST['quantity_unlimited']) && empty($_POST['quantity'])) {
//          $error = Zend_Registry::get('Zend_Translate')->_(' "*In Stock Quantity" Please complete this Field. Enter Value Other than Zero.');
//          $form->getDecorator('errors')->setOption('escape', false);
//          $form->addError($error);
//          return;
//        }
//        $total_quantity += $_POST['quantity'];
//        if (!empty($total_quantity) && !empty($product->in_stock) && ($total_quantity > $product->in_stock)) {
//          $error = Zend_Registry::get('Zend_Translate')->_('Total quantity should be less than the Original Quantity of the Product.');
//          $form->getDecorator('errors')->setOption('escape', false);
//          $form->addError($error);
//          return;
//        }
//      }
            if (count($optionData) != 0) {
//        if(!empty($quantityCheck))
//          $formOptionTable->update(array(
//              'quantity_unlimited' => $_POST['quantity_unlimited'], 'quantity' => $_POST['quantity'], 'price' => $_POST['price'], 'price_increment' => $_POST['price_increment']), array('option_id =?' => $option_id, 'field_id =?' => $field_id));
//       else
                $formOptionTable->update(array(
                    'price' => $_POST['price'], 'price_increment' => $_POST['price_increment']), array('option_id =?' => $option_id, 'field_id =?' => $field_id));
            }
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => ''
            ));
        }
    }

    public function productCategoryAttributesAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

        //CHECK USER ATHORIZATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $categoryDropDowns = array();
        $field_ids = array();

        //SELECTED TAB
        $this->view->sitestores_view_menu = 4;

        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $this->view->delete_old_combinations = $this->_getParam('delete_old_combinations', 0);
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');

        if (empty($sitestoreproduct))
            return;

        //CHECK FOR CONFIGURABLE AND VIRTUAL PRODUCTS
        if ($sitestoreproduct->product_type != 'configurable' && $sitestoreproduct->product_type != 'virtual')
            return $this->_forward('notfound', 'error', 'core');

        $this->view->success = $this->_getParam('success');

        $productCategoryDropDowns = Engine_Api::_()->sitestoreproduct()->getProductAttributes($sitestoreproduct);
        foreach ($productCategoryDropDowns as $dropDown) {
            $multioptions = array();
            foreach ($dropDown['multioptions'] as $option) {
                $field_id = $option['field_id'];
                $multioptions[$option['option_id']] = $option['label'];
            }
            $dropDown['multioptions'] = '';
            $dropDown['multioptions'] = $multioptions;
            $categoryDropDowns[$field_id] = $dropDown;
            $field_ids[] = $field_id;
        }
        $this->view->dropDowns = $categoryDropDowns;
        $this->view->combinationOptions = Engine_Api::_()->sitestoreproduct()->getCombinationOptions($product_id, null, false);

        $combinations = Engine_Api::_()->sitestoreproduct()->getCombinations($product_id);

        @krsort($combinations);
        $this->view->combinations = $combinations;
        $this->view->field_ids = $field_ids;

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->view->priceLabel = sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName);

        if ($this->getRequest()->isPost()) {
            $this->view->isPost = 1;
            $totalQuantity = 0;
            $formValues = $_POST;
            $combinations_ids = array();
            foreach ($formValues as $key => $value) {
                $parts = explode('_', $key);
                if (count($parts) == 2) {
                    if (!preg_match('/^[0-9]\d*/', $value)) {
                        $this->view->errorMessage = Zend_Registry::get('Zend_Translate')->_('Please enter a valid number in Quantity text field.');
                        return;
                    } elseif (empty($sitestoreproduct->stock_unlimited) && $value > $sitestoreproduct->in_stock) {
                        $this->view->errorMessage = Zend_Registry::get('Zend_Translate')->_("Any variation's quantity of this product can not be more than the product's In stock quantity.");
                        return;
                    } else {
                        $totalQuantity += $value;
                        $combinations_ids[$parts[1]] = $value;
                    }
                } elseif (count($parts) == 5) {
                    $combinationAttributesTable->update(array('price' => $value), array('product_id = ?' => $product_id, 'field_id = ?' => $parts[2], 'combination_attribute_id = ?' => $parts[3]));
                } elseif (count($parts) == 6) {
                    $combinationAttributesTable->update(array('price_increment' => $value), array('product_id = ?' => $product_id, 'field_id = ?' => $parts[2], 'combination_attribute_id = ?' => $parts[3]));
                }
            }

            if (!empty($combinations_ids)) {
                if (empty($sitestoreproduct->stock_unlimited) && $totalQuantity > $sitestoreproduct->in_stock) {
                    $this->view->errorMessage = Zend_Registry::get('Zend_Translate')->_("Total quantity of all the variations of this product must be less than the product's In stock quantity.");
                    return;
                } else {
                    foreach ($combinations_ids as $combination_id => $quantity)
                        $combinationsTable->update(array('quantity' => $quantity), array('combination_id = ?' => $combination_id));
                }
            }

            $this->_helper->redirector->gotoRoute(array('success' => true), false);
        }
    }

    //ACTION FOR CREATING NEW Combinations
    public function combinationCreateAction() {


        if ((!$this->_helper->requireUser()->isValid()))
            return;

        //GET PRODUCT ID AND PRODUCT OBJECT
        $product_id = $this->_getParam('product_id', null);
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (empty($sitestoreproduct))
            return;
        $priceAfterDisocunt = Engine_Api::_()->sitestoreproduct()->getProductDiscount($sitestoreproduct, true, array(), 1);

        $totalQuantity = Engine_Api::_()->sitestoreproduct()->getProductCombinationQuantity($product_id);

        if (empty($sitestoreproduct->stock_unlimited) && ($totalQuantity >= $sitestoreproduct->in_stock)) {
            $this->view->error_message = sprintf(Zend_Registry::get('Zend_Translate')->_('You have only %s quantities available for this product and you have already created the product variations with this much quantity. So you can not create more product variations for this product.'), $sitestoreproduct->in_stock);
            return;
        }

        $categoryDropDowns = $field_ids = array();
        $order = 0;

        //$productCategoryDropDowns = Engine_Api::_()->sitestoreproduct()->getProductCategoryFields($sitestoreproduct);
        $productCategoryDropDowns = Engine_Api::_()->sitestoreproduct()->getProductAttributes($sitestoreproduct);

        foreach ($productCategoryDropDowns as $index => $dropDown) {
            $multioptions = array();
            $multioptions[0] = '';
            foreach ($dropDown['multioptions'] as $option) {
                $field_id = $option['field_id'];
                $multioptions[$option['option_id']] = $option['label'];
            }
            $dropDown['multioptions'] = '';
            $dropDown['multioptions'] = $multioptions;
            $categoryDropDowns[$field_id] = $dropDown;
            $field_ids[] = $field_id;
        }

        $this->view->field_ids = $field_ids;
        $this->view->form = $form = new Sitestoreproduct_Form_Combination(array('categoryDropdowns' => $categoryDropDowns, 'productId' => $product_id));

        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
        $combinationAttributesTableName = $combinationAttributesTable->info('name');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info('name');
        $combinationTableName = $combinationsTable->info('name');

        if ($this->getRequest()->isPost()) {
            $combination_ids = array();
            $this->view->post = 1;
            $errorCount = $total_price = 0;
            $match_count = 1;

            foreach ($_POST as $key => $value) {
                $error = '';
                $parts = @explode('_', $key);
                if (count($parts) != 2 && $key != 'quantity')
                    continue;

                if ($key != 'quantity') {
                    $fieldLabel = Engine_Api::_()->getDbTable('cartproductFieldMeta', 'sitestoreproduct')->getFieldLabel($parts[1]);
                    if ($parts[0] == 'select' && empty($value)) {
                        $error = sprintf(Zend_Registry::get('Zend_Translate')->_('%s <br/> Please complete this field - it is required.'), $fieldLabel);
                        ++$errorCount;
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                    } elseif ($parts[0] == 'price' && !empty($_POST['select_' . $parts[1]]) && (!@preg_match('/^-?(?:\d+|\d*\.\d+)$/', $value))) {
                        $error = sprintf(Zend_Registry::get('Zend_Translate')->_('%s  - Price <br/> Please Enter a valid price.'), $fieldLabel);
                        ++$errorCount;
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                    }
                }

                if ($key == 'quantity') {
                    if (empty($value)) {
                        $error = Zend_Registry::get('Zend_Translate')->_('Quantity <br/> Please complete this field - it is required.');
                    } elseif ($value < 1)
                        $error = Zend_Registry::get('Zend_Translate')->_('Quantity <br/> Please enter a value greater than 1.');
                    elseif (!(preg_match('/^\d+$/', $value)))
                        $error = Zend_Registry::get('Zend_Translate')->_('Quantity <br/> Please enter a Integer Number.');

                    elseif (empty($sitestoreproduct->stock_unlimited)) {
                        $totalQuantity += $value;
                        if ($value > $sitestoreproduct->in_stock)
                            $error = Zend_Registry::get('Zend_Translate')->_('Quantity <br/> Variation Quantity must be less than the Product Quantity.');
                        elseif ($totalQuantity > $sitestoreproduct->in_stock)
                            $error = Zend_Registry::get('Zend_Translate')->_('Quantity <br/> Total quantity of all variations must be less than the Product Quantity.');
                    }
                    if (!empty($error)) {
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        ++$errorCount;
                    }
                }

                if (!empty($value) && $parts[0] == 'select') {
                    $attribute_combination_ids = $combinationAttributeMapsTable->select()
                                    ->setIntegrityCheck(false)
                                    ->from($combinationAttributeMapsTableName, 'combination_id')
                                    ->join($combinationAttributesTableName, "$combinationAttributeMapsTableName.attribute_id = $combinationAttributesTableName.attribute_id")
                                    ->where("$combinationAttributesTableName.product_id =?", $product_id)
                                    ->where("$combinationAttributesTableName.combination_attribute_id =?", $value)
                                    ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

                    if (!empty($attribute_combination_ids) && empty($combination_ids)) {
                        $combination_ids = $attribute_combination_ids;
                    } elseif (!empty($attribute_combination_ids)) {
                        $matched_combinations = array_intersect($attribute_combination_ids, $combination_ids);
                        !empty($matched_combinations) ? ++$match_count : '';
                    } elseif (empty($attribute_combination_ids)) {
                        --$match_count;
                    }
                }

                if (!empty($value) && $parts[0] == 'price') {
                    if (empty($_POST['select_response_' . $parts[1]])) {
                        if (empty($_POST['price_increment_' . $parts[1]]))
                            $value = '-' . $value;
                    } else
                        $value = $_POST['select_response_' . $parts[1]];

                    $total_price += $value;
                }
            }

            if (!empty($match_count) && $match_count == count($field_ids)) {
                $error = Zend_Registry::get('Zend_Translate')->_('This Combination is already exist. Please select some other combination.');
                if (!empty($error)) {
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    ++$errorCount;
                }
            }

            if ($total_price < 0) {
                $total_price = @abs($total_price);
                if ($total_price > $priceAfterDisocunt) {
                    $error = Zend_Registry::get('Zend_Translate')->_('The Variation Price cannot be more than the product discounted price (In Negative).');
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    ++$errorCount;
                }
            }

            if (!empty($errorCount)) {
                $form->populate($_POST);
                return;
            }

            foreach ($field_ids as $index => $field_id) {

                $attribute_id = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')
                                ->where('product_id =?', $product_id)
                                ->where('field_id =?', $field_id)
                                ->where('combination_attribute_id =?', $_POST['select_' . $field_id])
                                ->query()->fetchColumn();
                if (empty($attribute_id)) {
                    $combinationAttributesRow = $combinationAttributesTable->createRow();
                    $combinationAttributesRow->product_id = $product_id;
                    $combinationAttributesRow->field_id = $field_id;
                    $combinationAttributesRow->combination_attribute_id = $_POST['select_' . $field_id];
                    $combinationAttributesRow->price_increment = $_POST['price_increment_' . $field_id];
                    $combinationAttributesRow->price = $_POST['price_' . $field_id];
                    $combinationAttributesRow->order = $order++;
                    $combinationAttributesRow->save();
                    $attribute_id = $combinationAttributesRow->attribute_id;
                } else
                    $order++;

                if ($index == 0) {
                    $combinationsRow = $combinationsTable->createRow();
                    $combinationsRow->quantity = $_POST['quantity'];
                    $combinationsRow->status = $_POST['status'];
                    $combinationsRow->save();
                    $combination_id = $combinationsRow->combination_id;
                }

                $combinationAttributeMapsRow = $combinationAttributeMapsTable->createRow();
                $combinationAttributeMapsRow->combination_id = $combination_id;
                $combinationAttributeMapsRow->attribute_id = $attribute_id;
                $combinationAttributeMapsRow->save();
            }

            return $this->_forward('success', 'utility', 'core', array(
                        'message' => 'Successfully Saved.',
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => ''
            ));
        }
    }

    public function getAttributePriceAction() {
        $attribute_id = $this->_getParam('combination_attribute_id', null);
        $field_id = $this->_getParam('field_id', null);
        $product_id = $this->_getParam('product_id', null);

        $price = null;
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $select = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), array('price', 'price_increment'))->where('product_id =?', $product_id)->where('field_id =?', $field_id)->where('combination_attribute_id =?', $attribute_id);
        $result = $combinationAttributesTable->fetchRow($select);
        if ($result) {
            if (!empty($result->price_increment))
                $price = '+' . $result->price;
            else
                $price = '-' . $result->price;
        }
        echo $price;
        exit();
    }

    public function changeStatusAction() {
        $combination_id = $this->_getParam('combination_id', null);
        $status = $this->_getParam('status', null);
        $product_id = $this->_getParam('product_id', null);

        if (empty($combination_id))
            return;

        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
        $status = !empty($status) ? 0 : 1;

        $combinationsTable->update(array('status' => $status), array('combination_id = ?' => $combination_id));
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->gotoSimple('product-category-attributes', 'siteform', null, array('product_id' => $product_id));
    }

    public function deleteCombinationAction() {

        $combination_id = $this->_getParam('combination_id');
        if (empty($combination_id))
            return;

        $this->_helper->layout->setLayout('admin-simple');
        if (!$this->getRequest()->isPost()) {
            return;
        }
        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
        $combinationAttributes = $combinationAttributeMapsTable->select()->from($combinationAttributeMapsTable->info('name'), 'attribute_id')
                        ->where('combination_id =?', $combination_id)
                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        if (!empty($combinationAttributes)) {
            foreach ($combinationAttributes as $attribute_id) {
                $combination_ids = $combinationAttributeMapsTable->select()->from($combinationAttributeMapsTable->info('name'), 'combination_id')
                                ->where("attribute_id = ?", $attribute_id)
                                ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                if (count($combination_ids) > 1)
                    continue;
                else
                    $combinationAttributesTable->delete(array('attribute_id = ?' => $attribute_id));
            }
        }
        $combinationAttributeMapsTable->delete(array('combination_id = ?' => $combination_id));
        $combinationsTable->delete(array('combination_id = ?' => $combination_id));

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('Variation has been deleted.')
        ));
    }

}

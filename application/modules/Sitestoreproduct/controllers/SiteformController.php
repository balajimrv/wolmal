<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SiteformController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_SiteformController extends Sitestoreproduct_Controller_Abstract {

    protected $_requireProfileType = true;

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
            return;

        parent::init();

        // redirect to mobile actions
        if (!$this->getRequest()->isPost()) {
            $mobileSupportedAction = array(
                'index',
                'product-category-attributes',
                'delete-combination',
                'combination-create',
                'field-create',
                'field-edit',
                // 'map-delete', // AJAX
                'option-edit',
                    // 'option-delete', // AJAX
                    // 'option-create', 'option-detail' // Complex AJAX + Smoothbox
            );

            if (!Engine_Api::_()->seaocore()->checkSitemobileMode('fullsite-mode') && in_array($this->getRequest()->getActionName(), $mobileSupportedAction)) {
                return $this->_helper->redirector->gotoRoute(
                                array_merge(
                                        $this->getRequest()->getParams(), array("action" => $this->getRequest()->getActionName() . "-mobile", "rewrite" => null)
                                ), 'default', true);
            }
        }
        
    }

    ###########################
    ##### MOBILE SECTIONS #####
    ###########################

  
    public function indexMobileAction() {

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


    public function productCategoryAttributesMobileAction() {

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
                // should ignore some mobile specific parameters
                if (in_array($key, array("formatType", "contentType", "clear_cache"))) {
                    continue;
                }
                $parts = explode('_', $key);
                if (count($parts) == 2) {
                    if (!preg_match('/^[1-9]\d*/', $value)) {
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

  
    public function combinationCreateMobileAction() {


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
        $this->view->form = $form = new Sitestoreproduct_Form_Mobile_Combination(array('categoryDropdowns' => $categoryDropDowns, 'productId' => $product_id));

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

            return $this->_helper->redirector->gotoRoute(array(
                        'module' => 'sitestoreproduct',
                        'controller' => 'siteform',
                        'action' => 'product-category-attributes-mobile',
                        'product_id' => $product_id
                            ), 'default', true);
        }
    }

    
    public function deleteCombinationMobileAction() {

        $combination_id = $this->_getParam('combination_id', 0);

        // check product id
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $combinationAttributesTableName = $combinationAttributesTable->info("name");
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info("name");

        $this->view->product_id = $product_id = $combinationAttributeMapsTable->select()
                ->setIntegrityCheck(false)
                ->from($combinationAttributeMapsTableName, array())
                ->join($combinationAttributesTableName, "$combinationAttributesTableName.attribute_id = $combinationAttributeMapsTableName.attribute_id", array("product_id"))
                ->where("$combinationAttributeMapsTableName.combination_id = ?", $combination_id)
                ->where("$combinationAttributesTableName.product_id > ?", 0)
                ->query()
                ->fetchColumn();

        if (empty($combination_id) || empty($product_id))
            return;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
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

        return $this->_helper->redirector->gotoRoute(array(
                    'module' => 'sitestoreproduct',
                    'controller' => 'siteform',
                    'action' => 'product-category-attributes-mobile',
                    'product_id' => $product_id
                        ), 'default', true);
    }

  
    public function fieldCreateMobileAction() {

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

        $FormClass = 'Sitestoreproduct_Form_Mobile_Field';

        //CREATE FORM
        $this->view->form = $form = new $FormClass(array("productId" => $product_id));
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

        return $this->_helper->redirector->gotoRoute(array(
                    'module' => 'sitestoreproduct',
                    'controller' => 'siteform',
                    'action' => 'index-mobile',
                    'product_id' => $product_id,
                    'option_id' => $option_id,
                        ), 'default', true);
    }

  
    public function fieldEditMobileAction() {
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
            $FormClass = 'Sitestoreproduct_Form_Mobile_Field';
        }
        $FormClass = 'Sitestoreproduct_Form_Mobile_Field';

        //CREATE FORM
        $this->view->form = $form = new $FormClass(array("productId" => $product_id));
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

        return $this->_helper->redirector->gotoRoute(array(
                    'module' => 'sitestoreproduct',
                    'controller' => 'siteform',
                    'action' => 'index-mobile',
                    'product_id' => $product_id,
                    'option_id' => Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id),
                        ), 'default', true);
    }

    //ACTON FOR MAP DELETION
    public function mapDeleteMobileAction() {
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $this->view->option_id = $option_id = $this->_getParam('option_id');

        if (empty($product_id) || empty($option_id)) {
            return $this->_helper->requireSubject()->forward();
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $map = Engine_Api::_()->fields()->getMap($this->_getParam('child_id'), $this->_getParam('option_id'), $this->_fieldType);
        Engine_Api::_()->fields()->deleteMap($map);

        return $this->_helper->redirector->gotoRoute(array(
                    'module' => 'sitestoreproduct',
                    'controller' => 'siteform',
                    'action' => 'index-mobile',
                    'product_id' => $product_id,
                    'option_id' => $option_id,
                        ), 'default', true);
    }

  
    public function optionEditMobileAction() {
        // Flush cache when editing option (Fix crashing)
        Engine_Api::_()->fields()
                ->getTable(Engine_Api::_()->fields()->getFieldType($this->_fieldType), 'options')
                ->flushCache();

        $product_id = $this->_getParam('product_id');
        if (empty($product_id)) {
            return $this->_helper->requireSubject()->forward();
        }

        $option_id = $this->_getParam('option_id');
        $option = Engine_Api::_()->fields()->getOption($option_id, $this->_fieldType);
        $field = Engine_Api::_()->fields()->getField($option->field_id, $this->_fieldType);
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

        // Mobile Compatibility
        $cancelHref = Zend_Registry::get("Zend_View")->url(array(
            'module' => 'sitestoreproduct',
            'controller' => 'siteform',
            'action' => 'index-mobile',
            'product_id' => $product_id,
            'option_id' => Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id),
                ), 'default', true
        );
        $form->setAttrib("data-ajax", "false");
        if ($form->getDisplayGroup('buttons')) {
            $form->removeDisplayGroup('buttons');
        }
        if ($form->getElement('cancel')) {
            $form->removeElement('cancel');
        }
        $form->addElement('Button', 'cancel_mobile', array(
            'label' => 'cancel',
            'link' => true,
            'onclick' => "window.location.href='{$cancelHref}';return false;",
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
            'order' => 10001,
            'ignore' => true,
        ));
        $form->addDisplayGroup(array('submit', 'cancel_mobile'), 'buttons', array(
            'order' => 10002,
        ));

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

        // Flush cache again
        Engine_Api::_()->fields()
                ->getTable(Engine_Api::_()->fields()->getFieldType($this->_fieldType), 'options')
                ->flushCache();

        return $this->_helper->redirector->gotoUrl($cancelHref);
    }

  
    public function optionDeleteMobileAction() {
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $this->view->product_option_id = $product_option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id);

        if (empty($product_id) || empty($product_option_id)) {
            return $this->_helper->requireSubject()->forward();
        }

        $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        //DELETE ALL VALUES
        $option_id = $option->option_id;
        Engine_Api::_()->fields()->deleteOption($this->_fieldType, $option);

        return $this->_helper->redirector->gotoRoute(array(
                    'module' => 'sitestoreproduct',
                    'controller' => 'siteform',
                    'action' => 'index-mobile',
                    'product_id' => $product_id,
                    'option_id' => $product_option_id,
                        ), 'default', true);
    }

    // Merge two steps on full-site into one step on mobile
    public function optionCreateMobileAction() {
        $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);
        $field_id = $this->_getParam('field_id');
        $product_id = $this->_getParam('product_id');
        $total_quantity = 0;

        if (empty($product_id) || empty($field_id)) {
            return $this->_helper->requireSubject()->forward();
        }

        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $priceAfterDiscount = Engine_Api::_()->sitestoreproduct()->getProductDiscount($product, '', '', 1);

        $this->view->form = $form = new Sitestoreproduct_Form_Mobile_OptionDetail(array('inStock' => $product->in_stock, 'stockUnlimited' => $product->stock_unlimited, 'price' => $priceAfterDiscount));

    //Mobile Compatibility
        $cancelHref = Zend_Registry::get("Zend_View")->url(array(
            'module' => 'sitestoreproduct',
            'controller' => 'siteform',
            'action' => 'index-mobile',
            'product_id' => $product_id,
            'option_id' => Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($product_id),
                ), 'default', true
        );
        $form->setAttrib("data-ajax", "false");
        if ($form->getDisplayGroup('buttons')) {
            $form->removeDisplayGroup('buttons');
        }
        if ($form->getElement('cancel')) {
            $form->removeElement('cancel');
        }
        $form->addElement('Button', 'cancel_mobile', array(
            'label' => 'cancel',
            'link' => true,
            'onclick' => "window.location.href='{$cancelHref}';return false;",
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
            'order' => 10001,
            'ignore' => true,
        ));
        $form->addDisplayGroup(array('submit', 'cancel_mobile'), 'buttons', array(
            'order' => 10002,
        ));

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //CREATE NEW OPTION
        $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
            'label' => $_POST['label'],
        ));
        $option_id = $option->option_id;

        // Option details
        $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
        $formOptionSelect = $formOptionTable->select()->where('option_id =?', $option_id)->where('field_id =?', $field_id);
        $optionData = $formOptionTable->fetchRow($formOptionSelect);
        $formOptionTable->update(array(
            'price' => $_POST['price'], 'price_increment' => $_POST['price_increment']), array('option_id =?' => $option_id, 'field_id =?' => $field_id));

        return $this->_helper->redirector->gotoUrl($cancelHref);
    }

}

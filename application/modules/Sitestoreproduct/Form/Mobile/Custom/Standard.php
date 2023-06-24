<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Standard.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Mobile_Custom_Standard extends Sitestoreproduct_Form_Custom_Standard {

    protected $_isCreate;

    public function setIsCreate($value) {
        $this->_isCreate = $value;
    }

    // @override
    public function generate() {

        $orderIndex = 0;
        $categoryCombinations = null;
        $allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);
        $disabled_options = array();
        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();
        if (!empty($allowCombinations)) {
            if (isset($this->_productId) && !empty($this->_productId))
                $categoryCombinations = Engine_Api::_()->sitestoreproduct()->getCombinationOptions($this->_productId, 1);
            if (count($categoryCombinations) != 0) {
                foreach ($categoryCombinations as $field_id => $field) {
                    $select_box_id = 'select_' . $field_id;
                    $field_order = $field['order'];
                    $max_order = $field['max_order'];
                    $price_array = json_encode($field['price_array']);
                    if ($field_order != 0) {
                        $field['multioptions'] = '';
                        $field['multioptions'][0] = Zend_Registry::get('Zend_Translate')->_('-- Please Select --');
                    }
                    $this->addElement('Select', $select_box_id, array(
                        'label' => $field['label'],
                        'multiOptions' => $field['multioptions'],
                        'required' => true,
                        'onchange' => "showChildOptions(this, $field_id, $this->_productId, $field_order, $price_array, $max_order)",
                        'order' => $orderIndex++,
                    ));
                }
            }
        }
        $struct = $this->getFieldStructure();

        foreach ($struct as $fskey => $map) {
            $field = $map->getChild();

            // CTSTYLE-37
            if (($this->_isCreate && $fskey != "0_0_1") // on creation, hide all dynamic fields
                    || (!$this->_isCreate && $this->_productType == "configurable" && !in_array($field->type, array("profile_type", "text"))) // on configurable edit, only show plain text dynamic fields
            ) {
                continue;
            }

            // HIDE CATEGORY MAPPED PROFILE FIELDS IF COMBINATIONS ARE ALLOWED (CONFIGURABLE AND VIRTUAL PRODUCTS)

            if (((isset($this->_productType) && ($this->_productType == 'configurable' || $this->_productType == 'virtual')) && ($module == 'sitestoreproduct' && $controller == 'index' && ($action == 'create' || $action == 'edit')) && $field->type != 'profile_type' && $field->type == 'select') || (!empty($allowCombinations) && isset($this->_hideSelect) && !empty($this->_hideSelect) && $field->type == 'select'))
                continue;

            $readonly = false;
            // Skip fields hidden on signup
            if (isset($field->show) && !$field->show && $this->_isCreation) {
                continue;
            }

            // Add field and load options if necessary
            $params = $field->getElementParams($this->getItem());
            $price_array = array();

            /* PRICE INCREMENT / DECREMENT WORK FOR PRODUCT PROFILE FIELDS STARTS */
            if (isset($this->_hideSelect) && !empty($this->_hideSelect)) {
                if (isset($params['options']['multiOptions']) && !empty($params['options']['multiOptions'])) {
                    $options = $params['options']['multiOptions'];
                    //$params['options']['multiOptions'] = '';
                    if ($field->type == 'select' && !($module == 'sitestoreproduct' && $controller == 'index' && ($action == 'create' || $action == 'edit'))) {
                        $params['options']['multiOptions'] = '';
                        $params['options']['multiOptions'][0] = Zend_Registry::get('Zend_Translate')->_('-- Please Select --');
                    }
                    foreach ($options as $option_id => $option) {
                        if (!empty($option_id)) {
                            $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
                            $formOptionSelect = $formOptionTable->select()->from($formOptionTable->info('name'), array('price', 'price_increment', 'quantity', 'quantity_unlimited'))->where('option_id =?', $option_id);
                            $optionData = $formOptionTable->fetchRow($formOptionSelect);
                            if ($optionData) {
                                $price = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($optionData->price);
                                if ($optionData->price != '0.00') {
                                    if (!empty($optionData->price_increment)) {
                                        $params['options']['multiOptions'][$option_id] = $option . '   ' . '(+' . $price . ')';
                                        $price_array[$fskey . '_' . $option_id] = $optionData->price;
                                    } else {
                                        $params['options']['multiOptions'][$option_id] = $option . '   ' . '(-' . $price . ')';
                                        $price_array[$fskey . '_' . $option_id] = '-' . $optionData->price;
                                    }
                                } else {
                                    $params['options']['multiOptions'][$option_id] = $option;
                                }
                                if (empty($optionData->quantity_unlimited) && empty($optionData->quantity))
                                    $disabled_options[] = $option_id;
                            }
                        }
                    }
                }

                if ($field->type == 'checkbox') {
                    $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
                    $formOptionSelect = $formOptionTable->select()->from($formOptionTable->info('name'), array('option_id', 'price', 'price_increment'))->where('field_id =?', $field->field_id);
                    $optionData = $formOptionTable->fetchRow($formOptionSelect);
                    if ($optionData->price != '0.00') {
                        $price = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($optionData->price);
                        if (!empty($optionData->price_increment)) {
                            $params['options']['label'] .= '   ' . '(+' . $price . ')';
                            $price_array[$fskey] = $optionData->price;
                        } else {
                            $params['options']['label'] .= '   ' . '(-' . $price . ')';
                            $price_array[$fskey] = '-' . $optionData->price;
                        }
                    }
                }
            }

            /* END PRICE INCREMENT / DECREMENT WORK HERE */

            //$key = 'field_' . $field->field_id;
            $key = $map->getKey();

            // If value set in processed values, set in element
            if (!empty($this->_processedValues[$field->field_id])) {
                $params['options']['value'] = $this->_processedValues[$field->field_id];
            }

            if (!@is_array($params['options']['attribs'])) {
                $params['options']['attribs'] = array();
            }

            // Heading
            if ($params['type'] == 'Heading') {
                $params['options']['value'] = Zend_Registry::get('Zend_Translate')->_($params['options']['label']);
                unset($params['options']['label']);
            }

            // Order
            // @todo this might cause problems, however it will prevent multiple orders causing elements to not show up
            $params['options']['order'] = $orderIndex++;

            $inflectedType = Engine_Api::_()->fields()->inflectFieldType($params['type']);
            unset($params['options']['alias']);
            unset($params['options']['publish']);
            $this->addElement($inflectedType, $key, $params['options']);

            $element = $this->getElement($key);

            if (method_exists($element, 'setFieldMeta')) {
                $element->setFieldMeta($field);
            }

            // Set attributes for hiding/showing fields using javscript
            $classes = 'field_container field_' . $map->child_id . ' option_' . $map->option_id . ' parent_' . $map->field_id;
            $element->setAttrib('class', $classes);

            //
            if ($field->canHaveDependents()) {
                if (isset($this->_hideSelect) && !empty($this->_hideSelect)) {
                    $element->setAttrib('disable', $disabled_options);
                    $price_array = json_encode($price_array);
                    $price_array = str_replace('"', 'a', $price_array);
                    $element->setAttrib("onchange", "setConfigurablePrice(this, '$fskey' , '$price_array', '$field->type')");
                } elseif (isset($this->_listInformationWidget) && !empty($this->_listInformationWidget)) {
                    $element->setAttrib("onchange", "changeProfileFields(this)");
                } else {
                    $element->setAttrib("onchange", "changeFields(this)");
                }
            }

            if ($field->type == 'checkbox') {
                if (isset($this->_hideSelect) && !empty($this->_hideSelect)) {
                    $price_array = json_encode($price_array);
                    $price_array = str_replace('"', 'a', $price_array);
                    $element->setAttrib("onchange", "setConfigurablePrice(this, '$fskey' , '$price_array', '$field->type')");
                } elseif (isset($this->_listInformationWidget) && !empty($this->_listInformationWidget)) {
                    $element->setAttrib("onchange", "changeProfileFields(this)");
                } else
                    $element->setAttrib("onchange", "changeFields(this)");
            }

            // Set custom error message
            if ($field->error) {
                $element->addErrorMessage($field->error);
            }

            if ($field->isHeading()) {
                $element->removeDecorator('Label')
                        ->removeDecorator('HtmlTag')
                        ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');
            }

            // CODE TO HIDE CUSTOM FIELDS AT PRODUCT CREATION
            if ($module == 'sitestoreproduct' && $controller == 'index' && ($action == 'create' || $action == 'edit'))
                $element->getDecorator('HtmlTag2')->setOption('style', 'display:none');
        }

        $this->addElement('Button', 'submit_addtocart', array(
            'label' => 'Save',
            'type' => 'submit',
            'order' => 10000,
            'decorators' => array('ViewHelper'),
        ));
    }

}

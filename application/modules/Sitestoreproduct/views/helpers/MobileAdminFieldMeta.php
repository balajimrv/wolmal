<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFieldMeta.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Fields_View_Helper_MobileAdminFieldMeta extends Zend_View_Helper_Abstract {

    public function mobileAdminFieldMeta($map, $params) {
        $meta = $map->getChild();
        extract($params);

        if (!($meta instanceof Fields_Model_Meta)) {
            return '';
        }

        // Prepare translations
        $translate = Zend_Registry::get('Zend_Translate');

        // Prepare params
        if ($meta->type == 'heading') {
            $containerClass = 'heading';
        } else {
            $containerClass = 'field';
        }

        $key = $map->getKey();
        $label = $this->view->translate($meta->label);
        $type = $meta->type;

        $typeLabel = Engine_Api::_()->fields()->getFieldInfo($type, 'label');

        
        $fieldMetaIds = $this->_getFieldMetaIds($key);
        $fieldEditUrl = "{$staticBaseUrl}sitestoreproduct/siteform/field-edit-mobile/field_id/{$fieldMetaIds['child_id']}/product_id/{$product_id}";
        $fieldDeleteUrl = "{$staticBaseUrl}sitestoreproduct/siteform/map-delete-mobile/parent_id/{$fieldMetaIds['parent_id']}/option_id/{$fieldMetaIds['option_id']}/child_id/{$fieldMetaIds['child_id']}/product_id/{$product_id}";
        $fieldOrderUrl = "{$staticBaseUrl}sitestoreproduct/siteform/field-sort-mobile";
        $optionCreateUrl = "{$staticBaseUrl}sitestoreproduct/siteform/option-create-mobile/field_id/{$fieldMetaIds['child_id']}/product_id/{$product_id}";


        // Options data
        $optionContent = '';
        $dependentFieldContent = '';

        if ($meta->canHaveDependents()) {
            $extraOptionsClass = 'field_extraoptions ' . $this->_generateClassNames($key, 'field_extraoptions_');
            $optionContent .= <<<EOF
<div class="{$extraOptionsClass}" id="field_extraoptions_{$key}">
  <div class="field_extraoptions_contents_wrapper">
    <div class="field_extraoptions_contents">
      <div class="field_extraoptions_add">
        <a href="javascript:void(0);" onclick="window.location.href='{$optionCreateUrl}';return false;">
			{$this->view->translate("Create choice")}
		</a>
      </div>
EOF;

            $options = $meta->getOptions();
            $allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);
            if (!empty($options)) {
                $extraOptionsChoicesClass = 'field_extraoptions_choices ' . $this->_generateClassNames($key, 'field_extraoptions_choices_');
                $optionContent .= <<<EOF
      <ul class="{$extraOptionsChoicesClass}" id="admin_field_extraoptions_choices_{$key}">
EOF;
                foreach ($options as $option) {
                    $optionId = $option->option_id;
                    $optionLabel = $this->view->translate($option->label);
                    if ($type == 'select' && !empty($allowCombinations)) {
                        $optionPrice = '';
                    } else {
                        if (!empty($option->price)) {
                            if (!empty($option->price_increment))
                                $increasedProductPrice = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPrice + $option->price);
                            else
                                $increasedProductPrice = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPrice - $option->price);
                            $option->price = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($option->price);
                            if (!empty($option->price_increment))
                                $optionPrice = '+' . $option->price . ' (' . $increasedProductPrice . ') ';
                            else
                                $optionPrice = '-' . $option->price . ' (' . $increasedProductPrice . ') ';
                        }
                    }
                    $dependentFieldCount = count(Engine_Api::_()->fields()->getFieldsMaps($option->getFieldType())->getRowsMatching('option_id', $optionId));
                    $dependentFieldCountString = ( $dependentFieldCount <= 0 ? '' : ' (' . $dependentFieldCount . ')' );
                    $optionClass = 'field_option_select field_option_select_' . $optionId . ' ' . $this->_generateClassNames($key, 'field_option_select_');

                    
                    $optionEditUrl = "{$staticBaseUrl}sitestoreproduct/siteform/option-edit/option_id/{$optionId}/product_id/{$product_id}";
                    $optionDeleteUrl = "{$staticBaseUrl}sitestoreproduct/siteform/option-delete-mobile/option_id/{$optionId}/product_id/{$product_id}";

                    $optionContent .= <<<EOF
        <li id="field_option_select_{$key}_{$optionId}" class="{$optionClass}">
          <span class="field_extraoptions_choices_options">
            <a href="javascript:void(0);" onclick="window.location.href='{$optionEditUrl}';return false;">{$translate->_('edit')}</a>
            | <a href="javascript:void(0);" onclick="window.location.href='{$optionDeleteUrl}';return false;">X</a>
          </span>
          <b style="font-weight:normal;margin-left:5px;">
            {$optionLabel} 
          </b>
          <b style="font-weight:normal;margin-left:10px;">
            {$optionPrice} 
          </b>
        </li>
EOF;
                }

                $optionContent .= <<<EOF
      </ul>
EOF;
                foreach ($options as $option) {
                    $dependentFieldContent .= $this->view->adminFieldOption($option, $map);
                }
            }

            $optionContent .= <<<EOF
    </div>
  </div>
  <a href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
    {$translate->_('edit choices')}
  </a>
</div>
EOF;
        }

        if ($meta->type == 'checkbox') {
            $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
            $option_select = $formOptionTable->select()
                    ->where('field_id =?', $meta->field_id);
            $option = $formOptionTable->fetchRow($option_select);

            if ($option) {
                $optionClass = 'field_option_select field_option_select_' . $option->option_id . ' ' . $this->_generateClassNames($key, 'field_option_select_');
                $optionQuantity = $option->quantity;
                if (empty($optionQuantity) && !empty($option->quantity_unlimited))
                    $optionQuantity = 'U/L';
                if (!empty($option->price)) {
                    if (!empty($option->price_increment))
                        $increasedProductPrice = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPrice + $option->price);
                    else
                        $increasedProductPrice = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPrice - $option->price);
                    $option->price = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($option->price);
                    if (!empty($option->price_increment))
                        $optionPrice = '+' . $option->price . ' (' . $increasedProductPrice . ') ';
                    else
                        $optionPrice = '-' . $option->price . ' (' . $increasedProductPrice . ') ';
                }

                
                $optionEditUrl = "{$staticBaseUrl}sitestoreproduct/siteform/option-edit/option_id/{$option->option_id}/product_id/{$product_id}";

                $optionContent .= <<<EOF
      <div>
      <ul>   
      <li id="field_option_select_{$key}_{$option->option_id}" class="{$optionClass}">
      <span class="checkbox_field_extraoptions_choices_options">
            <a href="javascript:void(0);" onclick="window.location.href='{$optionEditUrl}';return false;">{$translate->_('edit')}</a>
      </span>
       <b style="font-weight:normal;margin-left:5px;">
            {$option->label} 
       </b>
        <b style="font-weight:normal;margin-left:10px;">
          {$optionQuantity} 
        </b>
        <b style="font-weight:normal;margin-left:10px;">
          {$optionPrice} 
        </b>
              </li>
        </ul>
          </div>
EOF;
            }
        }

        // Generate
        $contentClass = 'admin_field ' . $this->_generateClassNames($key, 'admin_field_');
        $content = <<<EOF
  <li id="admin_field_{$key}" class="{$contentClass}">
    <span class='{$containerClass}'>
      <div class='item_handle'>
        &nbsp;
      </div>
      <div class='item_options'> 
        <a href='javascript:void(0);' onclick='window.location.href="{$fieldEditUrl}";return false;' onmousedown="void(0);">{$translate->_('edit')}</a>
        | <a href='javascript:void(0);' onclick='window.location.href="{$fieldDeleteUrl}";return false;' onmousedown="void(0);">{$translate->_('delete')}</a>
		| <a href='javascript:void(0);' onclick='moveField("admin_field_{$key}","up");return false;'>{$translate->_('move up')}</a>
		| <a href='javascript:void(0);' onclick='moveField("admin_field_{$key}","down");return false;'>{$translate->_('move down')}</a>
      </div>
      <div class='item_title'>
        {$label}
        <span>({$translate->_($typeLabel)})</span>
      </div>
      {$optionContent}
    </span>
    {$dependentFieldContent}
  </li>
EOF;

        return $content;
    }

    protected function _generateClassNames($key, $prefix = '') {
        list($parent_id, $option_id, $child_id) = explode('_', $key);
        return
                $prefix . 'parent_' . $parent_id . ' ' .
                $prefix . 'option_' . $option_id . ' ' .
                $prefix . 'child_' . $child_id
        ;
    }

    protected function _getFieldMetaIds($key) {
        list($parent_id, $option_id, $child_id) = explode('_', $key);
        return array(
            "parent_id" => $parent_id,
            "option_id" => $option_id,
            "child_id" => $child_id,
        );
    }

}

<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Options.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Options extends Engine_Db_Table {

  protected $_name = 'sitestoreproduct_product_fields_options';
  protected $_rowClass = 'Sitestoreproduct_Model_Option';

  public function getFieldLabel($field_id) {
    
    $select = $this->select()
            ->where('field_id = ?', $field_id);
    $result = $this->fetchRow($select);
    
    return!empty($result) ? $result->label : '';
  }

  public function getProfileTypeLabel($option_id) {

    if (empty($option_id)) {
      return;
    }

    //GET FIELD OPTION TABLE NAME
    $tableFieldOptionsName = $this->info('name');

    //FETCH PROFILE TYPE LABEL
    $profileTypeLabel = $this->select()
            ->from($tableFieldOptionsName, array('label'))
            ->where('option_id = ?', $option_id)
            ->query()
            ->fetchColumn();

    return $profileTypeLabel;
  }
  
  public function getOptions($field_id){
    
    if(empty($field_id))
      return;
    
    $select = $this->select()
            ->where('field_id = ?', $field_id);
    $result = $this->fetchAll($select);
    if(!empty($result))
      return $result->toArray();
    else
      return;
  }
  
  public function getOptionLabel($field_id, $option_id){
    
    if(!$field_id || !$option_id)
      return ;
    
    return $this->select()->from($this->info('name'), 'label')
            ->where('field_id = ?', $field_id)
            ->where('option_id = ?', $option_id)
            ->query()->fetchColumn();
  }

}
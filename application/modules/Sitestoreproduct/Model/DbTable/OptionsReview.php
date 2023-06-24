<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: OptionsReview.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_OptionsReview extends Engine_Db_Table {

  protected $_name = 'sitestoreproduct_review_fields_options';

  public function getFieldLabelReview($field_id) {
    $select = $this->select()
            ->where('field_id = ?', $field_id);
    $result = $this->fetchRow($select);
    return!empty($result) ? $result->label : '';
  }

  public function getProfileTypeLabelReview($option_id) {

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

}

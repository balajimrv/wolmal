<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Sitestoreforms.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_Model_DbTable_Sitestoreforms extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreform_Model_Sitestoreform";

  function getFormData($store_id) {
    $formSelect = $this->select()->where('store_id = ?', $store_id);
    return $formSelectData = $this->fetchRow($formSelect);
  }

}
?>
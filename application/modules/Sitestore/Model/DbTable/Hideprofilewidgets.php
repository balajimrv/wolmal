<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Writes.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Hideprofilewidgets extends Engine_Db_Table {

  protected $_rowClass = "Sitestore_Model_Hideprofilewidget";
  
  /**
   * Gets hide widgets information
   *
   * @param all widgets name which are hidden
   */
  public function hideWidgets() {
    return $this->fetchAll($this->select());
  }
  
}

?>
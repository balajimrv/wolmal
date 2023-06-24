<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Model_DbTable_Content extends Engine_Db_Table {

  protected $_serializedColumns = array('params');

  public function getPageId($identity = null) {

    return $this->select()->from($this->info('name'), array('page_id'))->where('content_id =?', $identity)->query()->fetchColumn();
  }

  public function getTabId($name = null) {

    return $this->select()->from($this->info('name'), array('content_id'))->where('name =?', $name)->query()->fetchColumn();
  }

}
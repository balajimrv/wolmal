<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Pages.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Model_DbTable_Navigation extends Engine_Db_Table {

  function getNavigation($params = array()) {

    $select = $this->select()
            ->from($this->info('name'), '*')
            ->where('name = ?', $params['name']);

    return $this->fetchRow($select);
  }

}
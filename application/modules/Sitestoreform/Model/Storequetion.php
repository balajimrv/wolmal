<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Businesquestion.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_Model_Storequetion extends Core_Model_Item_Abstract {

  protected $_parent_type = 'user';
  protected $_searchTriggers = array('title', 'body', 'search');
  protected $_parent_is_owner = true;

}
?>
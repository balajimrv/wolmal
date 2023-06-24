<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Meta.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_Meta extends Core_Model_Item_Abstract {

  // Properties
  protected $_parent_type = 'meta';
  protected $_searchColumns = array('field_id', 'label');
  protected $_parent_is_owner = true;

}

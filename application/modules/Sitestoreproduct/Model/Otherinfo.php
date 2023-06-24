<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Otherinfo.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Otherinfo extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
  
   /**
   * Convert title text in to current selected language
   *
   */
	public function getOverview() {

		$overview = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('overview');

		if(empty($this->$overview)) {
			return $this->overview;
		}

		//RETURN VALUE
		return $this->$overview;
	}

}
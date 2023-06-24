<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Category.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesalbum_Model_Category extends Core_Model_Item_Abstract {
//get category title
 public function getTitle() {
		return $this->category_name;
	}

//get category table name
  public function getTable()
  {
    if( is_null($this->_table) )
    {
      $this->_table = Engine_Api::_()->getDbtable('categories', 'sesalbum');
    }

    return $this->_table;
  }
	//category href
	 public function getHref($params = array())
  	{
		if($this->slug == ''){
			return ;	
		}
    $params = array_merge(array(
      'route' => 'sesalbum_category_view',
      'reset' => true,
      'category_id' => $this->slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
	
	public function getBrowseCategoryHref($params = array()){
    $params = array_merge(array(
      'route' => 'sesalbum_general',
			'action'=>'browse',
      'reset' => true,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
	}
  public function isOwner($owner) {
    if ($owner instanceof Core_Model_Item_Abstract) {
      return ( $this->getIdentity() == $owner->getIdentity() && $this->getType() == $owner->getType() );
    } else if (is_array($owner) && count($owner) === 2) {
      return ( $this->getIdentity() == $owner[1] && $this->getType() == $owner[0] );
    } else if (is_numeric($owner)) {
      return ( $owner == $this->getIdentity() );
    }

    return false;
  }

  public function getOwner($recurseType = NULL) {
    return $this;
  }

}

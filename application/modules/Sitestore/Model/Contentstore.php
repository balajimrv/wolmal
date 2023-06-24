<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contentstore.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_Contentstore extends Core_Model_Item_Abstract {

  protected $_parent_type = 'user';
  protected $_searchTriggers = false;

  /**
   * Gets an absolute URL to the store to view this item
   *
   * @param array $params 
   * @return string
   */
  public function getHref($params = array()) {

    if (!empty($this->url)) {
      $id = str_replace(array('_', ' '), '-', $this->url);
    } else if (!empty($this->name)) {
      $id = str_replace(array('_', ' '), '-', $this->name);
    } else {
      $id = $this->store_id;
    }

    $params = array_merge(array(
        'route' => 'default',
        'reset' => true,
        'module' => 'sitestore',
        'controller' => 'store',
        'action' => $id
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
  }

}

?>
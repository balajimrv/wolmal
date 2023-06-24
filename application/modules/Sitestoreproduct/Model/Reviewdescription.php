<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Reviewdescription.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_ReviewDescription extends Core_Model_Item_Abstract {

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    //GET CONTENT ID
    $content_id = Engine_Api::_()->sitestoreproduct()->existWidget('sitestoreproduct_reviews', 0);
    $params = array_merge(array(
        'route' => "sitestoreproduct_entry_view",
        'reset' => true,
        'product_id' => $this->product_id,
        'slug' => $this->getSlug(),
        'tab' => $content_id,
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

}
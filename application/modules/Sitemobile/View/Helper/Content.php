<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Content.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_Content extends Engine_View_Helper_Content {

  /**
   * Render a content area by name
   * 
   * @param string $name
   * @return string
   */
  public function content($name = null, $setContentStorageFlage=false) {
    if ($setContentStorageFlage) {
      Engine_API::_()->sitemobile()->setContentStorage();
    }
    if(!empty ($name))
      return parent::content($name);
    else
      return parent::content();
  }

}
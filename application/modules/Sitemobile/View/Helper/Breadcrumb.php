<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Navigation.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_Breadcrumb extends Zend_View_Helper_Navigation {

  /**
   * View helper namespace
   *
   * @var string
   */
  public function breadcrumb($breadcrumb) {

    return $this->view->partial(
                    'breadcrumb.tpl', "sitemobile", array('brdObj'=>$breadcrumb)
    );
  }

}
<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FormDate.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_LocaleDateSM extends Zend_View_Helper_FormElement {

  public function localeDateSM() {
    $locale = $this->view->locale()->getLocale();
    return str_replace('//', '', preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('yy/', 'mm/', 'dd/'), preg_replace('/[^ymd]/i', '', strtolower(preg_replace('~\'[^\']+\'~', '', $locale->getTranslation('long', 'Date', $locale))))) . '/');
  }

}
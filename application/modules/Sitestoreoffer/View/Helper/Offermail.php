<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Offermail.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_View_Helper_Offermail extends Zend_View_Helper_Abstract {

  public function offermail($data = array()) {

    return $this->view->partial(
                    '_set-mail.tpl', 'sitestoreoffer',$data);
  }
}
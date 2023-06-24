<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Socialengineaddon
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestore_Widget_ExtensionShowController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

   $enableSubModules = array();
   $rss = Zend_Feed::import('http://www.socialengineaddons.com/bizextensions/feed');

    foreach( $rss as $item )
    {
      if($item->ptype() == 'contactstoreowners') {
        $enableSubModules[] = 'sitestoreadmincontact';
      } 
      elseif($item->ptype() == 'sitestoreshorturl') {
        $enableSubModules[] = 'sitestoreurl';
      }
      else {
				$enableSubModules[] = $item->ptype();
      }
    }
    $enableAllModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $enableModules = array_intersect($enableSubModules, $enableAllModules);
    $this->view->channel = $enableModules;
  }

}
?>
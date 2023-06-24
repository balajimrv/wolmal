<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.isActivate', 0);
if (empty($isActive)) {
  return;
}
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => 'Store Profile Form',
        'description' => 'This widget forms the Form tab on Store Profile and displays a form created by the Store admin. This form can to be filled by the Store viewers and gets submitted to the Store admins. It should be placed in the Tabbed Blocks area of the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestoreform.sitestore-viewform',
        'defaultParams' => array(
            'title' => $view->translate('Form'),
        ),
    ),
)
?>
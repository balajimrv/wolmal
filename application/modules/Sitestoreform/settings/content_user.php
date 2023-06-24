<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Store Profile Form'),
        'description' => $view->translate('Forms the Form tab of your Store and shows the form created by you from the "Form" section of Apps in your Dashboard. It should be placed in the Tabbed Blocks area of the Store Profile. Results of forms submitted by visitors are emailed to the Admins of your Store.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestoreform.sitestore-viewform',
        'defaultParams' => array(
            'title' => $view->translate('Form'),
        ),
    ),
)
?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
//  Example of how to use the library  -- contents put in $ret_array
include "contacts_fn.php";
$ret_array = get_people_array();

//to see a array dump...
print_r($ret_array);
?>   

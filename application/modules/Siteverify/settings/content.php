<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return
array(
	array(
		'title' => 'Verify Member Button & Stats',
		'description' => 'Displays the verify button to verify the member, and the stats for number of verifications for the member. This widget should be placed on Member Profile Page.',
		'category' => 'Verify',
		'type' => 'widget',
		'name' => 'siteverify.verify-button',
		'defaultParams' => array(
				'title' => '',
		),
	),
	array(
		'title' => 'Most Verified Members',
		'description' => 'Displays Most Verified Members.',
		'category' => 'Verify',
		'type' => 'widget',
		'name' => 'siteverify.verified-members',
		'defaultParams' => array(
				'title' => '',
		),
    'adminForm' => array(
        'elements' => array(
            array(
                'Radio',
                'is_ajax',
                array(
                    'label' => 'Widget Content Loading',
                    'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                    'multiOptions' => array(
                        '1' => 'Yes',
                        '0' => 'No',
                    ),
                    'value' => 1,
                )
            ),
            
            array(
                'Text',
                'itemCount',
                array(
                    'label' => 'Count',
                    'description' => '(number of members to show)',
                    'value' => 5,
                )
            ),
        ),
    )
	),
);
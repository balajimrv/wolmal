<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sespymk
 * @package    Sespymk
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: content.php 2017-03-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
return array(
  array(
    'title' => 'SES - PYMK - Display Sent Friend Requests',
    'description' => 'This widget displays the friend requests sent by the user who is viewing the widget. This widget can be placed anywhere on your website.',
    'category' => 'SES - People You May Know Plugin',
    'type' => 'widget',
    'name' => 'sespymk.friendrequestsent-page',
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
	    array(
            'Text',
            'itemCount',
            array(
              'label' => 'Count',
              'description' => '(number of sent friend requests to show)',
              'value' => 10,
              'validators' => array(
                  array('Int', true),
                  array('GreaterThan', true, array(0)),
              ),
            )
        ),
        array(
            'Radio',
            'paginationType',
            array(
                'label' => "Do you want the sent friend requests to be auto-loaded when users scroll down the page?",
                'multiOptions' => array(
                    '1' => 'Yes, Auto Load',
                    '2' => 'No, View more',
                ),
                'value' => 1,
            )
        ),
        array(
            'Radio',
            'linktopage',
            array(
                'label' => 'Do you want to give link to "People You May Know Page" in this widget?',
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => 1,
            )
        ),
      )
    )
  ),
  array(
    'title' => 'SES - PYMK - Display Received Friend Requests',
    'description' => 'This widget displays the friend requests received by the user who is viewing the widget. This widget can be placed anywhere on your website.',
    'category' => 'SES - People You May Know Plugin',
    'type' => 'widget',
    'name' => 'sespymk.friend-requests',
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
	    array(
            'Text',
            'itemCount',
            array(
              'label' => 'Count (number of received friend requests to show)',
              //'description' => '(number of sent friend requests to show)',
              'value' => 10,
              'validators' => array(
                  array('Int', true),
                  array('GreaterThan', true, array(0)),
              ),
            )
        ),
        array(
            'Radio',
            'paginationType',
            array(
                'label' => "Do you want the received friend requests to be auto-loaded when users scroll down the page?",
                'multiOptions' => array(
                    '1' => 'Yes, Auto Load',
                    '2' => 'No, View more',
                ),
                'value' => 1,
            )
        ),
        array(
            'Radio',
            'linktopage',
            array(
                'label' => 'Do you want to give link to "Sent Friend Requests Page" in this widget?',
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => 1,
            )
        ),
      )
    )
  ),
  array(
    'title' => 'SES - PYMK - Invite Friends',
    'description' => 'This widget displays the invite form using which your members can send invitation to their friends, family or any other user to your website. This widget can be placed anywhere on your website.',
    'category' => 'SES - People You May Know Plugin',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sespymk.invite',
  ),
  array(
    'title' => 'SES - PYMK - Link to PYMK Page Button',
    'description' => 'This widget shows a button clicking on which users will be redirected to the "People You May Know Page" of your website. This widget can be placed anywhere on your website.',
    'category' => 'SES - People You May Know Plugin',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sespymk.button',
  ),
  array(
      'title' => 'SES - PYMK - People You May Know Suggestions Carousel',
      'description' => 'This widget displays members to the user viewing this widget in attractive Carousel. This widget can be placed anywhere on your website.',
      'category' => 'SES - People You May Know Plugin',
      'type' => 'widget',
      'autoEdit' => true,
      'name' => 'sespymk.suggestion-carousel',
      'adminForm' => array(
          'elements' => array(
              array(
                'MultiCheckbox',
                'showdetails',
                array(
                  'label' => 'Choose from below the members who will be shown to the user viewing this widget. (This setting is dependent on "Advanced Members Plugin" and require this plugin to be installed and enabled on your site.)',
                  'multiOptions' => array(
                    'friends' => 'Friends of Friends',
                    'mutualfriends' => 'Mutual Friends',
                  ),
                )
              ),
              array(
                  'Select',
                  'viewType',
                  array(
                      'label' => "View Type",
                      'multiOptions' => array(
                          'horizontal' => 'Horizontal',
                          'vertical' => 'Vertical',
                      ),
                      'value' => 'horizontal',
                  ),
              ),
              array(
                  'Text',
                  'height',
                  array(
                      'label' => 'Enter the height of one member block.',
                      'value' => 200,
                      'validators' => array(
                          array('Int', true),
                          array('GreaterThan', true, array(0)),
                      )
                  ),
              ),
              array(
                  'Text',
                  'heightphoto',
                  array(
                      'label' => 'Enter the height of member photo.',
                      'value' => 200,
                      'validators' => array(
                          array('Int', true),
                          array('GreaterThan', true, array(0)),
                      )
                  ),
              ),
              array(
                'Text',
                'width',
                array(
                  'label' => 'Enter the width of one member block.',
                  'value' => 200,
                  'validators' => array(
                    array('Int', true),
                     array('GreaterThan', true, array(0)),
                  )
                ),
              ),
              array(
                'Text',
                'itemCount',
                array(
                  'label' => 'Count',
                  'description' => '(number of members to show)',
                  'value' => 5,
                  'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
                  ),
                )
              ),
          )
      ),
  ),
  array(
    'title' => 'SES - PYMK - People You May Know Suggestions in List or Grid View',
    'description' => 'This widget displays members to the user viewing this widget in list view or grid view. This widget must be places on "People You May Know Page" on your website.',
    'category' => 'SES - People You May Know Plugin',
    'type' => 'widget',
    'name' => 'sespymk.suggestion-page',
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
            'Select',
            'showType',
            array(
                'label' => 'Choose the View Type.',
                'multiOptions' => array(
                    "1" => "Grid View",
                    "0" => "List View"
                ),
            ),
        ),
        array(
            'Radio',
            'onlyphotousers',
            array(
                'label' => "Do you want to show only those members in this widget who have uploaded their profile photos?",
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => 0,
            )
        ),
				array(
					'MultiCheckbox',
					'showdetails',
					array(
						'label' => "Choose from below the members who will be shown to the user viewing this widget. (This setting is dependent on 'Advanced Members Plugin' and require this plugin to be installed and enabled on your site.)",
						'multiOptions' => array(
				        'friends' => 'Friends of Friends',
						'mutualfriends' => 'Mutual Friends',
						),
					)
				),
        array(
            'Text',
            'height',
            array(
              'label' => 'Member Grid Block Height',
              'value' => 230,
              'validators' => array(
                  array('Int', true),
                  array('GreaterThan', true, array(0)),
              ),
            )
        ),
        array(
            'Text',
            'horiwidth',
            array(
              'label' => 'Member Grid View Width',
              'value' => 150,
              'validators' => array(
                  array('Int', true),
                  array('GreaterThan', true, array(0)),
              ),
            )
        ),
        array(
            'Text',
            'horiheight',
            array(
              'label' => 'Member Photo Height in Grid',
              'value' => 150,
              'validators' => array(
                  array('Int', true),
                  array('GreaterThan', true, array(0)),
              ),
            )
        ),
        array(
            'Radio',
            'paginationType',
            array(
                'label' => "Do you want the suggestion to be auto-loaded when users scroll down the page?",
                'multiOptions' => array(
                    '1' => 'Yes, Auto Load',
                    '2' => 'No, View more',
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
              'value' => 10,
              'validators' => array(
                  array('Int', true),
                  array('GreaterThan', true, array(0)),
              ),
            )
        ),
      )
    )
  ),
  array(
    'title' => 'SES - PYMK - People You May Know Suggestions Widget',
    'description' => 'This widget displays members to the user viewing this widget. This widget can be placed anywhere on your website.',
    'category' => 'SES - People You May Know Plugin',
    'type' => 'widget',
    'name' => 'sespymk.suggestion-friends',
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
            'Select',
            'showType',
            array(
                'label' => 'Choose the View Type.',
                'multiOptions' => array(
                    "1" => "Horizantal",
                    "0" => "Vertical"
                ),
            ),
        ),
        array(
            'Radio',
            'onlyphotousers',
            array(
                'label' => "Do you want to show only those members in this widget who have uploaded their profile photos?",
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => 0,
            )
        ),
				array(
					'MultiCheckbox',
					'showdetails',
					array(
						'label' => "Choose from below the members who will be shown to the user viewing this widget. (This setting is dependent on 'Advanced Members Plugin' and require this plugin to be installed and enabled on your site.)",
						'multiOptions' => array(
				        'friends' => 'Friends of Friends',
						'mutualfriends' => 'Mutual Friends',
						),
					)
				),
        array(
            'Text',
            'horiwidth',
            array(
              'label' => 'Width in Horizantal View.',
              'value' => 150,
              'validators' => array(
                  array('Int', true),
                  array('GreaterThan', true, array(0)),
              ),
            )
        ),
        array(
            'Text',
            'itemCount',
            array(
              'label' => 'Count',
              'description' => '(number of members to show)',
              'value' => 5,
              'validators' => array(
                  array('Int', true),
                  array('GreaterThan', true, array(0)),
              ),
            )
        ),
      )
    )
  ),
);
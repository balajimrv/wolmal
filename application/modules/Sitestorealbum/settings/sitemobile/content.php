<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.isActivate', 0);
if ( empty($isActive) ) {
  return;
}
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Store Profile Albums'),
        'description' => $view->translate('This widget forms the Albums tab on the Store Profile and displays the albums of the Store. It also displays the photos added by the Store visitors other than the owner. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.sitemobile-photos-sitestore',
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of albums to show)'),
                        'value' => 10,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
               array(
                    'Text',
                    'itemCount_photo',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of photos to show in album)'),
                        'value' => 100,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
                array(
                    'Radio',
                    'albumsorder',
                    array(
                        'label' => $view->translate('Select the order below to display the albums on your site.'),
                        'multiOptions' => array(
                            1 => 'Newer to older',
                            0 => 'Older to newer'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Store Albums'),
        'description' => $view->translate('Displays the list of Albums from Stores created on your community. This widget should be placed in the widgetized Store Albums store. Results from the Search Store Albums form are also shown here.'),
        'category' => $view->translate('Stores / Marketplace - Stores'),
        'type' => 'widget',
        'name' => 'sitestorealbum.sitestore-album',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of albums to show)'),
                        'value' => 10,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
            ),
        ),
    ),
    array(
			'title' => $view->translate('Store Album View'),
			'description' => $view->translate("This widget should be placed on the Store Album View Store."),
      'category' => $view->translate('Stores / Marketplace - Stores'),
			'type' => 'widget',
			'name' => 'sitestorealbum.album-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
			'adminForm' => array(
					'elements' => array(
							array(
									'Radio',
									'photosorder',
									array(
											'label' => $view->translate('Select the order below to display the photos on your site.'),
											'multiOptions' => array(
													1 => 'Newer to older',
													0 => 'Older to newer'
											),
											'value' => 1,
									)
							),
					),
			),
	),
)
?>
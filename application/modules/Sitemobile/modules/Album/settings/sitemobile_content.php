<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitemobile_content.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


//IF SITEALBUM PLUGIN IS ENABLED
$sitealbumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum');
if($sitealbumModule)
  $filter_types = array("likedPhotos" => "Most Liked", "recentPhotos" => "Recent", "viewedPhotos" => "Most Viewed", 'commentedPhotos' => 'Most Commented', 'featuredPhotos' => 'Featured');
else
  $filter_types = array("recentPhotos" => "Recent", "viewedPhotos" => "Most Viewed", 'commentedPhotos' => 'Most Commented');

$tempOtherInfoElement = array(
    "ownerName" => "Owner Name",
    "creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "ratingStar" => "Ratings Star",
    "location" => "Location",
    "directionLink" => "Get Direction Link (Dependent on Location)",
);

$photoElement = array(
    'MultiCheckbox',
    'photoInfo',
    array(
        'label' => $view->translate('Choose the options that you want to be displayed for the photos in this block.'),
        'multiOptions' => array_merge($tempOtherInfoElement, array("photoTitle" => "Photo Title"))
//        'value' => array("totalPhotos", "albumTitle", "ownerName", "creationDate", "ratingStar", "viewCount", "likeCount", "commentCount", "location", "directionLink"),
    ),
);

$photoTitleTruncation = array(
    'Text',
    'photoTitleTruncation',
    array(
        'label' => $view->translate('Truncation limit for photo title.'),
        'value' => 22,
    ),
    'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
    ),
);
return array(
    array(
        'title' => 'Profile Albums',
        'description' => 'Displays a member\'s albums on their profile.',
        'category' => 'Albums',
        'type' => 'widget',
        'name' => 'sitemobile.album-profile-albums',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Albums',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
   array(
        'title' => $view->translate('Ajax based Tabbed widget for Photos'),
        'description' => $view->translate('Displays the Recent, Most Liked, Most Viewed, Most Commented, Most Rated and Featured Photos in separate AJAX based tabs. Settings for this widget are available in the Widget Settings section of Advanced Albums Plugin.'),
        'category' => 'Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemobile.album-browse-photos',
        'defaultParams' => array(
            'title' => 'Photos',
            'margin_photo' => 2,
            'showViewMore' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'margin_photo',
                    array(
                        'label' => 'Horizontal / Vertical Margin between Elements',
                        'description' => '(Horizontal margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => $view->translate('Enter the height of each photo.'),
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => $view->translate('Enter the width of each photo.'),
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For This Block.'),
                        'value' => '328',
                    )
                ),
                $photoElement,
                array(
                    'MultiCheckbox',
                    'filter_types',
                    array(
                        'label' => $view->translate('Select the options below that you want to be displayed in this block.'),
                        'multiOptions' => $filter_types,
                   
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Photos to show)',
                        'value' => 10,
                    )
                ),
//                array(
//                    'Radio',
//                    'showViewMore',
//                    array(
//                        'label' => 'Show "View More" link',
//                        'multiOptions' => array(
//                            '1' => 'Yes',
//                            '0' => 'No',
//                        ),
//                    )
//                ),
                $photoTitleTruncation,
            ),
        ),
    ),
);
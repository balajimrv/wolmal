<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: content.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
return array(
    array(
        'title' => 'SES Advanced Photos -  Album Tags Cloud',
        'description' => 'Displays all tags of albums in cloud view. Edit this widget to choose various other settings.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.tag-cloud-albums',
        'autoEdit' => true,
        'adminForm' => 'Sesalbum_Form_Admin_Tagcloudalbum',
    ),
    array(
        'title' => 'SES Advanced Photos -  Member Profile Photo',
        'description' => '',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.profile-photo',
        'autoEdit' => false,
    ),
    array(
        'title' => 'SES Advanced Photos - Album / Photo Home No Album Message',
        'description' => 'Displays a message when there is no Album or Photo on your website. Edit this widget to choose for which content you want to place this widget.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.album-home-error',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'itemType',
                    array(
                        'label' => 'Choose the content type.',
                        'multiOptions' =>
                        array(
                            'album' => 'Albums',
                            'photo' => 'Photos',
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Album tags',
        'description' => 'Displays all album tags on your website. The recommended page for this widget is "SES - Advanced Photos - Browse Tags Page".',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.tag-albums',
    ),
    array(
        'title' => 'SES Advanced Photos - Photo View Page Options',
        'description' => 'This widget enables you to choose various options to be shown on photo view page like Slideshow of other photos associated with same album as the current photo, etc.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.photo-view-page',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'criteria',
                    array(
                        'label' => 'Choose from below the options that you want to show in this widget.',
                        'multiOptions' =>
                        array(
                            'like' => 'People who Liked the current photo',
                            'favourite' => 'People who added current photo as Favourite',
                            'tagged' => 'People who are Tagged in current photo',
                            'slideshowPhoto' => 'Slideshow of other photos associated with same album',
                        ),
                    ),
                ),
                array(
                    'Text',
                    'maxHeight',
                    array(
                        'label' => 'Enter the height of photo.',
                        'value' => 550,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'view_more_like',
                    array(
                        'label' => 'Enter the number of photos to be shown in "People Who Liked This Photo" block. After the number of photos entered below, option to view more photos in popup will be shown.',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'view_more_favourite',
                    array(
                        'label' => 'Enter the number of photos to be shown in "People Who Favourite This Photo" block. After the number of photos entered below, option to view more photos in popup will be shown.',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'view_more_tagged',
                    array(
                        'label' => 'Enter the number of photos to be shown in "People Who are Tagged in This Photo" block. After the number of photos entered below, option to view more photos in popup will be shown. ',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Recently Viewed Photos / Albums',
        'description' => 'This widget displays the recently viewed albums or photos by the user who is currently viewing your website or by the logged in members friend or by all the members of your website. Edit this widget to choose whose recently viewed content will show in this widget.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.recently-viewed-item',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'category',
                    array(
                        'label' => 'Choose from below the content types that you want to show in this widget.',
                        'multiOptions' =>
                        array(
                            'album' => 'Albums',
                            'photo' => 'Photos',
                        ),
                    ),
                ),
                array(
                    'Select',
                    'criteria',
                    array(
                        'label' => 'Display Criteria',
                        'multiOptions' =>
                        array(
                            'by_me' => 'Viewed By logged-in member',
                            'by_myfriend' => 'Viewed By logged-in member\'s friend',
                            'on_site' => 'Viewed by all members of website'
                        ),
                    ),
                ),
                array(
                    'Select',
                    'insideOutside',
                    array(
                        'label' => "Choose where do you want to show the statistics of photos / albums.",
                        'multiOptions' => array(
                            'inside' => 'Inside the Photo / Album Block',
                            'outside' => 'Outside the Photo / Album Block',
                        ),
                        'value' => 'inside',
                    )
                ),
                array(
                    'Select',
                    'fixHover',
                    array(
                        'label' => "Show photo / album statistics Always or when users Mouse-over on photo / album blocks (this setting will work only if you choose to show information inside the Photo / Album block.)",
                        'multiOptions' => array(
                            'fix' => 'Always',
                            'hover' => 'On Mouse-over',
                        ),
                        'value' => 'fix',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show in this widget.",
                        'multiOptions' => array(
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'rating' => 'Rating Stars',
                            'view' => 'Views Count',
                            'title' => 'Photo / Album Title',
                            'by' => 'Owner\'s Name',
                            'socialSharing' => 'Social Sharing Buttons',
                            'favouriteCount' => 'Favourites Count',
                            'downloadCount' => 'Downloads Count',
                            'photoCount' => 'Photos Count',
                            'featured' => 'Featured Label',
                            'sponsored' => 'Sponsored Label',
                            'likeButton' => 'Like Button',
                            'favouriteButton' => 'Favourite Button',
                        ),
                    )
                ),
                array(
                    'Text',
                    'title_truncation',
                    array(
                        'label' => 'Photo / Album title truncation limit.',
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one photo / album block (in pixels).',
                        'value' => '180',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one photo / album block (in pixels).',
                        'value' => '180',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'limit_data',
                    array(
                        'label' => 'Count (number of photo / album to show.)',
                        'value' => 20,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Album Categories Cloud',
        'description' => 'Displays all categories of albums in cloud view. Edit this widget to choose various other settings.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.tag-cloud-category',
        'autoEdit' => true,
        'adminForm' => 'Sesalbum_Form_Admin_Tagcloudcategory',
    ),
    array(
        'title' => 'SES Advanced Photos - Breadcrumb for Album View Page',
        'description' => 'Displays breadcrumb for albums. This widget should be placed on the \'SES - Advanced Photos - Album View page\'.',
        'category' => 'SES - Advanced Photos & Albums',
        'autoEdit' => false,
        'type' => 'widget',
        'name' => 'sesalbum.breadcrumb-album-view',
    ),
    array(
        'title' => 'SES Advanced Photos - Category Banner Widget',
        'description' => 'Displays a banner for categories. You can place this widget at browse page of category on your site.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesalbum.banner-category',
        'requirements' => array(
            'subject' => 'album',
        ),
        'adminForm' => 'Sesalbum_Form_Admin_Categorywidget',
    ),
    array(
        'title' => 'SES Advanced Photos - Album Category Block',
        'description' => 'Displays album categories in block view with their icon, and statistics. We recommend you to place this widget on "SES - Advanced Photos - Browse Categories Page", but if you want, then you can place this widget on any widgetized page as per your requirement.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesalbum.album-category',
        'requirements' => array(
            'subject' => 'album',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one block (in pixels).',
                        'value' => '160px',
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one block (in pixels).',
                        'value' => '160px',
                    )
                ),
                array(
                    'Select',
                    'allign_content',
                    array(
                        'label' => "Where you want to show content of this widget?",
                        'multiOptions' => array(
                            'center' => 'In Center',
                            'left' => 'In Left',
                            'right' => 'In Right',
                        ),
                        'value' => 'center',
                    ),
                ),
                array(
                    'Select',
                    'criteria',
                    array(
                        'label' => "Choose Popularity Criteria.",
                        'multiOptions' => array(
                            'alphabetical' => 'Alphabetical order',
                            'most_album' => 'Categories with maximum albums first',
                            'admin_order' => 'Admin selected order for categories',
                        ),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show on each block.",
                        'multiOptions' => array(
                            'title' => 'Category title',
                            'icon' => 'Category icon',
                            'countAlbums' => 'Album count in each category',
                        ),
                    )
                )
            ),
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Category Based Albums Slideshow',
        'description' => 'Displays albums in slideshow on the basis of their categories. This widget can be placed any where on your website.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesalbum.category-associate-album',
        'requirements' => array(
            'subject' => 'album',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'view_type',
                    array(
                        'label' => "Select the view type.",
                        'multiOptions' => array(
                            '1' => 'Slideshow',
                            '0' => 'Advanced Grid View with 5 photos'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show for albums in this widget.",
                        'multiOptions' => array(
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'rating' => 'Rating Stars',
                            'view' => 'Views Count',
                            'title' => 'Album Title',
                            'description' => 'Album Description',
                            'favourite' => 'Favourites Count',
                            'by' => 'Owner\'s Name',
                            'featuredLabel' => 'Featured Label',
                            'sponsoredLabel' => 'Sponsored Label',
                            'albumPhoto' => 'Current Album\'s Main Photo',
                            'photoCounts' => 'Photo Thumbnails of Current Album',
                            'photoThumbnail' => 'Album Thumbnails below category name',
                            'albumCount' => 'Album Counts',
                        ),
                    )
                ),
                array(
                    'Radio',
                    'popularity_album',
                    array(
                        'label' => 'Choose Photo Display Criteria.',
                        'multiOptions' => array(
                            "recently_created" => "Recently Created",
                            "most_viewed" => "Most Viewed",
                            "most_liked" => "Most Liked",
                            "most_rated" => "Most Rated",
                            "most_commented" => "Most Commented",
                            "most_favourite" => "Most Favourite",
                            "most_download" => "Most Downloaded",
                            'featured' => 'Only Featured',
                            'sponsored' => 'Only Sponsored',
                        ),
                        'value' => 'most_liked',
                    )
                ),
                array(
                    'Radio',
                    'pagging',
                    array(
                        'label' => "Do you want the albums to be auto-loaded when users scroll down the page?",
                        'multiOptions' => array(
                            'auto_load' => 'Yes, Auto Load.',
                            'button' => 'No, show \'View more\' link.',
                            'pagging' => 'No, show \'Pagination\'.'
                        ),
                        'value' => 'auto_load',
                    )
                ),
                array(
                    'Select',
                    'count_album',
                    array(
                        'label' => "Show albums count in each category.",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                    ),
                ),
                array(
                    'Select',
                    'criteria',
                    array(
                        'label' => "Choose Popularity Criteria.",
                        'multiOptions' => array(
                            'alphabetical' => 'Alphabetical order',
                            'most_album' => 'Categories with maximum albums first',
                            'admin_order' => 'Admin selected order for categories',
                        ),
                    ),
                ),
                array(
                    'Text',
                    'category_limit',
                    array(
                        'label' => 'count (number of categories to show).',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'album_limit',
                    array(
                        'label' => 'count (number of albums to show in each category).',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'photo_limit',
                    array(
                        'label' => 'count (number of photos to show in each album).',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'seemore_text',
                    array(
                        'label' => 'Enter the text for "+ See All" link. Leave blank if you don\'t want to show this link. (Use[category_name] variable to show the associated category name).',
                        'value' => '+ See all [category_name]',
                    )
                ),
                array(
                    'Select',
                    'allignment_seeall',
                    array(
                        'label' => "Choose alignment of \"+ See All\" field",
                        'multiOptions' => array(
                            'left' => 'Left',
                            'right' => 'Right'
                        ),
                    ),
                ),
                array(
                    'Text',
                    'title_truncation',
                    array(
                        'label' => 'Album title truncation limit.',
                        'value' => '150',
                    )
                ),
                array(
                    'Text',
                    'description_truncation',
                    array(
                        'label' => 'Album description truncation limit.',
                        'value' => '200',
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one photo in Album block(in pixels).',
                        'value' => '80',
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one photo in Album block (in pixels).',
                        'value' => '120',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Category View Page for All Category Levels',
        'description' => 'Displays banner, 2nd-level or 3rd level categories, albums associated with the current category\'s view page.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.category-view',
        'autoEdit' => true,
        'requirements' => array(
            'subject' => 'album',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'show_subcat',
                    array(
                        'label' => "Show 2nd-level or 3rd level categories blocks.",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'show_subcatcriteria',
                    array(
                        'label' => "Choose from below the details that you want to show on each categpory block.",
                        'multiOptions' => array(
                            'title' => 'Category title',
                            'icon' => 'Category icon',
                            'countAlbums' => 'Album count in each category',
                        ),
                    )
                ),
                array(
                    'Text',
                    'heightSubcat',
                    array(
                        'label' => 'Enter the height of one 2nd-level or 3rd level categor\'s block (in pixels).',
                        'value' => '160px',
                    )
                ),
                array(
                    'Text',
                    'widthSubcat',
                    array(
                        'label' => 'Enter the width of one 2nd-level or 3rd level category\'s block (in pixels).',
                        'value' => '250px',
                    )
                ),
                array(
                    'dummy',
                    'dummy1',
                    array(
                        'label' => "Album Settings"
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show on each album block.",
                        'multiOptions' => array(
                            'featuredLabel' => 'Featured Label',
                            'sponsoredLabel' => 'Sponsored Label',
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'rating' => 'Rating Stars',
                            'view' => 'Views Count',
                            'title' => 'Album Title',
                            'by' => 'Album Owner\'s Name',
                            'favourite' => 'Favourites Count',
                            'photo' => 'Photos Count'
                        ),
                    )
                ),
                array(
                    'Radio',
                    'pagging',
                    array(
                        'label' => "Do you want the albums to be auto-loaded when users scroll down the page?",
                        'multiOptions' => array(
                            'auto_load' => 'Yes, Auto Load.',
                            'button' => 'No, show \'View more\' link.',
                            'pagging' => 'No, show \'Pagination\'.'
                        ),
                        'value' => 'auto_load',
                    )
                ),
                array(
                    'Text',
                    'album_limit',
                    array(
                        'label' => 'count (number of albums to show).',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one album block (in pixels). [Note: This setting will not affect the album blocks displayed in Advanced View.]',
                        'value' => '160px',
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one album block (in pixels). [Note: This setting will not affect the album blocks displayed in Advanced View.]',
                        'value' => '160px',
                    )
                )
            )
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Breadcrumb for Photo View Page',
        'description' => 'Displays breadcrumb for photos. This widget should be placed on the \'SES - Advanced Photos - Photo View page\'.',
        'category' => 'SES - Advanced Photos & Albums',
        'autoEdit' => false,
        'type' => 'widget',
        'name' => 'sesalbum.breadcrumb-photo-view',
    ),
    array(
        'title' => 'SES Advanced Photos - Profile Albums',
        'description' => 'Displays a member\'s albums, photos and favorite photos on their profile. The recommended page for this widget is "Member Profile Page".',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.profile-albums',
        'autoEdit' => true,
        'adminForm' => 'Sesalbum_Form_Admin_Profilealbums',
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Browse Albums',
        'description' => 'Display all albums on your website. The recommended page for this widget is "SES - Advanced Photos - Browse Albums Page".',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.browse-albums',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'load_content',
                    array(
                        'label' => "Do you want the albums to be auto-loaded when users scroll down the page?",
                        'multiOptions' => array(
                            'auto_load' => 'Yes, Auto Load.',
                            'button' => 'No, show \'View more\' link.',
                            'pagging' => 'No, show \'Pagination\'.'
                        ),
                        'value' => 'auto_load',
                    )
                ),
                array(
                    'Radio',
                    'sort',
                    array(
                        'label' => 'Choose Album Display Criteria.',
                        'multiOptions' => array(
                            "recentlySPcreated" => "Recently Created",
                            "mostSPviewed" => "Most Viewed",
                            "mostSPliked" => "Most Liked",
                            "mostSPated" => "Most Rated",
                            "mostSPcommented" => "Most Commented",
                            "mostSPfavourite" => "Most Favourite",
                            'featured' => 'Only Featured',
                            'sponsored' => 'Only Sponsored',
                        ),
                        'value' => 'most_liked',
                    )
                ),
                array(
                    'Select',
                    'view_type',
                    array(
                        'label' => "Choose the View Type.",
                        'multiOptions' => array(
                            '1' => 'Grid View',
                            '2' => 'Advanced Grid View with 4 photos',
                        ),
                        'value' => '2',
                    )
                ),
                array(
                    'Select',
                    'insideOutside',
                    array(
                        'label' => "Choose where do you want to show the statistics of albums.",
                        'multiOptions' => array(
                            'inside' => 'Inside the Album Block',
                            'outside' => 'Outside the Album Block',
                        ),
                        'value' => 'inside',
                    )
                ),
                array(
                    'Select',
                    'fixHover',
                    array(
                        'label' => "Show album statistics Always or when users Mouse-over on album blocks (this setting will work only if you choose to show information inside the Album block.)",
                        'multiOptions' => array(
                            'fix' => 'Always',
                            'hover' => 'On Mouse-over',
                        ),
                        'value' => 'fix',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show for albums in this widget.",
                        'multiOptions' => array(
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'rating' => 'Rating Stars',
                            'view' => 'Views Count',
                            'title' => 'Album Title',
                            'by' => 'Owner\'s Name',
                            'socialSharing' => 'Social Sharing Buttons',
                            'favouriteCount' => 'Favourites Count',
                            'downloadCount' => 'Downloads Count',
                            'photoCount' => 'Photos Count',
                            'featured' => 'Featured Label',
                            'sponsored' => 'Sponsored Label',
                            'likeButton' => 'Like Button',
                            'favouriteButton' => 'Favourite Button',
                        ),
                    //'value' => array('like','comment','view','rating','title','by','socialSharing'),
                    )
                ),
                array(
                    'Text',
                    'title_truncation',
                    array(
                        'label' => 'Album title truncation limit.',
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'limit_data',
                    array(
                        'label' => 'Count (number of albums to show.)',
                        'value' => 20,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one album block (in pixels).',
                        'value' => 200,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one album block (in pixels).',
                        'value' => 236,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Photo Slideshow with Album Blocks',
        'description' => 'This widget displays a slideshow of featured, sponsored, popular photos as chosen by you with Album blocks below the slideshow. Edit this widget to choose the photo display criteria and number of albums blocks to be shown in this widget. You can place this widget anywhere on your website.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.welcome',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'criteria_slide',
                    array(
                        'label' => 'Choose Photo Display Criteria.',
                        'multiOptions' => array(
                            "featured" => "Featured Photos Only",
                            "sponsored" => "Sponsored Photos Only",
                            "featuredSPSponsored" => "Featured & Sponsored Photos Both",
                            "allincludedfeaturedsponsored" => "All Photos including Featured & Sponsored Photos",
                            "allexcludingfeaturedsponsored" => "All Photos excluding Featured & Sponsored Photos",
                        ),
                        'value' => 'featured',
                    )
                ),
                array(
                    'Select',
                    'slide_to_show',
                    array(
                        'label' => 'Choose Photo Popularity Criteria.',
                        'multiOptions' => array(
                            "recently_created" => "Recently Created",
                            "most_viewed" => "Most Viewed",
                            "most_liked" => "Most Liked",
                            "most_rated" => "Most Rated",
                            "most_commented" => "Most Commented",
                            "most_favourite" => "Most Favourite",
                            "most_download" => "Most Downloaded"
                        ),
                        'value' => 'recently_created',
                    )
                ),
                array(
                    'Text',
                    'height_slideshow',
                    array(
                        'label' => 'Height of slideshow (in pixels)',
                        'value' => 400,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'limit_data_slide',
                    array(
                        'label' => 'Count (number of photos to show.)',
                        'value' => 6,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'slide_title',
                    array(
                        'label' => "Enter slideshow caption.",
                        'value' => 'We make you look good',
                    )
                ),
                array(
                    'Textarea',
                    'slide_descrition',
                    array(
                        'label' => "Enter slideshow description.",
                        'value' => 'Premium royalty-free stock photos from the 500px community.',
                    )
                ),
                array(
                    'Select',
                    'enable_search',
                    array(
                        'label' => "Do you want to enable searching (auto-suggest) of photos / albums?",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No',
                        ),
                        'value' => 'yes',
                    )
                ),
								
								array(
                    'Select',
                    'is_fullwidth',
                    array(
                        'label' => "Do you want to show this widget in full width?",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => '0',
                    )
                ),
								
                array(
                    'Select',
                    'search_criteria',
                    array(
                        'label' => "Choose the content belonging to which results will be shown in Search.",
                        'multiOptions' => array(
                            'albums' => 'Albums',
                            'photos' => 'Photos',
                        ),
                        'value' => 'albums',
                    )
                ),
                array(
                    'Select',
                    'show_album_under',
                    array(
                        'label' => "Display Album Blocks below the Slideshow.",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No',
                        ),
                        'value' => 'yes',
                    )
                ),
                array(
                    'Select',
                    'show_statistics',
                    array(
                        'label' => "Do you want to show Albums & Photos statistics on your website in this widget?",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No',
                        ),
                        'value' => 'yes',
                    )
                ),
                array(
                    'Select',
                    'criteria_slide_album',
                    array(
                        'label' => 'Choose Album Display Criteria.',
                        'multiOptions' => array(
                            "featured" => "Featured Albums Only",
                            "sponsored" => "Sponsored Albums Only",
                            "featuredSPSponsored" => "Featured & Sponsored Albums Both",
                            "allincludedfeaturedsponsored" => "All Albums including Featured & Sponsored Albums",
                            "allexcludingfeaturedsponsored" => "All Albums excluding Featured & Sponsored Albums",
                        ),
                        'value' => 'featured',
                    )
                ),
                array(
                    'Select',
                    'album_criteria',
                    array(
                        'label' => "Choose Album Popularity criteria.",
                        'multiOptions' => array(
                            "recently_created" => "Recently Created",
                            "most_viewed" => "Most Viewed",
                            "most_liked" => "Most Liked",
                            "most_rated" => "Most Rated",
                            "most_commented" => "Most Commented",
                            "most_favourite" => "Most Favourite",
                            "most_download" => "Most Downloaded",
                        ),
                        'value' => 'recently_created',
                    )
                ),
                array(
                    'Select',
                    'limit_data_album',
                    array(
                        'label' => "Count (number of albums to show.)",
                        'multiOptions' => array('3' => 3, '6' => 6, '9' => 9, '12' => 12, '15' => 15),
                    ),
                    'value' => 3,
                ),
                array(
                    'Text',
                    'title_truncation',
                    array(
                        'label' => 'Album title truncation limit.',
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Photos & Albums Navigation Menu',
        'description' => 'Displays a navigation menu bar in the Advanced Photos & Albums plugin\'s pages for Albums, Albums Home, Browse Albums, Photos Home, Browse Photos, Browse Categories, etc pages.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Popular / Featured / Sponsored Photos or Albums',
        'description' => "Displays photos or albums as chosen by you based on chosen criteria for this widget. The placement of this widget depends on the criteria chosen for this widget.",
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesalbum.featured-sponsored',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'tableName',
                    array(
                        'label' => 'Choose the content type.',
                        'multiOptions' => array(
                            "album" => "Album",
                            "photo" => "Photo"
                        )
                    ),
                    'value' => 'photo'
                ),
                array(
                    'Select',
                    'criteria',
                    array(
                        'label' => "Display Content",
                        'multiOptions' => array(
                            '5' => 'All including Featured and Sponsored',
                            '1' => 'Only Featured',
                            '2' => 'Only Sponsored',
                            '3' => 'Both Featured and Sponsored',
                            '4' => 'All except Featured and Sponsored',
                        ),
                        'value' => 5,
                    )
                ),
                array(
                    'Select',
                    'info',
                    array(
                        'label' => 'Choose Popularity Criteria.',
                        'multiOptions' => array(
                            "recently_created" => "Recently Created",
                            "most_viewed" => "Most Viewed",
                            "most_liked" => "Most Liked",
                            "most_rated" => "Most Rated",
                            "most_commented" => "Most Commented",
                            "most_favourite" => "Most Favourite",
                            "most_download" => "Most Downloaded",
                        )
                    ),
                    'value' => 'recently_updated',
                ),
                array(
                    'Select',
                    'insideOutside',
                    array(
                        'label' => "Choose where do you want to show the statistics of photos or albums.",
                        'multiOptions' => array(
                            'inside' => 'Inside the Photo / Album Block',
                            'outside' => 'Outside the Photo / Album Block',
                        ),
                        'value' => 'inside',
                    )
                ),
                array(
                    'Select',
                    'fixHover',
                    array(
                        'label' => "Show photo / album statistics Always or when users Mouse-over on photos / albums (this setting will work only if you choose to show information inside the Photo / Album block.)",
                        'multiOptions' => array(
                            'fix' => 'Always',
                            'hover' => 'On Mouse-over',
                        ),
                        'value' => 'fix',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show for Photos / Albums in this widget.",
                        'multiOptions' => array(
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'rating' => 'Rating Stars',
                            'view' => 'Views Count',
                            'title' => 'Photo / Album Title',
                            'by' => 'Owner\'s Name',
                            'socialSharing' => 'Social Sharing Buttons',
                            'favouriteCount' => 'Favourites Count',
                            'downloadCount' => 'Downloads Count',
                            'photoCount' => 'Photos Count',
                            'featured' => 'Featured Label',
                            'sponsored' => 'Sponsored Label',
                            'likeButton' => 'Like Button',
                            'favouriteButton' => 'Favourite Button',
                        ),
                    )
                ),
                array(
                    'Radio',
                    'view_type',
                    array(
                        'label' => "Choose the View Type.",
                        'multiOptions' => array(
                            '1' => 'Horizontal',
                            '2' => 'Vertical',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'title_truncation',
                    array(
                        'label' => 'Photo / Album title truncation limit.',
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one photo / album block (in pixels).',
                        'value' => '180',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one photo / album block (in pixels).',
                        'value' => '180',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'limit_data',
                    array(
                        'label' => 'Count (number of photos / albums to show).',
                        'value' => 5,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Featured / Sponsored Photos Slideshow',
        'description' => "Displays Featured or Sponsored Slideshow of photos.",
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesalbum.slideshows',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'featured_sponsored_carosel',
                    array(
                        'label' => "Choose the content you want to show in this widget.",
                        'multiOptions' => array(
                            '1' => 'Featured Photo',
                            '3' => 'Sponsored Photo',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'insideOutside',
                    array(
                        'label' => "Choose where do you want to show the statistics of photos.",
                        'multiOptions' => array(
                            'inside' => 'Inside the Photo Block',
                            'outside' => 'Outside the Photo Block',
                        ),
                        'value' => 'inside',
                    )
                ),
                array(
                    'Select',
                    'fixHover',
                    array(
                        'label' => "Show photo statistics Always or when users Mouse-over on photo / album blocks (this setting will work only if you choose to show information inside the Photo block.)",
                        'multiOptions' => array(
                            'fix' => 'Always',
                            'hover' => 'On Mouse-over',
                        ),
                        'value' => 'fix',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show for photos in this widget.",
                        'multiOptions' => array(
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'rating' => 'Rating Stars',
                            'view' => 'Views Count',
                            'title' => 'Photo Title',
                            'by' => 'Owner\'s Name',
                            'socialSharing' => 'Social Sharing Buttons',
                            'favouriteCount' => 'Favourites Count',
                            'downloadCount' => 'Downloads Count',
                            'photoCount' => 'Photos Count',
                            'featured' => 'Featured Label',
                            'sponsored' => 'Sponsored Label',
                            'likeButton' => 'Like Button',
                            'favouriteButton' => 'Favourite Button',
                        ),
                    )
                ),
                array(
                    'Text',
                    'height_container',
                    array(
                        'label' => 'Enter the height of slideshow (in pixels).',
                        'value' => '400',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'num_rows',
                    array(
                        'label' => 'Enter number of rows to be shown in the Slideshow.',
                        'value' => '2',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Select',
                    'info',
                    array(
                        'label' => 'Choose Popularity Criteria.',
                        'multiOptions' => array(
                            "recently_created" => "Recently Created",
                            "most_viewed" => "Most Viewed",
                            "most_liked" => "Most Liked",
                            "most_rated" => "Most Rated",
                            "most_commented" => "Most Commented",
                            "most_favourite" => "Most Favourite",
                            "most_download" => "Most Downloaded",
                        )
                    ),
                    'value' => 'recently_updated',
                ),
                array(
                    'Text',
                    'title_truncation',
                    array(
                        'label' => 'Photo title truncation limit.',
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'limit_data',
                    array(
                        'label' => 'Count (number of photos to show.)',
                        'value' => 20,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Featured / Sponsored Photos or Albums Carousel',
        'description' => "Disaplys Featured or Sponsored Carousel of photos / albums.",
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesalbum.featured-sponsored-carosel',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'featured_sponsored_carosel',
                    array(
                        'label' => "Choose the content you want to show in this widget.",
                        'multiOptions' => array(
                            '1' => 'Featured Photos',
                            '2' => 'Featured Albums',
                            '3' => 'Sponsored Photos',
                            '4' => 'Sponsored Albums'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'mouseover',
                    array(
                        'label' => "Show nav buttons on mouseover?",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Select',
                    'insideOutside',
                    array(
                        'label' => "Choose where do you want to show the statistics of photos / albums.",
                        'multiOptions' => array(
                            'inside' => 'Inside the Photo / Album Block',
                            'outside' => 'Outside the Photo / Album Block',
                        ),
                        'value' => 'inside',
                    )
                ),
                array(
                    'Select',
                    'fixHover',
                    array(
                        'label' => "Show photo / album statistics Always or when users Mouse-over on photo / album blocks (this setting will work only if you choose to show information inside the Photo / Album block.)",
                        'multiOptions' => array(
                            'fix' => 'Always',
                            'hover' => 'On Mouse-over',
                        ),
                        'value' => 'fix',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show for photos / albums in this widget.",
                        'multiOptions' => array(
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'rating' => 'Rating Stars',
                            'view' => 'Views Count',
                            'title' => 'Photo / Album Title',
                            'by' => 'Owner\'s Name',
                            'socialSharing' => 'Social Sharing Buttons',
                            'favouriteCount' => 'Favourites Count',
                            'downloadCount' => 'Downloads Count',
                            'photoCount' => 'Photos Count',
                            'featured' => 'Featured Label',
                            'sponsored' => 'Sponsored Label',
                            'likeButton' => 'Like Button',
                            'favouriteButton' => 'Favourite Button',
                        ),
                    )
                ),
                array(
                    'Text',
                    'duration',
                    array(
                        'label' => 'Enter the delay time.',
                        'value' => '300',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one photo / album block (in pixels).',
                        'value' => '200',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one photo / album block (in pixels).',
                        'value' => '200',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Select',
                    'info',
                    array(
                        'label' => 'Choose Popularity Criteria.',
                        'multiOptions' => array(
                            "recently_created" => "Recently Created",
                            "most_viewed" => "Most Viewed",
                            "most_liked" => "Most Liked",
                            "most_rated" => "Most Rated",
                            "most_commented" => "Most Commented",
                            "most_favourite" => "Most Favourite",
                            "most_download" => "Most Downloaded",
                        )
                    ),
                    'value' => 'recently_updated',
                ),
                array(
                    'Text',
                    'title_truncation',
                    array(
                        'label' => 'Photo / Album Title truncation limit.',
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'limit_data',
                    array(
                        'label' => 'Count (number of photos / albums to show.)',
                        'value' => 5,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Radio',
                    'aliganment_of_widget',
                    array(
                        'label' => "Choose the View Type.",
                        'multiOptions' => array(
                            '1' => 'Horizontal',
                            '2' => 'Vertical',
                        ),
                        'value' => 1,
                    )
                )
            )
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Create New Photo Album Link',
        'description' => 'Displays a link to create new photo album.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.browse-menu-quick',
    ),
    array(
        'title' => 'SES Advanced Photos - Photos / Albums of the Day',
        'description' => "This widget displays photos / albums of the day as chosen by you from the Edit Settings of this widget.",
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesalbum.of-the-day',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'ofTheDayType',
                    array(
                        'label' => 'Choose content type to be shown in this widget.',
                        'multiOptions' => array(
                            'albums' => 'Album',
                            'photos' => 'Photo',
                        ),
                        'value' => 'albums',
                    )
                ),
                array(
                    'Select',
                    'insideOutside',
                    array(
                        'label' => "Choose where do you want to show the statistics of photos / albums.",
                        'multiOptions' => array(
                            'inside' => 'Inside the Photo / Album Block',
                            'outside' => 'Outside the Photo / Album Block',
                        ),
                        'value' => 'inside',
                    )
                ),
                array(
                    'Select',
                    'fixHover',
                    array(
                        'label' => "Show photo / album statistics Always or when users Mouse-over on photo / album blocks (this setting will work only if you choose to show information inside the Photo / Album block.)",
                        'multiOptions' => array(
                            'fix' => 'Always',
                            'hover' => 'On Mouse-over',
                        ),
                        'value' => 'fix',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show for photos / albums in this widget.",
                        'multiOptions' => array(
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'rating' => 'Rating Stars',
                            'view' => 'Views Count',
                            'title' => 'Photo / Album Title',
                            'by' => 'Owner\'s Name',
                            'socialSharing' => 'Social Sharing Buttons',
                            'favouriteCount' => 'Favourites Count',
                            'downloadCount' => 'Downloads Count',
                            'photoCount' => 'Photos Count',
                            'featured' => 'Featured Label',
                            'sponsored' => 'Sponsored Label',
                            'likeButton' => 'Like Button',
                            'favouriteButton' => 'Favourite Button',
                        ),
                    )
                ),
                array(
                    'Text',
                    'title_truncation',
                    array(
                        'label' => 'Photo / Album title truncation limit.',
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one photo / album block (in pixels).',
                        'value' => '180',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one photo / album block (in pixels).',
                        'value' => '180',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Album View Page Options',
        'description' => "Album View Page",
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesalbum.album-view-page',
        'adminForm' => 'Sesalbum_Form_Admin_Albumviewpage',
    ),
    array(
        'title' => 'SES Advanced Photos - Album/Photo Browse Search',
        'description' => 'Displays a search form in the album / photo browse page as placed by you. Edit this widget to choose the search option to be shown in the search form.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.browse-search',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'search_for',
                    array(
                        'label' => "Choose the content for which results will be shown.",
                        'multiOptions' => array(
                            'album' => 'Albums',
                            'photo' => 'Photos'
                        ),
                        'value' => 'album',
                    )
                ),
                array(
                    'Radio',
                    'view_type',
                    array(
                        'label' => "Choose the View Type.",
                        'multiOptions' => array(
                            'horizontal' => 'Horizontal',
                            'vertical' => 'Vertical'
                        ),
                        'value' => 'vertical',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'search_type',
                    array(
                        'label' => "Choose options to be shown in \'Browse By\' search fields.",
                        'multiOptions' => array(
                            'recentlySPcreated' => 'Recently Created',
                            'mostSPviewed' => 'Most Viewed',
                            'mostSPliked' => 'Most Liked',
                            'mostSPcommented' => 'Most Commented',
                            'mostSPrated' => 'Most Rated',
                            'mostSPfavourite' => 'Most Favourite',
                            'featured' => 'Only Featured',
                            'sponsored' => 'Only Sponsored'
                        ),
                    )
                ),
                array(
                    'Select',
                    'default_search_type',
                    array(
                        'label' => "Default \'Browse By\' search fields.",
                        'multiOptions' => array(
                            'recentlySPcreated' => 'Recently Created',
                            'mostSPviewed' => 'Most Viewed',
                            'mostSPliked' => 'Most Liked',
                            'mostSPcommented' => 'Most Commented',
                            'mostSPrated' => 'Most Rated',
                            'mostSPfavourite' => 'Most Favourite',
                            'featured' => 'Only Featured',
                            'sponsored' => 'Only Sponsored'
                        ),
                    )
                ),
                array(
                    'Radio',
                    'friend_show',
                    array(
                        'label' => "Show \'View\' search field?",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No'
                        ),
                        'value' => 'yes',
                    )
                ),
                array(
                    'Radio',
                    'search_title',
                    array(
                        'label' => "Show \'Search Photos or Albums/Keyword\' search field?",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No'
                        ),
                        'value' => 'yes',
                    )
                ),
                array(
                    'Radio',
                    'browse_by',
                    array(
                        'label' => "Show \'Browse By\' search field?",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No'
                        ),
                        'value' => 'yes',
                    )
                ),
                array(
                    'Radio',
                    'categories',
                    array(
                        'label' => "Show \'Categories\' search field?",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No'
                        ),
                        'value' => 'yes',
                    )
                ),
                array(
                    'Radio',
                    'location',
                    array(
                        'label' => "Show \'Location\' search field?",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No'
                        ),
                        'value' => 'yes',
                    )
                ),
                array(
                    'Radio',
                    'kilometer_miles',
                    array(
                        'label' => "Show \'Kilometer or Miles\' search field?",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No'
                        ),
                        'value' => 'yes',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Albums / Photos Categories for Searching',
        'description' => 'This widget enabled searching of albums and photos on the basis of their categories. Edit this widget to choose the view type and various other settings.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.browse-categories',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => "Choose the content type belonging to which categories will be shown in this widget.",
                        'multiOptions' => array(
                            'album' => 'Albums',
                            'photo' => 'Photos',
                        ),
                        'value' => 'album',
                    )
                ),
                array(
                    'Select',
                    'show_category_has_count',
                    array(
                        'label' => "Show only those categories which have albums / photos in them.",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No',
                        ),
                        'value' => 'yes',
                    )
                ),
                array(
                    'Select',
                    'show_count',
                    array(
                        'label' => "Show number of albums / photos in each category.",
                        'multiOptions' => array(
                            'yes' => 'Yes',
                            'no' => 'No',
                        ),
                        'value' => 'yes',
                    )
                ),
                array(
                    'Select',
                    'allign',
                    array(
                        'label' => "View Type",
                        'multiOptions' => array(
                            '1' => 'Horizontal',
                            '2' => 'Vertical',
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'limit_data',
                    array(
                        'label' => 'Count (number of categories to show).',
                        'value' => 5,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'SES Advanced Photos - Tabbed widget for Popular Photos / Albums',
        'description' => 'Displays a tabbed widget for popular photos / albums on your website on various popularity criteria. Edit this widget to choose tabs to be shown in this widget.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.tabbed-widget',
        'autoEdit' => true,
        'adminForm' => 'Sesalbum_Form_Admin_Tabbed',
    ),
    array(
        'title' => 'SES Advanced Photos - Tabbed widget for Manage Photos / Albums ',
        'description' => 'This widget displays albums and photos created, favourite, liked, rated by the member who views the manage page. Edit this widget to configure various settings.',
        'category' => 'SES - Advanced Photos & Albums',
        'type' => 'widget',
        'name' => 'sesalbum.tabbed-manage-widget',
        'autoEdit' => true,
        'adminForm' => 'Sesalbum_Form_Admin_Tabbedmanage',
    )
);

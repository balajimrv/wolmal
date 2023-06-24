<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Profilealbums.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Form_Admin_Profilealbums extends Engine_Form
{
  public function init()
  {
		
		$this->addElement('Radio', "view_type", array(
			'label' => "Choose the View Type for photos.",
        'multiOptions' => array(
            'masonry' => 'Masonry View',
			'grid' => 'Grid View',
        ),
        'value' => 'masonry',
    ));
		$this->addElement('Select', "insideOutside", array(
			'label' => 'Choose where do you want to show the statistics of photos.',
        'multiOptions' => array(
            'inside' => 'Inside the Photo  Block',
			'outside' => 'Outside the Photo Block',
        ),
        'value' => 'inside',
    ));
		$this->addElement('Select', "fixHover", array(
			'label' => 'Show photo  statistics Always or when users Mouse-over on photo  blocks (this setting will work only if you choose to show information inside the Photo  block.)',
        'multiOptions' => array(
           'fix' => 'Always',
		 'hover' => 'On Mouse-over',
					),
						'value' => 'fix',
    ));
		//album
		$this->addElement('Select', "insideOutside_profileAlbums", array(
			'label' => 'Choose where do you want to show the statistics of  albums.',
        'multiOptions' => array(
            'inside' => 'Inside the  Album Block',
			'outside' => 'Outside the  Album Block',
        ),
        'value' => 'inside',
    ));
		$this->addElement('Select', "fixHover_profileAlbums", array(
			'label' => 'Show album statistics Always or when users Mouse-over on  album blocks (this setting will work only if you choose to show information inside the Album block.)',
        'multiOptions' => array(
           'fix' => 'Always',
		 			 'hover' => 'On Mouse-over',
					),
						'value' => 'fix',
    ));
		$this->addElement('MultiCheckbox', "show_criteria", array(
        'label' => "Choose from below the details that you want to show in this widget.",
        'multiOptions' => array(
						'like' => 'Likes Count',
						'comment' => 'Comments Count',
						'rating' => 'Rating Stars',
						'view' => 'Views Count',
						'title' => 'Photo / Album Title',
						'by' => 'Owner\'s Name',
						'socialSharing' =>'Social Sharing Buttons',
						'favouriteCount' => 'Favourites Count',
						'downloadCount' => 'Downloads Count',
						'photoCount' => 'Photos Count',
						'featured' =>'Featured Label',
						'sponsored'=>'Sponsored Label',
						'likeButton' =>'Like Button',
						'favouriteButton' =>'Favourite Button',
        ),
    ));
		$this->addElement('Radio', "pagging", array(
			'label' => "Do you want the photos / albums to be auto-loaded when users scroll down the page?",
        'multiOptions' => array(
            'button' => 'View more',
            'auto_load' => 'Yes, Auto Load.',
			'pagging' =>'No, show \'Pagination\'.'
        ),
        'value' => 'auto_load',
    ));		
		$this->addElement('Text', "title_truncation", array(
			'label' => 'Photo / Album title truncation limit.',
        'value' => 45,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
		
		$this->addElement('Text', "height", array(
			'label' => 'Enter the height of one photo / album block (for Grid view in pixels).',
        'value' => '160',
    ));
		$this->addElement('Text', "height_masonry", array(
			'label' => 'Enter the height of one photo block (for Masonry view in pixels)..',
        'value' => '160',
    ));
		$this->addElement('Text', "width", array(
			'label' => 'Enter the width of one photo / album block (for Grid view in pixels).',
        'value' => '140',
    ));
		$this->addElement('MultiCheckbox', "search_type", array(
			 'label' => "Choose from below the Tabs that you want to show in this widget.",
			'multiOptions' => array(
					'taggedPhoto' => 'Photos of Profile Owner [This will show all photos in which profile owner is tagged.]',
					'photoofyou' => 'Profile Owner\'s Photos [This will show photos uploaded by profile owner.]',
					'profileAlbums' => 'Profile Owner\'s Albums [This will show albums uploaded by profile owner.]',
					'albumCreate' => 'Create Album Button', 
			),
    ));
		$this->addElement('Dummy', "dummy", array(
			 'label' => "Enter the order of the Tabs to be shown in this widget. ",
    ));
		$this->addElement('Text', "taggedPhoto_order", array(
			 'label' => "Photos of Profile Owner ",
			'value' => '1',
    ));
		$this->addElement('Text', "photoofyou_order", array(
			'label' =>'Profile Owner\'s Photos',
			'value' => '2',
    ));
		$this->addElement('Text', "profileAlbums_order", array(
			'label' =>'Profile Owner\'s Albums',
			'value' => '3',
    ));
		$this->addElement('Text', "albumCreate_order", array(
			'label' =>'Add Album Button',
			'value' => '4',
    ));
		$this->addElement('Text', "limit_data", array(
			'label' => 'count (number of photos / albums to show).',
        'value' => 20,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
		$this->addElement('Select', "show_limited_data", array(
			'label' => 'Show only the number of photos / albums entered in above setting. [If you choose No, then you can choose how do you want to show more photos / albums in this widget in below setting.]',
			'multiOptions' => array(
            'yes' => 'Yes',
            'no' => 'No',
        ),
        'value' => 'no',
    ));		
  }
}
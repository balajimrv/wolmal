<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Tabbedmanage.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Form_Admin_Tabbedmanage extends Engine_Form
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
			'label' => 'Choose where do you want to show the statistics of photos / albums.',
        'multiOptions' => array(
            'inside' => 'Inside the Photo / Album Block',
			'outside' => 'Outside the Photo / Album Block',
        ),
        'value' => 'inside',
    ));
		$this->addElement('Select', "fixHover", array(
			'label' => 'Show photo / album statistics Always or when users Mouse-over on photo / album blocks (this setting will work only if you choose to show information inside the Photo / Album block.)',
        'multiOptions' => array(
           'fix' => 'Always',
		 'hover' => 'On Mouse-over',
				),
			'value' => 'fix',
    ));
		$this->addElement('Text', "limit_data", array(
			'label' => 'count (number of photos / albums to show).',
        'value' => 20,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));		
		$this->addElement('Radio', "pagging", array(
			'label' => "Do you want the photos / albums to be auto-loaded when users scroll down the page?",
        'multiOptions' => array(
			'auto_load' => 'Yes, Auto Load.',
			'button' => 'No, show \'View more\' link.',
			'pagging' =>'No, show \'Pagination\'.'
        ),
        'value' => 'auto_load',
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
			'label' => 'Enter the height of one photo block (for Masonry view in pixels).',
        'value' => '160',
    ));
		$this->addElement('Text', "width", array(
			'label' => 'Enter the width of one photo / album block (for Grid view in pixels).',
        'value' => '140',
    ));
		$this->addElement('MultiCheckbox', "search_type", array(
			 'label' => "Choose from below the Tabs that you want to show in this widget.",
			'multiOptions' => array(
					'ownalbum' => 'My Albums',
					'likeAlbum' => 'Liked Albums',
					'likePhoto' => 'Liked Photos',
					'ratedAlbums' => 'Rated Albums',
					'ratedPhotos' => 'Rated Photos',
					'favouriteAlbums' => 'Favourite Albums',
					'favouritePhotos' => 'Favourite Photos',
					'featuredAlbums' =>'Featured Albums',
					'featuredPhotos' =>'Featured Photos',
					'sponsoredPhotos'=>'Sponsored Photos',
					'sponsoredAlbums' =>'Sponsored Albums',
			),
    ));
		$limit = 1;
		
		// setting for my albums
		
		$this->addElement('Dummy', "dummy", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'My Albums' Tab</span>",
    ));
	
		$this->getElement('dummy')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
	
		
		$this->addElement('Text', "ownalbum_order", array(
			 'label' => "Order of this Tab.",
			'value' => $limit++,
    ));
		$this->addElement('Text', "ownalbum_label", array(
     		'label' => 'Title of this Tab.',
			'value' => 'Albums',
    ));
	
		// setting for Liked Albums
		
				$this->addElement('Dummy', "dummy1", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Liked Albums' Tab</span>",
    ));
	
		$this->getElement('dummy1')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		$this->addElement('Text', "likeAlbum_order", array(
			'label' =>'Order of this Tab.',
			'value' =>$limit++,
    ));
		$this->addElement('Text', "likeAlbum_label", array(
    		'label' => 'Title of this Tab.',
			'value' => 'Liked Albums',
    ));
	
		// setting for Liked Photos
		
						$this->addElement('Dummy', "dummy2", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Liked Photos' Tab</span>",
    ));
	
		$this->getElement('dummy2')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

		$this->addElement('Text', "likePhoto_order", array(
			'label' =>'Order of this Tab.',
			'value' =>$limit++,
    ));
		$this->addElement('Text', "likePhoto_label", array(
     		'label' => 'Title of this Tab.',
			'value' => 'Liked Photos',
    ));
	
		// setting for Rated Albums
		
		$this->addElement('Dummy', "dummy3", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Rated Albums' Tab</span>",
    ));
	
		$this->getElement('dummy3')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		$this->addElement('Text', "ratedAlbums_order", array(
			'label' =>'Order of this Tab.',
			'value' => $limit++,
    ));
		$this->addElement('Text', "ratedAlbums_label", array(
     		'label' => 'Title of this Tab.',			
			'value' => 'Rated Albums',
    ));
	
		// setting for Rated Photos
		
		$this->addElement('Dummy', "dummy4", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Rated Photos' Tab</span>",
    ));
	
		$this->getElement('dummy4')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		$this->addElement('Text', "ratedPhotos_order", array(
			'label' =>'Order of this Tab.',
			'value' => $limit++,
    ));
		$this->addElement('Text', "ratedPhotos_label", array(
     		'label' => 'Title of this Tab.',
			'value' => 'Rated Photos',
    ));
	
		// setting for Favorite Album
		
		$this->addElement('Dummy', "dummy5", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Favorite Albums' Tab</span>",
    ));
	
		$this->getElement('dummy5')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		$this->addElement('Text', "favouriteAlbums_order", array(
			'label' =>'Order of this Tab.',
			'value' => $limit++,
    ));
		$this->addElement('Text', "favouriteAlbums_label", array(
     		'label' => 'Title of this Tab.',
			'value' => 'Favourite Albums',
    ));
	
		// setting for Favorite Photos
	
		$this->addElement('Dummy', "dummy6", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Favorite Photos' Tab</span>",
    ));
	
		$this->getElement('dummy6')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
	
		$this->addElement('Text', "favouritePhotos_order", array(
			'label' =>'Order of this Tab.',
			'value' => $limit++,
    ));
		$this->addElement('Text', "favouritePhotos_label", array(
     		'label' => 'Title of this Tab.',
			'value' => 'Favourite Photos',
    ));
	
		// setting for Featured Albums
		
		
		$this->addElement('Dummy', "dummy7", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Featured Albums' Tab</span>",
    ));
	
		$this->getElement('dummy7')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		$this->addElement('Text', "featuredAlbums_order", array(
			'label' =>'Order of this Tab.',
			'value' => $limit++,
    ));
		$this->addElement('Text', "featuredAlbums_label", array(
     		'label' => 'Title of this Tab.',
			'value' => 'Featured Albums',
    ));
	
		// setting for Featured Photos
		
		$this->addElement('Dummy', "dummy8", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Featured Photos' Tab</span>",
    ));
	
		$this->getElement('dummy8')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		$this->addElement('Text', "featuredPhotos_order", array(
			'label' =>'Order of this Tab.',
			'value' => $limit++,
    ));
		$this->addElement('Text', "featuredPhotos_label", array(
     		'label' => 'Title of this Tab.',
			'value' => 'Featured Photos',
    ));
	
		// setting for Sponsored Albums
		
		$this->addElement('Dummy', "dummy9", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Sponsored Albums' Tab</span>",
    ));
	
		$this->getElement('dummy9')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		$this->addElement('Text', "sponsoredAlbums_order", array(
			'label' =>'Order of this Tab.',
			'value' => $limit++,
    ));
		$this->addElement('Text', "sponsoredAlbums_label", array(
     		'label' => 'Title of this Tab.',
			'value' => 'Sponsored Albums',
    ));
	
		// setting for Sponsored Photos
			
		$this->addElement('Dummy', "dummy10", array(
			 'label' => "<span style='font-weight:bold;'>Order and Title of 'Sponsored Photos' Tab</span>",
    ));
	
		$this->getElement('dummy10')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		$this->addElement('Text', "sponsoredPhotos_order", array(
			'label' =>'Order of this Tab.',
			'value' => $limit++,
    ));
		$this->addElement('Text', "sponsoredPhotos_label", array(
     		'label' => 'Title of this Tab.',
			'value' => 'Sponsored Photos',
    ));
  }
}
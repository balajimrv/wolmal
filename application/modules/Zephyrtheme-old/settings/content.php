<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  PixyThemes
 * @license    http://pixythemes.com/terms-and-conditions/
 * @author     http://pixythemes.com/
 */
 
// Get available files
$imageOptions = array('' => 'Default Text/Image');
$imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

$it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
foreach( $it as $file ) {
  if( $file->isDot() || !$file->isFile() ) continue;
  $basename = basename($file->getFilename());
  if( !($pos = strrpos($basename, '.')) ) continue;
  $ext = strtolower(ltrim(substr($basename, $pos), '.'));
  if( !in_array($ext, $imageExtensions) ) continue;
  $imageOptions['public/admin/' . $basename] = $basename;
}

return array(
  array(
    'title' => 'Header Menu',
    'description' => 'Displays Main Menu, Search, Logo, User Menu, Edit Profile Menu, User Photo, and other',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.header-menu',
    'autoEdit' => false,
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
	  )
	),
  ),
  
  array(
    'title' => 'Home - Main Image | Deprecated',
    'description' => 'Deprecated widget. Displays a nice presentation on Landing Page',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.home-main-old',
	'autoEdit' => true,
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Radio',
		  'home_imgposition',
		  array(
			'label' => 'Elements order',
			'description' => '',
			'multiOptions' => array(
			  0 => 'Image/Video/Signup on the left, text on the right',
			  1 => 'Text on the left, Image/Video/Signup  on the right',
			  2 => 'Text centered, no Image/Video/Signup',
			),
			'value' => 0,
		  )
		),
		array(
		  'Select',
		  'home_usesignup',
		  array(
			'label' => 'Show Signup Form(*1st step) instead of image/video?',
			'description' => '',
			'multiOptions' => array(
			  '1' => 'No',
			  '2' => 'Yes',
			),
			'value' => '1',
		  )
		),
		array(
		  'Select',
		  'home_img',
		  array(
			'label' => 'Illustration Image',
			'description' => '',
			'multiOptions' => $imageOptions
		  )
		),
		array(
		  'Textarea',
		  'home_video',
		  array(
			'label' => 'Video Embed Code',
			'description' => 'The video will replace the image selected above. Leave it blank if you want to see the image.',
			'value' => ''
		  )
		),
		array(
		  'Select',
		  'home_bgimg',
		  array(
			'label' => 'Background Image',
			'description' => '',
			'multiOptions' => $imageOptions
		  )
		),
		array(
		  'Select',
		  'home_h1',
		  array(
			'label' => '<h1> Headline',
			'description' => 'Show first Headline?',
			'multiOptions' => array(
			  0 => 'No',
			  1 => 'Yes',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'home_h2',
		  array(
			'label' => '<h2> Headline',
			'description' => 'Show second Headline?',
			'multiOptions' => array(
			  0 => 'No',
			  1 => 'Yes',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'home_signup',
		  array(
			'label' => 'Register Button',
			'description' => 'Show Register button?',
			'multiOptions' => array(
			  0 => 'No',
			  1 => 'Yes',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'home_uselogin',
		  array(
			'label' => 'Login Button',
			'description' => 'Show Login button?',
			'multiOptions' => array(
			  0 => 'No',
			  1 => 'Yes',
			),
			'value' => 1,
		  )
		),
		array(
		  'Text',
		  'home_imageheight',
		  array(
			'label' => 'The height of this area',
			'description' => 'E.g.: 520px',
			'value' => '520px',
		  )
		),
		array(
		  'Text',
		  'home_eltop',
		  array(
			'label' => 'Elements margin-top',
			'description' => 'E.g.: 10%',
			'value' => '10%',
		  )
		),
	  )
	),
  ),
  
  array(
    'title' => 'Home - Responsive Slider',
    'description' => '',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.home-slider',
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
	  )
	),
  ),

  array(
    'title' => 'Home - Features',
    'description' => 'Displays up to 4 boxes with image, title, and text.',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.home-boxes',
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
	  )
	),
  ),

  array(
    'title' => 'Home - Community',
    'description' => 'Displays two headlines and one image.',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.home-community',
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
	  )
	),
  ),

  array(
    'title' => 'Home - Logos',
    'description' => 'Displays logos at the bottoms of the Landing Page.',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.home-logos',
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
	  )
	),
  ),
  
  array(
    'title' => 'Home - Bottom',
    'description' => 'Displays the bottom area that contains two headlines and a signup button.',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.home-bottom',
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
	  )
	),
  ),
  
  array(
    'title' => 'Footer Menu',
    'description' => 'Displays Footer Menu, Copyright Notice, Language Box',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.footer-menu',
    'autoEdit' => false,
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
	  )
	),
  ),
  
  array(
    'title' => 'Member Photo - Menu',
    'description' => 'Display the member\'s profile picture and quicklinks.',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.member-photomenu',
    'autoEdit' => true,
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'mpmShowheadline',
		  array(
			'label' => 'Show the headline that contains the member\'s name?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'mpmShowphoto',
		  array(
			'label' => 'Show member\'s photo?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'mpmShowmenu',
		  array(
			'label' => 'Show quicklinks menu?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
	  )
	),
  ),
  
  array(
    'title' => 'Profile Photo - Menu',
    'description' => 'Display the member\'s profile picture and profile options menu.',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.profile-photomenu',
    'autoEdit' => true,
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Text',
		  'title',
		  array(
			'label' => 'Title',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Select',
		  'mpmShowphoto',
		  array(
			'label' => 'Show member\'s photo?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'mpmShowmenu',
		  array(
			'label' => 'Show profile options menu?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
	  )
	),
  ),

  array(
    'title' => 'Tabbed Members',
    'description' => 'This widget will display tabs with "Popular Members" - "Newest Members" - "Women" - "Men".',
    'category' => 'Zephyr Theme Widgets',
    'type' => 'widget',
    'name' => 'zephyrtheme.tabbed-members',
    'autoEdit' => true,
    'requirements' => array(
      'no-subject',
    ),
	'adminForm' => array(
	  'elements' => array(
		array(
		  'Select',
		  'nomobile',
		  array(
			'label' => 'Hide on mobile site?',
			'disableLoadDefaultDecorators' => true,
		  )
		),
		array(
		  'Text',
		  'tbbnumber',
		  array(
			'label' => 'Number of Members',
			'description' => 'Number of members shown in each tab (default: 22)',
			'value' => 22,
		  )
		),
		array(
		  'Select',
		  'tbbpopular',
		  array(
			'label' => 'Show "Popular Members" Tab?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'tbbnewest',
		  array(
			'label' => 'Show "Newest Members" Tab?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'tbbwomen',
		  array(
			'label' => 'Show "Women" Tab?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'tbbmen',
		  array(
			'label' => 'Show "Men" Tab?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'tbbbrowse',
		  array(
			'label' => 'Show "Browse Members" Link?',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Yes',
			  2 => 'No',
			),
			'value' => 1,
		  )
		),
		array(
		  'Select',
		  'tbbnavposition',
		  array(
			'label' => 'Navigation Menu Position',
			'description' => '',
			'multiOptions' => array(
			  1 => 'Top',
			  2 => 'Bottom',
			),
			'value' => 2,
		  )
		),
	  )
	),
  ),
  
  
) ?>
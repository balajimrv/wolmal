<?php
/**
 * Pixythemes
 *
 * @category   Application_Extensions
 * @package    ZephyrTheme
 * @copyright  Pixythemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */
return array(
  '1.0.0' => array(
    'Zephyr Theme' => 'First Version',
  ),
  '1.0.1' => array(
    'settings/changelog.php' => 'Incremented version',
	'settings/my.sql' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
	'Form/Admin/Styling.php' => 'Fixed content background-color field description',
	'Form/Admin/Typography.php' => 'Added 2 more websafe fonts and websafe fonts option for Headings',
	'widgets/header-menu/index.tpl' => 'Removed meta viewport for mobile devices',
	'widgets/header-menu/Controller.php' => 'Removed responsive setting',
	'Bootstrap.php' => 'Added better meta viewport for mobile devices',
	'widgets' => 'Changed the widgets author from SkayzThemes to Pixythemes',
	'application/themes/zephyr/manifest.php' => 'Incremented version',
	'application/themes/zephyr/media-queries.css' => 'Removed "mobile things"',
	'application/themes/zephyr/theme.css' => 'Small fixes. Added "mobile things" from media-queries.css',
	'application/themes/zephyr/theme.jpg' => 'Replaced SkayzThemes logo with PixyThemes logo on theme thumbnail',
  ),
  '1.0.2' => array(
    'settings/changelog.php' => 'Incremented version',
	'settings/my.sql' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
	'Form/Admin/Landingpage.php' => 'Added option for enable signup form and other two options for adjusting the top image area',
	'controllers/AdminLandingpageController' => 'Added constants support for a few settings',
	'widgets/home/index.tpl' => 'Added signup form',
	'widgets/home/Controller.php' => 'Added signup form setting',
	'application/themes/zephyr/constants.css' => 'Added code for Signup Form, plus other fixes',
	'application/themes/zephyr/theme.css' => 'Added constant for Signup Form, plus other fixes',
  ),
  '1.0.3' => array(
    'settings/changelog.php' => 'Incremented version',
	'settings/my.sql' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
	'widgets/header-menu/header_1-5.tpl' => 'Changed the ID of login button to Class',
	'Form/Admin/Landingpage.php' => 'Added option for login button',
	'widgets/home/index.tpl' => 'Added login button',
	'widgets/home/Controller.php' => 'Added login button settings',
	'application/themes/zephyr/theme.css' => 'Added styles for home login button',
	'application/themes/zephyr/constants.css' => 'Removed some spacing that was causing CDN to misinterpret some links',
	'application/themes/zephyr/media-queries.css' => 'Added some fixes',
	'application/languages/en/zephyrtheme.csv' => 'Added line for home login button',
  ),
  '1.1.0' => array(
    'settings/changelog.php' => 'Incremented version',
	'externals/images/' => 'Changed some images for Landing Page',
	'Form/Admin/Header.php' => 'Added "boxed" style option for Header. Fixed a issue with the images links',
	'Form/Admin/Footer.php' => 'Added "boxed" style option for Footer. Fixed a issue with the images links',
	'Form/Admin/Landingpage.php' => 'Added option for changing the style of the page',
	'controllers/AdminLandingpageController' => 'Added constants support for a few settings',
	'widgets/footer-menu/footer_1.php' => 'Removed an unneeded class',
	'widgets/footer-menu/footer_2.php' => 'Removed a piece of unneeded HTML code',
	'widget/home/index.tpl' => 'Added signup form fields description on focus',
	'application/themes/zephyr/theme.css' => 'Added styles for second home style, styles for Hire-Experts plugins, plus a few other things',
	'application/themes/zephyr/constants.css' => 'Added constant',
	'application/themes/zephyr/media-queries.css' => 'Added fixes for the new styles added, styles for Hire-Experts plugins',
  ),
  '1.1.1' => array(
    'settings/changelog.php' => 'Incremented version',
	'Form/Admin/Header.php' => 'Changed a property to fix a scaffold notice',
	'Form/Admin/Footer.php' => 'Changed a property to fix a scaffold notice',
	'Form/Admin/Landingpage.php' => 'Changed a property to fix a scaffold notice',
	'Form/Admin/Styling.php' => 'Changed a property to fix a scaffold notice',
	'application/themes/zephyr/constants.css' => 'Changed a few properties to fix some scaffold notices',
  ),
  '1.1.2' => array(
    'settings/changelog.php' => 'Incremented version',
	'Form/Admin/Header.php' => 'Added option for signup popup',
	'widgets/header-menu/index.tpl' => 'Added javascript for popup login-signup box',
	'widgets/header-menu/Controller.php' => 'Added option for signup popup',
	'widgets/header-menu/header_1,2,3,4,5.php' => 'Added signup popup',
	'widgets/home/index.tpl' => 'Added javascript for popup login-signup box',
	'widgets/header-menu/Controller.php' => 'Added option for signup popup',
	'application/themes/zephyr/theme.css' => 'Added styles for signup popup. Removed mobile styles',
	'application/themes/zephyr/media-queries.css' => 'Added styles for signup popup. Added mobile styles',
	'application/languages/en/zephyrtheme.csv' => 'Added new phrases for signup box',
  ),
  '1.1.3' => array(
    'settings/changelog.php' => 'Incremented version',
	'application/themes/zephyr/theme.css' => 'Added some fixes for login-signup popup box',
	'application/themes/zephyr/media-queries.css' => 'Added some fixes for login-signup popup box',
  ), 
  '1.1.4' => array(
    'settings/changelog.php' => 'Incremented version',
	'application/themes/zephyr/theme.css' => 'Added some fixes',
  ),  
  '1.2.0' => array(
    'settings/changelog.php' => 'Incremented version',
	'controllers/AdminLandingpageController' => 'Added settings for Slideshow',
	'controllers/AdminHeaderController' => 'Added settings for Main-Menu Icons and a few other',
	'Form/Admin/Ladingpage.php' => 'Removed settings for Main Image which has been replaced by Slideshow',
	'Form/Admin/Header.php' => 'Added a few settings',
	'widgets/home-boxes/Controller.php' => 'Fixed broken links',
	'widgets/home-boxes/index.tpl' => 'Fixed broken links',
	'widgets/header-menu' => 'Added new files. Modified all actual files to add new notification system, icons for Main-Menu, new layout, and a few more changes',
	'application/themes/zephyr/theme.css' => 'Code for the new notification system, main-menu icons, home page slideshow and other fixes',
	'application/themes/zephyr/constants.css' => 'Removed Google Fonts imports because they were creating some issues',
	'application/themes/zephyr/media-queries.css' => 'Added support for Landing Page slider and a few other fixes',
	'Bootstrap.php' => 'Added Google Fonts to HEAD',
  ),
) ?>
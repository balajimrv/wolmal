<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorelikebox_Form_Admin_Global extends Engine_Form {

  public function init() {

    $this->setTitle( 'General Settings' )
        ->setDescription( 'Embeddable Store Badge / Like Box Settings' ) ;

    //VALUE FOR WIDTH.
    $this->addElement( 'Radio' , 'likebox_width' , array (
      'label' => 'Badge Width' ,
      'description' => 'Do you want Store Admins to be able to set the width of their embeddable Store Badge / Like Box?' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'onclick' => 'showwidthOptions(this.value)' ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.width' , 1 ) ,
        ) ) ;

    //VALUE FOR DEFAULT WIDTH.
    $this->addElement( 'Text' , 'likebox_default_width' , array (
      'label' => 'Default Badge Width' ,
      'description' => 'Enter the default width of embeddable Store Badges / Like Box.' ,
      'allowEmpty' => false ,
      'required' => true ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.width' , 300 ) ,
        ) ) ;

    //VALUE FOR HEIGHT.
    $this->addElement( 'Radio' , 'likebox_hight' , array (
      'label' => 'Badge Height' ,
      'description' => 'Do you want Store Admins to be able to set the height of their embeddable Store Badge / Like Box?' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'onclick' => 'showheightOptions(this.value)' ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.hight' , 1 ) ,
        ) ) ;

    //VALUE FOR DEFAULT HEIGHT.
    $this->addElement( 'Text' , 'likebox_default_hight' , array (
      'label' => 'Default Badge Height' ,
      'description' => 'Enter the default height of embeddable Store Badges / Like Box.' ,
      'allowEmpty' => false ,
      'required' => true ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.hight' , 660 ) ,
        ) ) ;

    //VALUE FOR COLOR SEHEME.
    $this->addElement( 'Radio' , 'likebox_colorschme' , array (
      'label' => 'Color Scheme' ,
      'description' => 'Do you want Store Admins to be able to select a color scheme for their embeddable Store Badge / Like Box? (You can configure the 2 color schemes, light and dark to match your site’s theme from the Color Schemes section.)' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'onclick' => 'showcolorOptions(this.value)' ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.colorschme' , 1 ) ,
        ) ) ;

    //VALUE FOR COLOR SCHEME OPTION.
    $this->addElement( 'Select' , 'likebox_default_colorschme' , array (
      'label' => "Default Color Scheme" ,
      'description' => 'Select the default color scheme of embeddable Store Badges / Like Box.' ,
      'multiOptions' => array (
        "light" => "Light" ,
        "dark" => "Dark"
      ) ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.colorschme' , 'light' ) ,
        ) ) ;

    //VALUE FOR FACES.
    $this->addElement( 'Radio' , 'likebox_faces' , array (
      'label' => 'Profile Photos for Likes' ,
      'description' => 'Do you want Store Admins to be able to select whether or not to show the profile photos of users who Like their Store in their embeddable Store Badge / Like Box?' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'onclick' => 'showfacesOptions(this.value)' ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.faces' , 1 ) ,
        ) ) ;

    //VALUE DEFAULT FACES.
    $this->addElement( 'Select' , 'likebox_default_faces' , array (
      'label' => "Default Profile Photos Display" ,
      'description' => 'Select the default visibility for profile photos of users who Like that Store in its embeddable Store Badges / Like Box.' ,
      'multiOptions' => array (
        "display" => "Display" ,
        "donotdisplay" => "Do not Display"
      ) ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.faces' , 'display' ) ,
        ) ) ;

    //VALUE FOR HEADER.
    $this->addElement( 'Radio' , 'likebox_header' , array (
      'label' => 'Badge Header' ,
      'description' => 'Do you want Store Admins to be able to select whether or not to show the badge headers on embeddable Store Badges / Like Boxes? (Badge headers contain your Site Title and link back to your website.)' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'onclick' => 'showheaderOptions(this.value)' ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.header' , 1 ) ,
        ) ) ;

    //VALUE FOR DEFAULT HEADER.
    $this->addElement( 'Select' , 'likebox_default_header' , array (
      'label' => "Default Badge Header Display" ,
      'description' => 'Select the default visibility for badge header on embeddable Store Badges / Like Box.' ,
      'multiOptions' => array (
        "display" => "Display" ,
        "donotdisplay" => "Do not Display"
      ) ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.header' , 'display' ) ,
        ) ) ;

    //VALUE FOR BORDER COLOR.
    $this->addElement( 'Radio' , 'likebox_bordercolor' , array (
      'label' => 'Badge Border Color' ,
      'description' => 'Do you want Store Admins to be able to customize the border color for their embeddable Store Badge / Like Box? (You can set the border colors for the 2 color schemes from the Color Schemes section.)' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.bordercolor' , 1 ) ,
        ) ) ;

    //VALUE LIKE BUTTON.
    $this->addElement( 'Radio' , 'likebox_likebutton' , array (
      'label' => 'Like Button' ,
      'description' => 'Do you want the Like Button to be available in the embeddable Store Badges / Like Boxes? (With the Like Button, viewers of the embeddable Store Badges / Like Box will be easily and quickly able to Like its Store.)' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.likebutton' , 1 ) ,
        ) ) ;


    //VALUE FOR BADGES.
    if ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitestorebadge' ) && (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitestorebadge.isActivate' , 0 )) ) {
      $this->addElement( 'Radio' , 'likebox_badge' , array (
        'label' => 'Assigned Badge' ,
        'description' => 'Do you want to display the badge assigned to Stores in their embeddable Store Badge / Like Box?' ,
        'multiOptions' => array (
          1 => 'Yes' ,
          0 => 'No'
        ) ,
        'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.badge' , 1 ) ,
          ) ) ;
    }


    //VALUE FOR DUMMY ELEMENTS.
    $this->addElement( 'Dummy' , 'tab_common' , array (
      'label' => 'With the next few fields, you can choose the tabs that should be available in embeddable Store Badges / Like Box. Store Admins will additionally be able to choose while creating a Store Badge whether a tab selected by you should be visible in it or not. The names and sequence of displayed tabs will be same as that on Store Profile.' ,
      'value' => '0' ,
        ) ) ;

    //VALUE FOR INFO.
    $this->addElement( 'Radio' , 'likebox_info' , array (
      'label' => 'Info Tab' ,
      'description' => 'Do you want Info Tab to be available for display in embeddable Store Badges / Like Boxes? (It will contain the same information as the Info Tab on Store Profile.)' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.info' , 1 ) ,
        ) ) ;

    //VALUE FOR MAP.
    $this->addElement( 'Radio' , 'likebox_map' , array (
      'label' => 'Map Tab' ,
      'description' => 'Do you want Map Tab to be available for display in embeddable Store Badges / Like Boxes?' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.map' , 1 ) ,
        ) ) ;

    // Element:modules
    //$includeModules = Engine_Api::_()->sitestore()->getEnableSubModules() ;

    $enableSubModules = array ( ) ;
    $includeModule = array ( "sitestorealbum" => "Photos" , "sitestorepoll" => "Polls" , "sitestoredocument" => 'Documents' , "sitestoreoffer" => 'Offers' , "sitestorevideo" => "Videos" , "sitestoreevent" => "Events" , "sitestorenote" => "Notes" , "sitestorediscussion" => "Discussions" , "sitestoremusic" => "Music" , "sitestorereview" => "Reviews & Ratings" , "sitestoreform" => "Form" , "sitestoreinvite" => "Invite & Promote" , "sitestorebadge" => "Badges" , "sitestorelikebox" => "External Badge" ) ;

    $enableAllModule = Engine_Api::_()->getDbtable( 'modules' , 'core' )->getEnabledModuleNames() ;
    $includeModules = array_intersect( array_keys( $includeModule ) , $enableAllModule ) ;
    foreach ( $includeModules as $module ) {
      if ( Engine_Api::_()->sitestore()->isPluginActivate( $module ) ) {
        $enableSubModules[$module] = $includeModule[$module] ;
      }
    }

    unset( $enableSubModules['sitestorelikebox'] ) ;
    if ( isset( $enableSubModules['sitestorebadge'] ) )
      unset( $enableSubModules['sitestorebadge'] ) ;
    if ( isset( $enableSubModules['sitestoreinvite'] ) )
      unset( $enableSubModules['sitestoreinvite'] ) ;
    if ( isset( $enableSubModules['sitestoreform'] ) )
      unset( $enableSubModules['sitestoreform'] ) ;

    if ( isset( $enableSubModules['sitestorereview'] ) ) {
      unset( $enableSubModules['sitestorereview'] ) ;
      $enableSubModules['review'] = "Reviews & Ratings" ;
    }

    $modules_likebox = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'modules.likebox' ) ;
    $values['modules_likebox'] = unserialize( $modules_likebox ) ;

    if ( !empty( $enableSubModules ) ) {
      $desc_modules = Zend_Registry::get( 'Zend_Translate' )->_( "Select the Apps from which tabs should be available for display in embeddable Store Badges / Like Boxes. (The Apps that you see below are from the extensions that are installed on your website. To see the complete list of available extensions, please visit <a href='http://www.socialengineaddons.com/catalog/directory-stores-extensions' target='_blank' >here</a>.)" ) ;

      //VALUE FOR MODULES OPTION.
      $this->addElement( 'MultiCheckbox' , 'modules_likebox' , array (
        'description' => $desc_modules ,
        'label' => 'Tabs from Apps' ,
        'multiOptions' => $enableSubModules ,
        'value' => $values['modules_likebox'] ,
          ) ) ;
      $this->modules_likebox->addDecorator( 'Description' , array ( 'placement' => Zend_Form_Decorator_Abstract::PREPEND , 'escape' => false ) ) ;
    }

    //VALUE FOR POWRED BY.
    $this->addElement( 'Radio' , 'likebox_powred' , array (
      'label' => 'Powered By' ,
      'description' => 'Do you want to display the ‘Powered By’ text and the ‘logo’ of your site in embeddable Store Badges / Like Boxes? (If yes, then you will be able to upload the logo below. These will link back to your website.)' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'onclick' => 'showfileOptions(this.value)' ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.powred' , 1 ) ,
        ) ) ;


    //VALUE FOR TITLE OF POWERED BY.
    $this->addElement( 'Radio' , 'logo_title' , array (
      'label' => 'Logo or Title' ,
      'description' => 'For the “Powered By”, do you want to show your site’s Logo with Site Title on hover? (You can upload logo below. If you select No, then your Site Title text will be shown after Powered By)' ,
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'onclick' => 'showlogotitleOptions(this.value)' ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'logo.title' , 1 ) ,
        ) ) ;


    //VALUE FOR LOGO PHOTO.
    $this->addElement( 'File' , 'logo_photo' , array (
      'label' => 'Upload Logo' ,
      'description' => 'Upload the image for site Logo. (The recommended dimension is: 30 x 20 pixels.)' ,
        ) ) ;

    $this->logo_photo->addValidator( 'Extension' , false , 'jpg,png,gif,jpeg' ) ;
    $description = "<div class='tip'><span>" . Zend_Registry::get( 'Zend_Translate' )->_( "You have not uploaded an image for site logo. Please upload an image." ) . "</span></div>" ;
    $logo_photo = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'logo.photo' ) ;
    if ( !empty( $logo_photo ) ) {
      $view = Zend_Registry::isRegistered( 'Zend_View' ) ? Zend_Registry::get( 'Zend_View' ) : null ;
      $photoName = $view->baseUrl() . '/public/sitestorelikebox/logo/' . $logo_photo ;
      $description = "<img src='$photoName' />" ;
    }

    //VALUE FOR LOGO PREVIEW.
    $this->addElement( 'Dummy' , 'logo_photo_preview' , array (
      'label' => 'Logo Preview' ,
      'description' => $description ,
        ) ) ;
    $this->logo_photo_preview
        ->addDecorator( 'Description' , array ( 'placement' => Zend_Form_Decorator_Abstract::PREPEND , 'escape' => false ) ) ;

    //VALUE FOR CONTENT SHOW.
    $this->addElement( 'Text' , 'likebox_contentshow' , array (
      'label' => 'Content Count' ,
      'allowEmpty' => false ,
      'required' => true ,
      'maxlength' => '3' ,
      'description' => "For Store content from various Apps, how many content items should be shown in their tabs in embeddable Store Badges / Like Boxes? (If there are more content items of this type than the limit, then a 'View More' link will direct users to the website to view them.)" ,
      'required' => true ,
      'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.contentshow' , 5 ) ,
      'validators' => array (
        array ( 'Int' , true ) ,
        array ( 'GreaterThan' , true , array ( 0 ) ) ,
      ) ,
        ) ) ;

    // ADD SUBMIT BUTTON.
    $this->addElement( 'Button' , 'submit' , array (
      'label' => 'Save Changes' ,
      'type' => 'submit' ,
      'ignore' => true
        ) ) ;
  }

}
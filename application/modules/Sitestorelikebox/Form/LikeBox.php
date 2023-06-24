<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LikeBox.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorelikebox_Form_LikeBox extends Engine_Form {

  public $_error = array ( ) ;
  protected $_item ;

  public function getItem() {
    return $this->_item ;
  }

  public function setItem( Core_Model_Item_Abstract $item ) {
    $this->_item = $item ;
    return $this ;
  }

  public function init() {
    parent::init() ;
    $sitestore = $this->getItem() ;
    $hasPackageEnable = Engine_Api::_()->sitestore()->hasPackageEnable() ; 
    $paramaName = Engine_Api::_()->sitestorelikebox()->getWidgteParams();
    foreach( $paramaName as $order => $infoArray ) {
			switch($infoArray['name']) {
				case 'advancedactivity.home-feeds':
				case 'activity.feed':
				case 'seaocore.feed':
				$flag = 1;
				break;
				case 'sitestore.info-sitestore':
				$flag1 = 1;
				break;
				case 'sitestore.location-sitestore':
				$flag2 = 1;
				break;
				case 'sitestore.discussion-sitestore':
				$flag3 = 1;
				break;			
				case 'sitestore.photos-sitestore':
				$flag4 = 1;
				break;
				case 'sitestoreevent.profile-sitestoreevents':
				$flag5 = 1;
				break;
				case 'sitestorepoll.profile-sitestorepolls':
				$flag6 = 1;
				break;
				case 'sitestorenote.profile-sitestorenotes':
				$flag7 = 1;
				break;
				case 'sitestoreoffer.profile-sitestoreoffers':
				$flag8 = 1;
				break;
				case 'sitestorevideo.profile-sitestorevideos':
				$flag9 = 1;
				break;			
				case 'sitestoremusic.profile-sitestoremusic':
				$flag10 = 1;
				break;
				case 'sitestorereview.profile-sitestorereviews':
				$flag11 = 1;
				break;
				case 'sitestoredocument.profile-sitestoredocuments':
				$flag12 = 1;
				break;			
			}
		}
    $this
        //->setTitle('Like Box Settings')
        // ->setDescription('Overview enables you to create a rich profile for your Store using the editor below. Compose the overview and click "Save Overview" to save it.')
        ->setAttrib( 'name' , 'sitestores_likebox' ) ;

    //VALUE FOR URL
    $this->addElement( 'Text' , 'url' , array (
      'label' => 'Your Store URL' ,
      'decorators' => array ( array ( 'ViewScript' , array (
            'viewScript' => '_formurlField.tpl' ,
            'class' => 'form element'
          ) ) )
        ) ) ;

		$apiSettings = Engine_Api::_()->getApi( 'settings' , 'core' );
    $view = Zend_Registry::get('Zend_View');
    if ( $apiSettings->getSetting( 'likebox.width' , 1 ) ) {

      //VALUE FOR WIDTH.
      $this->addElement( 'Text' , 'widht' , array (
        'label' => Zend_Registry::get( 'Zend_Translate' )->_( "Badge Width (px)" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Width of the embeddable badge in pixels.' ) . "</span> </a>" ,
        'attribs' => array ( 'style' => 'width:80px; max-width:80px;' ) ,
        'onblur' => "setLikeBox()" ,
        'value' => "300" ,
          ) ) ;
      $this->getElement( 'widht' )->getDecorator( 'Label' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.hight' , 1 ) ) {

      //VALUE FOR HEIGHT.
      $this->addElement( 'Text' , 'height' , array (
        'label' => Zend_Registry::get( 'Zend_Translate' )->_( "Badge Height (px)" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Height of the embeddable badge in pixels.' ) . "</span> </a>" ,
        'attribs' => array ( 'style' => 'width:80px; max-width:80px;' ) ,
        'onblur' => "setLikeBox()" ,
        'value' => "660" ,
          ) ) ;
      $this->getElement( 'height' )->getDecorator( 'Label' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.colorschme' , 1 ) ) {

      //VALUE FOR COLOR SCHEME.
      $this->addElement( 'Select' , 'colorscheme' , array (
        'label' => Zend_Registry::get( 'Zend_Translate' )->_( "Color Scheme" ) . "<a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Color scheme of the embeddable badge.' ) . "</span></a>" ,
        'multiOptions' => array ( 'light' => "Light" , 'dark' => "Dark" ) ,
        'onchange' => "setLikeBox()"
          ) ) ;
      $this->getElement( 'colorscheme' )->getDecorator( 'Label' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.faces' , 1 ) ) {

      //VALUE FOR FACES.
      $this->addElement( 'checkbox' , 'faces' , array (
        'label' => 'Show profile photos' ,
        'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Profile Photos for Likes" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show profile photos of users who like the store in the embeddable badge.' ) . "</span></a>" ,
        'value' => "1" ,
        'onchange' => "setLikeBox()" ,
          ) ) ;
      $this->getElement( 'faces' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.bordercolor' , 1 ) ) {

      //VALUE FOR BORDER COLOR.
      $this->addElement( 'Text' , 'border_color' , array (
        'label' => 'Color Scheme Code:' ,
        'decorators' => array ( array ( 'ViewScript' , array (
              'viewScript' => '_formBorderColor.tpl' ,
              'class' => 'form element' ) ) )
          ) ) ;
    }

    //VALUE FOR TITLE TRUNCATION.
    $this->addElement( 'Text' , 'titleturncation' , array (
      'label' => Zend_Registry::get( 'Zend_Translate' )->_( "Title Truncation Limit" ) . "<a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Number of characters to be shown in the Title.' ) . "</span></a>" ,
      'attribs' => array ( 'style' => 'width:80px; max-width:80px;' ) ,
      'onblur' => "setLikeBox()" ,
      'value' => "50" ,
        ) ) ;
    $this->getElement( 'titleturncation' )->getDecorator( 'Label' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;

    if ( $apiSettings->getSetting( 'likebox.header' , 1 ) ) {

      //VALUE FOR HEADER.
      $this->addElement( 'checkbox' , 'header' , array (
        'label' => 'Show header' ,
        'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Header" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . sprintf( Zend_Registry::get( 'Zend_Translate' )->_( 'Show the \'Find us on %s\' bar at top of the embeddable badge.' ) , $apiSettings->getSetting( 'core.general.site.title' ) ) . "</span></a>" ,
        'value' => "1" ,
        'onchange' => "setLikeBox()" ,
          ) ) ;
      $this->getElement( 'header' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }


    //VALUE FOR STREAM.
    $this->addElement( 'checkbox' , 'stream' , array (
      'label' => 'Show store data and content' ,
      'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Store Data and Content" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show data and content of store in the embeddable badge.<br/> Below, you can further choose what all to show.' ) . "</span></a>" ,
      'value' => "1" ,
      //'onchange' => "setLikeBox()" ,
      'onchange' => "showOptions(this.value)" ,
        ) ) ;
    $this->getElement( 'stream' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;

    if ( !empty($flag) ) {
    //VALUE FOR UPDATES.
    $this->addElement( 'checkbox' , 'streamupdatefeed' , array (
      'label' => 'Show Updates' ,
      'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Updates" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store updates in the embeddable badge.' ) . "</span></a>" ,
      'value' => "1" ,
      'onchange' => "setLikeBox()" ,
        ) ) ;
    $this->getElement( 'streamupdatefeed' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.info' , 1 )  && ( !empty($flag1) )) {

      //VALUE FOR INFO.
      $this->addElement( 'checkbox' , 'streaminfo' , array (
        'label' => 'Show Info' ,
        'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Info" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store information in the embeddable badge.' ) . "</span></a>" ,
        'value' => "1" ,
        'onchange' => "setLikeBox()" ,
          ) ) ;
      $this->getElement( 'streaminfo' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

		//VALUE FOR MAP.
		if ( !empty($sitestore->location) && $apiSettings->getSetting( 'likebox.map' , 1 ) && ( !empty($flag2) ) ) {
			$this->addElement( 'checkbox' , 'streammap' , array (
				'label' => 'Show Map' ,
				'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Map" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store location map in the embeddable badge.' ) . "</span></a>" ,
				'value' => "1" ,
				'onchange' => "setLikeBox()" ,
					) ) ;
			$this->getElement( 'streammap' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
		}


    $moduleName = $apiSettings->getSetting( 'modules_likebox' ) ;
    $moduleName = unserialize( $moduleName ) ;
    $moduleName_temp = array ( ) ;

    foreach ( $moduleName as $key => $values ) {

      if ( $values != 'review' ) {

        if ( !Engine_Api::_()->sitestorelikebox()->allowModule( $sitestore , $values , $hasPackageEnable ) )
          continue ;
      } else {
// 					if(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitestorereview' ) && (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitestorereview.isActivate' , 0 ))) {
						$values = 'sitestorereview' ;
// 					} else {
// 						$values = '' ;
// 					}
      }
			$elementName=null;
			switch ($values) {
				case 'sitestorealbum':
				if (!empty($flag4)) {
					$elementName =  'streamalbum';
					$label = 'Show Photos';
					$description = Zend_Registry::get( 'Zend_Translate' )->_( "Photos" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store photos in the embeddable badge.' ) . "</span></a>" ;
				}
				break;

				case 'sitestoremusic':
				if (!empty($flag10)) {
					$elementName =   'streammusic';
					$label = 'Show Music';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Music" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store music in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitestoreevent':
				if (!empty($flag5)) {
					$elementName =   'streamevent';
					$label = 'Show Events';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Events" ) . "<a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store events in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitestorereview':
				if (!empty($flag11)) {
					if(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitestorereview' ) && (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitestorereview.isActivate' , 0 ))){
					$elementName =   'streamreview';
					$label = 'Show Reviews & Ratings';
					$description = Zend_Registry::get( 'Zend_Translate' )->_( "Reviews" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store reviews and ratings in the embeddable badge.' ) . "</span></a>" ;}
				}
				break;

				case 'sitestorepoll':
				if (!empty($flag6)) {
					$elementName =   'streampoll';
					$label = 'Show Polls';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Polls" ) . "<a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store polls in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitestorediscussion':
				if (!empty($flag3)) {
					$elementName =   'streamdiscussion';
					$label = 'Show Discussions';
					$description = Zend_Registry::get( 'Zend_Translate' )->_( "Discussions" ) . "<a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store discussions in the embeddable badge.' ) . "</span></a>" ; 
				}
				break;

				case 'sitestorenote':
				if (!empty($flag7)) {
					$elementName =   'streamnote';
					$label = 'Show Notes';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Notes" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store notes in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitestorevideo':
				if (!empty($flag9)) {
					$elementName =   'streamvideo';
					$label = 'Show Videos';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Videos" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store videos in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitestoreoffer':
				if (!empty($flag8)) {
					$elementName =   'streamoffer';
					$label = 'Show Offers';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Offers" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store offers in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitestoredocument':
				if (!empty($flag12)) {
					$elementName =   'streamdocument';
					$label = 'Show Documents';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Documents" ) . " <a href='javascript:void(0);' class='sitestorelikebox_show_tooltip_wrapper'> [?] <span class='sitestorelikebox_show_tooltip'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show store documents in the embeddable badge.' ) . "</span></a>" ;
				}
				break;
			}
			if(!empty($elementName)){
			$this->addElement( 'checkbox' , $elementName , array (
          'label' => $label ,
          'description' => $description ,
          'value' => "1" ,
          'onchange' => "setLikeBox()" ,
            ) ) ;
      $this->getElement( $elementName )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
		}
	}

    //ADD FOR BUTTON.
    $this->addElement( 'Button' , 'save' , array (
      'label' => 'Get Code' ,
      'href' => 'javascript:void(0);' ,
      'link' => true ,
      'decorators' => array ( 'ViewHelper' ) ,
      'onclick' => "getCode()" ,
        ) ) ;
  }
}
?>
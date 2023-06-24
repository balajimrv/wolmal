<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Bootstrap.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
class Sesalbum_Bootstrap extends Engine_Application_Bootstrap_Abstract {
  public function __construct($application) {
    parent::__construct($application);
		$baseURL = Zend_Registry::get('StaticBaseUrl');	
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$view->headTranslate(array(
		'Album added as Favourite successfully', 'Album Unfavourited successfully', 'Photo Liked successfully', 'Photo Unliked successfully', 'Unmark as Featured', 'Mark Featured', 'Unmark as Sponsored', 'Mark Sponsored', 'Photo Unfavourited successfully', 'Photo added as Favourite successfully', 'Album liked successfully', 'Album Unliked successfully'
		));
		 if(strpos(str_replace('/','',$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']),str_replace('/','',$_SERVER['SERVER_NAME'].'admin'))=== FALSE){
			$this->initViewHelperPath();
			$headScript = new Zend_View_Helper_HeadScript();
			$headScript->appendFile($baseURL
								 .'application/modules/Sesalbum/externals/scripts/core.js');			
			$headScript->appendFile($baseURL
								. 'application/modules/Sesbasic/externals/scripts/flexcroll.js');
						
			$headScript
			->appendFile($baseURL . 'externals/moolasso/Lasso.js')
			->appendFile($baseURL . 'application/modules/Sesbasic/externals/scripts/Lasso.Crop.js');
		if (APPLICATION_ENV == 'production')
			$headScript
				->appendFile($baseURL . 'externals/autocompleter/Autocompleter.min.js');
		else
			$headScript
				->appendFile($baseURL . 'externals/autocompleter/Observer.js')
				->appendFile($baseURL . 'externals/autocompleter/Autocompleter.js')
				->appendFile($baseURL . 'externals/autocompleter/Autocompleter.Local.js')
				->appendFile($baseURL. 'externals/autocompleter/Autocompleter.Request.js');
		$headScript
			->appendFile($baseURL. 'application/modules/Sesbasic/externals/scripts/tagger.js');
			if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum_enable_location', 1))
				$headScript->appendFile('https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.mapApiKey', ''));
		 }
  }
}
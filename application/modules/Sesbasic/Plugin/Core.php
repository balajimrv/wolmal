<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesbasic
 * @package    Sesbasic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php 2015-07-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesbasic_Plugin_Core extends Zend_Controller_Plugin_Abstract {
	
	public function onRenderLayoutDefaultSimple($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
  
	public function onRenderLayoutMobileDefault($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
  
	public function onRenderLayoutMobileDefaultSimple($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
  
	public function onRenderLayoutDefault($event) {
	
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $themeName = $view->layout()->themes[0];
    if ($themeName == 'sesmodern' || $themeName == 'sesclean')
      include APPLICATION_PATH . '/application/modules/Sesbasic/views/scripts/theme_responsive.tpl';
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$moduleName = $request->getModuleName();
		$actionName = $request->getActionName();
		$controllerName = $request->getControllerName();
		if($controllerName == 'error' && $moduleName == 'core' && $actionName == 'requireuser'){
      $headScript = new Zend_View_Helper_HeadScript();
      $headScript->prependFile(Zend_Registry::get('StaticBaseUrl')
									 .'application/modules/Sesbasic/externals/scripts/sesJquery.js');
    }
		$script =
"var videoURLsesbasic;
 var moduleName;
 var itemType;
 var sesbasicdisabletooltip = ".Engine_Api::_()->getApi('settings', 'core')->getSetting('sesbasic.disable.tooltip',0).";";
$script .=
            "var openVideoInLightBoxsesbasic = " . Engine_Api::_()->getApi('settings', 'core')->getSetting('sesbasic.enable.lightbox', 1) . ";
";
    $view->headScript()->appendScript($script);
    $checkPaymentExtentionsEnable = Engine_Api::_()->sesbasic()->checkSesPaymentExtentionsEnable();
		$getCurrentCurrency = Engine_Api::_()->sesbasic()->getCurrentCurrency();
    if($checkPaymentExtentionsEnable && Engine_Api::_()->sesbasic()->multiCurrencyActive()) {
      $fullySupportedCurrencies = Engine_Api::_()->sesbasic()->getSupportedCurrency();
      $currencyData = '<li class="sesbasic_mini_menu_currency_chooser"><a href="javascript:;" id="sesbasic_btn_currency"><span>'.Engine_Api::_()->sesbasic()->getCurrentCurrency().'</span><i class="fa fa-caret-down"></i></a><div class="sesbasic_mini_menu_currency_chooser_dropdown" id="sesbasic_currency_change"><ul id="sesbasic_currency_change_data">';
      foreach ($fullySupportedCurrencies as $key => $values) {
				if($getCurrentCurrency == $key)
					$active ='selected';
				else
					$active ='';
        $currencyData .= '<li class="'.$active.'"><a href="javascript:;" data-rel="'.$key.'">'.$key.'</a></li>';
      }
      $currencyData .= '</ul></div></li>';
      $script = 'sesJqueryObject(document).ready(function(e){
          if(!sesJqueryObject(".sesariana_currencydropdown").length)
          sesJqueryObject("#core_menu_mini_menu").find("ul").first().append(\''.$currencyData.'\');
          else{
          sesJqueryObject(".sesariana_currencydropdown").html(\''.$currencyData.'\');
          if(!sesJqueryObject(".sesariana_currencydropdown").children().length)
            sesJqueryObject(".sesariana_currencydropdown").parent().remove();
          }
      })';
      $view->headScript()->appendScript($script);
    } else{
      $script = 'sesJqueryObject(document).ready(function(e){
            sesJqueryObject(".sesariana_currencydropdown").parent().remove();
      })';
      $view->headScript()->appendScript($script);
    }
    $sesalbum_enable_module = Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesalbum'));
    $sesvideo_enable_module = Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesvideo'));
//     if($actionName == 'index' && $controllerName == 'index' && $moduleName == 'core'){
//     } else {
      if(($sesalbum_enable_module || $sesvideo_enable_module) && Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.allow.adult.filtering',1)){
        $getvalue =  Engine_Api::_()->getApi('core', 'sesbasic')->checkAdultContent();
        if($getvalue)
          $attr = 'checked=""';
        else
          $attr = '';
        $contentAdultFiltering = '<li class="onoffswitch-wrapper"><div class="onoffswitch"><input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" '.$attr.' ><label class="onoffswitch-label" for="myonoffswitch"><span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label></div><span>Allow 18+ Content</span></li>';
        $script = 'sesJqueryObject(document).ready(function(e){
        sesJqueryObject("#core_menu_mini_menu").find("ul").first().append(\''.$contentAdultFiltering.'\');
        })';
        $view->headScript()->appendScript($script);
      }
    //}
  }
}
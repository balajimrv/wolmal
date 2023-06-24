<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_ShoppingCartController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $button_position = $this->_getParam('position');
    $button_title = $this->_getParam('title');
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    
    
    $script = <<<EOF
      
  var shoppingCartHandler;
  en4.core.runonce.add(function() {
    try {
      shoppingCartHandler = new ShoppingCartHandler({
				'shoppingCart_text' : 'Shopping Cart',
        'baseUrl' : 'sitestoreproduct',
        'enableShoppingCart' : true,
        'stylecolor' : 'red',
        'mouseovercolor' : 'green',
        'classname' : 'shoppingCart-button'
      });

        shoppingCartHandler.start();
      window._shoppingCartHandler = shoppingCartHandler;
    } catch( e ) {
      //if( \$type(console) ) console.log(e);
    }
  });
EOF;
      
      $view->headScript()
        ->appendFile($view->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/scripts/shopping_cart.js')
        ->appendScript($script);
      
 }

}

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formCopyProduct.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$product_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('product_id', 0);
$store_id = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id)->store_id;
$getTemUrl = $view->url(array('action' => 'store', 'store_id' => $store_id), 'sitestore_dashboard', true);
echo '<div class="form-label"><label></label></div>
<div class="form-element" id="buttons-element">

<button type="submit" id="execute" name="execute"> ' . $this->translate("Save Settings") . '</button>

 or <a href="' . $getTemUrl . '" type="button" id="cancel" name="cancel"> ' . $this->translate("cancel") . '</a></div>';
  
?>
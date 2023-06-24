<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formCreateProduct.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', 0);
$getTemUrl = $view->url(array('module'=>'sitestoreproduct', 'controller'=>'product','action' => 'manage', 'store_id' => $store_id), 'default', true);
?>
<div class="form-label">
	<label></label>
</div>
<div id="buttons-element" class="form-wrapper">
	<button type="submit" id="execute" name="execute">
		<?php echo $this->translate("Create");?>
	</button>
	<?php echo $this->translate("or");?>
	<a onclick="window.location.href ='<?php echo $getTemUrl;?>';return false;" name="cancel_mobile">
		<?php echo $this->translate("cancel");?>
	</a>
</div>
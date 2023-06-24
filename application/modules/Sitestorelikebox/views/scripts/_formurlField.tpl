<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formurlField.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$sitestore = Engine_Api::_()->getItem( 'sitestore_store' , $request->getParam( 'store_id' ) ) ;
	$url = "http://" . $_SERVER['HTTP_HOST'] . $this->url( array ( 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl( $request->getParam( 'store_id' ) ) ) , 'sitestore_entry_view' , true ) ;
?>
<div id="url-wrapper" class="form-wrapper">
	<label for="url" class="optional"><?php echo $this->translate('Your Store URL'); ?>
		<a href="javascript:void(0);" class="sitestorelikebox_show_tooltip_wrapper"> [?]
			<span class="sitestorelikebox_show_tooltip">
				<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/tooltip_arrow.png"><?php echo $this->translate('The Store Title and Store Photo will link to this URL.') ?>
			</span>
		</a>
	</label>
	<div id="url-element" class="form-element sitestorelikebox_show_tooltip_wrapper">
		<input type="text" name="url" id="url" value="<?php echo $url; ?>" style="width:250px; max-width:250px;" disabled="disabled">
		<span class="sitestorelikebox_show_tooltip">
			<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/tooltip_arrow.png">
			<?php echo $url; ?>
		</span>
	</div>
</div>
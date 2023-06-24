<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSetAdminTaxRegion.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php
echo '
<div id="region_backgroundimage"> </div>
<div id="state-wrapper" class="form-wrapper">
	<div id="state-label" class="form-label">
	 <label for="state" class="optional">' . $this->translate("Regions / States") . '</label>
	</div>
	<div id="state-element" class="form-element">
		<select name="state[]" id="state" multiple="multiple">
			
		</select>
	</div>
</div>';


?>
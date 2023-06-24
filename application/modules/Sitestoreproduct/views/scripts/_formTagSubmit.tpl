<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formTagSubmit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
echo '
<button type="submit" id="submit" name="submit">' . $this->translate("Save Settings") . '</button><div id="spiner-image" style="display: inline-block;"></div>';
echo $this->translate(" or ");
?><a href="javascript:void(0);" onclick='manage_store_dashboard(62, "manage", "printing-tag")' ><?php echo $this->translate("Cancel") ?></a>

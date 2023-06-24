<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-tab.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form class="global_form_popup" method="POST">
	<?php echo $this->translate('Edit Tab Name:');?>
	<input type="text" name="tab_name" value="<?php echo $this->offer_tab_name;?>"><br /><br />
	<button type="submit" id="done" name="done" >
		<?php echo $this->translate('Save Changes'); ?>
	</button>
	<?php echo $this->translate(" or "); ?> 
	<a onclick="javascript:parent.Smoothbox.close();" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('cancel'); ?></a>
</form>

<script type="text/javascript">
  var success = '<?php echo $this->success;?>';
  if(success == 1) {
   parent.Smoothbox.close();
  }
</script>
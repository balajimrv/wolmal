<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/admin-review/_navigationAdmin.tpl'; ?>

<div class='seaocore_settings_form'>
	<div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">

	window.addEvent('domready', function(){
		prosconsInReviews('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.proscons', 1); ?>');
	});

	function prosconsInReviews(option) {

		if($('sitestoreproduct_proncons-wrapper')) {
			if(option == 1) {
			$('sitestoreproduct_proncons-wrapper').style.display = 'block';
			$('sitestoreproduct_limit_proscons-wrapper').style.display = 'block';
			} else {
				$('sitestoreproduct_proncons-wrapper').style.display = 'none';
				$('sitestoreproduct_limit_proscons-wrapper').style.display = 'none';
			}
		}

	}

</script>
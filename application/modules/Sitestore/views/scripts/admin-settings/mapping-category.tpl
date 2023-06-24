<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitestore
* @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: mapping-category.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>
<?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
<?php if( @$this->closeSmoothbox || $this->close_smoothbox): ?>
	<script type="text/javascript">
		window.parent.location.href='<?php echo $baseurl?>' +'/admin/sitestore/settings/sitestorecategories';
		window.parent.Smoothbox.close();
	</script>
<?php endif; ?>

<script type="text/javascript">
	function closeSmoothbox() {
		window.parent.savecat_result('<?php echo $this->catid;?>', '<?php echo $this->catid;?>', '<?php echo $this->oldcat_title;?>', '<?php echo $this->subcat_dependency;?>');
		window.parent.Smoothbox.close();
	}
</script>


<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editstyle.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
  var submitformajax = 1;
</script>
<?php
	$this->headScript()
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
	?>
<?php if (empty($this->is_ajax)) : ?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
	<div class="layout_middle">
		<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; ?>
		<div class="sitestore_edit_content">
			<div class="sitestore_edit_header">
				<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()),$this->translate('VIEW_STORE')) ?>
				<h3><?php echo $this->translate('Dashboard: ').$this->sitestore->title; ?></h3>
			</div>
      <div id="show_tab_content">
<?php endif; ?> 
  
	<?php echo $this->form->render($this); ?>
	<br />
  <div id="show_tab_content_child">
  </div>
	<?php if (empty($this->is_ajax)) : ?>
	    </div>
	  </div>
  </div>
<?php endif; ?>
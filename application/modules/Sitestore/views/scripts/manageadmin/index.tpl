<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manageadmins.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
  var submitformajax = 1;
  var manage_admin_formsubmit = 1;
</script>
<script type="text/javascript">
  var viewer_id = '<?php echo  $this->viewer_id; ?>';
  var url = '<?php  echo $this->url(array(), 'sitestore_general', true) ?>';
</script>

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
		<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/scripts/core.js'); ?>
		<div class="sitestore_form">
			<div>
				<div>
					<div class="sitestore_manageadmins">
						<h3> <?php echo $this->translate('Manage Store Admins'); ?> </h3>
						<p class="form-description"><?php echo $this->translate("Below you can see all the admins who can administer and manage your store, like you can do. You can add new members as admins of this store and remove any existing ones. Note that admins selected by you for this store will get complete authority like you to manage this store, including deleting it. Thus you should be specific in selecting them.") ?></p>
						<br />
						<?php foreach ($this->manageHistories as $item):?>
							<div id='<?php echo $item->manageadmin_id ?>_store_main'  class='sitestore_manageadmins_list'>
								<div class='sitestore_manageadmins_thumb' id='<?php echo $item->manageadmin_id ?>_storethumb'>
									<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
								</div> 
								<div id='<?php echo $item->manageadmin_id ?>_store' class="sitestore_manageadmins_detail">
									<div class="sitestore_manageadmins_cancel">
			             <?php $url = $this->url(array('action' => 'delete'), 'sitestore_manageadmins', true);?>
										<?php if ( $this->owner_id != $item->user_id ) :?>
											<a href="javascript:void(0);" onclick="manageinfo('<?php echo $item->manageadmin_id?>', '<?php echo $item->getOwner()->getIdentity()?>', '<?php echo $url;?>', '<?php echo $this->store_id ?>','<?php echo $this->viewer_id;?>');"><?php echo $this->translate('Remove');?></a>
                    <?php endif;?>
									</div>
									<span><?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?></span>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php  $item = count($this->paginator) ?>
					<input type="hidden" id='count_div' value='<?php echo $item ?>' />
					<form id='video_selected' method='post' class="global_form mtop10" action='<?php echo $this->url(array('action' => 'index', 'store_id' => $this->store_id), 'sitestore_manageadmins') ?>'>
						<div class="fleft">
							<div>
								<?php if (!empty($this->message)): ?>
								<div class="tip">
									<span>
										<?php echo $this->message; ?>
									</span>
								</div>
								<?php  endif;?>
								<div class="sitestore_manageadmins_input ">
								<?php echo $this->translate("Start typing the name of the member...") ?> <br />	
									<input type="text" id="searchtext" name="searchtext" value="" />
									<input type="hidden" id="user_id" name="user_id" />
								</div>
								<div class="sitestore_manageadmins_button">
									<button type="submit"  name="submit"><?php echo $this->translate("Add as Admin") ?></button>
								</div>	
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<br />	
		<div id="show_tab_content_child">
		</div>
<?php if (empty($this->is_ajax)) : ?>
		  </div>
	  </div>
  </div>
<?php endif; ?> 	
<?php
	$this->headScript()
				->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
				->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
				->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
				->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
en4.core.runonce.add(function()
{
	var contentAutocomplete = new Autocompleter.Request.JSON('searchtext', '<?php echo $this->url(array( 'action' => 'manage-auto-suggest', 'store_id' => $this->store_id), 'sitestore_manageadmins', true) ?>', {
		'postVar' : 'text',
		'minLength': 1,
		'maxChoices': 40,
		'selectMode': 'pick',
		'autocompleteType': 'tag',
		'className': 'searchbox_autosuggest',
		'customChoices' : true,
		'filterSubset' : true,
		'multiple' : false,
		'injectChoice': function(token){
				var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
				new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
				this.addChoiceEvents(choice).inject(this.choices);
				choice.store('autocompleteChoice', token);
			}
	});

	contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
		document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;

	});
});

function activ_autosuggest () {
	var contentAutocomplete = new Autocompleter.Request.JSON('searchtext', '<?php echo $this->url(array( 'action' => 'manage-auto-suggest', 'store_id' => $this->store_id), 'sitestore_manageadmins', true) ?>', {
		'postVar' : 'text',
		'minLength': 1,
		'maxChoices': 40,
		'selectMode': 'pick',
		'autocompleteType': 'tag',
		'className': 'searchbox_autosuggest',
		'customChoices' : true,
		'filterSubset' : true,
		'multiple' : false,
		'injectChoice': function(token){
				var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
				new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
				this.addChoiceEvents(choice).inject(this.choices);
				choice.store('autocompleteChoice', token);
			}
	});
	contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
		document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
	});
}	
</script>
<style type="text/css">
.global_form > div > div{background:none;border:none;padding:0px;}
</style>
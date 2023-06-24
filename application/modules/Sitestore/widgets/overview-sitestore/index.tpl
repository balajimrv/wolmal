<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore_profile.css')
?>


<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/hideWidgets.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/hideTabs.js');
?>
<?php //if (empty($this->isajax)) : ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php //endif;?>

<?php //if (!empty($this->show_content)) : ?>
	<?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_overview">
			<?php echo $this->translate($this->sitestore->getTitle(). "'s ");?><?php echo $this->translate("Overview");?>
		</div>
	<?php endif;?>	
	
		<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adoverviewwidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)) : ?>
			<div class="layout_right" id="communityad_overview">
				<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adoverviewwidget', 3),"loaded_by_ajax"=>0,'widgetId'=>'store_overview'))?>
			</div>
			<div class="layout_middle">
		<?php endif;?>
		<?php if ($this->can_edit && $this->can_edit_overview):?>
			<?php if(!empty($this->sitestore->overview)):?>
				<div class="seaocore_add">
					<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'action' => 'overview'), 'sitestore_dashboard', true) ?>'  class="icon_sitestores_overview buttonlink"><?php echo $this->translate('Edit Overview'); ?></a>
				</div>
			<?php endif;?>
		<?php endif;?>
	<div>
	<?php if(!empty($this->sitestore->overview)):?>
		<?php echo $this->sitestore->overview ?>
	<?php else:?>
		<div class="tip">
			<span>
				<?php   echo $this->translate("No overview has been composed for this Store yet.");?>
				<?php if($this->can_edit && $this->can_edit_overview):?>
					<?php   echo $this->translate("Click ").$this->htmlLink(
										array('route' => 'sitestore_dashboard', 'action' => 'overview','store_id' => $this->sitestore->store_id),
										$this->translate('here')

									).  $this->translate(" to compose it.");?>
				<?php endif; ?>
			</span>
		</div>
	<?php endif;?>
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adoverviewwidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)) : ?>
</div>
<?php endif;?>
<?php //endif;?>
<?php //if (empty($this->isajax)) : ?>
	</div>
<?php //endif;?>
<script type="text/javascript">
    var store_communityad_integration = '<?php echo $store_communityad_integration; ?>';
    var show_widgets = '<?php echo $this->widgets ?>';
    var adwithoutpackage = '<?php echo Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore) ?>';
		var store_communityads;
    var contentinformtion;
    var store_showtitle;
    var overview_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adoverviewwidget', 3);?>';

    $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function(event) {
    	$('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'block';
     
   	  if(store_showtitle != 0) {
   	  	if($('profile_status') && show_widgets == 1) {
	  	    $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Overview');?></h2>";	
   	  	}	
   	  }
      hideWidgetsForModule('sitestoreoverview');
      $('id_' + <?php echo $this->content_id ?>).style.display = "block";
	    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
	      $$('.'+ prev_tab_class).setStyle('display', 'none');      
	    }

	    prev_tab_id = '<?php echo $this->content_id; ?>';	
	  	prev_tab_class = 'layout_sitestore_overview_sitestore';

	    if(store_showtitle == 1 && store_communityads == 1 && overview_ads_display != 0 && store_communityad_integration != 0 && adwithoutpackage != 0) {
				setLeftLayoutForStore();    	
	    } else if(store_showtitle == 0 && store_communityads == 1 && overview_ads_display != 0 && store_communityad_integration != 0 && adwithoutpackage != 0) {
				setLeftLayoutForStore(1);
	    }

	    if(store_communityads == 1 && overview_ads_display == 0 ) {
				setLeftLayoutForStore();   	   	
	    }
	     if($(event.target).get('tag') !='div' && ($(event.target).getParent('.layout_sitestore_overview_sitestore')==null)){
         scrollToTopForStore($("global_content").getElement(".layout_sitestore_overview_sitestore"));
       }	        
   });
 
</script>
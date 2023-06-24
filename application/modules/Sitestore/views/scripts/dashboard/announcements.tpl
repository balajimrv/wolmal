<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: foursquare.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
  var submitformajax = 1;
 // var manage_admin_formsubmit = 1;
</script>
<script type="text/javascript">
  var viewer_id = '<?php echo  $this->viewer_id; ?>';
  var url = '<?php  echo $this->url(array(), 'sitestore_general', true) ?>';
  
  var manageinfo = function(announcement_id, url,store_id) {
		var childnode =  $(announcement_id + '_store_main');
		childnode.destroy();
		en4.core.request.send(new Request.JSON({
			url : url,
			data : {
				announcement_id : announcement_id,
				store_id : store_id
			},
			onSuccess : function(responseJSON) {
			}
		}))
	};
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
					<div class="sitestore_manage_announcements">
						<h3> <?php echo $this->translate('Manage Announcements'); ?> </h3>
						<p class="form-description"><?php echo $this->translate("Below, you can manage the announcements for your store. Announcements are shown on the store profile.") ?></p>
						<br />
						<div class="">
							<a href='<?php echo $this->url(array('action' => 'create-announcement', 'store_id' => $this->store_id ),'sitestoremember_approve', true) ?>' class="buttonlink seaocore_icon_add"><?php echo $this->translate("Post New Announcement");?></a>
						</div>
						<?php if (count($this->announcements) > 0) : ?>
						<?php foreach ($this->announcements as $item): ?>
							<div id='<?php echo $item->announcement_id ?>_store_main'  class='sitestore_manage_announcements_list'>
								<div id='<?php echo $item->announcement_id ?>_store'>
                  	<div class="sitestore_manage_announcements_title">
                    <div class="sitestore_manage_announcements_option">
                    
											<?php if($item->status == 1):?>
												<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_approved1.gif', '', array('title'=> $this->translate('Enabled'))); ?>
											<?php else: ?>
												<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_approved0.gif', '', array('title'=> $this->translate('Disabled'))); ?>
											<?php endif; ?>
                   
                     <?php $url = $this->url(array('action' => 'delete-announcement'),'sitestoremember_approve', true); ?>
                      <a href='<?php echo $this->url(array('action' => 'edit-announcement', 'announcement_id' => $item->announcement_id , 'store_id' => $this->store_id ),'sitestoremember_approve', true) ?>' class="buttonlink seaocore_icon_edit"><?php echo $this->translate("Edit ");?></a>
                      <?php //if ( $this->owner_id != $item->user_id ) :?>
                        <a href="javascript:void(0);" onclick="manageinfo('<?php echo $item->announcement_id ?>', '<?php echo $url;?>', '<?php echo $this->store_id ?>')"; class="buttonlink seaocore_icon_delete" ><?php echo $this->translate('Remove');?></a>
                      <?php //endif;?>
                    </div>
										<span><?php echo $item->title; ?></span>
                 	</div> 
                 	<div class="sitestore_manage_announcements_dates seaocore_txt_light">
										<b><?php echo $this->translate("Start Date: ")?></b> <?php echo $this->translate( gmdate('M d, Y', strtotime($item->startdate))); ?>&nbsp;&nbsp;&nbsp;
										<b><?php echo $this->translate("End Date: ") ?></b><?php echo $this->translate( gmdate('M d, Y', strtotime($item->expirydate))); ?>
                 	</div>
                 		<div class="sitestore_manage_announcements_body show_content_body"> 
										<?php echo $item->body ?>
                 	</div> 
								</div>
							</div>
						<?php endforeach; ?>
						<?php else: ?>
            	<br />
							<div class="tip">
							<span><?php echo $this->translate('No announcements have been posted for this store yet.'); ?></span>
							</div>
						<?php endif; ?>
					</div>
					<?php  $item = count($this->paginator) ?>
					<input type="hidden" id='count_div' value='<?php echo $item ?>' />
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
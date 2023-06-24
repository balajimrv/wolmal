<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: app.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
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
  	<?php $canShowMessage=true;?>
		  <ul class="sitestore_getstarted">
		    <?php $i = 1; ?>
		   	<?php if($this->can_create_sitestoreproduct_product): ?>
		    	<li> <?php $canShowMessage=false;?>
			  		<div class="sitestore_getstarted_num">
			  			<?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'store_id' => $this->store_id, 'tab' => $this->sitestoreproducttab_id), '<i class="icon_app_product"></i>') ?>
			  		</div>
			  		<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Create New Products'); ?></b>
							<p><?php echo $this->translate('Create and sell products from your store.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('action' => 'create', 'store_id' => $this->store_id), 'sitestoreproduct_general', true) ?>'><?php echo $this->translate('Create Products');?></a>
                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->sitestoreproducttab_id)), $this->translate('Manage Product')) ?>
							</div>
						</div>
					</li>		
		    <?php endif; ?>
		  	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')):?>
		      <?php if(!empty($this->allowed_upload_photo)): ?>
						<li> <?php $canShowMessage=false;?>
			    		<div class="sitestore_getstarted_num">																
								<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'album_id' => $this->default_album_id, 'tab' => $this->albumtab_id), 'sitestore_photoalbumupload', true) ?>'><i class="icon_app_photo"></i></a>						
			    		</div>
			    		<div class="sitestore_getstarted_des">
				      	<b><?php echo $this->translate('Photos'); ?></b>
								<p><?php echo $this->translate('Create albums and add photos for this store.'); ?></p>
								<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'album_id' => $this->default_album_id, 'tab' => $this->albumtab_id), 'sitestore_photoalbumupload', true) ?>'><?php echo $this->translate('Add Photos'); ?></a>
								<?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->albumtab_id)), $this->translate('Manage Albums')) ?>
								</div>
							</div>	
			   	  </li>
		      <?php endif;?> 
		    <?php endif;?>

				<?php if($this->can_invite): ?>
					<li> <?php $canShowMessage=false;?>
						<div class="sitestore_getstarted_num">
							<?php echo $this->htmlLink(array('route' => 'sitestoreinvite_invite', 'user_id' => $this->viewer_id,'sitestore_id' => $this->store_id), '<i class="icon_app_invite"></i>') ?>
						</div>
						<div class="sitestore_getstarted_des">
								<b><?php echo $this->translate('Invite &amp; Promote'); ?></b>
								<p><?php echo $this->translate('Tell your friends, fans and customers about this store and make it popular.'); ?></p>
								<div class="sitestore_getstarted_btn">
									<a href='<?php echo $this->url(array('user_id' => $this->viewer_id,'sitestore_id' => $this->store_id), 'sitestoreinvite_invite', true) ?>'><?php echo $this->translate('Invite Friends &amp; Fans');?></a>
								</div>
							</div>
					</li>
				<?php endif; ?>

		    <?php if($this->can_create_poll): ?>
		    	<li> <?php $canShowMessage=false;?>
			  		<div class="sitestore_getstarted_num">
			  			<?php echo $this->htmlLink(array('route' => 'sitestorepoll_create', 'store_id' => $this->store_id,'tab' => $this->polltab_id), '<i class="icon_app_poll"></i>') ?>
			  		</div>
			  		<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Polls'); ?></b>
							<p><?php echo $this->translate('Get feedback from visitors to your store.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('store_id' => $this->store_id,'tab' => $this->polltab_id), 'sitestorepoll_create', true) ?>'><?php echo $this->translate('Create a Poll');?></a> 
                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->polltab_id)), $this->translate('Manage Polls')) ?>
							</div>
						</div>
					</li>		
		    <?php endif; ?>

		    <?php if($this->can_create_doc): ?>
                               
                         <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')): ?>                
		    	<li> <?php $canShowMessage=false;?>
			  		<div class="sitestore_getstarted_num">
							<?php echo $this->htmlLink(array('route' => 'sitestoredocument_create', 'store_id' => $this->store_id, 'tab' => $this->documenttab_id), '<i class="icon_app_document"></i>') ?>
						</div>	
			  		<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Documents'); ?></b>
							<p><?php echo $this->translate('Add and showcase documents on your store.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('store_id' => $this->store_id, 'tab' => $this->documenttab_id), 'sitestoredocument_create', true) ?>'><?php echo $this->translate('Add a Document');?></a>
                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->documenttab_id)), $this->translate('Manage Documents')) ?>
							</div>
						</div>
                            
                            
					</li>	
                                        <?php else:?>
                                        <li> <?php $canShowMessage=false;?>
			  		<div class="sitestore_getstarted_num">
							<?php echo $this->htmlLink(array('route' => 'document_create', 'parent_id' => $this->store_id, 'parent_type' => 'sitestore_store', 'tab' => $this->documenttab_id), '<i class="icon_app_document"></i>') ?>
						</div>	
			  		<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Documents'); ?></b>
							<p><?php echo $this->translate('Add and showcase documents on your store.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('parent_id' => $this->store_id, 'parent_type' => 'sitestore_store', 'tab' => $this->documenttab_id), 'document_create', true) ?>'><?php echo $this->translate('Add a Document');?></a>
                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->documenttab_id)), $this->translate('Manage Documents')) ?>
							</div>
						</div>
                            
                            
					</li>
                                        <?php endif;?>
		    <?php endif; ?>

		    <?php if($this->moduleEnable && !empty($this->can_offer)): ?>
					<li> <?php $canShowMessage=false;?>
						<div class="sitestore_getstarted_num">
							<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'store_id' => $this->store_id, 'tab' => $this->offertab_id), '<i class="icon_app_offer"></i>') ?>
						</div>
						<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Coupons'); ?></b>
							<p><?php echo $this->translate('Create and display attractive coupons on your store.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('action' => 'create','store_id' => $this->store_id, 'tab' => $this->offertab_id),'sitestoreoffer_general', true) ?>'><?php echo $this->translate('Add Coupon');?></a>
                <a href='<?php echo $this->url(array('action' => 'index','store_id' => $this->store_id, 'tab' => $this->offertab_id),'sitestoreoffer_general', true) ?>'><?php echo $this->translate('Manage Coupons');?></a>
							</div>
						</div>
					</li>
		    <?php endif; ?>
		
		    <?php if($this->option_id && !empty ($this->can_form)): ?>
					<li> <?php $canShowMessage=false;?>
						<div class="sitestore_getstarted_num">
							<?php echo $this->htmlLink(array('route' => 'sitestoreform_general', 'option_id' => $this->option_id,'store_id' => $this->store_id, 'tab' => $this->formtab_id), '<i class="icon_app_question"></i>') ?>
						</div>
						<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Form'); ?></b>
							<p><?php echo $this->translate('Gather useful information from visitors by creating your form with relevant questions.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('action' => 'index','option_id' => $this->option_id,'store_id' => $this->store_id, 'tab' => $this->formtab_id),'sitestoreform_general', true) ?>'><?php echo $this->translate('Manage Form');?></a>
								<?php $can_edit_tabname = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.edit.name', 1);?>
								<?php if(!empty($can_edit_tabname)):?>
								  <?php echo $this->htmlLink(array('route' => 'default', 'store_id' => $this->store_id,'module' => 'sitestoreform', 'controller' => 'siteform', 'action' => 'edit-tab'), $this->translate("Edit Form Tab’s Name"), array('onclick' => 'owner(this);return false')) ?>
								<?php endif;?>
							</div>
						</div>
					</li>		
		    <?php endif; ?>

		     <?php if ($this->can_create_video): ?>
                    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitestore_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitestorevideo_create', 'store_id' => $this->store_id, 'tab' => $this->videotab_id), '<i class="icon_app_video"></i>') ?>
                        </div>
                        <div class="sitestore_getstarted_des">
                            <b><?php echo $this->translate('Videos'); ?></b>
                            <p><?php echo $this->translate('Add and share videos for this store.'); ?></p>
                            <div class="sitestore_getstarted_btn">
                                <a href='<?php echo $this->url(array('store_id' => $this->store_id, 'tab' => $this->videotab_id), 'sitestorevideo_create', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab' => $this->videotab_id)), $this->translate('Manage Videos')) ?>
                            </div>
                        </div>
                    </li>
                    <?php else:?>
                     <li> <?php $canShowMessage = false; ?>
                        <div class="sitestore_getstarted_num">
                            
                            <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                            <?php echo $this->htmlLink(array('route' => 'sitevideo_video_general', 'action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitestore_store', 'parent_id' => $this->store_id,), '<i  class="icon_app_video"></i>', array('class' => 'seao_smoothbox', 'data-SmoothboxSEAOClass' => 'seao_add_video_lightbox')); ?> 
                            <?php else:?>
                            <?php echo $this->htmlLink(array('route' => 'sitevideo_video_general', 'action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitestore_store', 'parent_id' => $this->store_id,), '<i class="icon_app_video"></i>'); ?> 
                            <?php endif;?>
                        </div>
                        <div class="sitestore_getstarted_des">
                            <b><?php echo $this->translate('Videos'); ?></b>
                            <p><?php echo $this->translate('Add and share videos for this store.'); ?></p>
                            <div class="sitestore_getstarted_btn">
                                <a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_video_lightbox" href='<?php echo $this->url(array('action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitestore_store', 'parent_id' => $this->store_id,), 'sitevideo_video_general', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab' => $this->videotab_id)), $this->translate('Manage Videos')) ?>
                            </div>
                        </div>
                    </li>
                    <?php endif;?>
                <?php endif; ?>
		    
		    <?php if($this->can_create_event): ?>
					<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')):?>
						<li> 
							<?php $canShowMessage=false;?>
							<div class="sitestore_getstarted_num">
								<?php echo $this->htmlLink(array('route' => 'sitestoreevent_create', 'store_id' => $this->store_id, 'tab_id' => $this->eventtab_id), '<i class="icon_app_event"></i>') ?>
							</div>
							<div class="sitestore_getstarted_des">
								<b><?php echo $this->translate('Events'); ?></b>
								<p><?php echo $this->translate('Organize events for this store.'); ?></p>
								<div class="sitestore_getstarted_btn">
									<a href='<?php echo $this->url(array('store_id' => $this->store_id, 'tab_id' => $this->eventtab_id), 'sitestoreevent_create', true) ?>'><?php echo $this->translate('Create an Event');?></a>
									<?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->eventtab_id)), $this->translate('Manage Events')) ?>
								</div>
							</div>
						</li>		
					<?php else:?>
						<li> 
							<?php $canShowMessage=false;?>
							<div class="sitestore_getstarted_num">
                <?php if (isset($this->siteeventVersion) && Engine_Api::_()->siteevent()->hasPackageEnable()):?>
                <?php echo $this->htmlLink(array('route' => 'siteevent_package', 'action' =>'index', 'parent_type' => 'sitestore_store', 'parent_id' => $this->store_id, 'tab_id' => $this->eventtab_id), '<i class="icon_app_event"></i>') ?>
                <?php else:?>
								<?php echo $this->htmlLink(array('route' => 'siteevent_general', 'action' =>'create', 'parent_type' => 'sitestore_store', 'parent_id' => $this->store_id, 'tab_id' => $this->eventtab_id), '<i class="icon_app_event"></i>') ?>
                <?php endif; ?>			
								
							</div>
							<div class="sitestore_getstarted_des">
								<b><?php echo $this->translate('Events'); ?></b>
								<p><?php echo $this->translate('Organize events for this store.'); ?></p>
								<div class="sitestore_getstarted_btn">
                <?php if (isset($this->siteeventVersion) && Engine_Api::_()->siteevent()->hasPackageEnable()):?>
                  <a href='<?php echo $this->url(array('parent_type' => 'sitestore_store','action' =>'index', 'parent_id' => $this->store_id, 'tab_id' => $this->eventtab_id), 'siteevent_package', true) ?>'><?php echo $this->translate('Create an Event');?></a>
                <?php else:?>
								<a href='<?php echo $this->url(array('parent_type' => 'sitestore_store','action' =>'create', 'parent_id' => $this->store_id, 'tab_id' => $this->eventtab_id), 'siteevent_general', true) ?>'><?php echo $this->translate('Create an Event');?></a>
                <?php endif; ?>			
									
									<?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->eventtab_id)), $this->translate('Manage Events')) ?>
								</div>
							</div>
						</li>		
					<?php endif;?>
		    <?php endif; ?>

		    <?php if($this->can_create_notes): ?>
		    	<li> <?php $canShowMessage=false;?>
			  		<div class="sitestore_getstarted_num">
			  			<?php echo $this->htmlLink(array('route' => 'sitestorenote_create', 'store_id' => $this->store_id, 'tab' => $this->notetab_id), '<i class="icon_app_note"></i>') ?>
			  		</div>
			  		<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Notes'); ?></b>
							<p><?php echo $this->translate('Share updates and lots more by publishing notes in this blog-like section of your store.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('store_id' => $this->store_id, 'tab' => $this->notetab_id), 'sitestorenote_create', true) ?>'><?php echo $this->translate('Write a Note');?></a>
                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->notetab_id)), $this->translate('Manage Notes')) ?>
							</div>
						</div>
					</li>		
		    <?php endif; ?>
		    
		    <?php if($this->can_create_discussion):?>
		    	<li> <?php $canShowMessage=false;?>
			  		<div class="sitestore_getstarted_num">
			  			<?php echo $this->htmlLink(array(
									'route' => 'sitestore_extended',
									'controller' => 'topic',
									'action' => 'create',
									'subject' => $this->subject()->getGuid(),
									 'tab' => $this->discussiontab_id,
									  'store_id' => $this->store_id
								), '<i class="icon_app_topic"></i>') ?>
			  		</div>
			  		<div class="sitestore_getstarted_des">
					    <b><?php echo $this->translate('Discussions'); ?></b>
					    <p><?php echo $this->translate('Enable interactions and information sharing on your store using threaded discussions.'); ?></p>
					    <div class="sitestore_getstarted_btn">
								<?php echo $this->htmlLink(array(
									'route' => 'sitestore_extended',
									'controller' => 'topic',
									'action' => 'create',
									'subject' => $this->subject()->getGuid(),
									 'tab' => $this->discussiontab_id,
									  'store_id' => $this->store_id
								), $this->translate('Post a Topic')) ?>
                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->discussiontab_id)), $this->translate('Manage Discussions')) ?>
					   	</div>
					  </div>	
					</li>  
		   	<?php endif; ?>
		   	<?php if($this->can_create_musics): ?>
		    	<li> <?php $canShowMessage=false;?>
			  		<div class="sitestore_getstarted_num">
			  			<?php echo $this->htmlLink(array('route' => 'sitestoremusic_create', 'store_id' => $this->store_id, 'tab' => $this->musictab_id), '<i class="icon_app_music"></i>') ?>
			  		</div>
			  		<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Music'); ?></b>
							<p><?php echo $this->translate('Add and share music for this store.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('store_id' => $this->store_id, 'tab' => $this->notetab_id), 'sitestoremusic_create', true) ?>'><?php echo $this->translate('Upload Music');?></a>
                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->musictab_id)), $this->translate('Manage Music')) ?>
							</div>
						</div>
					</li>		
		    <?php endif; ?>
		    
		    <?php //START FOR INRAGRATION WORK WITH OTHER PLUGIN.// ?>
					<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/storeintergration_app.tpl'; ?>
        <?php //END FOR INRAGRATION WORK WITH OTHER PLUGIN// ?>
		  </ul>

		   <?php if($canShowMessage): ?>
		  <ul class="sitestore_getstarted">
		    <li> 
		      <div class="tip">
		        <span>
		          <?php  if (Engine_Api::_()->sitestore()->hasPackageEnable()): ?>
		            <?php $a = "<a  href='".$this->url(array('action' => 'update-package', 'store_id' => $this->store_id), 'sitestore_packages', true)."'>". $this->translate('here')."</a>";
		            echo $this->translate("Your current package does not provide any apps for your store. Please click %s to upgrade your store package.", $a)?>
		          <?php else:?>
		            <?php echo $this->translate("Please upgrade member level.")?>
		          <?php endif; ?>
		        </span>
		      </div>
		    </li>
		  </ul>
		  <?php endif; ?>
<?php if (empty($this->is_ajax)) : ?>
      </div>
	  </div>
  </div>
<?php endif; ?>
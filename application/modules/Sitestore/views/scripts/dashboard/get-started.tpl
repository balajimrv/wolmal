<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getstarted.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
    <div class="sitestore_getstarted_head">
    	<?php  echo $this->translate('Welcome to your Store. Let\'s get started!'); ?>
    </div>
     
    <?php if(empty($this->isConfiguredVat)): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have not configured any VAT values for this store yet. Please %1sclick here%2s to configure the VAT.', '<a href="' . $this->url(array('action' => 'store', 'store_id' => $this->sitestore->store_id, 'type' => 'tax', 'menuId' => 52, 'method' => 'vat'), 'sitestore_store_dashboard', false) . '">', '</a>'); ?>
      </span>
    </div>
    <?php endif;?>
        
<!-- STORE TIP MESSAGE FOR PAYMENT DETAIL AND SHIPPING METHODS -->
    <?php if( !empty($this->allowSellingProducts) ) : ?>
      <?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_storeTip.tpl';  ?>
    <?php endif; ?>
    <ul class="sitestore_getstarted">
      <?php $i = 1; ?>
      <?php if($this->photo_id == 0): ?>
				<li>
					<div class="sitestore_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
					</div>
					<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Add an image'); ?></b>
						<p><?php echo $this->translate('Make your Store more recognized by adding an image as it\'s profile picture.'); ?></p><br />
						<div class="sitestore_getstarted_upload">
							<div class="fleft">
								<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/nophoto_store_thumb_profile.png" alt="" class="photo" />
							</div>
							<div class="sitestore_getstarted_upload_options">
								<a href='<?php echo $this->url(array('action' => 'profile-picture', 'store_id' => $this->store_id), 'sitestore_dashboard', true) ?>'><?php echo $this->translate('Upload an image'); ?></a>
							</div>
						</div>
					</div>
				</li>
      <?php endif;?>
  
       <?php if($this->updatestab_id): ?>
				<li> <?php $canShowMessage=false;?>
          <div class="sitestore_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
          </div>
					<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Post updates'); ?></b>
						<p><?php echo $this->translate("Share your updates and latest news with the visitors of this Store."); ?></p>
						<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($this->store_id)),'sitestore_entry_view', true) ?>'><?php echo $this->translate('Post Update');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>
  
      <?php if($this->overviewPrivacy): ?>
				<li> <?php $canShowMessage=false;?>
          <div class="sitestore_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
          </div>
					<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Create Rich Overview'); ?></b>
						<p><?php echo $this->translate('Create a rich profile for your Store.'); ?></p>
						<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('action' => 'overview', 'store_id' => $this->store_id), 'sitestore_dashboard', true) ?>'><?php echo $this->translate('Edit Overview');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>
      <?php if($this->can_create_sitestoreproduct_product  && !empty($this->sitestoreproduct_store_admin)): ?>
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitestore_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?>
            </div>
	    		</div>
	    		<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Create New Products'); ?></b>
						<p><?php echo $this->translate('Create and sell products from your Store.'); ?></p>
						<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('action' => 'create', 'store_id' => $this->store_id), 'sitestoreproduct_general', true) ?>'><?php echo $this->translate('Create Products');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>
      <?php if($this->can_invite): ?>
				<li> <?php $canShowMessage=false;?>
					<div class="sitestore_getstarted_num">
						<div>
							<?php echo $i; $i++;?>
						</div>
					</div>
					<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Promote to your fans'); ?></b>
							<p><?php echo $this->translate('Tell your friends, fans and customers about this Store and make it popular.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('user_id' => $this->viewer_id,'sitestore_id' => $this->store_id), 'sitestoreinvite_invite', true) ?>'><?php echo $this->translate('Invite Friends &amp; Fans');?></a>
							</div>
				  </div>
				</li>
			<?php endif; ?>

      <?php if($this->moduleEnable && !empty($this->can_offer)): ?>
				<li> <?php $canShowMessage=false;?>
					<div class="sitestore_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
          </div>
					<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Create Coupons'); ?></b>
						<p><?php echo $this->translate('Create and display attractive coupons on your Store.'); ?></p>
						<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('action' => 'create','store_id' => $this->store_id, 'tab' => $this->offertab_id),'sitestoreoffer_general', true) ?>'><?php echo $this->translate('Add Coupon');?></a>
						</div>
					</div>
				</li>
      <?php endif; ?>

		  <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')):?>
        <?php if(!empty($this->allowed_upload_photo)): ?>
    		  <li> <?php $canShowMessage=false;?>
	      		<div class="sitestore_getstarted_num">
	      			<div>
                <?php echo $i; $i++;?> 
              </div>
	      		</div>
	      		<div class="sitestore_getstarted_des">
		        	<b><?php echo $this->translate('Add more photos'); ?></b>
							<p><?php echo $this->translate('Create albums and add photos for this Store.'); ?></p>
							<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id, 'album_id' => $this->default_album_id, 'tab' => $this->albumtab_id), 'sitestore_photoalbumupload', true) ?>'><?php echo $this->translate('Add Photos'); ?></a>
							</div>
						</div>	
	     	  </li> 
        <?php endif;?>
      <?php endif;?>
   
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
	      			<div>
                <?php echo $i; $i++;?> 
              </div>
	      		</div>
                        <div class="sitestore_getstarted_des">
                            <b><?php echo $this->translate('Videos'); ?></b>
                            <p><?php echo $this->translate('Add and share videos for this store.'); ?></p>
                            <div class="sitestore_getstarted_btn">
                                <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                                <a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_video_lightbox"  href='<?php echo $this->url(array('action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitestore_store', 'parent_id' => $this->store_id,), 'sitevideo_video_general', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php else:?>
                                <a href='<?php echo $this->url(array('action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitestore_store', 'parent_id' => $this->store_id,), 'sitevideo_video_general', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php endif;?>
                                <?php echo $this->htmlLink($this->sitestore->getHref(array('tab' => $this->videotab_id)), $this->translate('Manage Videos')) ?>
                            </div>
                        </div>
                    </li>
                    <?php endif;?>
                <?php endif; ?>

      <?php if($this->can_create_doc): ?>
                      <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')): ?> 
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitestore_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Add New Documents'); ?></b>
						<p><?php echo $this->translate('Add and showcase documents on your Store.'); ?></p>
						<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('store_id' => $this->store_id, 'tab' => $this->documenttab_id), 'sitestoredocument_create', true) ?>'><?php echo $this->translate('Add a Document');?></a>
						</div>
					</div>
				</li>
                                <?php else:?>
                                <li> <?php $canShowMessage=false;?>
	    		<div class="sitestore_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Add New Documents'); ?></b>
						<p><?php echo $this->translate('Add and showcase documents on your Store.'); ?></p>
						<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('parent_type' => 'sitestore_store', 'parent_id' => $this->store_id, 'tab' => $this->documenttab_id), 'document_create', true) ?>'><?php echo $this->translate('Add a Document');?></a>
						</div>
					</div>
				</li>
                                <?php endif;?>
      <?php endif; ?>
    
      <?php if($this->can_create_notes): ?>
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitestore_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Write Notes'); ?></b>
						<p><?php echo $this->translate('Share updates and lots more by publishing notes in this blog-like section of your Store.'); ?></p>
						<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('store_id' => $this->store_id, 'tab' => $this->notetab_id), 'sitestorenote_create', true) ?>'><?php echo $this->translate('Write a Note');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>
      <?php if($this->can_create_event): ?>
        <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')):?>
					<li> <?php $canShowMessage=false;?>
						<div class="sitestore_getstarted_num">
							<div>
								<?php echo $i;$i++;?> </div>
							</div>
						<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Create New Events'); ?></b>
							<p><?php echo $this->translate('Organize events for this store.'); ?></p>
							<div class="sitestore_getstarted_btn">
								<a href='<?php echo $this->url(array('store_id' => $this->store_id, 'tab_id' => $this->eventtab_id), 'sitestoreevent_create', true) ?>'><?php echo $this->translate('Create an Event');?></a>
							</div>
						</div>
					</li>		
				<?php else:?>
					<li> <?php $canShowMessage=false;?>
						<div class="sitestore_getstarted_num">
							<div>
								<?php echo $i;$i++;?> </div>
							</div>
						<div class="sitestore_getstarted_des">
							<b><?php echo $this->translate('Create New Events'); ?></b>
							<p><?php echo $this->translate('Organize events for this store.'); ?></p>
							<div class="sitestore_getstarted_btn">
                <?php if (isset($this->siteeventVersion) && Engine_Api::_()->siteevent()->hasPackageEnable()):?>
                	<a href='<?php echo $this->url(array('action' =>'index','parent_type' => 'sitestore_store', 'parent_id' => $this->store_id, 'tab_id' => $this->eventtab_id), 'siteevent_package', true) ?>'><?php echo $this->translate('Create an Event');?></a>
                <?php else:?>
									<a href='<?php echo $this->url(array('action' =>'create','parent_type' => 'sitestore_store', 'parent_id' => $this->store_id, 'tab_id' => $this->eventtab_id), 'siteevent_general', true) ?>'><?php echo $this->translate('Create an Event');?></a>
                <?php endif; ?>	
							
							</div>
						</div>
					</li>
				<?php endif;?>
      <?php endif; ?>

      <?php if($this->can_create_poll): ?>
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitestore_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?>
            </div>
	    		</div>
	    		<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Create New Polls'); ?></b>
						<p><?php echo $this->translate('Get feedback from visitors to your Store.'); ?></p>
						<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('store_id' => $this->store_id,'tab' => $this->polltab_id), 'sitestorepoll_create', true) ?>'><?php echo $this->translate('Create a Poll');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>
      
      <?php if($this->can_create_discussion):?>
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitestore_getstarted_num">
	    			<div>
            <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitestore_getstarted_des">
			      <b><?php echo $this->translate('Post New Topics'); ?></b>
			      <p><?php echo $this->translate('Enable interactions and information sharing on your Store using threaded discussions.'); ?></p>
			      <div class="sitestore_getstarted_btn">
							<?php echo $this->htmlLink(array(
								'route' => 'sitestore_extended',
								'controller' => 'topic',
								'action' => 'create',
								'subject' => $this->subject()->getGuid(),
							   'tab' => $this->discussiontab_id,
							    'store_id' => $this->store_id
							), $this->translate('Post a Topic')) ?>
			     	</div>
			    </div>	
			  </li>  
     	<?php endif; ?>
     	
      <?php if($this->can_create_musics): ?>
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitestore_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Upload Music'); ?></b>
						<p><?php echo $this->translate('Add and share music for this Store.'); ?></p>
						<div class="sitestore_getstarted_btn">
							<a href='<?php echo $this->url(array('store_id' => $this->store_id, 'tab' => $this->musictab_id), 'sitestoremusic_create', true) ?>'><?php echo $this->translate('Upload Music');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>

      <?php if($this->option_id && !empty ($this->can_form)): ?>
				<li> <?php $canShowMessage=false;?>
          <div class="sitestore_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
          </div>
					<div class="sitestore_getstarted_des">
						<b><?php echo $this->translate('Configure your Form'); ?></b>
						<p><?php echo $this->translate('Gather useful information from visitors by creating your form with relevant questions.'); ?></p>
						<div class="sitestore_getstarted_btn">
						  <?php $canAddquestions = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.add.question', 1);?>
							<a href='<?php echo $this->url(array('action' => 'index','option_id' => $this->option_id,'store_id' => $this->store_id, 'tab' => $this->formtab_id),'sitestoreform_general', true) ?>'><?php if($canAddquestions):?><?php echo $this->translate('Add a Question');?><?php else:?><?php echo $this->translate('Manage Form');?><?php endif;?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>

			<?php //START FOR INRAGRATION WORK WITH OTHER PLUGIN. ?>
        <?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/storeintergration_getstarted.tpl'; ?>
      <?php //START FOR INRAGRATION WORK WITH OTHER PLUGIN. ?>
      
    </ul>

    <?php if($canShowMessage): ?>
    <ul class="sitestore_getstarted">
      <li>
        <div class="tip">
          <span>
            <?php  if (Engine_Api::_()->sitestore()->hasPackageEnable()): ?>
              <?php echo $this->translate("Please click	<a  href='".$this->url(array('action' => 'update-package', 'store_id' => $this->store_id), 'sitestore_packages', true)."'>". $this->translate('here')."</a> for upgrading the package of your Store.")?>
            <?php else:?>
              <?php echo $this->translate("Please upgrade your member level.")?>
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
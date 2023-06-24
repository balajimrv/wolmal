<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: marketing.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >

function owner(thisobj) {
	var Obj_Url = thisobj.href ;
	Smoothbox.open(Obj_Url);
}

var showFeedDialogue_FB = function (feedurl) {

  var  current_window_url_sitestore='<?php echo (_ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url() ?>';
    activityfeedtype = 'facebook';

  if (history.pushState)
    history.pushState( {}, document.title, current_window_url_sitestore);

  var child_window = window.open (feedurl ,'mywindow', 'width=500,height=500');

}
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
      <?php $showMessage=true;?>
			<div class="global_form">
				<div>
					<div>
						<h3><?php echo $this->translate('Marketing your Store'); ?></h3>
						<p class="form-description"><?php echo $this->translate('Below are some effective tools to market your Store and increase its popularity.'); ?></p>
						<div class="sitestore_marketing mtop10">
							<ul class="sitestore_getstarted">
		      			<?php $sitestorecommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad'); ?>

								<?php								// check if it is upgraded version
									$updated_ads = 0;
									$adversion = null;
									if($sitestorecommunityadEnabled) {
										$communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
										$adversion = $communityadmodulemodule->version;
										if($adversion >= '4.1.5') {
												$updated_ads = 1;
										}
									}
								?>
								<?php if($sitestorecommunityadEnabled && $updated_ads): ?>
									<li><?php $showMessage=false;?>
										<div class="sitestore_getstarted_num">
											<?php echo $this->htmlLink(array('route' => 'communityad_listpackage', 'type' => 'sitestore', 'type_id' => $this->store_id), '<img alt="" src="'. $this->layout()->staticBaseUrl .'application/modules/Sitestore/externals/images/advertise.png" />') ?>

						    		</div>
						    		<div class="sitestore_getstarted_des">
						      		<b>
												<?php $site_title =  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'Advertisement')?>
												<?php echo $this->htmlLink(array('route' => 'communityad_listpackage', 'type' => 'sitestore', 'type_id' => $this->store_id), $this->translate("Advertise on %s", $site_title)) ?>
											</b>
											<p><?php echo $this->translate('Popularize your Store on %s with an attractive ad.', $site_title) ?></p>
										</div>
									</li>
								<?php endif; ?>

								<?php $sitestoreinviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreinvite'); ?>
								<?php if($sitestoreinviteEnabled && !empty($this->enableInvite) ): ?>
									<li><?php $showMessage=false;?>
										<div class="sitestore_getstarted_num">
											<?php echo $this->htmlLink(array('route' => 'sitestoreinvite_invite', 'user_id' => $this->viewer_id, 'sitestore_id' => $this->store_id), '<img alt="" src="'. $this->layout()->staticBaseUrl .'application/modules/Sitestore/externals/images/friends.png" />') ?>

						    		</div>
						    		<div class="sitestore_getstarted_des">
						      		<b>
												<?php echo $this->htmlLink(array('route' => 'sitestoreinvite_invite', 'user_id' => $this->viewer_id, 'sitestore_id' => $this->store_id), $this->translate('Invite &amp; Promote')) ?>
											</b>
											<p><?php echo $this->translate('Tell your friends, fans and customers about this store and make it popular.') ?></p>
										</div>
									</li>
								<?php endif;?>
                <?php if(!empty($this->enableSendUpdate)):?>
								<li><?php $showMessage=false;?>
									<div class="sitestore_getstarted_num">
										<?php echo $this->htmlLink(array('route' => 'sitestore_like', 'store_id' => $this->store_id, 'action' => 'send-update'), '<img alt="" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/message48.png" />', array('onclick' => 'owner(this);return false')) ?>
					    		</div>
					    		<div class="sitestore_getstarted_des">
					      		<b>
											<?php echo $this->htmlLink(array('route' => 'sitestore_like', 'store_id' => $this->store_id, 'action' => 'send-update'), $this->translate('Send an Update'), array('onclick' => 'owner(this);return false')) ?>
										</b>
										<p><?php echo $this->translate('Send updates to people who have liked your Store.') ?></p>
									</div>
								</li>
                <?php endif; ?>
                <?php if(false && !empty($this->enableFoursquare)):?>
								<li><?php $showMessage=false;?>
									<div class="sitestore_getstarted_num">
										<?php echo $this->htmlLink(array('route' => 'sitestore_dashboard', 'store_id' => $this->store_id, 'action' => 'foursquare'), '<img alt="" src="'. $this->layout()->staticBaseUrl  . 'application/modules/Sitestore/externals/images/foursquare.png" />', array('onclick' => 'owner(this);return false')) ?>
					    		</div>
					    		<div class="sitestore_getstarted_des">
					      		<b>
											<?php echo $this->htmlLink(array('route' => 'sitestore_dashboard', 'store_id' => $this->store_id, 'action' => 'foursquare'), $this->translate("'Save to foursquare' Button"), array('onclick' => 'owner(this);return false')) ?>
										</b>
										<p><?php echo $this->translate('This button will enable visitors on your Store to add your place or tip to their foursquare To-Do List.') ?></p>
									</div>
								</li>
               <?php endif; ?>
								<?php $sitestorelikebox_isActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorelikebox.isActivate', null); ?>
               <?php if(!empty($this->enableLikeBox) && !empty($sitestorelikebox_isActivate)):?>
									<li><?php $showMessage=false;?>
										<div class="sitestore_getstarted_num">
											<?php echo $this->htmlLink(array('route' => 'sitestorelikebox_general', 'store_id' => $this->store_id, 'action' => 'like-box'), '<img alt="" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/likebox.png" />', array()) ?>
										</div>
										<div class="sitestore_getstarted_des">
											<b>
												<?php echo $this->htmlLink(array('route' => 'sitestorelikebox_general', 'store_id' => $this->store_id, 'action' => 'like-box'), $this->translate("Promote this Store on your external blogs or websites"), array()) ?>
											</b>
											<p><?php echo $this->translate("Attract people to your Store and gain popularity by using attractive embeddable badge of your Store which can be shared across the web.",Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')) ?></p>
										</div>
									</li>
              <?php endif; ?>
							<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter') && !empty($this->enabletwitter)):?>
								<li><?php $showMessage=false;?>
									<div class="sitestore_getstarted_num">
										<?php echo $this->htmlLink(array('route' => 'sitestore_dashboard', 'store_id' => $this->store_id, 'action' => 'twitter'), '<img alt="" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestoretwitter/externals/images/twitter.png" />', array('onclick' => 'owner(this);return false')) ?>
					    		</div>
					    		<div class="sitestore_getstarted_des">
					      		<b>
											<?php echo $this->htmlLink(array('route' => 'sitestore_dashboard', 'store_id' => $this->store_id, 'action' => 'twitter'), $this->translate("Add Twitter Profile Widget"), array('onclick' => 'owner(this);return false')) ?>
										</b>
										<p><?php echo $this->translate('Attract people and involve them in your conversation on Twitter by displaying your recent tweets.') ?></p>
									</div>
								</li>
               <?php endif; ?>
 <?php 
                                $advfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('advancedactivity');
                                
                      if(!empty($this->fblikebox_id) || (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postfbstore', 1) && !empty ($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5' )):?>
								<li><?php $showMessage=false;?>
									<div class="sitestore_getstarted_num">
										<?php echo $this->htmlLink(array('route' => 'sitestore_dashboard', 'store_id' => $this->store_id, 'action' => 'facebook', 'fblikebox_id' => $this->fblikebox_id), '<img alt="" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/sitestore-facebook.png" />', array('onclick' => 'owner(this);return false')) ?>
					    		</div>
					    		<div class="sitestore_getstarted_des">
					      		<b>
                                                            
                                                <?php
      $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook');
      $Api_facebook = Engine_Api::_()->getApi('facebook_Facebookinvite', 'seaocore');
      $facebook_userfeed = $Api_facebook->getFBInstance();
      
      if (!empty($settings['appid']) && !empty($settings['secret'])) {
        $FBloginURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble(array('module' => 'seaocore', 'controller' => 'auth', 'action' => 'facebook'), 'default', true) . '?' . http_build_query(array('redirect_urimain' => urlencode(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url() . '?redirect_fb=1'), 'manage_pages' => true));

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('facebook.enable', Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable == 'publish' ? 1 : 0)) {
          $session = new Zend_Session_Namespace();

          $session_userfeed = $facebook_userfeed;
          $fbLoginUrlFinal = '';
          if (!empty($facebook_userfeed)) {

            $fbLoginUrlFinal = '';
            $checksiteIntegrate = true;
            $facebookCheck = new Seaocore_Api_Facebook_Facebookinvite();
            $fb_checkconnection = $facebookCheck->checkConnection(null, $facebook_userfeed);

            if ($session_userfeed && $fb_checkconnection) {
              //$session->fb_checkconnection = true;
              $core_fbenable = Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable;
              $enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
              if (('publish' == $core_fbenable || 'login' == $core_fbenable || $enable_socialdnamodule) && (!$fb_checkconnection)) {
                $checksiteIntegrate = false;
              } else {
                try {
                  if (!isset($session->fb_canread)) {
                    $permissions = $facebook_userfeed->api("/me/permissions");
                    if (!$facebookCheck->checkPermission('manage_pages', $permissions)) {
                      $session->fb_can_managepages = false;
                    } else {
                      $session->fb_can_managepages = true;
                    }
                  }
                  if ($subject && ($subject->getType() == 'sitestore_store') && !$session->fb_can_managepages) {
                    $checksiteIntegrate = false;
                  }
                } catch (Exception $e) {
                  $checksiteIntegrate = false;
                }
              }
            }
            if (!$session_userfeed || !$fb_checkconnection || !$checksiteIntegrate) {
              $fbLoginUrlFinal = $FBloginURL;
            }
          }
        }
      } ?>
                                                <?php if(empty($fbLoginUrlFinal)): ?>
                                                <?php echo $this->htmlLink(array('route' => 'sitestore_dashboard', 'store_id' => $this->store_id, 'action' => 'facebook', 'fblikebox_id' => $this->fblikebox_id), $this->translate("Link your Store to Facebook"), array('onclick' => 'owner(this);return false')) ?>
                                                <?php else: ?>
                                                <?php echo $this->htmlLink("javascript:void(0);", $this->translate("Link your Store to Facebook"), array('onclick' => 'showFeedDialogue_FB(\'' . $fbLoginUrlFinal . '\')')) ?>
                                                <?php endif; ?>
											
										</b>
										<p><?php 
										$description_fblikebox = '';
										         if (!empty ($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5'  && !empty($this->fblikebox_id))
																$description_fblikebox = "Publish your Store's updates to your Facebook Page and show your Facebook Page Like Box on your Store.";
																
														 else if (!empty($this->fblikebox_id))
																$description_fblikebox = "Show your Facebook Page Like Box on your Store.";
																
																
														 else if (!empty ($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5' ) 
																$description_fblikebox = "Publish your Store's updates to your Facebook Page."; 
										echo $this->translate($description_fblikebox) ?></p>
									</div>
								</li>
						<?php endif; ?>
             <?php if($showMessage):?>
                <li>
                  <div class="tip">
                    <span>
                  <?php  if (Engine_Api::_()->sitestore()->hasPackageEnable()):?>
									<?php echo $this->translate("In this package not any effective tools available to market your Store.");?>
                  <?php else:?>
                  	<?php echo $this->translate("For this level user not any effective tools available to market your Store.");?>
                  <?php endif; ?>
                    </span>
                  </div>
								</li>
              <?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
	<?php if (empty($this->is_ajax)) : ?>
		</div>
	</div>
  </div>
<?php endif; ?>
<?php
$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
if((isset($p['module']) && $p['module'] == 'user') && isset($p['action']) && ($p['action'] == 'index' || $p['action'] == 'home'))
  $statusboxHeaderTitle = 'Update Status';
else
  $statusboxHeaderTitle = 'Post Update';
?>

<div data-role="composer-wrapper" >
  <?php if ($this->enableComposer): ?> 
    <?php if ($this->resource_type == ''): ?>

      <div class="activity-post-options ui-btn-corner-all">
        <table>
          <tr>
            <?php if (Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('sitetagcheckin') || Engine_Api::_()->sitemobile()->enableComposer('photo')): ?>
              <td>
                <div>
                  <?php if ($this->resource_type == '' || $this->resource_type == 'user'): ?>
                    <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input', true, '');
                        sm4.activity.feedURL = sm4.core.baseUrl + 'advancedactivity/index/post';" class="ui-link-inherit">
                      <i class="icon_pencil"></i> 
                      <span><?php echo $this->translate('Status'); ?></span>
                    </a>
                  <?php else : ?>
                    <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input', true, '');
                        sm4.activity.feedURL = sm4.core.baseUrl + 'advancedactivity/index/post';" class="ui-link-inherit">
                      <i class="icon_pencil"></i> 
                      <span><?php echo $this->translate('Post'); ?></span>
                    </a>
                  <?php endif; ?>
                  <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                   <span class="ui-icon ui-icon-caret-up"></span>
                  <?php endif; ?>
                </div>
              </td>
            <?php endif; ?>
            <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
              <td>
                <div>
                  <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input', true, 'addphoto');
                      sm4.activity.feedURL = sm4.core.baseUrl + 'advancedactivity/index/post';" >
                    <i class="icon_photo"></i> 
                    <span><?php echo $this->translate('Photo'); ?></span>
                  </a>
                </div>   
              </td>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('sitetagcheckin')): ?>
              <td>
                <div>
                  <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input', true, 'checkin');
                      sm4.activity.feedURL = sm4.core.baseUrl + 'advancedactivity/index/post';">
                    <i class="icon_checkin"></i> 
                    <span><?php echo $this->translate('Check In'); ?></span>
                  </a>
                </div>  
              </td>
            <?php endif; ?>
          </tr>
        </table>
      </div>
    <?php endif; ?>

    <?php if ($this->resource_type): ?>
      <div class="activity-post-options activity-profile-post-options ui-btn-corner-all">
        <table>
          <tr>
              <td>
                <div>
                  <?php if ($this->resource_type == '' || $this->resource_type == 'user'): ?>
                    <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input', true, '');
                        sm4.activity.feedURL = 'advancedactivity/index/post';">
                      <i class="icon_pencil"></i> 
                      <span><?php echo $this->translate('Status'); ?></span>
                    </a>
                  <?php else : ?>
                    <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input', true, '');
                        sm4.activity.feedURL = sm4.core.baseUrl + 'advancedactivity/index/post';">
                      <i class="icon_pencil"></i> 
                      <span><?php echo $this->translate('Post'); ?></span>
                    </a>
                  <?php endif; ?>
               </div>
              </td>

            <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
              <td>
                <div>
                  <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input', true, 'addphoto');
                      sm4.activity.feedURL = sm4.core.baseUrl + 'advancedactivity/index/post';" >
                    <i class="icon_photo"></i> 
                    <span><?php echo $this->translate('Photo'); ?></span>
                  </a>
                </div>   
              </td>
            <?php endif; ?>
						<?php
								$coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
								$menuoptions = $coreSettingsApi->getSetting('advancedactivity.composer.menuoptions', Engine_Api::_()->advancedactivity()->getComposerMenuList());
						?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin') && is_array($menuoptions) && in_array('checkinXXXsitetagcheckin', $menuoptions)): ?>
              <td>
                <div>
                  <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input', true, 'checkin');
                      sm4.activity.feedURL = sm4.core.baseUrl + 'advancedactivity/index/post';">
                    <i class="icon_checkin"></i> 
                    <span><?php echo $this->translate('Check In'); ?></span>
                  </a>
                </div>  
              </td>
            <?php endif; ?>
          </tr>
        </table>
      </div>
    <?php endif; ?>

    <?php
    if ($this->resource_type == '' || (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin') && !Engine_Api::_()->sitemobile()->enableComposer('photo'))):

      $showDefaultTextbox = "display:block;";
    else :
      $showDefaultTextbox = "display:none";
    endif;
    ?>


  <div style="display:none;" class="activity-post-container-input ui-bar-b" onclick="sm4.activity.toggleFeedArea(this, true, '');
                  sm4.activity.feedURL = sm4.core.baseUrl + 'advancedactivity/index/post';" style='<?php echo $showDefaultTextbox; ?>' >   
      <input type="text" class="ui-input-text" data-role="none" placeholder="<?php echo $this->translate('Post Something...'); ?>"/>
    </div>

    <div class="activity-post-container" style="display:none;" >
      <?php $composerOptions = $this->settingsApi->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")); ?>
      <form method="post" class="activity" enctype="application/x-www-form-urlencoded" id="activity-form" data-ajax="false">
        <?php //STATUS BOX HEADER   ?>

        <div class="ui-header ui-bar-a o_hidden" id="ui-header">
          <?php  if (!Engine_Api::_()->sitemobile()->isApp()) :?>
          <a data-role="button" data-icon="false" href="" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a" class="ui-btn-left" onclick="sm4.activity.toggleFeedArea(this, false, 'status');"><?php echo $this->translate('Cancel'); ?></a>
          <?php else: ?>
           <a href='javascript://' class='ui-btn-left'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="sm4.activity.toggleFeedArea(this, false, 'status');"></a> <?php endif; ?>
          <h2 class="ui-title" role="heading" aria-level="1"><?php echo $this->translate($statusboxHeaderTitle); ?></h2>

          <button id="compose-submit" type="submit" data-theme="a" data-role="button" class="ui-btn-right done"><?php echo $this->translate("Post") ?></button>
        </div>
        <?php //CHECKIN HEADER   ?>

        <div class="ui-header ui-bar-a o_hidden" id="ui-header-checkin" style="display:none;">
                    <?php  if (!Engine_Api::_()->sitemobile()->isApp()) :?>
          <a data-role="button" data-icon="false" href="" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a" class="ui-btn-left" onclick="sm4.activity.toggleFeedArea(this, false, 'checkin');"><?php echo $this->translate('Cancel'); ?></a>
          <?php else: ?>
           <a href='javascript://' class='ui-btn-left'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="sm4.activity.toggleFeedArea(this, false, 'checkin');" ></a>
           <?php endif; ?>
          <h2 class="ui-title" role="heading" aria-level="1"><?php echo $this->translate('Where are you?'); ?></h2>

        </div>
        <?php //ADD PEOPLE HEADER   ?>

        <div class="ui-header ui-bar-a o_hidden" id="ui-header-addpeople" style="display:none;">
          <?php  if (!Engine_Api::_()->sitemobile()->isApp()) :?>
          <a data-role="button" data-icon="false" href="" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a" class="ui-btn-left" onclick="$('#aff_mobile_aft_search').val(''); sm4.activity.toggleFeedArea(this, false, 'addpeople');"><?php echo $this->translate('Cancel'); ?></a>
          <?php else: ?>
           <a href='javascript://' class='ui-btn-left'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="$('#aff_mobile_aft_search').val(''); sm4.activity.toggleFeedArea(this, false, 'addpeople');" ></a>
           <?php endif; ?>
          <h2 class="ui-title" role="heading" aria-level="1"><?php echo $this->translate('Who are you with?'); ?></h2>	
          <button id="compose-submit" data-role="button" data-icon="false" class="ui-btn-right" onclick="$('#aff_mobile_aft_search').val('');
                  sm4.activity.composer.addpeople.addFriends();
                  return false;"><?php echo $this->translate("Add") ?></button>
        </div>
        <div class="sm-post-wrap ui-page-content">
          <textarea id="activity_body" cols="1" rows="1" name="body" placeholder="<?php echo $this->translate('Post Something...'); ?>"></textarea>          
          <div id="toValuesdone-wrapper" class="sm-post-show-tags" style="display:none;"></div> 
          
            <?php 
                 //IF THE MODE IS APP MODE THEN WE WILL NOT SHOW SOCIAL SHARE OPTIONS FOR FIRST RELEASE OF APPL
          if (!Engine_Api::_()->sitemobile()->isApp()) :?>
            <div class="compose_share_op" style="display:none;">
            <?php echo $this->partial('application/modules/Sitemobile/modules/Advancedactivity/views/scripts/_composerSocialServices.tpl', array()); ?>
            </div>
          <?php endif;?>
          <?php if(Engine_Api::_()->sitemobile()->isApp()):?>
              <div class="compose_buttons compose-footer composer-bottom-box">
            <?php else: ?>
                <div class="compose_buttons">
            <?php endif;?>    
            <div class="left_options">
              <?php if (in_array("withtags", $composerOptions)): ?>
                <a href="javascript:void(0);" data-role="none" onclick="$(this).children('i').addClass('active');
                    sm4.activity.composer.showPluginForm(this, 'addpeople');">
                  <i class="cm-icons cm-icon-user"></i>
                </a>
              <?php endif; ?>

              <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')): ?>
                <a href="javascript:void(0);" data-role="none" onclick="sm4.activity.composer.showPluginForm(this, 'checkin');">
                  <i class="cm-icons cm-icon-map-marker"></i>
                </a>
              <?php endif; ?>

              <?php
              $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
              if (in_array("emotions", $composerOptions) && $SEA_EMOTIONS_TAG && isset($SEA_EMOTIONS_TAG[0])):
                ?>
                <a href="javascript:void(0);" data-role="none" id="emoticons-button"  class="emoticons-button"  onclick="setEmoticonsBoard();
                    sm4.activity.statusbox.toggleEmotions($(this));
                    $(this).children('i').toggleClass('active');" >
                  <i class="cm-icons cm-icon-emoticons"></i>
                </a>
              <?php endif; ?>
              <div id="composer-options">
                <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo', array('auth' => (array('album', 'create'))))) : ?>
                  <a href="javascript:void(0);" onclick="$('#attachment-options').css('display', 'block');
                    $('#smactivityoptions-popup').css('display', 'none');
                    return sm4.activity.composer.showPluginForm(this, 'photo');" data-role="none">
                    <i class="cm-icons cm-icon-photo"></i>
                  </a>
                <?php endif; ?>
                <a href="javascript:void(0);" data-role="none" onclick="$(this).css('display', 'none');
                  $('#activitypost-container-temp').find('.sm-post-composer-options').toggle();" id="attachment-options">
                  <i class="cm-icons cm-icon-attachement"></i>
                </a>
                <div id="smactivityoptions-popup" class="sm-post-composer-options" style="display:none;"> 
                  <!--                Upload music to activity feed only on page profile if page music is enabled-->

                  <?php //if (Engine_Api::_()->sitemobile()->enableComposer('music',array('auth'=>(array('music_playlist','create')))) && ($this->resource_type == 'sitepage_page' || ($this->resource_type =='sitebusiness_business' ))):  ?>
                  <?php if (Engine_Api::_()->sitemobile()->enableComposer('music', array('auth' => (array('music_playlist', 'create'))))) : ?>

                    <a href="javascript:void(0);" onclick="$('#attachment-options').css('display', 'block');
                    $('#smactivityoptions-popup').css('display', 'none');
                    return sm4.activity.composer.showPluginForm(this, 'music');" data-role="none" >
                      <i class="cm-icons cm-icon-music"></i>
                    </a>
                  <?php endif; ?>
                  <?php if (Engine_Api::_()->sitemobile()->enableComposer('video', array('auth' => (array('video', 'create'))))) : ?>
                  <?php 
                  $user = Engine_Api::_()->user()->getViewer();
                  $allowed_upload = (bool) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'upload'); ?>
                  
                  <a data-uploadurl="<?php echo $this->url(array('action' => 'create', 'type'=>3), 'video_general')?>" data-allowed="<?php echo $allowed_upload; ?>" href="javascript:void(0);" onclick="$('#attachment-options').css('display', 'block');
                    $('#smactivityoptions-popup').css('display', 'none');
                    return sm4.activity.composer.showPluginForm(this, 'video');" data-role="none" >
                      <i class="cm-icons cm-icon-video"></i>
                    </a>
                  <?php endif; ?>
                  <?php if (Engine_Api::_()->sitemobile()->enableComposer('link', array('auth' => (array('core_link', 'create'))))) : ?>
                    <a href="javascript:void(0);" onclick="$('#attachment-options').css('display', 'block');
                    $('#smactivityoptions-popup').css('display', 'none');
                    return sm4.activity.composer.showPluginForm(this, 'link');" data-role="none">
                      <i class="cm-icons cm-icon-link"></i>
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="right_options">
               <?php 
                 //IF THE MODE IS APP MODE THEN WE WILL NOT SHOW SOCIAL SHARE OPTIONS FOR FIRST RELEASE OF APPL
          if (!Engine_Api::_()->sitemobile()->isApp()) :?>
              <a href="javascript:void(0);" data-role="none" class="right_button dnone" id= "socialshare-button" onclick="$('.compose_share_op').toggle();
                  $('.cm-icon-share').toggleClass('active');">
                <i class="cm-icons cm-icon-share"></i>
              </a>
           <?php endif;?>   
              <?php if ($this->showPrivacyDropdown): ?>
                <a href="javascript:void(0);" data-role="none" onclick="sm4.activity.statusbox.togglePrivacy($(this));" id="addprivacy">

                  <span class="ui-icon ui-icon-caret-down"></span>
                </a>
              <?php endif; ?>
            </div>		
          </div>

          <div id="emoticons-board" class="compose_embox_cont ui-page-content <?php if(Engine_Api::_()->sitemobile()->isApp()) echo 'compose-footer';?>" style="display:none;">
            <div class="sm-seaocore-embox">
              <span class="sm-seaocore-embox-arrow ui-icon ui-icon-caret-up"></span>
              <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key => $tag): ?>         
                <span class="sm-seaocore-embox-icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag))) ?>", "<?php echo $this->string()->escapeJavascript($tag_key) ?>")' onclick='addEmotionIcon("<?php echo $this->string()->escapeJavascript($tag_key) ?>")'  title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag)) . "&nbsp;" . $tag_key; ?>"><?php
                  echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"" . $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);
                  ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <?php if(Engine_Api::_()->sitemobile()->isApp()):?>
          <div class="composer_status_share_options composer-bottom-box composer_status_privacy_options ui-page-content"> 
          <?php else: ?>
            <div class="composer_status_share_options ui-page-content" style="display:none;">
          <?php endif;?>  
            <?php if ($this->showPrivacyDropdown): ?> 
              <?php $content = (isset($this->availableLabels[$this->showDefaultInPrivacyDropdown]) || !empty($this->privacylists) ) ? $this->showDefaultInPrivacyDropdown : $this->settingsApi->getSetting('activity.content', 'everyone'); ?> 
              <?php $availableLabels = $this->availableLabels; ?>
              <?php
              if (count($this->privacylists) > 1):
                $content = "friends";
              endif;
              ?>
              <div class="compose_pr_op ui-page-content" id="aaf_tabs_feed">
                <table width="100%" cellpadding="0" cellspacing="0">
                  <?php $active_icon_class = ''; ?>
                  <?php foreach ($availableLabels as $key => $value): ?>
                    <tr onclick="sm4.activity.statusbox.addPrivacy('<?php echo $key; ?>')" id="cm-icon-<?php echo $key; ?>">
                      <td class="compose_pr_op_left">
                        <i class="cm-icons cm-icon-<?php echo $key; ?>"></i>
                      </td>
                      <td class="compose_pr_op_middle"><?php echo $this->translate($value); ?></td>
                      <td class="compose_pr_op_right">
                        <?php if ($content == $key): $active_icon_class = $key; ?>
                          <i class="ui-icon ui-icon-ok"></i>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if ($this->enableList): ?>  
                    <?php foreach ($this->lists as $list): ?>
                      <tr onclick="sm4.activity.statusbox.addPrivacy('<?php echo 'list_' . $list->list_id; ?>')" id="cm-icon-<?php echo 'list_' . $list->list_id; ?>" >
                        <td class="compose_pr_op_left">
                          <i class="cm-icons cm-icon-list"></i>
                        </td>
                        <td class="compose_pr_op_middle"><?php echo $this->translate($list->getTitle()); ?></td>
                        <td class="compose_pr_op_right">
                          <?php if ($content == $list->list_id): $active_icon_class = 'list'; ?>
                            <i class="ui-icon ui-icon-ok"></i>
                          <?php endif; ?>
                        </td>
                      </tr>                    
                    <?php endforeach; ?>
                  <?php endif; ?>
                  <?php if ($this->enableNetworkList): ?>
                    <?php foreach ($this->network_lists as $list): ?>
                      <tr onclick="sm4.activity.statusbox.addPrivacy('<?php echo "network_" . $list->getIdentity(); ?>')" id="cm-icon-<?php echo "network_" . $list->getIdentity(); ?>">
                        <td class="compose_pr_op_left">
                          <i class="cm-icons cm-icon-network-list"></i>
                        </td>
                        <td class="compose_pr_op_middle"><?php echo $this->translate($list->getTitle()); ?></td>
                        <td class="compose_pr_op_right">
                          <?php if ($content == "network_" . $list->getIdentity()): $active_icon_class = 'network-list'; ?>
                            <i class="ui-icon ui-icon-ok"></i>
                          <?php endif; ?>
                        </td>
                      </tr>                         
                    <?php endforeach; ?>
                  <?php endif; ?>

                </table>
              </div>          
            <?php else: ?>
              <?php
              $content = $this->settingsApi->getSetting('activity.content', 'everyone');
              $active_icon_class = 'everyone';
              ?>						
            <?php endif; ?>
          </div>
          <input type="hidden" id="auth_view" name="auth_view" value="<?php echo $content; ?>" />

          <div id="composer-checkin-tag" class="composer-options" style="display:none;">

            <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
            <input type="hidden" name="activity_type" value="1" />
            <?php if ($this->viewer() && $this->subject()): ?>
              <input type="hidden" id="subject" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
            <?php endif; ?>
            <?php if ($this->formToken): ?>
              <input type="hidden" name="token" value="<?php echo $this->formToken ?>" />
            <?php endif ?>

            <!-- Add Pwople -->
            <div class="adv_post_compose_menu" id="adv_post_container_icons">
              <span class="aaf_activaor_end" style="display:none;"></span>
            </div>
            <div class="compose-menu_before" >                    
            </div>       



            <div id="compose-menu" class="compose-menu ui-page-content">				
            </div>

          </div>     
        </div>

        <?php //ADD PEOPLE WORK STARTS HERE...  ?>


        <div id="adv_post_container_tagging" class="post_container_tags ui-page-content" style="display:none;" title="<?php echo $this->translate('Who are you with?') ?>" >
          <div id="aff_mobile_aft_search-element">
            <div class="sm-post-search-fields">
              <table width="100%">
                <tr>
                  <td class="sm-post-search-fields-left">
                    <input class="ui-input-field " type="text" autocomplete="off" value="" id="aff_mobile_aft_search" name="aff_mobile_aft_search" placeholder='<?php echo $this->translate("Start typing a name..."); ?>' data-role="none" />
                  </td>
                </tr>
              </table>			
              <span role="status" aria-live="polite"></span>
            </div>
            <div class="sm-post-show-tags" id="toValues-temp-wrapper" style="border:none;">
              <div id="toValues-temp-element">
                <input type="hidden" id="toValues-temp" value=""  name="toValues-temp" />
                <input type="hidden" id="toValues" value=""  name="toValues" />
              </div>
               <div class="sm-post-search-loading" id="addpeople_loading" style="display: none;"><img alt="Loading..." src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitemobile/modules/Core/externals/images/loading.gif';?>"></div>
            </div> 

          </div>
        </div>


        <?php //CHECKING WORK STARTS HERE...  ?>	
        <div id="sitetagchecking_mob"></div>		

      </form>

    </div>  

    <script type="text/javascript">
    sm4.core.runonce.add(function() {
<?php if ($this->showPrivacyDropdown): ?>

        $.mobile.activePage.find('#addprivacy').children('i').remove();
        $.mobile.activePage.find('#addprivacy').prepend($('<i />', {
          'class': 'cm-icons cm-icon-' + '<?php echo $active_icon_class; ?>'
        }));
<?php endif; ?>
    });
    </script>

  <?php endif; ?>
</div>
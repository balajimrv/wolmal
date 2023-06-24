<div data-role="composer-wrapper" >
<?php if (1): ?> 
<?php //if ($this->resource_type == ''): ?>

    <div class="activity-post-options activity-profile-post-options ui-btn-corner-all">
      <table>
        <tr>
              <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')): ?>
            <td>
              <div>
                 <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input-linkedinfeed',true, '');sm4.activity.feedURL= sm4.core.baseUrl+ 'advancedactivity/socialfeed/post';" class="ui-link-inherit">
                    <i class="icon_pencil"></i> 
                    <span><?php echo $this->translate('Status'); ?></span>
                  </a>
                
              </div>
            </td>
            <td>
              <div>
                <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input-linkedinfeed',true, 'addphoto');sm4.activity.feedURL= sm4.core.baseUrl+ 'advancedactivity/socialfeed/post';" >
                  <i class="icon_photo"></i> 
                  <span><?php echo $this->translate('Photo'); ?></span>
                </a>
              </div>   
            </td>
    <?php endif; ?>
   
        </tr>
      </table>
    </div>
  <?php //endif; ?>

  <?php if ($this->resource_type): ?>
<!--    <div class="activity-post-options activity-profile-post-options ui-btn-corner-all">
      <table>
        <tr>
              <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin') || Engine_Api::_()->sitemobile()->enableComposer('photo')): ?>
            <td>
              <div>
      <?php if ($this->resource_type == '' || $this->resource_type == 'user'): ?>
                  <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input',true, '');">
                    <i class="icon_pencil"></i> 
                    <span><?php echo $this->translate('Status'); ?></span>
                  </a>
      <?php else : ?>
                  <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input',true, '');">
                    <i class="icon_pencil"></i> 
                    <span><?php echo $this->translate('Post'); ?></span>
                  </a>
            <?php endif; ?>
              </div>
            </td>
    <?php endif; ?>
    <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
            <td>
              <div>
                <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input',true, 'addphoto');" >
                  <i class="icon_photo"></i> 
                  <span><?php echo $this->translate('Photo'); ?></span>
                </a>
              </div>   
            </td>
    <?php endif; ?>
    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')): ?>
            <td>
              <div>
                <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input',true, 'checkin');">
                  <i class="icon_checkin"></i> 
                  <span><?php echo $this->translate('Check In'); ?></span>
                </a>
              </div>  
            </td>
    <?php endif; ?>
        </tr>
      </table>
    </div>-->
  <?php endif; ?>

  <?php
  if (1):

    $showDefaultTextbox = "display:block;";
  else :
    $showDefaultTextbox = "display:none";
  endif;
  ?>


  <div class="activity-post-container-input-linkedinfeed activity-post-container-input ui-body-b" onclick="sm4.activity.toggleFeedArea(this,true, '');sm4.activity.feedURL= sm4.core.baseUrl+ 'advancedactivity/socialfeed/post';" style='display:none;' >   
    <input type="text" class="ui-input-text" data-role="none" placeholder="<?php echo $this->translate('Post Something...'); ?>"/>
  </div>

  <div class="activity-post-container" style="display:none;" > 
    <form method="post" class="activity" enctype="application/x-www-form-urlencoded" id="activity-form" data-ajax="false">
  <?php //STATUS BOX HEADER  ?>
      <div class="ui-header ui-bar-a o_hidden" id="ui-header">
        <a data-icon="chevron-left" data-role="button" href="" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a" class="ui-btn-left" onclick="sm4.activity.toggleFeedArea(this,false, 'status');"><?php //echo $this->translate('Cancel'); ?></a>
        <h2 class="ui-title" role="heading" aria-level="1"><?php echo $this->translate('Update Status'); ?></h2>

        <button id="compose-submit" type="submit" data-theme="a" data-role="button" class="ui-btn-right done"><?php echo $this->translate("Post") ?></button>
      </div>
  <?php //ADD PEOPLE HEADER  ?>
      <div class="sm-post-wrap ui-page-content ui-body-c">
        <textarea id="activity_body" cols="1" rows="1" name="body" placeholder="<?php echo $this->translate('Post Something...'); ?>"></textarea>
        <div id="toValuesdone-wrapper" class="sm-post-show-tags" style="display:none;"></div> 
        <div class="compose_share_op" style="display:none;">
  <?php //echo $this->partial('application/modules/Sitemobile/modules/Advancedactivity/views/scripts/_composerSocialServices.tpl', array()); ?>
        </div>

        <div class="compose_buttons"> 
          <div class="left_options">


            <div id="composer-options">
              <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo',array('auth' =>(array('album','create'))))) : ?>
                <a href="javascript:void(0);" onclick="$('#attachment-options').css('display', 'block'); $('#smactivityoptions-popup').css('display', 'none'); return sm4.activity.composer.showPluginForm(this, 'photo');" data-role="none">
                  <i class="cm-icons cm-icon-photo"></i>
                </a>
                <?php endif; ?>
              <a href="javascript:void(0);" data-role="none" onclick="$(this).css('display', 'none');$('#activitypost-container-temp').find('.sm-post-composer-options').toggle();" id="attachment-options">
                <i class="cm-icons cm-icon-attachement"></i>
              </a>
              <div id="smactivityoptions-popup" class="sm-post-composer-options" style="display:none;"> 
<!--                Upload music to activity feed only on page profile if page music is enabled-->

                <?php //if (Engine_Api::_()->sitemobile()->enableComposer('music',array('auth'=>(array('music_playlist','create')))) && ($this->resource_type == 'sitepage_page' || ($this->resource_type =='sitebusiness_business' ))): ?>
<?php if (Engine_Api::_()->sitemobile()->enableComposer('music',array('auth'=>(array('music_playlist','create'))))) : ?>

                  <a href="javascript:void(0);" onclick="$('#attachment-options').css('display', 'block'); $('#smactivityoptions-popup').css('display', 'none');return sm4.activity.composer.showPluginForm(this, 'music');" data-role="none" >
                    <i class="cm-icons cm-icon-music"></i>
                  </a>
                <?php endif; ?>
                <?php if (Engine_Api::_()->sitemobile()->enableComposer('video',array('auth' =>(array('video','create'))))) : ?>
                  <a href="javascript:void(0);" onclick="$('#attachment-options').css('display', 'block'); $('#smactivityoptions-popup').css('display', 'none');return sm4.activity.composer.showPluginForm(this, 'video');" data-role="none" >
                    <i class="cm-icons cm-icon-video"></i>
                  </a>
                <?php endif; ?>
                <?php if (Engine_Api::_()->sitemobile()->enableComposer('link',array('auth' =>(array('core_link','create'))))) : ?>
                  <a href="javascript:void(0);" onclick="$('#attachment-options').css('display', 'block'); $('#smactivityoptions-popup').css('display', 'none');return sm4.activity.composer.showPluginForm(this, 'link');" data-role="none">
                    <i class="cm-icons cm-icon-link"></i>
                  </a>
  <?php endif; ?>
              </div>
            </div>
          </div>		
        </div>   
      </div>
      <input type="hidden" id="activity_type" value="3"  name="activity_type" />
      <input type="hidden" id="linkedinmin_id" value=""  name="linkedinmin_id" />
    </form>

  </div>  
<?php endif; ?>
</div>
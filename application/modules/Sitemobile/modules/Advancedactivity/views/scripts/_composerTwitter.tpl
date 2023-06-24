<div data-role="composer-wrapper" >
<?php if (1): ?> 
<?php //if ($this->resource_type == ''): ?>

    <div class="activity-post-options activity-profile-post-options ui-btn-corner-all">
      <table>
        <tr>            
            <td>
              <div>
                 <a href="javascript:void(0);" onclick="sm4.activity.toggleFeedArea('.activity-post-container-input-tweetfeed',true, '');sm4.activity.feedURL= sm4.core.baseUrl+ 'advancedactivity/socialfeed/post';" class="ui-link-inherit">
                    <i class="icon_pencil"></i> 
                    <span><?php echo $this->translate('Status'); ?></span>
                  </a>
                
              </div>
            </td> 
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


  <div class="activity-post-container-input-tweetfeed activity-post-container-input ui-body-b" onclick="sm4.activity.toggleFeedArea(this,true, '');sm4.activity.feedURL= sm4.core.baseUrl+ 'advancedactivity/socialfeed/post';" style='display:none;' >   
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
      <div class="sm-post-wrap ui-page-content ui-body-c" style="overflow: hidden;">
        <textarea id="activity_body" cols="1" rows="1" name="body" placeholder="<?php echo $this->translate('Post Something...'); ?>" onKeyDown="sm4.socialactivity.twitter.limitText($(this),140);"></textarea>        
        <div class="fright compose_buttons">
            <div id="show_loading" class="show_loading" style="display: inline-block;">140</div>
          
          </div>
      </div>
      <input type="hidden" id="activity_type" value="2"  name="activity_type" />     
    </form>

  </div>  
<?php endif; ?>
</div>
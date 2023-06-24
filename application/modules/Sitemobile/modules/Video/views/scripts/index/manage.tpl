<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if (($this->current_count >= $this->quota) && !empty($this->quota)): ?>
<?php if($this->autoContentLoad == 0) : ?>
  <div class="tip">
      <?php endif; ?>
    <span>
      <?php echo $this->translate('You have already created the maximum number of videos allowed. If you would like to post a new video, please delete an old one first.'); ?>
    </span>
      <?php if($this->autoContentLoad == 0) : ?>
  </div>
  <br/>
  <?php endif; ?>
<?php endif; ?>
  

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
  <?php if($this->autoContentLoad == 0) : ?>
    <div class="videos-listing manage-videos-listing" id="managevideos_ul">
      <ul data-role="none">
               <?php endif;?>
        <?php foreach ($this->paginator as $item): ?>
          <li>
            <div class="videos-listing-top">
              <a href="<?php echo $item->getHref() ?>">
                <?php
                if ($item->photo_id) {
                  echo $this->itemPhoto($item, 'thumb.normal');
                } else {
                  echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
                }
                ?>
                <i class="ui-icon ui-icon-play"></i>
              </a>
              <?php if ($item->duration): ?>
                <span class="video-duration">
                  <?php
                  if ($item->duration >= 3600) {
                    $duration = gmdate("H:i:s", $item->duration);
                  } else {
                    $duration = gmdate("i:s", $item->duration);
                  }
                  echo $duration;
                  ?>
                </span>
              <?php endif ?>
            </div>
            
            <div class="videos-listing-bottom">
              <div class="videos-listing-left">
                <p class="video-title"><?php echo $item->getTitle() ?></p>
                <p class="listing-counts video-stats fleft">
                  <span class="f_small"><?php echo $item->likes()->getLikeCount(); ?></span>
                  <i class="ui-icon-thumbs-up-alt"></i>
                  <span class="f_small"><?php echo $this->locale()->toNumber($item->comment_count) ?></span>
                  <i class="ui-icon-comment"></i>
                  <span class="f_small"><?php echo $this->locale()->toNumber($item->view_count) ?></span>
                  <i class="ui-icon-eye-open"></i>
                </p>
                <p class="ui-li-aside-rating fright video-stats"> 
                  <?php if( $item->rating > 0 ): ?>
                    <?php for( $x=1; $x<=$item->rating; $x++ ): ?>
                      <span class="rating_star_generic rating_star"></span>
                    <?php endfor; ?>
                    <?php if( (round($item->rating) - $item->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half"></span>
                    <?php endif; ?>
                  <?php endif; ?>
                </p>
              </div>
              <div class="videos-listing-right">
                <a href="#manage_<?php echo $item->getGuid() ?>" data-rel="popup" data-transition="pop" class="ui-icon-ellipsis-vertical"></a>
              </div>
            </div>
            <div data-role="popup" id="manage_<?php echo $item->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
              <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
                <a class="ui-btn-default ui-btn-action" href="<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'edit', 'video_id' => $item->video_id), 'default', 'true'); ?>"><?php echo $this->translate('Edit Video') ?></a>
                <?php
                if ($item->status != 2) {
                  echo $this->htmlLink(array('route' => 'default', 'module' => 'video', 'controller' => 'index', 'action' => 'delete', 'video_id' => $item->video_id), $this->translate('Delete Video'), array(
                      'class' => 'ui-btn-default ui-btn-danger'));
                }
                ?>					
              </div> 
            </div>
            <?php if($item->status == 0):?>
          <div class="tip">
            <span>
              <?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.')?>
            </span>
          </div>
        <?php elseif($item->status == 2):?>
          <div class="tip">
            <span>
              <?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.')?>
            </span>
          </div>
        <?php elseif($item->status == 3):?>
          <div class="tip">
            <span>
             <?php echo $this->translate('Video conversion failed. Please try uploading again.'); ?>
            </span>
          </div>
        <?php elseif($item->status == 4):?>
          <div class="tip">
            <span>
             <?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try uploading again.'); ?>
            </span>
          </div>
         <?php elseif($item->status == 5):?>
          <div class="tip">
            <span>
             <?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try uploading again.'); ?>

            </span>
          </div>
         <?php elseif($item->status == 7):?>
          <div class="tip">
            <span>
             <?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try uploading a smaller file, or delete some files to free up space.'); ?>

            </span>
          </div>
        <?php endif;?>
          </li>
        <?php endforeach; ?>
          <?php if($this->autoContentLoad == 0) : ?>
      </ul>
      <?php //echo $this->paginationControl($this->paginator); ?>
    </div>
  <?php endif; ?>
  <?php else: ?>
    <div class="sm-content-list ui-list-manage-page">
      <ul data-role="listview" data-inset="false">
        <?php foreach ($this->paginator as $item): ?>
          <li data-icon="cog" data-inset="true">
            <a href="<?php echo $item->getHref() ?>">
              <?php
              if ($item->photo_id) {
                echo $this->itemPhoto($item, 'thumb.icon');
              } else {
                echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
              }
              ?>
              <div class="ui-listview-play-btn"><i class="ui-icon ui-icon-play-circle"></i></div>
              <div class="ui-list-content">
                <h3><?php echo $item->getTitle() ?></h3>
                <?php if ($item->duration): ?>
                  <p class="ui-li-aside">
                    <?php
                    if ($item->duration >= 3600) {
                      $duration = gmdate("H:i:s", $item->duration);
                    } else {
                      $duration = gmdate("i:s", $item->duration);
                    }
                    //$duration = ltrim($duration, '0:');
                    //              if( $duration[0] == '0' ) {
                    //                $duration= substr($duration, 1);
                    //              }
                    echo $duration;
                    ?>
                  </p>
                <?php endif ?>
                <p> 
                  <?php echo $this->translate(array('%1$s view', '%1$s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
                </p>
                            <?php if($item->status == 0):?>
          <div class="tip">
            <span>
              <?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.')?>
            </span>
          </div>
        <?php elseif($item->status == 2):?>
          <div class="tip">
            <span>
              <?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.')?>
            </span>
          </div>
        <?php elseif($item->status == 3):?>
          <div class="tip">
            <span>
             <?php echo $this->translate('Video conversion failed. Please try uploading again.'); ?>
            </span>
          </div>
        <?php elseif($item->status == 4):?>
          <div class="tip">
            <span>
             <?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try uploading again.'); ?>
            </span>
          </div>
         <?php elseif($item->status == 5):?>
          <div class="tip">
            <span>
             <?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try uploading again.'); ?>

            </span>
          </div>
         <?php elseif($item->status == 7):?>
          <div class="tip">
            <span>
             <?php echo $this->translate('Video conversion failed. You may be over the site upload limit. Try uploading a smaller file, or delete some files to free up space.'); ?>

            </span>
          </div>
        <?php endif;?>
              </div>
            </a>
            <a href="#manage_<?php echo $item->getGuid() ?>" data-rel="popup" data-transition="pop"></a>
            <div data-role="popup" id="manage_<?php echo $item->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
              <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
                <h3><?php echo $item->getTitle() ?></h3>
                <a class="ui-btn-default ui-btn-action" href="<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'edit', 'video_id' => $item->video_id), 'default', 'true'); ?>"><?php echo $this->translate('Edit Video') ?></a>
                <?php
                if ($item->status != 2) {
                  echo $this->htmlLink(array('route' => 'default', 'module' => 'video', 'controller' => 'index', 'action' => 'delete', 'video_id' => $item->video_id, 'format' => 'smoothbox'), $this->translate('Delete Video'), array(
                      'class' => 'smoothbox ui-btn-default ui-btn-danger'));
                }
                ?>					
                <a href="#" data-rel="back" class="ui-btn-default">
                  <?php echo $this->translate('Cancel'); ?>
                </a>
              </div> 
            </div>
            
          </li>
          
        <?php endforeach; ?>
       <?php if($this->autoContentLoad == 0) : ?> 
      </ul>
         <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
      <?php echo $this->paginationControl($this->paginator); ?>
        <?php endif; ?>
        <?php endif; ?>
    </div>
  <?php endif?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You do not have any videos.'); ?>
      <?php if ($this->can_create): ?>
        <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a href="' . $this->url(array('action' => 'create')) . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

  <script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) :?>
     <?php $current_url = $this->url(array('action' => 'manage')); ?>  
          sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->searchParams);?>, 'contentUrl' : '<?php echo $current_url; ?>', 'activeRequest' : false, 'container' : 'managevideos_ul' };  
          });
   <?php endif; ?>    
</script>
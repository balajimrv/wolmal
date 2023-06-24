<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */

?>

<div class="ui-page-content video-view-page">
  <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>' style='display:none;'>
    <input type="hidden" id="tag" name="tag" value=""/>
  </form>
  
  <!--VIDEO PLAYER CONDITION FOR MOBILE APP AND MOBILE.-->
    <?php if ($this->video->type == 3): ?>
      <div class="video-player prelative">
        <?php if ($this->video->duration): ?>
          <div class="video-duration">
            <strong>
              <?php
              if ($this->video->duration >= 3600) {
                $duration = gmdate("H:i:s", $this->video->duration);
              } else {
                $duration = gmdate("i:s", $this->video->duration);
              }
              echo $duration;
              ?>
            </strong>	
          </div>
        <?php endif ?>
          <?php if (stripos($_SERVER['HTTP_USER_AGENT'], "Android")): ?>
        <a onclick="window.videoPlayer.player('<?php echo $this->video_location ?>')" >
            <?php else: ?>
       <div id="video_embed" class="video_embed">
      <video id="video" controls preload="auto" width="100%" height="386">
        <source type='video/mp4;' src="<?php echo $this->video_location ?>">
      </video>
       </div>   
            <?php endif; ?>
          <?php
          if ($this->video->photo_id) {
              echo $this->itemPhoto($this->video, 'thumb.profile');
          } else {
              echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
          }
          ?>
          <span></span>
          <?php if (stripos($_SERVER['HTTP_USER_AGENT'], "Android")): ?>
          <i class="ui-icon ui-icon-play"></i>
          <?php endif; ?>
        </a>
      </div>
    <?php elseif ($this->video->type == 1 && 0): ?>
      <div class="video-player prelative">
        <?php if ($this->video->duration): ?>
          <div class="video-duration">
            <strong>
              <?php
              if ($this->video->duration >= 3600) {
                $duration = gmdate("H:i:s", $this->video->duration);
              } else {
                $duration = gmdate("i:s", $this->video->duration);
              }
              echo $duration;
              ?>
            </strong>	
          </div>
        <?php endif ?>
        <a onclick="window.videoPlayer.youtube('<?php echo $this->video->code ?>')">
          <?php
          if ($this->video->photo_id) :
            echo $this->itemPhoto($this->video, 'thumb.profile');
          else:
            echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png" />';
          endif;
          ?>
          <span></span>
          <i class="ui-icon ui-icon-play"></i>
        </a>
      </div>
    <?php else: ?>
      <div class="sm-ui-video-embed"><?php echo $this->videoEmbedded ?></div>
    <?php endif; ?>

  
  <div class="video-view-content">      
    <div class="video-title">
      <?php echo $this->video->getTitle() ?>
    </div>
          
    <div class="video-stats t_light f_small">  
      <?php echo $this->translate('By'); ?>
      <?php echo $this->htmlLink($this->subject()->getOwner(), $this->subject()->getOwner()->getTitle()) ?>  
    </div>    
    <div class="video-stats t_light f_small">
      <?php echo $this->timestamp($this->video->creation_date) ?> 
      <?php if ($this->category): ?>
        - 
        <?php
        echo $this->htmlLink(array(
            'route' => 'video_general',
            'QUERY' => array('category' => $this->category->category_id)
                ), $this->translate($this->category->category_name)
        )
        ?>
      <?php endif; ?>
      <?php if (count($this->videoTags)): ?>
        -
        <?php foreach ($this->videoTags as $tag): ?>
          <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text ?></a>&nbsp;
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
          
    <div class="video-stats t_light f_small">
      <?php echo $this->translate(array('%s view', '%s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
    </div>  

    <?php if (!empty($this->video->description)): ?>
      <div class="sm-ui-cont-cont-des f_small">
          <?php echo nl2br($this->viewMore($this->translate($this->video->description), 80));  ?>
        <?php //echo nl2br($this->video->description) ?> 
      </div>
    <?php endif ?>

    <div class="sm-ui-video-rating">
    <div id="video_rating" class="rating" onmouseout="rating_out();" valign="top">
      <span id="rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(1,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(1);"></span>
      <span id="rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(2,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(2);"></span>
      <span id="rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(3,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(3);"></span>
      <span id="rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(4,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(4);"></span>
      <span id="rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(5,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(5);"></span>
      <div id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></div>
    </div>
  </div>	
  </div>
</div>
<script type="text/javascript">
 sm4.core.runonce.add(function() { 
             $('.layout_page_video_index_view').find('.layout_sitemobile_sitemobile_headingtitle').html('');    
          });
 </script>
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
<div class="ui-page-content">
  <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>' style='display:none;'>
    <input type="hidden" id="tag" name="tag" value=""/>
  </form>

  <div class="sm-ui-cont-head">
    <div class="sm-ui-cont-author-photo">
<?php echo $this->htmlLink($this->subject()->getOwner(), $this->itemPhoto($this->subject()->getOwner(), 'thumb.icon')) ?>
    </div>
    <div class="sm-ui-cont-cont-info">
      <div class="sm-ui-cont-author-name">
<?php echo $this->htmlLink($this->subject()->getOwner(), $this->subject()->getOwner()->getTitle()) ?>
      </div>
      <div class="sm-ui-cont-cont-date">
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
      <div class="sm-ui-cont-cont-date">
<?php echo $this->translate(array('%s view', '%s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
      </div>
    </div>
  </div>
  <div class="sm-ui-video-view">
    <?php if (!empty($this->video->description)): ?>
      <div class="sm-ui-cont-cont-des">
      <?php echo nl2br($this->video->description) ?>
      </div>
    <?php endif ?>
    <?php if ($this->video->duration && !Engine_Api::_()->sitemobile()->isApp()): ?>
      <div class="sm-ui-video-duration">
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
    <?php if ($this->video->type == 3): ?>
    <?php if( $this->video_extension === 'flv' ): ?>
      <div class="tip clr">
        <span><?php echo $this->translate("For Playing this video, the Flash Plugin is required. But Mobile / Tablet device browser does not support Flash Plugin. So you can not play this video now.");?></span>
      </div>
    <div class="video_embed" class="sm-ui-video-embed" style="display:none;"></div>
    <?php else: ?>
      <div id="video_embed" class="video_embed">
      <video id="video" controls preload="auto" width="100%" height="386">
        <source type='video/mp4;' src="<?php echo $this->video_location ?>">
      </video>
  </div>
    <?php endif;?>
      
    <?php else: ?>
      <div class="sm-ui-video-embed"><?php echo $this->videoEmbedded ?></div>
    <?php endif; ?>

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

    <div data-type="horizontal" data-role="controlgroup" valign="top" class="sm-ui-video-op">
      <?php if ($this->can_embed): ?>
        <?php
        echo $this->htmlLink(array(
            'module' => 'video',
            'controller' => 'video',
            'action' => 'embed',
            'route' => 'default',
            'id' => $this->video->getIdentity(),
            'format' => 'smoothbox'
                ), $this->translate("Embed"), array(
            'class' => 'smoothbox',
            'data-role' => "button", 'data-icon' => "wrench", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
        ));
        ?>
      <?php endif ?>
      <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
        <?php
        echo $this->htmlLink(array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'route' => 'default',
            'type' => 'video',
            'id' => $this->video->getIdentity(),
            'format' => 'smoothbox',
                ), $this->translate("Share"), array(
            'class' => 'smoothbox',
            'data-role' => "button", 'data-icon' => "comments-alt", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
        ));
        ?>
        <?php
        echo $this->htmlLink(array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'route' => 'default',
            'subject' => $this->video->getGuid(),
            'format' => 'smoothbox'
                ), $this->translate("Report"), array(
            'class' => 'smoothbox',
            'data-role' => "button", 'data-icon' => "flag", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
        ));
        ?>
<?php endif ?>
    </div>
  </div>	
</div>
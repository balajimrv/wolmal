<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php //echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>
<?php if( $this->tag ): ?>
  <h3>
    <?php echo $this->translate('Videos using the tag') ?>
    #<?php echo $this->tag ?>
    <a href="javascript://" onclick="$.mobile.changePage('<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>');return false;">(x)</a>
  </h3>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
<?php if($this->autoContentLoad == 0) : ?>
    <div class="videos-listing" id='browsevideos_ul'>
      <ul data-role="none">
           <?php endif;?>
        <?php foreach( $this->paginator as $item ): ?>
          <li>
            <div class="videos-listing-top">
              <a href="<?php echo $item->getHref(); ?>" data-title="<?php echo $item->getTitle() ?>">
                <?php
                  if( $item->photo_id ) {
                    echo $this->itemPhoto($item, 'thumb.profile');
                  } else {
                    echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
                  }
                ?>
                <i class="ui-icon ui-icon-play"></i>
              </a>
              <?php if( $item->duration ): ?>
                <span class="video-duration">
                  <?php
                    if( $item->duration >= 3600 ) {
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
                <p class="video-stats f_small t_light">
                  <?php echo $this->translate('By'); ?>
                  <?php echo $item->getOwner()->getTitle(); ?>
                </p>
              </div>
              <div class="videos-listing-right">
                <p> 
                  <?php if( $item->rating > 0 ): ?>
                    <?php for( $x=1; $x<=$item->rating; $x++ ): ?>
                      <span class="rating_star_generic rating_star"></span>
                    <?php endfor; ?>
                    <?php if( (round($item->rating) - $item->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half"></span>
                    <?php endif; ?>
                  <?php endif; ?>
                </p>
                <p class="listing-counts">
                  <span class="f_small"><?php echo $item->likes()->getLikeCount(); ?></span>
                  <i class="ui-icon-thumbs-up-alt"></i>
                  <span class="f_small"><?php echo $this->locale()->toNumber($item->comment_count) ?></span>
                  <i class="ui-icon-comment"></i>
                  <span class="f_small"><?php echo $this->locale()->toNumber($item->view_count) ?></span>
                  <i class="ui-icon-eye-open"></i>
                </p>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
          <?php if($this->autoContentLoad == 0) : ?>
      </ul>
    </div>
<?php endif; ?>
    <?php else :?>
 <?php if($this->autoContentLoad == 0) : ?>
      <div class="sm-content-list ui-listgrid-view">
        <ul data-role="listview" data-inset="false" data-icon="arrow-r">
          <?php endif; ?>
            <?php foreach( $this->paginator as $item ): ?>
            
            <li>  
              <a href="<?php echo $item->getHref(); ?>">
              <?php
                if( $item->photo_id ) {
                  echo $this->itemPhoto($item, 'thumb.profile');
                } else {
                  echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
                }
              ?>
              <div class="ui-listview-play-btn"><i class="ui-icon ui-icon-play-circle"></i></div>
              <h3><?php echo $item->getTitle() ?></h3>
              <?php if( $item->duration ): ?>
                <p class="ui-li-aside">
                  <?php
                    if( $item->duration >= 3600 ) {
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
              <?php endif; ?>
              <p><?php echo $this->translate('By'); ?>
                <strong><?php echo $item->getOwner()->getTitle(); ?></strong>
              </p>
            <!--	<p> 
                <?php //echo $this->translate(array('%1$s view', '%1$s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
              </p>-->
              <p class="ui-li-aside-rating"> 
                <?php if( $item->rating > 0 ): ?>
                  <?php for( $x=1; $x<=$item->rating; $x++ ): ?>
                    <span class="rating_star_generic rating_star"></span>
                  <?php endfor; ?>
                  <?php if( (round($item->rating) - $item->rating) > 0): ?>
                    <span class="rating_star_generic rating_star_half"></span>
                  <?php endif; ?>
                <?php endif; ?>
              </p>
              </a> 
            </li>
          <?php endforeach; ?>
             <?php if($this->autoContentLoad == 0) : ?>     
        </ul>
      </div>

 <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
	<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
			'pageAsQuery' => true,
		)); ?>
 <?php endif; ?>     
 <?php endif; ?>
<?php endif;?>
<?php elseif( $this->category || $this->tag || $this->text ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted a video with that criteria.');?>
      <?php if ($this->can_create):?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), "video_general").'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a video yet.');?>
      <?php if ($this->can_create):?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), "video_general").'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

<script type="text/javascript">

	function redirectVideo() {
		window.location.href='<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>';
	}

<?php if (Engine_Api::_()->sitemobile()->isApp()) :?>
     <?php $current_url = $this->url(array('action' => 'browse')); ?>    
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : '<?php echo $current_url; ?>', 'activeRequest' : false, 'container' : 'browsevideos_ul' };  
          });
         
   <?php endif; ?>    
</script>
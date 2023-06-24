<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
  <div class="videos-listing manage-videos-listing" id="profile_videos">
    <ul data-role="none">
      <?php foreach ($this->paginator as $item): ?>
        <li>
          <div class="videos-listing-top">
            <a href="<?php echo $item->getHref(); ?>">
              <?php
              if ($item->photo_id) {
                echo $this->itemPhoto($item, 'thumb.profile');
              } else {
                echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
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
            <p class="video-title"><?php echo $item->getTitle() ?></p>
            <p class="listing-counts video-stats fleft">
              <span class="f_small">23</span>
              <i class="ui-icon-thumbs-up-alt"></i>
              <span class="f_small">45</span>
              <i class="ui-icon-comment"></i>
              <span class="f_small"><?php echo $this->locale()->toNumber($item->view_count) ?></span>
              <i class="ui-icon-eye-open"></i>
            </p>
            <?php if ($item->rating > 0): ?>
              <p class="video-stats fright"> 
              <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                  <span class="rating_star_generic rating_star"></span>
                <?php endfor; ?>
                <?php if ((round($item->rating) - $item->rating) > 0): ?>
                  <span class="rating_star_generic rating_star_half"></span>
                <?php endif; ?>
              </p>
              <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php else :?>
 <div class="sm-content-list ui-listgrid-view ui-listgrid-view-no-caption" id="profile_videos">
    <ul data-role="listview" data-inset="false" data-icon="arrow-r">
  <?php foreach ($this->paginator as $item): ?>
        <li>  
          <a href="<?php echo $item->getHref(); ?>">
            <?php
            if ($item->photo_id) {
              echo $this->itemPhoto($item, 'thumb.profile');
            } else {
              echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
            }
            ?>
            <div class="ui-listview-play-btn"><i class="ui-icon ui-icon-play-circle"></i></div>
              <h3><?php echo $item->getTitle() ?></h3>
    <?php if ($item->rating > 0): ?>
                <p class="ui-li-aside-rating"> 
                <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                    <span class="rating_star_generic rating_star"></span>
                  <?php endfor; ?>
                  <?php if ((round($item->rating) - $item->rating) > 0): ?>
                    <span class="rating_star_generic rating_star_half"></span>
                  <?php endif; ?>
                </p>
                <?php endif; ?>
              <?php if ($item->duration): ?>
              <p class="ui-li-aside">
              <?php
              if ($item->duration >= 3600) {
                $duration = gmdate("H:i:s", $item->duration);
              } else {
                $duration = gmdate("i:s", $item->duration);
              }
              echo $duration;
              ?>
              </p>
              <?php endif ?>

          </a> 
        </li>
  <?php endforeach; ?>
    </ul>
  </div>
<?php endif?>

<?php if ($this->paginator->count() > 1): ?>
  <?php
  echo $this->paginationAjaxControl(
          $this->paginator, $this->identity, 'profile_videos');
  ?>
<?php endif; ?>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-all-dislike-user.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php //GET VERSION OF SITEMOBILE APP.
  $RemoveClassDone = true;
  if(Engine_Api::_()->sitemobile()->isApp()) {
   if(Engine_Api::_()->sitemobile()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitemobileapp')->version, '4.8.6') )
      $RemoveClassDone = false;
  }
 
?>
<?php 
    if(Engine_Api::_()->seaocore()->checkEnabledNestedComment($this->subject()->getType())):
        include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';

    if($showAsLike) {
        $showLikeWithoutIcon=1;
    }
    endif;
?>
<div class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper">
  <?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/modules/User/View/Helper', 'User_View_Helper'); ?>
  <?php if ($this->likes->getTotalItemCount() > 0): // COMMENTS -------  ?>
    <?php $action = $this->action; ?>
    <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>

    <?php if ($this->page == 1): ?>
      <div class="sm-ui-popup-top ui-header ui-bar-a">
        <?php if (!empty($action)): ?> 
          <a data-iconpos="notext" data-role="button" data-icon="chevron-left" data-corners="true" data-shadow="true" class="ui-btn-left " onclick= "$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'block');$('#dislike-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');"><?php //echo $this->translate('back');?></a>
        <?php else : ?>
          <?php $this->headScriptSM()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/smActivity.js'); ?>
    <?php endif; ?>
        <a href="javascript:void(0);" data-iconpos="notext" data-role="button" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ps-close-popup close-feedsharepopup ui-btn-right" ></a>
        <?php if(isset($showLikeWithoutIcon) && $showLikeWithoutIcon != 3):?>
            <h2 class="ui-title"><?php echo $this->translate('People who dislike this'); ?></h2>
        <?php else:?>
            <h2 class="ui-title"><?php echo $this->translate('People who down voted this'); ?></h2>
        <?php endif;?>
      </div>

      <div class="sm-ui-popup-likes sm-content-list">
        <ul id="dislikemembers_ul" class="ui-member-list" data-role="listview" data-icon="none"> 
        <?php endif; ?>
        <?php foreach ($this->likes as $like): ?>
          <?php $user = $this->item($like->poster_type, $like->poster_id); ?>
          <?php
          $table = Engine_Api::_()->getDbtable('block', 'user');
          $select = $table->select()
                  ->where('user_id = ?', $user->getIdentity())
                  ->where('blocked_user_id = ?', $viewer->getIdentity())
                  ->limit(1);
          $row = $table->fetchRow($select);
          ?>
          <li>
              <?php if ($row == NULL && $this->viewer()->getIdentity() && $this->userFriendshipSM($user)): ?>
              <div class="ui-item-member-action">
              <?php echo $this->userFriendshipSM($user) ?>
              </div>
              <?php endif; ?>
            <a href="<?php echo $user->getHref() ?>">
    <?php echo $this->itemPhoto($user, 'thumb.icon') ?>
              <div class="ui-list-content">
                <h3><?php echo $user->getTitle() ?></h3>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
  <?php if ($this->page == 1): ?>
        </ul>
        <div class="like_viewmore" id="dislike_viewmore" style="display: none;">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
              'id' => 'dislike_viewmore_link',
              'class' => 'buttonlink icon_viewmore',
              'onclick' => 'sm4.activity.getDisLikeUsers(' . $this->action_id . ',' . ($this->page + 1) . ');'
          ))
          ?>
        </div>
      </div>	
    <?php endif; ?>
<?php endif; ?>
</div>
<div style="display:none;">
  <script type="text/javascript">
      sm4.core.runonce.add(function() {
        $('.ps-close-popup').on('click', function() {
          <?php if($RemoveClassDone):?>   
              $('.ui-page-active').removeClass('dnone');
           <?php else : ?>
             $('.ui-page-active').removeClass('pop_back_max_height');
           <?php endif;?>  
          $('.ps-close-popup').closest('#feedsharepopup').remove();
          $.mobile.silentScroll(parentScrollTop); 
        });
      });
<?php if ($this->page && $this->likes->getCurrentPageNumber() >= $this->likes->count()): ?>
        var nextdislikepage = 0;
<?php else: ?>
        var nextdislikepage = 1;
<?php endif; ?>
<?php if (!empty($action)): ?>
        window.onscroll = sm4.activity.doOnScrollLoadActivityDisLikes('<?php echo $this->action_id; ?>', true, '<?php echo ($this->page + 1); ?>');

<?php else: ?>
  <?php if ($this->page == 1): ?>

          function doOnScrollLoadActivityDisLikes() {
            if (nextdislikepage == 0) {
              window.onscroll = '';
              return;
            }
            if ($.type($('#feed_viewmore').get(0)) != 'undefined') {
              if ($.type($('#dislike_viewmore').get(0).offsetParent) != 'undefined') {
                var elementPostionY = $('#dislike_viewmore').get(0).offsetTop;
              } else {
                var elementPostionY = $$('#dislike_viewmore').get(0).y;
              }
              if (elementPostionY <= $(window).scrollTop() + ($(window).height() - 40)) {
                $('#dislike_viewmore').css('display', 'block');
                $('#dislike_viewmore').html('<i class="icon-spinner icon-spin ui-icon"></i>');
                getDisLikeUsers();
              }
            }
          }
          function getDisLikeUsers() {
            $('#dislike_viewmore').css('display', 'block');
            if ($.type(sm4.core.subject) != 'undefined') {
              var subjecttype = sm4.core.subject.type;
              var subjectid = sm4.core.subject.id;
            }
            else {
              var subjecttype = '';
              var subjectid = '';
            }

            $.ajax({
              type: "POST",
              dataType: "html",
              url: sm4.core.baseUrl + 'advancedactivity/index/get-all-dislike-user',
              data: {
                'format': 'html',
                'type': subjecttype,
                'id': subjectid,
                'page': '<?php echo ($this->page + 1); ?>'
              },
              success: function(responseHTML, textStatus, xhr) {
                activeRequestDislike = false;
                $('#dislike_viewmore').css('display', 'none');
                $(document).data('loaded', true);
                $('#dislikemembers_ul').append(responseHTML);
                sm4.core.dloader.refreshPage();
                sm4.core.runonce.trigger();
              }
            });
          }
          window.onscroll = doOnScrollLoadActivityDisLikes();
  <?php endif; ?>
<?php endif; ?>
  </script>  
</div>
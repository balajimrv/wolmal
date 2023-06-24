<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-all-like-user.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper">
  <?php if ($this->likes->getTotalItemCount() > 0): // COMMENTS -------  ?>
    <?php $action = $this->action; ?>
    <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>

    <?php if ($this->page == 1): ?>
      <div class="sm-ui-popup-top ui-header ui-bar-a">
        <?php if (!empty($action)): ?> 
          <a data-iconpos="notext" data-icon="chevron-left" data-corners="true" data-shadow="true" class="ui-icon-left " onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'block');$('#like-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');"><?php //echo $this->translate('back');?></a>
        <?php else : ?>
          <?php $this->headScriptSM()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/smActivity.js'); ?>
        <?php endif; ?>
        <a href="javascript:void(0);" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-icon-right close-feedsharepopup" onclick="$('.ui-page-active').removeClass('dnone');$('#feedsharepopup').remove();$(window).scrollTop(parentScrollTop)"></a>
        <h2 class="ui-title"><?php echo $this->translate('People who like this'); ?></h2>
      </div>

      <div class="sm-ui-popup-container sm-ui-popup-likes sm-content-list">
        <ul id="likemembers_ul" class="ui-member-list" data-role="listview" data-icon="none">
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
        <div class="like_viewmore" id="like_viewmore" style="display: none;">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
              'id' => 'like_viewmore_link',
              'class' => 'buttonlink icon_viewmore',
              'onclick' => 'sm4.activity.getLikeUsers(' . $this->action_id . ',' . ($this->page + 1) . ');'
          ))
          ?>
        </div>
      </div>	
    <?php endif; ?>
<?php endif; ?>
</div>
<div style="display:none;">
  <script type="text/javascript">
<?php if ($this->page && $this->likes->getCurrentPageNumber() >= $this->likes->count()): ?>
         var nextlikepage = 0;
<?php else: ?>
         var nextlikepage = 1;
<?php endif; ?>
<?php if (!empty($action)): ?>
            window.onscroll = sm4.activity.doOnScrollLoadActivityLikes('<?php echo $this->action_id; ?>', true, '<?php echo ($this->page + 1); ?>');
            
<?php else: ?> 
  <?php if ($this->page == 1): ?>
             
          function doOnScrollLoadActivityLikes () {
            if( nextlikepage == 0){ window.onscroll = '';return;}
            if($.type($('#feed_viewmore').get(0)) != 'undefined'){ 
              if( $.type( $('#like_viewmore').get(0).offsetParent ) != 'undefined' ) {
                var elementPostionY=$('#like_viewmore').get(0).offsetTop;
              }else{
                var elementPostionY=$$('#like_viewmore').get(0).y; 
              }
              if(elementPostionY <= $(window).scrollTop()+($(window).height() -40)){ 
                $('#like_viewmore').css('display', 'block'); 
                $('#like_viewmore').html('<i class="icon-spinner icon-spin ui-icon"></i>'); 
                getLikeUsers();
              }
            }
          }
          function getLikeUsers () {
            $('#like_viewmore').css('display', 'block');
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
              url: sm4.core.baseUrl + 'advancedactivity/index/get-all-like-user',
              data: { 
                'format': 'html',
                'type': subjecttype,
                'id' : subjectid,
                'page' : '<?php echo ($this->page + 1); ?>'          
              },
              success:function( responseHTML, textStatus, xhr ) { 
                activeRequest = false;
                $('#like_viewmore').css('display', 'none');
                $(document).data('loaded', true);             
                $('#likemembers_ul').append(responseHTML);
                sm4.core.dloader.refreshPage();
                sm4.core.runonce.trigger();
              }
            });        
          }
          window.onscroll = doOnScrollLoadActivityLikes();
  <?php endif; ?>
<?php endif; ?>   
  
  </script>  
</div>

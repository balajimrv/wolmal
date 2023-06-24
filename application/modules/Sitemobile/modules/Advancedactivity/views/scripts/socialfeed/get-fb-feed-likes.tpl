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
  <?php if (count($this->fbLikes['data']) > 0): // COMMENTS -------  ?>   
   

    <?php if ($this->page == 1): ?>
      <div class="sm-ui-popup-top ui-header ui-bar-a">
        <?php if (!empty($this->action_id)): ?> 
           <?php if (!empty($this->comment_id)): ?>  
          <a data-iconpos="notext" data-icon="chevron-left" data-corners="true" data-shadow="true" class="ui-icon-left " onclick="$('#comment-activity-item-' + <?php echo $this->action_id ?>).css('display', 'block');$('#like-comment-item-' + <?php echo $this->action_id ?>).css('display', 'none');"><?php //echo $this->translate('back');?></a>
          <?php else : ?>
              <a data-iconpos="notext" data-icon="chevron-left" data-corners="true" data-shadow="true" class="ui-icon-left " onclick="$('#comment-activity-item-' + <?php echo $this->action_id ?>).css('display', 'block');$('#like-activity-item-' + <?php echo $this->action_id ?>).css('display', 'none');"><?php //echo $this->translate('back');?></a>
          <?php endif;?>
        <?php endif; ?>
        <a href="javascript:void(0);" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-icon-right close-feedsharepopup" onclick="$('.ui-page-active').removeClass('dnone');$('#feedsharepopup').remove();$(window).scrollTop(parentScrollTop)"></a>
        <h2 class="ui-title"><?php echo $this->translate('People who like this'); ?></h2>
      </div>

      <div class="sm-ui-popup-container sm-ui-popup-likes sm-content-list">
        <ul id="likemembers_ul" class="ui-member-list" data-role="listview" data-icon="none">
        <?php endif; ?>
        <?php foreach ($this->fbLikes['data'] as $like): ?>       
          <li>           
            <a href="https://facebook.com/<?php echo $like['id']?>" target="_blank">
              <img src="https://graph.facebook.com/<?php echo $like['id'];?>/picture" alt="" />
              <div class="ui-list-content">
                <h3><?php echo $like['name'] ?></h3>
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
              'onclick' => 'sm4.socialactivity.getLikeUsers(' . $this->action_id . ',' . ($this->page + 1) . ');'
          ))
          ?>
        </div>
      </div>	
    <?php endif; ?>
<?php endif; ?>
</div>
<div style="display:none;">
  <script type="text/javascript">
<?php if ($this->page && count($this->fbLikes['data']) < 15): ?>
         var nextlikepage = 0;
<?php else: ?>
         var nextlikepage = 1;
<?php endif; ?>
<?php //if (!empty($action)): ?>
            window.onscroll = sm4.socialactivity.doOnScrollLoadActivityLikes('<?php echo $this->action_id; ?>', true, '<?php echo ($this->page + 1); ?>');
            
<?php //else: ?> 
  <?php if ($this->page == 1): ?>

          //window.onscroll = doOnScrollLoadActivityLikes();
  <?php //endif; ?>
<?php endif; ?>   
  
  </script>  
</div>

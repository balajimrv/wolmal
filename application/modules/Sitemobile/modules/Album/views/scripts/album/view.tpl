<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if(!$this->autoContentLoad) : ?>
<div class="ui-page-content">
  <?php if ('' != trim($this->album->getDescription()) && !Engine_Api::_()->sitemobile()->isApp()): ?>
    <div class="sm-ui-cont-cont-des">
      <?php echo $this->album->getDescription() ?>
    </div>
  <?php endif ?>
  <ul class="thumbs thumbs_nocaptions" id="browsealbumphotos_ul">
    <?php endif;?>
    <?php foreach ($this->paginator as $photo): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span class="lazy" data-src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>"></span>
        </a>
        <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
           <?php if($photo->likes()->getLikeCount() > 0 || $photo->comment_count > 0) : ?>
          <span class="photo-stats" onclick='sm4.core.comments.comments_likes_popup("<?php echo $photo->getType();?>", <?php echo $photo->getIdentity();?>, "<?php echo $this->url(array('module' => 'core', 'controller' => 'photo-comment', 'action' => 'list'), 'default', 'true'); ?>")'>
            <?php if($photo->likes()->getLikeCount() > 0) : ?>
              <span class="f_small"><?php echo $photo->likes()->getLikeCount(); ?></span>
             <i class="ui-icon-thumbs-up-alt"></i>
            <?php endif;?>
            <?php if($photo->comment_count > 0) : ?>
                <span class="f_small"><?php echo $this->locale()->toNumber($photo->comment_count) ?></span>
                <i class="ui-icon-comment"></i>
            <?php endif;?>
          </span>
        <?php endif;?>
        <?php endif;?>
      </li>
    <?php endforeach; ?>
   <?php if(!$this->autoContentLoad) : ?>   
  </ul>
  <?php if ($this->paginator->count() > 0 && !Engine_Api::_()->sitemobile()->isApp()): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>
</div>
<?php endif;?>
<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
          
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' :{}, 'contentUrl' : '<?php echo $this->url(array('action' => 'view'));?>', 'activeRequest' : false, 'container' : 'browsealbumphotos_ul' };
              
          $('.layout_page_album_album_view').find('.layout_sitemobile_sitemobile_headingtitle').html('');    
          });
         
   <?php } ?>    
</script>
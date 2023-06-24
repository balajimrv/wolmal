<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9800 2012-10-17 01:16:09Z richard $
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
<select name="filter_type" id="filter_type" >
  <?php foreach($this->filterTypes as $key => $filter): ?>
  <option value="<?php echo $key;?>" <?php if($key == $this->activeTab):?> selected="selected" <?php endif;?>  >  <?php echo $this->translate($filter); ?></option>
  <?php  endforeach;?>
</select>
<?php endif;?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
    <?php if(!$this->autoContentLoad) : ?>
      <div class="ui-page-content" >
        <ul class="thumbs thumbs_nocaptions" id='browsephotos_ul'>
    <?php endif;?>
        <?php foreach ($this->paginator as $item): ?>
          <li>
            <a href="<?php echo $item->getHref(); ?>" class="thumbs_photo" data-linktype='photo-gallery'>
              <?php //echo $this->itemPhoto($album, 'thumb.icon'); ?>
              <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Album/externals/images/nophoto_album_thumb_normal.png'; $temp_url=$item->getPhotoUrl('thumb.main'); if(!empty($temp_url)): $url=$item->getPhotoUrl('thumb.main'); endif;?>
              <span class="lazy" data-src="<?php echo $url; ?>"> </span>
              
               <?php
                  $owner = $item->getOwner();
                  $parent = $item->getParent();
                  ?>
                             
            </a>
                 
                 
                 
                 <?php if($item->likes()->getLikeCount() > 0 || $item->comment_count > 0) : ?>
                    <span class="photo-stats" onclick='sm4.core.comments.comments_likes_popup("<?php echo $item->getType();?>", <?php echo $item->getIdentity();?>, "<?php echo $this->url(array('module' => 'core', 'controller' => 'photo-comment', 'action' => 'list'), 'default', 'true'); ?>")'>
                      <?php if($item->likes()->getLikeCount() > 0) : ?>
                        <span class="f_small"><?php echo $item->likes()->getLikeCount(); ?></span>
                       <i class="ui-icon-thumbs-up-alt"></i>
                      <?php endif;?>
                      <?php if($item->comment_count > 0) : ?>
                          <span class="f_small"><?php echo $this->locale()->toNumber($item->comment_count) ?></span>
                          <i class="ui-icon-comment"></i>
                      <?php endif;?>
                    </span>
                  <?php endif;?>            
          </li>
        <?php endforeach; ?>
    <?php if(!$this->autoContentLoad) : ?>
      </ul>
    </div>
    <?php endif ?>
  <?php else :?>

    <?php if(!$this->autoContentLoad) : ?>
      <div class="sm-content-list ui-listgrid-view">
        <ul data-role="listview" data-inset="false" data-icon="arrow-r" id='browsephotos_ul'>
    <?php endif;?>     
          <?php foreach ($this->paginator as $item):  ?>
            <li>
              <a href="<?php echo $item->getHref(); ?>" data-linktype='photo-gallery'>
                <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
                <h3><?php echo $this->string()->chunk($this->string()->truncate($item->getTitle(), 45), 10); ?></h3>
                
                <p><?php echo $this->translate('Posted By'); ?>
                  <strong><?php echo $item->getOwner()->getTitle(); ?></strong>
                </p>
              </a> 
            </li>
          <?php endforeach; ?>
      <?php if(!$this->autoContentLoad) : ?>     
        </ul>
      </div> 

      <?php if ($this->paginator->count() > 1): ?>
        <?php
        echo $this->paginationControl(
                $this->paginator, null, null, array(
            'pageAsQuery' => false,
            'query' => $this->searchParams
        ));
        ?>
      <?php endif; ?>
  <?php endif;?>
<?php endif;?>   
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded yet.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('%1$sClick here%2$s to add photos!', '<a href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

<script type="text/javascript">
<?php //if (Engine_Api::_()->sitemobile()->isApp()) { ?>
          
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : {'tabName' : '<?php echo $this->activeTab;?>'}, 'contentUrl' : sm4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>, 'activeRequest' : true, 'container' : 'browsephotos_ul' };  
          });
         
   <?php //} ?>    
</script>
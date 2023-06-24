<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
 <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
<?php if($this->autoContentLoad == 0) : ?>
  <div class="sm-content-list" id="blog_browseul">
    <ul data-role="listview" data-inset="false" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>
         <?php endif;?>
        <?php else : ?>
        <div class="sm-content-list">
    <ul data-role="listview" data-inset="false" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>
    <?php endif ; ?>
            <?php foreach ($this->paginator as $blog): ?>
        <li>
          <a href="<?php echo $blog->getHref(); ?>">  
            <?php if (Engine_Api::_()->sitemobile()->isApp()): ?> 
              <?php echo $this->itemPhoto($blog->getOwner(), 'thumb.icon') ?>
            <?php endif?>
            <h3><?php echo $blog->getTitle() ?></h3>
            <?php if (Engine_Api::_()->sitemobile()->isApp()): ?> 
              <p>
                <?php echo $this->translate('By'); ?>
                <strong><?php echo $blog->getOwner()->getTitle(); ?></strong>
                <span class="t_light"><?php echo $this->translate('on'); ?></span>
                <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
              </p>
            <?php else : ?>
               <p>
                <?php echo $this->translate('Posted by'); ?>
                <strong><?php echo $blog->getOwner()->getTitle(); ?></strong>
               </p>
               <p>
                <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
               </p>
            <?php endif?>
          </a> 
        </li>
      <?php endforeach; ?>
    </ul>
      
    <?php
    if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): 
      echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => true,
          'query' => $this->formValues,
              //'params' => $this->formValues,
      ));
    endif;
    ?>
  </div>	
<?php elseif ($this->category || $this->show == 2 || $this->search): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has written a blog entry with that criteria.'); ?>
      <?php if (TRUE): // @todo check if user is allowed to create a poll ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'blog_general') . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has written a blog entry yet.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'blog_general') . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
        
<script type="text/javascript">
        <?php if (Engine_Api::_()->sitemobile()->isApp()) :?>
        <?php $current_url = $this->url(array('action' => 'index')); ?>    
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->searchForm);?>, 'contentUrl' : '<?php echo $current_url; ?>', 'activeRequest' : false, 'container' : 'blog_browseul' };  
          });
         
   <?php endif; ?>   
</script>   
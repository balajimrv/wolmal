<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9800 2012-10-17 01:16:09Z richard $
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
    <?php if($this->autoContentLoad == 0) : ?>
  <div class="sm-content-list" id="blog_manageul">
    <ul data-role="listview" data-inset="false">
<?php endif; ?>     
      <?php foreach ($this->paginator as $blog): ?>
        <li <?php if (Engine_Api::_()->sitemobile()->isApp()): ?> data-icon="ellipsis-vertical" <?php else : ?> data-icon="cog" <?php endif?> data-inset="true">
          <a href="<?php echo $blog->getHref(); ?>">
            <?php if (Engine_Api::_()->sitemobile()->isApp()): ?> 
              <?php echo $this->itemPhoto($blog->getOwner(), 'thumb.icon') ?>
            <?php endif?>
            <h3><?php echo $blog->getTitle() ?></h3>
            <?php if (Engine_Api::_()->sitemobile()->isApp()): ?> 
              <p>
               <?php echo $this->translate('On') ?>
               <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
              </p>
            <?php else : ?>
              <p>
               <?php echo $this->translate('Posted by') ?>
               <strong><?php echo $blog->getOwner()->getTitle(); ?></strong>
              </p>
              <p>
               <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
              </p>
            <?php endif?>
          </a>
          <a href="#manage_<?php echo $blog->getGuid() ?>" data-rel="popup" data-transition="pop"></a>
          <div data-role="popup" id="manage_<?php echo $blog->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                <h3><?php echo $blog->getTitle() ?></h3>
              <?php endif ?>
              <?php
              echo $this->htmlLink(array(
                  'action' => 'edit',
                  'blog_id' => $blog->getIdentity(),
                  'route' => 'blog_specific',
                  'reset' => true,
                      ), $this->translate('Edit Entry'), array(
                  'class' => 'ui-btn-default ui-btn-action'
              ))
              ?>
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'blog', 'controller' => 'index', 'action' => 'delete', 'blog_id' => $blog->getIdentity()), $this->translate('Delete Entry'), array(
                  'class' => 'smoothbox ui-btn-default ui-btn-danger',
              ));
              ?>  
              <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                <a href="#" data-rel="back" class="ui-btn-default">
                  <?php echo $this->translate('Cancel'); ?>
                </a>
              <?php endif?>
            </div> 
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php
    if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): 
    echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    ));
    endif;
    ?>
  </div>
    <?php elseif ($this->search): ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('You do not have any blog entries that match your search criteria.'); ?>
    </span>
  </div>
    <?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You do not have any blog entries.'); ?>
  <?php if ($this->canCreate): ?>
    <?php echo $this->translate('Get started by %1$swriting%2$s a new entry.', '<a href="' . $this->url(array('action' => 'create'), 'blog_general') . '">', '</a>'); ?>
  <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

<script type="text/javascript">
        <?php if (Engine_Api::_()->sitemobile()->isApp()) :?>
          <?php $current_url = $this->url(array('action' => 'manage')); ?>
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->searchForm);?>, 'contentUrl' : '<?php echo $current_url; ?>', 'activeRequest' : false, 'container' : 'blog_manageul' };  
          });
         
   <?php endif; ?>   
</script> 
<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if($this->pageapp == 1) : ?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
<h2><?php echo $this->translate('Search') ?></h2>
<?php endif;?>
<div id="searchform" class="sm-ui-search">
  <?php echo $this->form->render($this) ?>
</div>
<?php if (empty($this->paginator)): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Please enter a search query.') ?>
    </span>
  </div>
<?php return;?>
<?php elseif ($this->paginator->getTotalItemCount() <= 0): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No results were found.') ?>
    </span>
  </div>
<?php return;?>
<?php else: ?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
  <div class="sm-ui-search-result">
    <?php echo $this->translate(array('%s result found', '%s results found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
<?php endif;?>
  <div class="sm-ui-search-result-list sm-content-list">                       
    <ul data-role="listview" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else: ?>data-icon="arrow-r"<?php endif;?> id="searchmembers_ul">
  <?php
   endif;
   endif;
  foreach ($this->paginator as $item):
    $item = $this->item($item->type, $item->id);
    if (!$item)
      continue;
    ?> <?php $target=$item->getType()==='core_link'?'target="_system"':""?>
        <li>
          <a href="<?php echo $item->getHref(); ?>" <?php echo $target;?>>
              <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
            <h3>
              <?php if ('' != $this->query): ?>
                <?php echo $this->highlightText($item->getTitle(), $this->query) ?>
              <?php else: ?>
                <?php echo $item->getTitle() ?>
    <?php endif; ?>
            </h3>
            <p>
              <?php if ('' != $this->query): ?>
                <?php echo $this->highlightText($item->getDescription(), $this->query); ?>
              <?php else: ?>
                <?php echo $item->getDescription(); ?>
    <?php endif; ?>
            </p>
          </a> 
        </li>
  <?php endforeach; ?>
   <?php if($this->pageapp == 1) : ?>       
    </ul>
  </div>
  <div>
    <?php //IF THE MODE IS MOBILE PLUGIN.
    if(!Engine_Api::_()->sitemobile()->isApp()) {
    echo $this->paginationControl($this->paginator, null, null, array(
        'query' => array(
            'query' => $this->query,
            'type' => $this->type,
        ),
    ));
    } else {  ?>
      
      <div class="search_viewmore" id="search_viewmore" style="display: none;">
          <div class="feeds_loading" id="feed_loading-sitefeed" >
        <i class="icon_loading"></i>
      </div>
      </div>
   <?php }
    ?>
  </div>
<?php endif; ?>


<script type="text/javascript"> 
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
    
    sm4.core.runonce.add(function() { 
     sm4.core.Module.core.search.currentPage = '<?php echo sprintf('%d', $this->page) ?>';
     sm4.core.Module.core.search.totalPages = '<?php echo sprintf('%d', $this->totalPages) ?>';
    });
<?php } ?>
</script> 

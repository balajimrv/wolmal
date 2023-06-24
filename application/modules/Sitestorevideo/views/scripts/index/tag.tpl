<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tag.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestorevideo/externals/styles/style_sitestorevideo.css')
?>
<div class="headline">
  <h2> <?php echo $this->translate('Stores'); ?> </h2>
  <div class="tabs">
    <?php
    // Render the menu
    echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
    ?>
  </div>
</div>

<div class="sitestore_viewstores_head">
	<?php echo $this->htmlLink(array('route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($this->sitestore->store_id)), $this->itemPhoto($this->sitestore, 'thumb.icon', '' , array('align'=>'left'))) ?>
	<h2>
	  <?php echo $this->sitestore->__toString() ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->htmlLink(array( 'route' => 'sitestore_entry_view', 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl($this->sitestore->store_id), 'tab' => $this->tab_selected_id), $this->translate('Videos')) ?>
	</h2>
</div>
<div class='layout_right'>
  <?php echo $this->form->render($this) ?>
  <?php if($this->canCreateVideo):?>
		<div class="quicklinks">
			<ul>
				<li>
				<?php echo $this->htmlLink(array('route' => 'sitestorevideo_create','store_id' => $this->sitestore->store_id,'tab' => $this->tab_selected_id), $this->translate('Post New Video'), array(
					'class' => 'buttonlink icon_video_new'
				)) ?>
			</ul>
		</div>
  <?php endif; ?>
</div>

<div class='layout_middle'>
  <?php if( $this->tag ): ?>
    <div class="sitestorestore_video_tag_options">
      <?php echo $this->translate('Videos using the tag') ?>
      #<?php echo $this->tag ?>
      <a href="<?php echo $this->url(array('tab' => $this->tab_selected_id,'store_id' => $this->sitestore->store_id ), 'sitestorevideo_tagcreate', true); ?>">(x)</a>
    </div>
  <?php endif; ?>

  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class="sitestorevideos_tags">
    <?php foreach( $this->paginator as $item ): ?>
      <li>
        
        <div class="sitestore_video_thumb_wrapper">
          <?php if( $item->duration ): ?>
          <span class="sitestore_video_length">
            <?php
              if( $item->duration > 360 ) $duration = gmdate("H:i:s", $item->duration); else $duration = gmdate("i:s", $item->duration);
              if( $duration[0] == '0' ) $duration = substr($duration,1);
              echo $duration;
            ?>
          </span>
          <?php endif ?>
          <?php
            if( $item->photo_id ) echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
            else echo '<img alt="" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestorevideo/externals/images/sitestorevideo.png">';
          ?>
        </div>
        <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'sitestorevideo_title')) ?>
        <div class="sitestorevideo_tag_icon">
					<?php if($item->featured == 1): ?>
						<?php echo $this->htmlImage($this->layout()->staticBaseUrl .'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon sitestorevideo_tag_featured', 'title' => $this->translate('Featured'))) ?>
					<?php endif;?>
          <?php if( $item->rating > 0 ): ?>
            <?php for( $x=1; $x<=$item->rating; $x++ ): ?>
              <span class="rating_star_generic rating_star"></span>
            <?php endfor; ?>
            <?php if( (round($item->rating) - $item->rating) > 0): ?>
              <span class="rating_star_generic rating_star_half"></span>
            <?php endif; ?>
          <?php endif; ?>
				</div>
        <div class="sitestorevideo_author clear">
          <?php echo $this->translate('By') ?>
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
        </div>
        <div class="sitestorevideo_stats">
          <span class="sitestorevideo_views" style="display:block;">
            <?php echo $this->translate(array('%s comment', '%s comments', $item->comments()->getCommentCount()),$this->locale()->toNumber($item->comments()->getCommentCount())) ?>, <?php echo $this->translate(array('%s like', '%s likes', $item->likes()->getLikeCount()),$this->locale()->toNumber($item->likes()->getLikeCount())) ?>
          </span>
          <span class="sitestorevideo_views" style="display:block;">
          	<?php echo $this->translate(array('%s view', '%s views', $item->view_count),$this->locale()->toNumber($item->view_count)) ?>
          </span>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>  
	<?php elseif($this->paginator->count() <= 0 ):?>	
		<div class="tip" id='sitestorennote_search'>
			<span>
				<?php echo $this->translate('No videos were found matching your search criteria.');?>
			</span>
		</div>
  <?php endif; ?>
	<?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues,
      'storeAsQuery' => true,
    )); ?>
</div>

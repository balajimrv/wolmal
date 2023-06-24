<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->paginator->getTotalItemCount()): ?>
  <form id='filter_form_store' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitestorealbum_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="store" name="store"  value=""/>
  </form>

  <div class="sm-content-list ui-listgrid-view">
    <ul data-role="listview" data-inset="false" data-icon="arrow-r">
      <?php foreach ($this->paginator as $albums): ?>
        <li id="thumbs-photo-<?php echo $albums->photo_id ?>">
          <a href="<?php echo $albums->getHref(array('store_id' => $albums->store_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug())); ?>">
            <p class="ui-li-aside"><?php echo $this->locale()->toNumber($albums->count()) ?></p>	
            <?php if(empty($albums->photo_id)):?>
            <?php echo $this->itemPhoto($albums, 'thumb.normal'); ?>	
            <?php else:?>
            <?php echo $this->itemPhoto($albums, 'thumb.profile'); ?>	
            <?php endif;?>
            <h3><?php echo $this->string()->chunk($this->string()->truncate($albums->getTitle(), 45), 10); ?></h3>
            <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $albums->store_id); ?>
            <p><?php echo $this->translate("in ") ?>
            <b><?php echo $sitestore_object->title ?></b></p>     
          </a>
        </li>		      
      <?php endforeach; ?>
    </ul>
  </div>

<?php if( $this->paginator->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no search results to display.'); ?>
    </span>
  </div>
<?php endif; ?>

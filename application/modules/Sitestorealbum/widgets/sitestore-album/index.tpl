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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php if($this->paginator->getTotalItemCount()):?>
  <form id='filter_form_store' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitestorealbum_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="store" name="store"  value=""/>
  </form>


	<ul class="thumbs clr sitestore_content_thumbs">
		<?php foreach ($this->paginator as $albums): ?>
      <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $albums->store_id, $layout);?>
			<li id="thumbs-photo-<?php echo $albums->photo_id ?>"> 
					<?php if($albums->photo_id != 0):?>
						<a class="thumbs_photo" href="<?php echo $albums->getHref(array( 'store_id' => $albums->store_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $tab_id)); ?>">
								<span style="background-image: url(<?php echo $albums->getPhotoUrl('thumb.normal'); ?>);"></span>
						</a>
          <?php else:?>
						<a class="thumbs_photo" href="<?php echo $albums->getHref(array( 'store_id' => $albums->store_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $tab_id)); ?>">
								<span><?php echo $this->itemPhoto($albums, 'thumb.normal'); ?></span>
						</a>
          <?php endif;?>
					<p class="thumbs_info">
						<span class="thumbs_title">
              <?php if($albums->featured):?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'fright', 'title' => $this->translate('Featured'))) ?>
              <?php endif;?>
							<?php if (($albums->price>0)): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'fright', 'title' => $this->translate('Sponsored'))) ?>
							<?php endif; ?>
							<a href="<?php echo $this->url(array( 'store_id' => $albums->store_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $tab_id), 'sitestore_albumphoto_general') ?>" title="<?php echo $albums->title;?>"><?php echo $albums->title;?></a>
						</span>
              <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $albums->store_id);?>
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($albums->store_id, $albums->owner_id, $albums->getSlug()),  $sitestore_object->title) ?> <br />

						<?php echo $this->translate(array('%s photo', '%s photos', $albums->count()),$this->locale()->toNumber($albums->count())) ?>
						-		        	
						<?php echo $this->translate(array('%s like', '%s likes', $albums->like_count), $this->locale()->toNumber($albums->like_count)) ?>  
            - 
            <?php echo $this->translate(array('%s view', '%s views', $albums->view_count), $this->locale()->toNumber($albums->view_count)) ?>       
					</p>
				</li>		      
			<?php endforeach; ?>
		</ul>
	<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitestorealbum"), array("orderby" => $this->orderby)); ?>

<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There are no search results to display.');?>
		</span>
	</div>
<?php endif;?>


<script type="text/javascript">
  var storeAction = function(store){
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_store')){
				form=$('filter_form_store');
			}
    form.elements['store'].value = store;
    
		form.submit();
  } 
</script>
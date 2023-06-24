<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(empty($this->is_ajax)) :?>
<?php $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl .
'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css');
    
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
    
	$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/core.js');
?>
<?php 
  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
  if (empty ($fbmodule) || empty($fbmodule->enabled) || $fbmodule->version <=  '4.2.3')
   $enable_facebookse = 0;
   
  else 
     $enable_facebookse = 1;
?>
<?php if ($this->can_edit):?>
	<script type="text/javascript">
    
    function SortablesInstance(){
var SortablesInstance;
    //en4.core.runonce.add(function() {
      $$('.thumbs_nocaptions > li').addClass('sortable');
      SortablesInstance = new Sortables($$('.thumbs_nocaptions'), {
        clone: true,
        constrain: true,
        //handle: 'span',
        onComplete: function(e) {
          var ids = [];
          $$('.thumbs_nocaptions > li').each(function(el) {
            ids.push(el.get('id').match(/\d+/)[0]);
          });
          //console.log(ids);

          // Send request
          var url = '<?php echo $this->url(array('action' => 'order')) ?>';
          var request = new Request.JSON({
            'url' : url,
            'data' : {
              format : 'json',
              order : ids
            }
          });
          request.send();
        }
      });
    //});
    }
  </script>
<?php endif ;?>  
<script type="text/javascript" >
	function editalbum(thisobj) {
		var Obj_Url = thisobj.href;
		Smoothbox.open(Obj_Url);
	}
</script>
<div class="sitestore_viewstores_head">
	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php $link =  $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Albums')) ?>
	  <?php echo $this->translate('%1$s  &raquo; ' .  $link . ' &raquo;  %2$s',
	    $this->sitestore->__toString(),
	    ( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>')
	  ); ?>
	</h2>
	<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) :?>
		<div class="seaotagsitestorealbumcheckinshowlocation" style="overflow:hidden;">
			<?php
				// Render LOCATION WIDGET
				echo $this->content()->renderWidget("sitetagcheckin.location-sitetagcheckin"); 
			?>
		</div>
	<?php endif;?>
</div>	
<?php endif;?>




<?php if(empty($this->is_ajax)) :?>
<div class="layout_middle">
  <div class="sitestore_album_options">
		<!--FACEBOOK LIKE BUTTON START HERE-->
		<?php  if ($enable_facebookse) { ?>
				<div class="mbot15">
					<script type="text/javascript">
						var fblike_moduletype = 'sitestore_album';
						var fblike_moduletype_id = '<?php echo $this->album->album_id ?>';
					</script>
				<?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
			</div>
		
		<?php } ?>  
    <?php
			$url = $this->url(array('action' => 'view','store_id' => $this->sitestore->store_id, 'album_id' => $this->album_id, 'slug' => $this->album->getSlug(), 'tab' => $this->tab_selected_id), 'sitestore_albumphoto_general', true);
			//Checking layout for user is enabled or not.
			$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);

			//Getting the tab id.
			$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $this->album->store_id, $layout);
    ?>
    <?php if(($this->level_id == '1')&& !$this->allowView):?>
    <div class="tip">
			<span>
				<?php echo $this->translate('You can not make this album as "Album of the Day" or "Featured Album" because its store privacy is not set to "Everyone" or "All Registered Members" and thus cannot be highlighted to users.');?>
			</span>
    </div>
    <?php endif;?>
		<!--  Start: Suggest to Friend link show work -->
		<?php if( !empty($this->albumSuggLink) ): ?>				
			<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->album->album_id, 'sugg_type' => 'store_album'), $this->translate('Suggest to Friends'), array(
					'class'=>'buttonlink  icon_store_friend_suggestion smoothbox')) ?>
		<?php endif; ?>					
		<!--  End: Suggest to Friend link show work -->
    <?php if($this->album_count > 1):?>
	  	<?php echo $this->htmlLink(array('route' => 'sitestore_albumphoto_general', 'action' => 'view-album','store_id' => $this->album->store_id, 'slug' => $this->album->getSlug(),'tab' => $this->tab_selected_id), $this->translate('View Albums'), array(
	      'class' => 'buttonlink sitestore_icon_photos_manage'
	    )) ?>
    <?php endif;?>
		<?php if ($this->upload_photo == 1):?>
	    <?php echo $this->htmlLink(array('route' => 'sitestore_photoalbumupload','album_id' => $this->album_id, 'store_id' => $this->sitestore->store_id, 'tab' => $this->tab_selected_id), $this->translate('Add More Photos'), array(
	      'class' => 'buttonlink sitestore_icon_photos_new'
	    )) ?>
		<?php endif;?>
    <?php if ($this->can_edit):?>
			<?php if ($this->total_images):?> 
				<?php echo $this->htmlLink(array('route' => 'sitestore_albumphoto_general', 'action' => 'edit-photos', 'album_id' => $this->album_id, 'store_id' => $this->sitestore->store_id, 'slug' => $this->album->getSlug(),'tab' => $this->tab_selected_id), $this->translate('Manage Photos'), array(
						'class' => 'buttonlink sitestore_icon_photos_manage'
					)) ?>
			<?php endif;?>
				<?php echo $this->htmlLink(array('route' => 'sitestore_albumphoto_general', 'action' => 'edit', 'album_id' => $this->album_id, 'store_id' => $this->sitestore->store_id, 'slug' => $this->album->getSlug(), 'tab' => $tab_id), $this->translate('Edit Album'), array(
					'class' => 'buttonlink sitestore_icon_photos_settings', 'onclick' => 'editalbum(this);return false'
				)) ?>	
			<?php if($this->default_value != 1):?>
					<?php echo $this->htmlLink(array('route' => 'sitestore_albumphoto_general', 'action' => 'delete','album_id' => $this->album_id, 'store_id' => $this->sitestore->store_id, 'slug' => $this->album->getSlug(), 'tab' => $this->tab_selected_id), $this->translate('Delete Album'), array(
						'class' => 'buttonlink sitestore_icon_photos_delete', 'onclick' => 'editalbum(this);return false'
					)) ?>
			<?php endif;?>
			<?php if($this->allowView ): ?>    
				<a href="javascript:void(0);" class="buttonlink seaocore_icon_featured" onclick='featured("<?php echo $this->album->album_id;?>");' ><span id="featured_sitestorealbum" <?php if($this->album->featured): ?> style="display:none;" <?php endif;?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitestorealbum" <?php if(!$this->album->featured): ?> style="display:none;" <?php endif;?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>
				<?php echo $this->htmlLink(array('route' => 'default','module'=> 'sitestore', 'controller'=>'album','action' => 'add-album-of-day', 'album_id' => $this->album->album_id, 'format' => 'smoothbox'), $this->translate('Make Album of the Day'), array(
				'class' => 'buttonlink smoothbox sitestore_icon_photos_manage'
			)) ?>
			<?php endif;?>
		<?php endif;?>
  </div>
<?php endif;?>

  <?php if(empty($this->is_ajax)) :?>
    <?php if(!empty($this->total_images)):?>
		<div class="sitestore_album_box" id="sitestorealbum_content">
		  <ul class="thumbs thumbs_nocaptions">

<?php endif;?>
<?php endif;?>
		    <?php foreach( $this->photos as $photo ): ?> 
		      <li id="thumbs-photo-<?php echo $photo->photo_id ?>">	                   
						<a href="<?php echo $photo->getHref(); ?>"  <?php if(SEA_SITESTOREALBUM_LIGHTBOX) :?> onclick ='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");return false;' <?php endif;?> class="thumbs_photo">               
							<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
						</a>
		      </li>
		    <?php endforeach;?>
	  <?php if (empty($this->is_ajax)) : ?>
	    <?php
	     if(empty($this->total_images)):?>
	      <li style="width:80% !important;margin:0px;">
	        <div class="tip">
	          <span>
	            <?php echo $this->translate('There are no photos in this store album.')?>
	          </span>
	        </div>
	      </li>
	    <?php endif; ?>
    <?php endif; ?>
<?php if(!empty($this->total_images)):?>
<?php if(empty($this->is_ajax)) :?>
		  </ul>
		<div class="sitealbum-album-more" id="view_more" onclick="viewMorePhoto()">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
				'id' => 'feed_viewmore_link',
				'class' => 'buttonlink icon_viewmore'
			)) ?>
	  </div>
		<div class="sitealbum-album-more" id="loding_image" style="display: none;">
		  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
		  <?php echo $this->translate("Loading ...") ?>
		</div>
	</div>
  <?php endif; ?>
  <?php endif; ?>
 <?php if(!empty($this->total_images)):?>
<?php if(empty($this->is_ajax)) :?>
	</div> 

  <?php endif; ?>
  <?php endif; ?>

  

<?php if(empty($this->is_ajax)) :?>
	  <?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
    ?>
<?php endif;?>

<?php if(empty($this->is_ajax)) :?>
</div>
<?php endif;?>
<script type="text/javascript">
    function getNextStore(){
      return <?php echo sprintf('%d', $this->currentStoreNumbers + 1) ?>
    }
    en4.core.runonce.add(function() {
    hideViewMoreLink();
    <?php if( $this->can_edit ): ?>
    SortablesInstance();
    <?php endif; ?>
  });
  function viewMorePhoto()
  {
    $('view_more').style.display ='none';
    $('loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({

      'url' : en4.core.baseUrl + 'widget/index/mod/sitestorealbum/name/album-content',
      'data' : {
        format : 'html',
        isajax : 1,
        itemCountPerStore : '20',
        //margin_photo : '<?php echo $this->marginPhoto ?>',
        stores: getNextStore(),
        'store_id': '<?php echo $this->sitestore->store_id;?>',
        'album_id': '<?php echo $this->album_id;?>',
        'slug': '<?php echo $this->album->getSlug();?>',
        'tab': '<?php echo  $this->tab_selected_id;?>',
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
        Elements.from(responseHTML).inject($('sitestorealbum_content').getElement('.thumbs_nocaptions'));
        $('loding_image').style.display ='none';
        <?php if( $this->can_edit ): ?>
            SortablesInstance();
        <?php endif; ?>
      }
    }));

    return false;

  }  
    function hideViewMoreLink(){
        $('view_more').style.display = '<?php echo ( $this->maxstore == $this->currentStoreNumbers || $this->total_images == 0 ? 'none' : '' ) ?>';
    }
</script>
<script type="text/javascript">
  function featured(album_id)
  {
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'sitestore/album/featured',
      'data' : {
        format : 'html',
        'album_id' : album_id
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {       
       if($('featured_sitestorealbum').style.display=='none'){
        $('featured_sitestorealbum').style.display="";
        $('un_featured_sitestorealbum').style.display="none";
       }else{
          $('un_featured_sitestorealbum').style.display="";
        $('featured_sitestorealbum').style.display="none";
       }
      }
    }));

    return false;

  }
</script>
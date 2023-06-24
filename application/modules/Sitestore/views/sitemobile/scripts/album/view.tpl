<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css');
	$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/core.js');
?>

<?php if ($this->can_edit):?>
	<script type="text/javascript">
    var SortablesInstance;
    sm4.core.runonce.add(function() {
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
    });
  </script>
<?php endif ;?>  

<script type="text/javascript" >
//	function editalbum(thisobj) {
//		var Obj_Url = thisobj.href;
//		Smoothbox.open(Obj_Url);
//	}
</script>

<a id="sitestore_profile_photo_anchor" style="position:absolute;"></a>
<script type="text/javascript">
  var paginateStorePhotos = function(stores, url) {
  	$('album_backgroundimage').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loader.gif" /></center>';
    var url = url;
    sm4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'stores' : stores
      },
     		onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          $('sitestore_profile_photo_anchor').getParent().innerHTML=responseHTML;
          if($('white_content_default')){  
           $('white_content_default').addEvent('click', function(event) {
            event.stopPropagation();
            });
          }
        }

    }), {
     // 'element' : $('sitestore_profile_photo_anchor').getParent()
    });
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
</div>	
<!--RIGHT AD START HERE-->
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumview', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)):?>
	<div class="layout_right" id="communityad_albumview">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumview', 3),"loaded_by_ajax"=>1,'widgetId'=>'store_albumview'))?>
	</div>
<?php endif;?>
<!--RIGHT AD END HERE-->
<div class="layout_middle">


  <div class="sitestore_album_options">
  
  <!--FACEBOOK LIKE BUTTON START HERE-->
  
   <?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
        if (!empty ($fbmodule)) :
          $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
          if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
            $fbversion = $fbmodule->version; 
            if (!empty($fbversion) && ($fbversion >= '4.1.5')) { ?>
               <div class="mbot15">
                <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
              </div>
            
            <?php } ?>
          <?php endif; ?>
   <?php endif; ?>
   
  <?php
	  $url = $this->url(array('action' => 'view','store_id' => $this->sitestore->store_id, 'album_id' => $this->album_id, 'slug' => $this->album->getSlug(), 'tab' => $this->tab_selected_id), 'sitestore_albumphoto_general', true);
    //Checking layout for user is enabled or not.
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);

    //Getting the tab id.
    $tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $this->album->store_id, $layout);
    ?>
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
		<?php endif;?>
  </div>
	<?php if($this->total_images > $this->photos_per_store) :?>
 		<div class="sitestore_profile_album_paging">
		  <?php $next_store = $this->currentStoreNumbers+1; ?>
		  <?php $previous_store = $this->currentStoreNumbers-1; ?>
		  <?php $maxstores = $this->maxstore;?>
		  <?php if($this->maxstore >= $next_store) :?>
			  <div id="user_group_members_next">
			        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Last') , array(
			          'onclick' => "paginateStorePhotos('$maxstores', '$url')",
			        )); ?>
				</div>				
				<div id="user_group_members_next">
				  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
				    'onclick' => "paginateStorePhotos('$next_store', '$url')",
				  )); ?>
				</div>
			<?php endif; ?>
			<div class="paging_count">
	 			<?php foreach($this->storearray as $values) :
					 if($values['link'] == 1) { 	
					 	 echo $values['store'] ?>
					 <?php  }
					 else { ?>
					   <a href='javascript:void(0);' onclick="paginateStorePhotos('<?php echo $values['store'] ?>', '<?php echo $url ?>')"><?php echo $values['store'] ?></a>
					 	  <?php
					 }
	  		endforeach; ?> 
			</div>			
 			<?php if($this->pstart != $this->currentStoreNumbers):?>
				<?php if($this->currentStoreNumbers != $this->pstart+1): ?>
					<div id="user_group_members_previous">
			    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Prev') , array(
			          'onclick' => "paginateStorePhotos('$previous_store', '$url')",
			        )); ?>
				 	</div> 
				<?php endif; ?>
  	    <div id="user_group_members_previous">
			    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('First') , array(
			      'onclick' => "paginateStorePhotos('$this->pstart', '$url')",
			    )); ?>
				</div> 
		  <?php endif; ?>
		</div>  
	<?php endif; ?>
	<?php if($this->total_images) :?>
		<div class="sitestore_album_box" id="album_backgroundimage">
		  <ul class="thumbs thumbs_nocaptions">
		    <?php foreach( $this->photos as $photo ): ?>
		      <li id="thumbs-photo-<?php echo $photo->photo_id ?>">	         
            <?php //if(!$this->showLightBox):?>
<!--              <a class="thumbs_photo" href="<?php //echo $photo->getHref(); ?>" title="<?php //echo $photo->title;?>">
                <span style="background-image: url(<?php //echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
              </a>-->
              <?php //else:?>            
                <a href="<?php echo $photo->getHref(); ?>"  <?php if(SEA_SITESTOREALBUM_LIGHTBOX) :?> onclick ='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");return false;' <?php endif;?> class="thumbs_photo">               
                  <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
                </a>
            <?php //endif; ?>
		      </li>
		    <?php endforeach;?>
		  </ul>
		</div> 
  <?php endif; ?>

	<?php echo $this->action("list", "comment", "core", array("type"=>"sitestore_album", "id"=> $this->album->album_id)); ?>
</div>
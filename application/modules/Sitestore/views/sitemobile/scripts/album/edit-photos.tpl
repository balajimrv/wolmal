<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css');
?>
<?php $i = 0; ?>
<script type="text/javascript">
  var paginateStorePhotos = function(stores, url) {  	

   $('subcategory_backgroundimage').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loader.gif" /></center>';

  	var is_ajax = 1;
    var url = url;
   url = url + '/stores/' + stores;
   if (history.pushState)
    history.pushState( {}, document.title, url );
   else{
    window.location.hash = photoUrl;
   }
     sm4.core.request.send(new Request.HTML({
      'url' : url,
      'method' : 'get',
         'data' : {
        'format' : 'html', 
        'is_ajax' :  is_ajax,
         'stores' : stores       
      }  
    }), {
      'element' : $('sitestore_profile_photo_anchors').getParent()
    });
  }
</script>

<a id="sitestore_profile_photo_anchors" style="position:absolute;"></a>



<div class="sitestore_viewstores_head">
	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>	
	  <?php echo $this->sitestore->__toString() ?>	
	  <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Albums')) ?>
    <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->album->getTitle();?>
  </h2>  
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumeditphoto', 5) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)):?>
	<div class="layout_right" id="communityad_editphotos">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumeditphoto', 3),"loaded_by_ajax"=>1,'widgetId'=>'store_editphotos'))?>
	</div>
<?php endif;?>
<div class="layout_middle">
<h3><?php echo $this->translate('Edit Photos');?></h3>
<p><?php echo $this->translate('Here, you can edit photos of this album.');?></p>
<br />
<?php $url = $this->url(array('action' => 'edit-photos','store_id' => $this->store_id, 'album_id' => $this->album_id, 'slug' => $this->album->getSlug(), 'tab' => $this->tab_selected_id), 'sitestore_albumphoto_general', true);?>
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
				   <a href='javascript:void(0);' onclick="paginateStorePhotos('<?php echo $values['store'] ?>','<?php echo $url ?>')"><?php echo $values['store'] ?></a>
				 	  <?php
				 }
			 	endforeach; ?>
		 	</div> 	
	 		<?php if($this->pstart != $this->currentStoreNumbers):?>
				<?php if($this->currentStoreNumbers != $this->pstart+1): ?>
					<div id="user_group_members_previous">
			    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous') , array(
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
	<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" id="subcategory_backgroundimage" class="sitestore_album_box">
	  <?php echo $this->form->album_id; ?>
	  <ul class='sitestores_editphotos'>
	    <?php foreach( $this->photos as $photo ): ?>
	      <li>
	        <div class="sitestores_editphotos_photo">	      
	          <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
	        </div>
	        <div class="sitestores_editphotos_info">
	          <?php 
	            $key = $photo->getGuid();
	            echo $this->form->getSubForm($key)->render($this);	            
	          ?>
				    <div class="albums_editphotos_cover">
				    	<input type="radio" name="cover" value="<?php echo $photo->file_id ?>" 
	          <?php if(empty($this->album->photo_id) && $i==0): $i = 1;?>	          
	            checked="checked"
	          <?php else: ?>
	          <?php if( $this->album->photo_id == $photo->file_id ): ?> 
	             checked="checked"
	          <?php endif;?>
	           <?php endif;?>
	           />
				    </div>
				    <div class="albums_editphotos_label">
				    	<label><?php echo $this->translate('Album Cover');?></label>
				    </div>

            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')): ?>
							<div class="albums_editphotos_cover">
								<input type="radio" name="store_cover" value="<?php echo $photo->file_id ?>" 
							<?php if(empty($this->sitestore->store_cover) && $i==0): $i = 1;?>
								checked="checked"
							<?php else: ?>
							<?php if( $this->sitestore->store_cover == $photo->file_id ): ?> 
								checked="checked"
							<?php endif;?>
							<?php endif;?>
							/>
							</div>
							<div class="albums_editphotos_label">
								<label><?php echo $this->translate('Make Store Cover');?></label>
							</div>
				    <?php endif;?>
				    
	        </div>
	      </li>
	    <?php endforeach; ?>
	  </ul><br />
	  <div class="form-wrapper" >
	  	<div class="form-element">
			  <button type="submit" id="submit" name="submit"><?php echo $this->translate('Save Changes');?></button> <?php echo $this->translate(' or ');?><a onclick="javascript:history.go(-1);return false;" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('cancel');?></a>
	    </div>
	 </div>
	  <br />
	</form>
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
						<a href='javascript:void(0);' onclick="paginateStorePhotos('<?php echo $values['store'] ?>', '<?php echo $url?>')"><?php echo $values['store'] ?></a>
					<?php
					}
	  		endforeach; ?>
	  	</div>	
	 		<?php if($this->pstart != $this->currentStoreNumbers):?>
				<?php if($this->currentStoreNumbers != $this->pstart+1): ?>
					<div id="user_group_members_previous">
			    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous') , array(
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
</div>
	<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _recently_popular_random_store.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $enableBouce=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.sponsored', 1);
?>
<?php  $latitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.latitude', 0); ?>
<?php  $longitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.longitude', 0); ?>
<?php  $defaultZoom=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.zoom', 1); ?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
  <script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
<script type="text/javascript">
  var sitestores_likes = function(resource_id, resource_type) {
		var content_type = 'sitestore';
    //var error_msg = '<?php //echo $this->result['0']['like_id']; ?>';

		// SENDING REQUEST TO AJAX
		var request = createLikestore(resource_id, resource_type,content_type);

		// RESPONCE FROM AJAX
		request.addEvent('complete', function(responseJSON) {
			if (responseJSON.error_mess == 0) {
				$(resource_id).style.display = 'block';
				if(responseJSON.like_id )
				{
					$('backgroundcolor_'+ resource_id).className ="sitestore_browse_thumb sitestore_browse_liked";
					$('sitestore_like_'+ resource_id).value = responseJSON.like_id;
					$('sitestore_most_likes_'+ resource_id).style.display = 'none';
					$('sitestore_unlikes_'+ resource_id).style.display = 'block';
					$('show_like_button_child_'+ resource_id).style.display='none';
				}
				else
				{  $('backgroundcolor_'+ resource_id).className ="sitestore_browse_thumb";
					$('sitestore_like_'+ resource_id).value = 0;
					$('sitestore_most_likes_'+ resource_id).style.display = 'block';
					$('sitestore_unlikes_'+ resource_id).style.display = 'none';
					$('show_like_button_child_'+ resource_id).style.display='none';
				}

			}
			else {
				en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
				return;
			}
		});
	}
	// FUNCTION FOR CREATING A FEEDBACK
	var createLikestore = function( resource_id, resource_type, content_type )
	{
		if($('sitestore_most_likes_'+ resource_id).style.display == 'block')
			$('sitestore_most_likes_'+ resource_id).style.display='none';


		if($('sitestore_unlikes_'+ resource_id).style.display == 'block')
			$('sitestore_unlikes_'+ resource_id).style.display='none';
			$(resource_id).style.display='none';
			$('show_like_button_child_'+ resource_id).style.display='block';

		if (content_type == 'sitestore') {
			var like_id = $(content_type + '_like_'+ resource_id).value
		}
	//	var url = '<?php echo $this->url(array('action' => 'global-likes' ), 'sitestore_like', true);?>';
		var request = new Request.JSON({
			url : '<?php echo $this->url(array('action' => 'global-likes' ), 'sitestore_like', true);?>',
			data : {
				format : 'json',
				'resource_id' : resource_id,
				'resource_type' : resource_type,
				'like_id' : like_id
			}
		});
		request.send();
		return request;
	}
</script>

<?php 
       $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$MODULE_NAME = 'sitestore';
	$RESOURCE_TYPE = 'sitestore_store';
 ?>
<?php if( $this->list_view): ?>
<div id="rgrid_view_store"  <?php if($this->defaultView == 0): ?> style="display: block;" <?php else: ?> style="display: none;" <?php endif; ?>>
 <?php $sitestore_entry = Zend_Registry::isRegistered('sitestore_entry') ? Zend_Registry::get('sitestore_entry') : null; ?>
	<?php if (count($this->sitestoresitestore)): ?>
		<?php $counter='1';
				$limit = $this->active_tab_list;
		?>
		<ul class="seaocore_browse_list">
			<?php foreach ($this->sitestoresitestore as $sitestore): ?>
				<?php if($counter > $limit):
					break;
					endif;
					$counter++;
				?>
				<li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?><?php if($sitestore->featured):?> class="lists_highlight"<?php endif;?><?php endif;?>>
						<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
							<?php if($sitestore->featured):?>
								<i title="<?php echo $this->translate('Featured')?>" class="seaocore_list_featured_label"><?php echo $this->translate('Featured') ?></i>
							<?php endif;?>
						<?php endif;?>
					<div class='seaocore_browse_list_photo'>
						<?php if(!empty($sitestore_entry)) { echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id,$sitestore->getSlug()), $this->itemPhoto($sitestore, 'thumb.normal', '', array('align'=>'left'))); }else { exit(); } ?>
							<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
								<?php if (!empty($sitestore->sponsored)): ?>
									<?php //$sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1);
									//if (!empty($sponsored)) { ?>
										<div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
											<?php echo $this->translate('SPONSORED'); ?>                 
										</div>
									<?php //} ?>
								<?php endif; ?>
							<?php endif; ?>
					</div>
					<div class='seaocore_browse_list_info'>            
						<div class='seaocore_browse_list_info_title'>
              <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.fs.markers', 1)):?>
                <span>
                  <?php if ($sitestore->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                  <?php if ($sitestore->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                </span>
              <?php endif;?>
							<div class="seaocore_title">
								<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), Engine_Api::_()->sitestore()->truncation($sitestore->getTitle()), array('title' => $sitestore->getTitle())) ?>
							</div>
						</div>
						
						<?php if(in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
							<?php if (($sitestore->rating > 0)): ?>

								<?php 
									$currentRatingValue = $sitestore->rating;
									$difference = $currentRatingValue- (int)$currentRatingValue;
									if($difference < .5) {
										$finalRatingValue = (int)$currentRatingValue;
									}
									else {
										$finalRatingValue = (int)$currentRatingValue + .5;
									}	
								?>

								<span class="clr" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
									<?php for ($x = 1; $x <= $sitestore->rating; $x++): ?>
										<span class="rating_star_generic rating_star" ></span>
									<?php endfor; ?>
									<?php if ((round($sitestore->rating) - $sitestore->rating) > 0): ?>
										<span class="rating_star_generic rating_star_half" ></span>
									<?php endif; ?>
								</span>
							<?php endif; ?>
						<?php endif; ?>

						<div class='seaocore_browse_list_info_date'>
							<?php echo $this->timestamp(strtotime($sitestore->creation_date)) ?>
							<?php if($postedBy):?>
							 - <?php echo $this->translate('posted by'); ?>
								<?php echo $this->htmlLink($sitestore->getOwner()->getHref(), $sitestore->getOwner()->getTitle()) ?>
							<?php endif; ?>
						</div>
						
						<?php if (!empty($this->statistics)) : ?>
							<div class='seaocore_browse_list_info_date'>
							<?php 
                $statistics = '';
                
                if(in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)).', ';
                }
                if(in_array('followCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s follower', '%s followers', $sitestore->follow_count), $this->locale()->toNumber($sitestore->follow_count)).', ';
                }

                if(in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
                $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
									if ($sitestore->member_title && $memberTitle) {
										if ($sitestore->member_count == 1) : 
											echo $sitestore->member_count . ' member'.', ';
										else:  
											echo $sitestore->member_count . ' ' .  $sitestore->member_title.', ';
										endif; 
									} else {
										$statistics .= $this->translate(array('%s member', '%s members', $sitestore->member_count), $this->locale()->toNumber($sitestore->member_count)).', ';
									}
                }
                
                if(in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                  $statistics .= $this->translate(array('%s review', '%s reviews', $sitestore->review_count), $this->locale()->toNumber($sitestore->review_count)).', ';
                }
                
                if(in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)).', ';
                }
                
                if(in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count)).', ';
                }
                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
              ?>
              <?php echo $statistics; ?>
							</div>
						<?php endif; ?>
           
           
					<?php if(!empty($sitestore->price) && $this->enablePrice): ?>
              <div class='seaocore_browse_list_info_date'>
                <?php
                     echo $this->translate("Price: "); echo Engine_Api::_()->sitestore()->getPriceWithCurrency($sitestore->price); 
                 ?>
              </div>
           <?php  endif;?>						
          <?php
            if(!empty($sitestore->location) && $this->enableLocation ):
              echo "<div class='seaocore_browse_list_info_date'>";
              echo $this->translate("Location: "); echo $this->translate($sitestore->location);
              $location_id = Engine_Api::_()->getDbTable('locations', 'sitestore')->getLocationId($sitestore->store_id, $sitestore->location); ?>&nbsp; - <b> <?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $sitestore->store_id, 'resouce_type' => 'sitestore_store', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?> </b>
						  <?php echo "</div>";
          endif;
          ?>
						 
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php  if( $this->grid_view):?>
<div id="rimage_view_store" <?php if($this->defaultView == 1): ?> style="display: block;" <?php else: ?> style="display: none;" <?php endif; ?>>
	<?php if (count($this->sitestoresitestore)): ?>
	
	  <?php $counter=1;
	  			$total_sitestore = count($this->sitestoresitestore);
					$limit =  $this->active_tab_image;
		?> 
		<div class="sitestore_img_view">
			<div class="sitestore_img_view_sitestore">
				<?php foreach ($this->sitestoresitestore as $sitestore): ?>
          <?php // start like Work on the browse store ?>
        	<?php if($counter > $limit):
					break;
					endif;
					$counter++;
				?>
			    <?php
						$likeStore=false;
						if(!empty($viewer_id)):
						$likeStore=Engine_Api::_()->sitestore()->hasStoreLike($sitestore->store_id,$viewer_id);
						endif;
					?>
    
          <div class="sitestore_browse_thumb <?php if($likeStore): ?> sitestore_browse_liked <?php endif; ?>" id = "backgroundcolor_<?php echo $sitestore->store_id; ?>" style="width:<?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;" >

          <div class="sitestore_browse_thumb_list" <?php if(!empty($viewer_id) && !empty($this->showlikebutton)) : ?> onmouseOver=" $('like_<?php echo $sitestore->getIdentity(); ?>').style.display='block'; if($('<?php echo $sitestore->getIdentity(); ?>').style.display=='none')$('<?php echo $sitestore->getIdentity(); ?>').style.display='block';"  onmouseout="$('like_<?php echo $sitestore->getIdentity(); ?>').style.display='none'; $('<?php echo $sitestore->getIdentity(); ?>').style.display='none';" <?php endif; ?> >
           <?php // end like Work on the browse store ?>

            <?php if (!empty($this->showlikebutton)) :?>
							<a href="javascript:void(0);">
						<?php else :?>
							<a href="<?php echo Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id,$sitestore->getSlug()) ?>">
						<?php endif; ?>
								<?php                         
                if ($this->photoWidth >= 720):
                  $photo_type = 'thumb.main';
                elseif ($this->photoWidth >= 140):
                  $photo_type = 'thumb.normal';
                elseif ($this->photoWidth >= 100):
                  $photo_type = 'thumb.icon';
                else:
                  $photo_type = 'thumb.profile';
                endif;               
                $url= $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/nophoto_store_thumb_profile.png'; $temp_url=$sitestore->getPhotoUrl($photo_type); if(!empty($temp_url)): $url=$sitestore->getPhotoUrl($photo_type); endif;
                ?>
								<span style="background-image: url(<?php echo $url; ?>);"> </span>
								<?php if (empty($this->showlikebutton) && !empty($this->titlePosition)) :?>
									<div class="sitestore_browse_title">
										<p title="<?php echo $sitestore->getTitle()?>"><?php echo Engine_Api::_()->sitestore()->truncation($sitestore->getTitle(),$this->turncation); ?></p>
									</div>
						    <?php endif; ?>
							</a>
				
						<?php if(!empty($viewer_id)) : ?>
            	<div id="like_<?php echo $sitestore->getIdentity() ?>" style="display:none;">
							<?php
								$RESOURCE_ID = $sitestore->getIdentity(); ?>
								<div class="" style="display:none;" id="<?php echo $RESOURCE_ID; ?>">
								<?php
									// Check that for this 'resurce type' & 'resource id' user liked or not.
									$check_availability = Engine_Api::_()->$MODULE_NAME()->checkAvailability( $RESOURCE_TYPE, $RESOURCE_ID );
									if( !empty($check_availability) )
									{
										$label = 'Unlike this';
										$unlike_show = "display:block;";
										$like_show = "display:none;";
										$like_id = $check_availability[0]['like_id'];
									}
									else
									{
										$label = 'Like this';
										$unlike_show = "display:none;";
										$like_show = "display:block;";
										$like_id = 0;
									}
							?>
							<div class="sitestore_browse_thumb_hover_color">
							</div>
							<div class="seaocore_like_button sitestore_browse_thumb_hover_unlike_button" id="sitestore_unlikes_<?php echo $RESOURCE_ID;?>" style='<?php echo $unlike_show;?>' >
								<a href = "javascript:void(0);" onclick = "sitestores_likes('<?php echo $RESOURCE_ID; ?>', 'sitestore_store');">
									<i class="seaocore_like_thumbdown_icon"></i>
									<span><?php echo $this->translate('Unlike') ?></span>
								</a>
							</div>
							<div class="seaocore_like_button sitestore_browse_thumb_hover_like_button" id="sitestore_most_likes_<?php echo $RESOURCE_ID;?>" style='<?php echo $like_show;?>'>
								<a href = "javascript:void(0);" onclick = "sitestores_likes('<?php echo $RESOURCE_ID; ?>', 'sitestore_store');">
									<i class="seaocore_like_thumbup_icon"></i>
									<span><?php echo $this->translate('Like') ?></span>
								</a>
							</div>
							<input type ="hidden" id = "sitestore_like_<?php echo $RESOURCE_ID;?>" value = '<?php echo $like_id; ?>' />
					</div>
         </div>
					<div id="show_like_button_child_<?php echo $RESOURCE_ID;?>" style="display:none;" >
						<div class="sitestore_browse_thumb_hover_color"></div>
						<div class="sitestore_browse_thumb_hover_loader">
							<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loader.gif" class="mtop5" />
						</div>
					</div>
					<?php endif; ?>
					<?php // end like Work on the browse store ?>
          
          <?php if ($sitestore->featured == 1 && !empty($this->showfeaturedLable)): ?>
          	<span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured')?>"><?php echo $this->translate('Featured')?></span>
          <?php endif; ?>
          <?php if( !empty($this->titlePosition) ) : ?>
          <div class="sitestore_browse_title">
            <?php  echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id,$sitestore->getSlug()), Engine_Api::_()->sitestore()->truncation($sitestore->getTitle(),$this->turncation)); ?>
            </div>
            <?php endif; ?>
		      </div>
        
					<?php if (!empty($sitestore->sponsored) && !empty($this->showsponsoredLable)): ?>
						<?php //$sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.image', 1);
						//if (!empty($sponsored)) { ?>
							<div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#fc0505'); ?>;'>
								<?php echo $this->translate('SPONSORED'); ?>                 
							</div>
						<?php //} ?>
					<?php endif; ?>

          <div class="sitestore_browse_thumb_info">
            <?php if( empty($this->titlePosition) ) : ?>
              <div class="seaocore_title bold">
                <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), Engine_Api::_()->sitestore()->truncation($sitestore->getTitle(), 21), array('title' => $sitestore->getTitle())) ?>
              </div>
            <?php endif; ?>
          
            <?php if(in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) : ?>
							<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
							if ($sitestore->member_title && $memberTitle) : ?>
								<div class="member_count">
									<?php if ($sitestore->member_count == 1) : ?>
										<?php echo $sitestore->member_count . ' ' . ucfirst('member'); ?> 
									<?php  else: ?>
										<?php echo $sitestore->member_count . ' ' .  ucfirst($sitestore->member_title); ?>
									<?php endif; ?>
								</div>
							<?php else : ?>
								<div class="member_count">
									<?php echo $this->translate(array('%s '. ucfirst('member'), '%s '. ucfirst('members'), $sitestore->member_count), $this->locale()->toNumber($sitestore->member_count)) ?>
								</div> 
							<?php endif; ?>
						<?php endif; ?>
            <?php if (!empty($this->statistics)) : ?>         
							<div class='sitestore_browse_thumb_stats seaocore_txt_light'>
							<?php 
									$statistics = '';
									if(in_array('likeCount', $this->statistics)) {
										$statistics .= $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)).', ';
									}

									if(in_array('followCount', $this->statistics)) {
										$statistics .= $this->translate(array('%s follower', '%s followers', $sitestore->follow_count), $this->locale()->toNumber($sitestore->follow_count)).', ';
									}

									if(in_array('commentCount', $this->statistics)) {
										$statistics .= $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)).', ';
									}
									if(in_array('viewCount', $this->statistics)) {
										$statistics .= $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count)).', ';
									}
									$statistics = trim($statistics);
									$statistics = rtrim($statistics, ',');
								?>
								<?php echo $statistics; ?>
							</div>
            <?php endif; ?>
            <?php if(!empty($this->statistics) && is_array($this->statistics) && in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
            <div class='sitestore_browse_thumb_stats seaocore_txt_light'>
            <?php if ($sitestore->review_count) : ?>
            <?php echo $this->translate(array('%s review', '%s reviews', $sitestore->review_count), $this->locale()->toNumber($sitestore->review_count)); ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php endif ; ?>
							<?php if (($sitestore->rating > 0)): ?>

								<?php 
									$currentRatingValue = $sitestore->rating;
									$difference = $currentRatingValue- (int)$currentRatingValue;
									if($difference < .5) {
										$finalRatingValue = (int)$currentRatingValue;
									}
									else {
										$finalRatingValue = (int)$currentRatingValue + .5;
									}	
								?>

								<span class="clr" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
									<?php for ($x = 1; $x <= $sitestore->rating; $x++): ?>
										<span class="rating_star_generic rating_star" ></span>
									<?php endfor; ?>
									<?php if ((round($sitestore->rating) - $sitestore->rating) > 0): ?>
										<span class="rating_star_generic rating_star_half" ></span>
									<?php endif; ?>
								</span>
							<?php endif; ?>
						</div>
						<?php endif; ?>
						<?php if(!empty($this->showpostedBy) && $postedBy):?>
							<div class='seaocore_browse_list_info_date'>
									<?php echo $this->translate('posted by'); ?>
									<?php echo $this->htmlLink($sitestore->getOwner()->getHref(), $sitestore->getOwner()->getTitle()) ?>
							</div>
						<?php endif; ?>
						<?php if (!empty($this->showdate)) :?>
							<div class='seaocore_browse_list_info_date'>
								<?php echo $this->timestamp(strtotime($sitestore->creation_date)) ?>
							</div>
						<?php endif ; ?>
						<?php if(!empty($this->showprice) && !empty($sitestore->price) && $this->enablePrice): ?>
							<div class='seaocore_browse_list_info_date'>
								<?php echo $this->translate("Price: "); echo Engine_Api::_()->sitestore()->getPriceWithCurrency($sitestore->price); ?>
							</div>
						<?php  endif;?>
						<?php
							if(!empty($sitestore->location) && $this->enableLocation && !empty($this->showlocation)):
							echo "<div class='seaocore_browse_list_info_date'>";
							echo $this->translate("Location: "); echo $this->translate($sitestore->location);
							$location_id = Engine_Api::_()->getDbTable('locations', 'sitestore')->getLocationId($sitestore->store_id, $sitestore->location); ?>&nbsp; - <b> <?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $sitestore->store_id, 'resouce_type' => 'sitestore_store', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?> </b>
							<?php echo "</div>";
						endif;
						?>
          </div>
			  </div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
<?php endif; ?>
<div id="rmap_canvas_view_store" <?php if($this->defaultView == 2): ?> style="display: block;" <?php else: ?> style="display: none;" <?php endif; ?>>
	<div class="seaocore_map clr" style="overflow:hidden;">
	  <div id="rmap_canvas_store"> </div>
		<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
		<?php if (!empty($siteTitle)) : ?>
			<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
		<?php endif; ?>
	</div>	
	
    <?php if( $this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
  	<a href="javascript:void(0);" onclick="rtoggleBounceStore()" class="stop_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
    <br />
    <?php endif;?>
</div>


<?php if( $this->enableLocation && $this->map_view): ?>
	<?php 
	$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey(); 


$this->headScript()
  ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>

	<script type="text/javascript">
   // arrays to hold copies of the markers and html used by the side_bar
  // because the function closure trick doesnt work there
  var rgmarkersStore = [];

  // global "map" variable
  var rmap_store = null;
  // A function to create the marker and set up the event window function
  function rcreateMarkerStore(latlng, name, html, title_store) {
    var contentString = html;
    if(name ==0)
    {
      var marker = new google.maps.Marker({
        position: latlng,
        map: rmap_store,
        title:title_store,
        animation: google.maps.Animation.DROP,
        zIndex: Math.round(latlng.lat()*-100000)<<5
      });
    }
    else{
      var marker =new google.maps.Marker({
        position: latlng,
        map: rmap_store,
        title:title_store,
        draggable: false,
        animation: google.maps.Animation.BOUNCE
      });
    }
    rgmarkersStore.push(marker);
    google.maps.event.addListener(marker, 'click', function() {
      infowindow.setContent(contentString);
		google.maps.event.trigger(rmap_store, 'resize');

      infowindow.open(rmap_store,marker);

    });
  }

  function rinitializeStore() {
    // create the map
    var myOptions = {
      zoom: <?php echo $defaultZoom; ?>,
      center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    rmap_store = new google.maps.Map(document.getElementById("rmap_canvas_store"),
    myOptions);

    google.maps.event.addListener(rmap_store, 'click', function() {
    infowindow.close();
		google.maps.event.trigger(rmap_store, 'resize');

    });
<?php $textPostedBy='';?>
<?php   foreach ($this->locations as $location) : ?>
<?php if($postedBy):?>
<?php $textPostedBy = $this->string()->escapeJavascript($this->translate('posted by')); ?>
<?php $textPostedBy.= " " . $this->htmlLink($this->sitestore[$location->store_id]->getOwner()->getHref(), $this->string()->escapeJavascript($this->sitestore[$location->store_id]->getOwner()->getTitle())) ?>
<?php endif; ?>
     // obtain the attribues of each marker
     var lat = <?php echo $location->latitude ?>;
     var lng =<?php echo $location->longitude  ?>;
     var point = new google.maps.LatLng(lat,lng);
     <?php if(!empty ($enableBouce)):?>
      var sponsored = <?php echo $this->sitestore[$location->store_id]->sponsored ?>
     <?php else:?>
      var sponsored =0;
     <?php endif; ?>
     // create the marker
		 <?php $store_id = $this->sitestore[$location->store_id]->store_id; ?>
     var contentString = '<div id="content">'+
       '<div id="siteNotice">'+
       '</div>'+'  <ul class="sitestores_locationdetails"><li>'+
       '<div class="sitestores_locationdetails_info_title">'+

       '<a href="<?php echo $this->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id)), 'sitestore_entry_view', true) ?>">'+"<?php echo $this->string()->escapeJavascript($this->sitestore[$location->store_id]->getTitle()); ?>"+'</a>'+

				'<div class="firght">'+
       '<span >'+
              <?php if ($this->sitestore[$location->store_id]->featured == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
                  '</span>'+
                    '<span>'+
              <?php if ($this->sitestore[$location->store_id]->sponsored == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
              <?php endif; ?>
            '</span>'+
	        '</div>'+
        '<div class="clr"></div>'+
        '</div>'+
       '<div class="sitestores_locationdetails_photo" >'+
       '<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore[$location->store_id]->store_id, $this->sitestore[$location->store_id]->owner_id,$this->sitestore[$location->store_id]->getSlug()), $this->itemPhoto($this->sitestore[$location->store_id], 'thumb.normal')) ?>'+
       '</div>'+
       '<div class="sitestores_locationdetails_info">'+
				<?php if (in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
					<?php if (($this->sitestore[$location->store_id]->rating > 0)): ?>
							'<span class="clr">'+
							<?php for ($x = 1; $x <= $this->sitestore[$location->store_id]->rating; $x++): ?>
									'<span class="rating_star_generic rating_star"></span>'+
							<?php endfor; ?>
							<?php if ((round($this->sitestore[$location->store_id]->rating) - $this->sitestore[$location->store_id]->rating) > 0): ?>
									'<span class="rating_star_generic rating_star_half"></span>'+
							<?php endif; ?>
									'</span>'+
					<?php endif; ?>
				<?php endif; ?>
             
              '<div class="sitestores_locationdetails_info_date">'+
                '<?php echo $this->timestamp(strtotime($this->sitestore[$location->store_id]->creation_date)) ?>'+' - <?php echo $textPostedBy?>'+
                '</div>'+
                
            <?php if (!empty($this->statistics)) : ?>
							'<div class="sitestores_locationdetails_info_date">'+
							<?php 
                $statistics = '';
                if(in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s like', '%s likes', $this->sitestore[$location->store_id]->like_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->like_count))).', ';
                }

                if(in_array('followCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s follower', '%s followers', $this->sitestore[$location->store_id]->follow_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->follow_count))).', ';
                }

                if(in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
                $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
									if ($this->sitestore[$location->store_id]->member_title && $memberTitle) {
										if ($this->sitestore[$location->store_id]->member_count == 1) : 
											$statistics .=  $this->sitestore[$location->store_id]->member_count . ' member'.', ';
										else:  
											$statistics .=  $this->sitestore[$location->store_id]->member_count . ' ' .  $this->sitestore[$location->store_id]->member_title.', ';
										endif; 
									} else {
										$statistics .= $this->string()->escapeJavascript($this->translate(array('%s member', '%s members', $this->sitestore[$location->store_id]->member_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->member_count))).', ';
									}
                }
                if(in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->sitestore[$location->store_id]->comment_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->comment_count))).', ';
                }
                if(in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s review', '%s reviews', $this->sitestore[$location->store_id]->review_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->review_count))).', ';
                }

                if(in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $this->sitestore[$location->store_id]->view_count), $this->locale()->toNumber($this->sitestore[$location->store_id]->view_count))).', ';
                }
                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
              ?>
              '<?php echo $statistics; ?>'+
							'</div>'+
						<?php endif; ?>
						'<div class="sitestores_locationdetails_info_date">'+
								<?php if (!empty($this->sitestore[$location->store_id]->phone)): ?>
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Phone: ")) . $this->sitestore[$location->store_id]->phone ?><br />"+
								<?php endif; ?>
								<?php if (!empty($this->sitestore[$location->store_id]->email)): ?>
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Email: ")) . $this->sitestore[$location->store_id]->email ?><br />"+
								<?php endif; ?>
								<?php if (!empty($this->sitestore[$location->store_id]->website)): ?>
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Website: ")) .$this->sitestore[$location->store_id]->website ?>"+
								<?php endif; ?>
             '</div>'+
              <?php if($this->sitestore[$location->store_id]->price && $this->enablePrice): ?>
                '<div class="sitestores_locationdetails_info_date">'+
								"<?php echo $this->string()->escapeJavascript($this->translate("Price: ")); echo  Engine_Api::_()->sitestore()->getPriceWithCurrency($this->sitestore[$location->store_id]->price) ?>"+
							'</div>'+
              <?php endif; ?>
							'<div class="sitestores_locationdetails_info_date">'+
						  "<?php echo  $this->translate("Location: "); echo $this->string()->escapeJavascript($location->location); ?>"+
							'</div>'+
              '</div>'+
              '<div class="clr"></div>'+
              ' </li></ul>'+


              '</div>';

            var marker = rcreateMarkerStore(point,sponsored,contentString, "<?php echo str_replace('"',' ',$this->sitestore[$location->store_id]->getTitle()); ?>");
      <?php   endforeach; ?>



        }


        var infowindow = new google.maps.InfoWindow(
        {
          size: new google.maps.Size(250,50)
        });

        function rtoggleBounceStore() {
          for(var i=0; i<rgmarkersStore.length;i++){
            if (rgmarkersStore[i].getAnimation() != null) {
              rgmarkersStore[i].setAnimation(null);
            }
          }
        }
        //]]>
</script>
<?php endif;?>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);
 ?>
<?php
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.latitude', 0); ?>
<?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.longitude', 0); ?>
<?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.zoom', 1); ?>

<script type="text/javascript">

  var current_store = '<?php echo $this->current_store; ?>';
  var paginateStoreLocations = function(store) {
		var  formElements = document.getElementById('filter_form');
		var parms = formElements.toQueryString(); 
		var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html&store=' + store;
		document.getElementById('store_location_loding_image').style.display ='';
    var url = en4.core.baseUrl + 'widget/index/mod/sitestore/name/browselocation-sitestore';
    //clearOverlays();
    gmarkers = [];
    en4.core.request.send(new Request.HTML({
      method : 'post',
			'url' : url,
			'data' : param,
      onSuccess :function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('store_location_loding_image').style.display ='none';
				document.getElementById('sitestore_location_map_anchor').getParent().innerHTML = responseHTML;
				setMarker();
      }
    })
    );
  }
</script>
<script type="text/javascript">
  var pageAction = function(store) {
		paginateStoreLocations(store);
  }
</script>

<?php if (empty($this->is_ajax)) : ?>
<div class="sitestore_browse_location" id="sitestore_browse_location" >
	<?php if (count($this->paginator) > 0): ?>
		<div class="sitestore_map_container_right" id ="sitestore_map_container_right"></div>
		<div id="sitestore_map_container" class="sitestore_map_container absolute" style="visibility:hidden;">
			<div class="sitestore_map_container_topbar" id='sitestore_map_container_topbar' style ='display:none;'>
				<a id="largemap" href="javascript:void(0);" onclick="smallLargeMap(1)" class="bold fleft">&laquo; <?php echo $this->translate('Large Map'); ?></a>
				<a id="smallmap" href="javascript:void(0);" onclick="smallLargeMap(0)" class="bold fleft"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
			</div>

			<div class="sitestore_map_container_map_area fleft seaocore_map" id="sitestorelocation_map">
				<div class="sitestore_map_content" id="sitestorelocation_browse_map_canvas" ></div>
				<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
				<?php if (!empty($siteTitle)) : ?>
					<div class="seaocore_map_info"><?php echo $this->translate("Locations on %s","<a href='' target='_blank'>$siteTitle</a>");?></div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="sitestore_map_container_list" id="sitestore_content_content">
<?php endif; ?>

  <a id="sitestore_location_map_anchor" class="pabsolute"></a>
		<?php if (count($this->paginator) > 0): ?>
			<ul class="seaocore_browse_list" id="seaocore_browse_list"><?php if (!empty($this->is_ajax)) : ?>	
			  <li style="border:none">
					<p>
						<?php echo $this->translate(array('%s store found.', '%s stores found.', $this->paginator->getTotalItemCount()),$this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
					</p>
				</li>
				<?php foreach ($this->paginator as $item): ?>
				<?php if(!empty($item->location) || !empty($this->locationVariable)) : ?>
					<li>
						<div class="seaocore_browse_list_photo">
							<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('title' => $item->getTitle(), 'target' => '_parent', 'class' => !empty($item->location)? "marker_photo_".$item->store_id :'un_location_sitestore')); ?>
						</div>
		
							<div class='seaocore_browse_list_info'>
								<div class='seaocore_browse_list_info_title'>
									<span>
										<?php   if( !empty($item->closed) ): ?>
											<img alt="close" src='<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/close.png'/>
										<?php endif;?>  
										<?php if (!empty($item->sponsored)): ?>
											<?php echo $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
										<?php endif; ?>
										<?php if (!empty($item->featured)): ?>
											<?php echo $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/images/featured.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
										<?php endif; ?>
									</span>
									<?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('title'
										=> $item->getTitle(), 'target' => '_parent', 'class' =>!empty($item->location)? "marker_".$item->store_id :'un_location_sitestore')); ?>
							  </div>
								<div class='seaocore_browse_list_info_date'>
									<?php echo $this->timestamp(strtotime($item->creation_date)) ?> 
										<?php if($postedBy):?> 
										- <?php echo $this->translate('posted by'); ?>
										<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>,
										<?php endif;?>
										<?php 
										$statistics = '';
										
										$statistics .= $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)).', ';
										
										$statistics .= $this->translate(array('%s follower', '%s followers', $item->follow_count), $this->locale()->toNumber($item->follow_count)).', ';


										if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
										$memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'storemember.member.title' , 1);
											if ($item->member_title && $memberTitle) {
												if ($item->member_count == 1) : 
													echo $item->member_count . ' member'.', ';
												else:  
													echo $item->member_count . ' ' .  $item->member_title.', ';
												endif; 
											} else {
												$statistics .= $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)).', ';
											}
										}

										if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
											$statistics .= $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)).', ';
										}
										
										$statistics .= $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)).', ';

										$statistics .= $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)).', ';

										$statistics = trim($statistics);
										$statistics = rtrim($statistics, ',');
										?>
										<?php echo $statistics; ?>
							  </div>
							<?php if((!empty($item->location) && $this->enableLocation) || (!empty($item->price) && $this->enablePrice) ): ?>
								<div class="seaocore_browse_list_info_date"><?php if(!empty($item->price) && $this->enablePrice): ?><?php echo $this->translate("Price: "); echo Engine_Api::_()->sitestore()->getPriceWithCurrency($item->price); ?><?php endif; ?><?php if((!empty($item->location) && $this->enableLocation) && (!empty($item->price ) && $this->enablePrice)): ?><?php echo $this->translate(", "); ?>
									<?php endif; ?>
									<?php if(!empty($item->location) && $this->enableLocation): ?>
										<?php  echo $this->translate("Location: "); echo $this->translate($item->location); ?>
											- <b>
													<?php if (!empty($this->mobile)) : ?>
														<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->store_id, 'resouce_type' => 'sitestore_store', 'is_mobile' => $this->mobile), $this->translate("Get Directions"), array('target' => '_blank')) ; ?>
													<?php else: ?>
														<?php if (!empty($this->is_ajax)) : ?>
															<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->store_id, 'resouce_type' => 'sitestore_store'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?>
															<?php else : ?>
																<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->store_id, 'resouce_type' => 'sitestore_store'), $this->translate("Get Directions"), array('class' => 'smoothbox')) ; ?>
															<?php endif; ?>
													<?php endif; ?>
												</b>
									<?php endif; ?>
								</div>
								<?php if (!empty($item->distance) && isset($item->distance)): ?>
									<div class="seaocore_browse_list_info_stat seaocore_txt_light">
										<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer')): ?>
											<b><?php echo $this->translate("approximately %s miles", round($item->distance, 2)); ?></b>
										<?php else: ?>
											<b><?php $distance = (1 / 0.621371192) * $item->distance; echo $this->translate("approximately %s kilometers", round($distance, 2)); ?></b>
										<?php endif; ?>
									</div>
								<?php endif; ?>
						  <?php endif; ?>
							<?php if (!empty($item->body)): ?>
								<div class="seaocore_browse_list_info_blurb">
									<?php echo $this->viewMore($item->body) ?>
								</div>
							<?php elseif (!empty($item->description)): ?>
								<div class="seaocore_browse_list_info_blurb">
									<?php echo $this->viewMore($item->description) ?>
								</div>
							<?php endif; ?>
						</div>
					</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<div class="clr sitestore_browse_location_paging" style="margin-top:10px;">
				<?php echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "sitestore"), array("orderby" => $this->orderby)); ?>
				<?php if( count($this->paginator) > 1 ): ?>
					<div class="fleft" id="store_location_loding_image" style="display: none;margin:5px;">
						<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" />
					</div>
				<?php endif; ?>
			</div>	<?php endif; ?>
		<?php else: ?>
			<div class="tip"> 
				<span><?php echo $this->translate('Nobody has created a store with that criteria.'); ?></span>
			</div>
		<?php endif; ?>
		<?php if (empty($this->is_ajax)) : ?>	
	</div>
</div>

<script type="text/javascript" >

  /* moo style */
  window.addEvent('domready',function() {
    //smallLargeMap(1);
    var Clientwidth = $('global_content').getElement(".layout_sitestore_browselocation_sitestore").clientWidth;

		var offsetWidth = $('sitestore_map_container').offsetWidth;
		$('sitestorelocation_browse_map_canvas').setStyle("height",offsetWidth);

    if (document.getElementById("smallmap"))
    document.getElementById("smallmap").style.display = "none";
    if ($('sitestore_map_right'))
			$('sitestore_map_right').style.display = 'none';

    <?php if($this->paginator->count()>0):?>
			<?php if( $this->enableLocation): ?>
				initialize();
			<?php endif; ?>  
    <?php endif;?>
  });
  
	if ($('seaocore_browse_list')) {

		var elementStartY = $('sitestorelocation_map').getPosition().x ;
		var offsetWidth = $('sitestore_map_container').offsetWidth;
		var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);


		function setMapContent () {

			if (!$('seaocore_browse_list')) {
				return;
			}
			
			var element=$("sitestore_map_container");
			if (element.offsetHeight > $('seaocore_browse_list').offsetHeight) {
				if(!element.hasClass('absolute')) {
					element.addClass('absolute');
					element.removeClass('fixed');
				if(element.hasClass('bottom'))
					element.removeClass('bottom');
				}
				return;
			}
			
			var elementPostionStartY = $('seaocore_browse_list').getPosition().y ;
			var elementPostionStartX = $('sitestore_map_container').getPosition().x ;
			var elementPostionEndY = elementPostionStartY + $('seaocore_browse_list').offsetHeight - element.offsetHeight;

			if( ((elementPostionEndY) < window.getScrollTop())) {
				if(element.hasClass('absolute'))
					element.removeClass('absolute');
				if(element.hasClass('fixed'))
					element.removeClass('fixed');
				if(!element.hasClass('bottom'))
					element.addClass('bottom');
			} 
			else if(((elementPostionStartY)  < window.getScrollTop())) {
				if(element.hasClass('absolute'))
					element.removeClass('absolute');
				if(!element.hasClass('fixed'))
					element.addClass('fixed');
				if(element.hasClass('bottom'))
					element.removeClass('bottom');
					element.setStyle("right",actualRightPostion);
					element.setStyle("width",offsetWidth);
			}
			else if(!element.hasClass('absolute')) {
				element.addClass('absolute');
				element.removeClass('fixed');
				if(element.hasClass('bottom'))
					element.removeClass('bottom');
			}
		}

		window.addEvent('scroll', function () {
			setMapContent();
		});
		
	}

  function smallLargeMap(option) {
		if(option == '1') {
		  $('sitestorelocation_browse_map_canvas').setStyle("height",'400px');
			document.getElementById("largemap").style.display = "none";
			document.getElementById("smallmap").style.display = "block";
			if(!$('sitestore_map_container').hasClass('sitestore_map_container_exp'))
				$('sitestore_map_container').addClass('sitestore_map_container_exp');
		} else {
		$('sitestorelocation_browse_map_canvas').setStyle("height",offsetWidth);
			document.getElementById("largemap").style.display = "block";
			document.getElementById("smallmap").style.display = "none";
			if($('sitestore_map_container').hasClass('sitestore_map_container_exp'))
				$('sitestore_map_container').removeClass('sitestore_map_container_exp');
			
		}
		setMapContent();
		google.maps.event.trigger(map, 'resize');
	}
</script>
  <script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/infobubble.js");
?>
<script type="text/javascript" >
    //<![CDATA[
  // this variable will collect the html which will eventually be placed in the side_bar
  var side_bar_html = "";

  // arrays to hold copies of the markers and html used by the side_bar
  // because the function closure trick doesnt work there
  var gmarkers = [];
  var infoBubbles;
  var markerClusterer = null;
  // global "map" variable
  var map = null;
  // A function to create the marker and set up the event window function
  function createMarker(latlng, name, html,title_store, store_id) {
    var contentString = html;
    if(name ==0) {
      var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: title_store,
       // store_id : store_id,
        animation: google.maps.Animation.DROP,
        zIndex: Math.round(latlng.lat()*-100000)<<5
      });
    }
    else {
      var marker =new google.maps.Marker({
        position: latlng,
        map: map,
        title: title_store,
        //store_id: store_id,
        draggable: false,
        animation: google.maps.Animation.BOUNCE
      });
    }

    gmarkers.push(marker);
    google.maps.event.addListener(marker, 'click', function() {
			google.maps.event.trigger(map, 'resize');
			map.setCenter(marker.position);
			//map.setZoom(<?php //echo '5'; ?> );
      infoBubbles.open(map,marker);
      infoBubbles.setContent(contentString);
    });

    //Show tooltip on the mouse over.
	  $$('.marker_' + store_id).each(function(locationMarker) {
			locationMarker.addEvent('mouseover',function(event) {
				google.maps.event.trigger(map, 'resize');
				map.setCenter(marker.position);
				infoBubbles.open(map,marker);
				infoBubbles.setContent(contentString);
			});			
    });
    
    //Show tooltip on the mouse over.
	  $$('.marker_photo_' + store_id).each(function(locationMarker) {
			locationMarker.addEvent('mouseover',function(event) {
				google.maps.event.trigger(map, 'resize');
				map.setCenter(marker.position);
				infoBubbles.open(map,marker);
				infoBubbles.setContent(contentString);
			});
    });
  }

  function initialize() {

    // create the map
    var myOptions = {
      zoom: <?php echo '1';?>,
      center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
      //  mapTypeControl: true,
      // mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    
    map = new google.maps.Map(document.getElementById("sitestorelocation_browse_map_canvas"),
    myOptions);

    google.maps.event.addListener(map, 'click', function() {
      <?php if( $this->enableLocation && $this->paginator->count() > 0): ?>
				infoBubbles.close();
      <?php endif; ?>
    });
    setMarker();
    
   
  }

  function setMapCenterZoomPoint(bounds, maplocation) {
    if (bounds && bounds.min_lat && bounds.min_lng && bounds.max_lat && bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
    }
    if (bounds &&  bounds.center_lat &&  bounds.center_lng) {
      maplocation.setCenter(new google.maps.LatLng( bounds.center_lat,  bounds.center_lng), 4);
    } else {
      maplocation.setCenter(new google.maps.LatLng(lat, lng), 4);
    }
    if (bds) {
      maplocation.setCenter(bds.getCenter());
      maplocation.fitBounds(bds);
    }
  }
  
  infoBubbles = new InfoBubble({
		maxWidth: 400,
		maxHeight: 400,
		shadowStyle: 1,
		padding: 0,
		backgroundColor: '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.tooltip.bgcolor', '#ffffff');?>',
		borderRadius: 5,
		arrowSize: 10,
		borderWidth: 1,
		borderColor: '#2c2c2c',
		disableAutoPan: true,
		hideCloseButton: false,
		arrowPosition: 50,
		//backgroundClassName: 'sitetag_checkin_map_tip',
		arrowStyle: 0
	});
</script>

<style type="text/css">
  #sitestorelocation_browse_map_canvas {
    width: 100% !important;
    height: 400px;
    float: left;
  }
  #sitestorelocation_browse_map_canvas > div{
    height: 300px;
  }
  #infoPanel {
    float: left;
    margin-left: 10px;
  }
  #infoPanel div {
    margin-bottom: 5px;
  }
</style>
<?php endif; ?>

<script type="text/javascript" >

function setMarker() {

<?php if (count($this->locations) > 0) : ?>
<?php   foreach ($this->locations as $location) : ?>
	// obtain the attribues of each marker
	var lat = <?php echo $location->latitude ?>;
	var lng =<?php echo $location->longitude  ?>;
	var point = new google.maps.LatLng(lat,lng);
	var store_id = <?php echo $this->sitestore[$location->store_id]->store_id  ?>;
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
		'<a href="<?php echo $this->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id)), 'sitestore_entry_view', true) ?>">'+"<?php echo  $this->string()->escapeJavascript($this->sitestore[$location->store_id]->getTitle()); ?>"+'</a>'+

		'<div class="fright">'+
		'<span >'+
					<?php if ($this->sitestore[$location->store_id]->featured == 1): ?>
							'<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
							'</span>'+
								'<span>'+
					<?php if ($this->sitestore[$location->store_id]->sponsored == 1): ?>
							'<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
					<?php endif; ?>
				'</span>'+
			'</div>'+
		'<div class="clr"></div>'+
		'</div>'+

		'<div class="sitestores_locationdetails_photo" >'+
		'<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore[$location->store_id]->store_id, $this->sitestore[$location->store_id]->owner_id,$this->sitestore[$location->store_id]->getSlug()), $this->itemPhoto($this->sitestore[$location->store_id], 'thumb.icon')) ?>'+
		'</div>'+
		'<div class="sitestores_locationdetails_info">'+

		<?php if ($this->ratngShow): ?>
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
						"<?php  $this->translate("Location: "); echo $this->string()->escapeJavascript($location->location); ?> "+
						<?php //if (!empty($this->getdirection)) : ?>
						<?php //echo  $this->htmlLink(array('route' => 'sitestore_viewmap', 'controller' => 'index', 'action' => 'view-map', 'id' => $location->store_id), $this->translate('Get Direction'), array('class' => 'smoothbox')) ?>
							'<?php //echo $this->htmlLink("https://maps.google.com/?daddr=".urlencode($location->location), $this->translate("Get Direction"), array('target' => 'blank')) ?>'
					  <?php //endif; ?>
					'</div>'+
					'</div>'+
					'<div class="clr"></div>'+
					' </li></ul>'+
					'</div>';
				var marker = createMarker(point,sponsored,contentString,"<?php echo str_replace('"',' ',$this->sitestore[$location->store_id]->getTitle()); ?>", store_id);

<?php   endforeach; ?>
$('sitestore_map_container').style.display = 'block';
google.maps.event.trigger(map, 'resize');
<?php else: ?>
$('sitestore_map_container').style.display = 'none';
<?php endif; ?>
//  markerClusterer = new MarkerClusterer(map, gmarkers, {
//  });
<?php if (!empty($this->locations)): ?>
	setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,map);
<?php endif; ?>

 //$$('.un_location_sitestore').each(function(el) { 
   $$('.un_location_sitestore').addEvent('mouseover',function(event) {
    infoBubbles.close();
    });
  //  });
}

</script>
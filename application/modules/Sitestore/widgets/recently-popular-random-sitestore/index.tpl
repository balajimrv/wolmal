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
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/core.js');
?>
<?php if( !empty($this->is_ajax_load) && !empty($this->titleLink) && ($this->titleLinkPosition == 'top') ) : ?>
  <span class="sitestore_top_link">
    <?php echo $this->titleLink; ?>
  </span>
<?php endif; ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';?>
<?php $enableBouce=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.sponsored', 1); 
$recently_randum_isthumb = Zend_Registry::isRegistered('sitestore_is_random_thumb') ? Zend_Registry::get('sitestore_is_random_thumb') : null;
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');?>

<?php if($this->is_ajax_load): ?>
<div class="layout_core_container_tabs clr">
<?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view) || count($this->tabs)>1): ?>
<?php if(count($this->tabs)>1): ?>
<div class="tabs_alt tabs_parent">
<?php else: ?>
<div class="sitestore_view_select">
<?php endif;?>
  <ul id="main_tabs">
      <?php if(count($this->tabs)>1): ?>
      <?php $active=true; ?> 
      <?php foreach ($this->tabs as $key => $tab): ?>
      <?php $class = $active ? 'active' : '' ?>
      <?php $active=false; ?> 
        <li class = '<?php echo $class ?>'  id = '<?php echo 'sitestore_home_store_' . $key.'_tab' ?>'>
          <a href='javascript:void(0);'  onclick="showListSitestore('<?php echo $tab['tabShow']; ?>','<?php echo $key; ?>');"><?php echo $this->translate($tab['title']) ?></a>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>  
      
      <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)): ?>
    <?php  if( $this->enableLocation && $this->map_view): ?>
      <?php  $latitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.latitude', 0); ?>
      <?php  $longitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.longitude', 0); ?>
      <?php  $defaultZoom=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.map.zoom', 1); ?>
        <li class="seaocore_tab_select_wrapper fright">
          <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View") ?></div>
          <span class="seaocore_tab_icon tab_icon_map_view" onclick="rswitchviewStore(2)" ></span>
        </li>
    <?php endif;?>
    <?php  if( $this->grid_view): ?> 
      <li class="seaocore_tab_select_wrapper fright">
        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View") ?></div>
        <span class="seaocore_tab_icon tab_icon_grid_view" onclick="rswitchviewStore(1)" ></span>
      </li> 
    <?php endif;?>
    <?php  if( $this->list_view): ?>
      <li class="seaocore_tab_select_wrapper fright">
        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View") ?></div>
        <span class="seaocore_tab_icon tab_icon_list_view" onclick="rswitchviewStore(0)" ></span>
      </li> 
    <?php endif;?>
   <?php endif;?>
  </ul>
</div>
<?php endif; ?>
<div id="dynamic_app_info_store">
<?php
	if( !empty($recently_randum_isthumb) ) {    
		include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/_recently_popular_random_store.tpl';
	}
?>
 </div>
 </div>
  
<?php if( !empty($this->is_ajax_load) && !empty($this->titleLink) && ($this->titleLinkPosition == 'bottom') ) : ?>
  <span class="sitestore_bottom_link">
    <?php echo $this->titleLink; ?>
  </span>
<?php endif; ?>
  
<script type="text/javascript" >
	function rswitchviewStore(flage){
	if(flage==2){
      if($('rmap_canvas_view_store')){
			$('rmap_canvas_view_store').style.display='block';
			google.maps.event.trigger(rmap_store, 'resize');
			rmap_store.setZoom(<?php echo $defaultZoom; ?> );
			rmap_store.setCenter(new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>));
      }
			if($('rgrid_view_store'))
      $('rgrid_view_store').style.display='none';
     if($('rimage_view_store'))
		$('rimage_view_store').style.display='none';
		}else if(flage==1){
     if($('rmap_canvas_view_store'))
		$('rmap_canvas_view_store').style.display='none';
    if($('rgrid_view_store'))
		$('rgrid_view_store').style.display='none';
    if($('rimage_view_store'))
		$('rimage_view_store').style.display='block';
		}else{
    if($('rmap_canvas_view_store'))
		$('rmap_canvas_view_store').style.display='none';
    if($('rgrid_view_store'))
		$('rgrid_view_store').style.display='block';
    if($('rimage_view_store'))
		$('rimage_view_store').style.display='none';
		}
	}
</script>
<script type="text/javascript">

/* moo style */
window.addEvent('domready',function() {
	if($('rimage_view_store')){
  //showtooltipStore();
  }
   <?php if( $this->enableLocation && $this->map_view): ?>
    rinitializeStore();
    <?php endif; ?>
 rswitchviewStore(<?php echo $this->defaultView ?>);
   $$('.tab_layout_sitestore_recently_popular_random_sitestore').addEvent('click', function() {
      
    	google.maps.event.trigger(rmap_store, 'resize');
			rmap_store.setZoom(<?php echo $defaultZoom; ?> );
			rmap_store.setCenter(new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>));  
    });
});

//var showtooltipStore = function (){
//  if($('rimage_view_store')){
//  //opacity / display fix
//	$$('.sitestore_tooltip').setStyles({
//		opacity: 0,
//		display: 'block'
//	});
//	//put the effect in place
//	$$('.jq-sitestore_tooltip li').each(function(el,i) {
//		el.addEvents({
//			'mouseenter': function() {
//				el.getElement('div').fade('in');
//			},
//			'mouseleave': function() {
//				el.getElement('div').fade('out');
//			}
//		});
//	});
//  }
//}
</script>

<script type="text/javascript">

   var showListSitestore = function (tabshow,tabName) {
     <?php foreach ($this->tabs as $key=> $tab): ?>
  if($('<?php echo 'sitestore_home_store_'.$key.'_tab' ?>'))
        $('<?php echo 'sitestore_home_store_' .$key.'_tab' ?>').erase('class');
  <?php  endforeach; ?>
if($('sitestore_home_store_'+tabName+'_tab'))
        $('sitestore_home_store_'+tabName+'_tab').set('class', 'active');
      
   if($('dynamic_app_info_store') != null) {

      $('dynamic_app_info_store').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loader.gif" class="sitestore_tabs_loader_img" /></center>';
    }

    var request = new Request.HTML({
      'url' : '<?php echo $this->url(array(), 'sitestore_ajaxhomelist', true) ?>',
      'data' : {
        'format' : 'html',
        'task' : 'ajax',
        'tab_show' : tabshow,
        'list_limit':'<?php echo  $this->active_tab_list; ?>',
        'grid_limit':'<?php echo $this->active_tab_image; ?>',
        'list_view':'<?php echo $this->list_view; ?>',
        'grid_view':'<?php echo $this->grid_view; ?>',
        'map_view':'<?php echo $this->map_view; ?>',
        'category_id':<?php echo $this->category_id; ?>,
        'defaultView':<?php echo $this->defaultView; ?>, 
        'columnWidth':<?php echo $this->columnWidth; ?>,
        'columnHeight':<?php echo $this->columnHeight; ?>,
        'showlikebutton':<?php echo $this->showlikebutton; ?>,
        'turncation':<?php echo $this->turncation; ?>,
        'showfeaturedLable':<?php echo $this->showfeaturedLable; ?>,
        'showsponsoredLable':<?php echo $this->showsponsoredLable; ?>,
        'showlocation':<?php echo $this->showlocation; ?>,
        'showprice':<?php echo $this->showprice; ?>,
        'showpostedBy':<?php echo $this->showpostedBy; ?>,
        'showdate':<?php echo $this->showdate; ?>,
        'statistics': '<?php echo json_encode($this->statistics) ?>',
        'detactLocation': '<?php echo $this->detactLocation ?>',
				'defaultLocationDistance': '<?php echo $this->defaultLocationDistance ?>',
				'latitude': window.locationsParamsSEAO && window.locationsParamsSEAO.latitude ? window.locationsParamsSEAO.latitude:0,
				'longitude': window.locationsParamsSEAO && window.locationsParamsSEAO.longitude ? window.locationsParamsSEAO.longitude:0
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('dynamic_app_info_store').innerHTML = responseHTML;
        
       // showtooltipStore();
        <?php if( $this->enableLocation && $this->map_view): ?>
        rinitializeStore();
        <?php endif; ?>
         rswitchviewStore(<?php echo $this->defaultView ?>);
      }
    });

    request.send();
  }
</script>
<style type="text/css">
  #rmap_canvas_store {
    width: 100% !important;
    height: 400px;
    float: left;
  }
  #rmap_canvas_store > div{
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
<?php else: ?>

  <div id="layout_sitestore_recently_popular_random_sitestore_<?php echo $this->identity;?>">
<!--    <div class="seaocore_content_loader"></div>-->
  </div>

  <?php if($this->detactLocation):?>
  <script type="text/javascript">
    var requestParams = $merge(<?php echo json_encode($this->params);?>, {'content_id': '<?php echo $this->identity;?>'})
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'defaultLocationDistance': '<?php echo $this->defaultLocationDistance ?>',
      'responseContainer' : 'layout_sitestore_recently_popular_random_sitestore_<?php echo $this->identity;?>',
       requestParams: requestParams      
    };

    en4.seaocore.locationBased.startReq(params);
  </script>  
  <?php else: ?>
     <script type="text/javascript">
     window.addEvent('domready',function(){
         en4.sitestore.ajaxTab.sendReq({
            loading:true,
            requestParams:$merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
            responseContainer: [$('layout_sitestore_recently_popular_random_sitestore_<?php echo $this->identity; ?>')]
        });
        });
    </script>  
  <?php endif; ?>


<?php endif; ?>

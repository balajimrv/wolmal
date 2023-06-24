<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');
?>
<script type="text/javascript">

	    var contentinformtion;
      var store_showtitle;
  if(contentinformtion == 0) {
		if($('global_content').getElement('.layout_activity_feed')) {
			$('global_content').getElement('.layout_activity_feed').style.display = 'none';
		}
		if($('global_content').getElement('.layout_core_profile_links')) {
			$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
		}
		if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
			$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'none';
		}

		if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
			$('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'none';
		}
  }
</script>

<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php $sitestore=$this->sitestore;  ?>
<script type="text/javascript">
  var myLatlng;
  function initialize() {
    var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
    var myOptions = {
      zoom: <?php echo $this->location->zoom; ?> ,
      center: myLatlng,
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+''+
				'<div class="splb_mtt">'+
		    	"<?php echo $this->string()->escapeJavascript($sitestore->getTitle())?>"+
	      '</div>'+
				'<div class="splb_mtl">'+
					"<?php echo $this->string()->escapeJavascript( $this->location->location); ?>"+
		     '</div>'+
      '</div>';


    var infowindow = new google.maps.InfoWindow({
      content: contentString ,
      size: new google.maps.Size(250,50)

    });

    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: "<?php echo str_replace('"', ' ',$sitestore->getTitle())?>"
    });
    google.maps.event.addListener(marker, 'click', function() {

      infowindow.open(map,marker);
    });


      $('map_likebox_active').addEvent('click', function() {  
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });
  

    google.maps.event.addListener(map, 'click', function() {

      infowindow.close();
			google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });


  }

</script>
<div id="map_canvas"></div>


<style type="text/css">
  #map_canvas {
    width: 100%;
    height: 300px;
    margin:0 auto;
  }
  #map_canvas > div{
    position: static !important;
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
<script type="text/javascript">
  window.addEvent('domready',function(){
    initialize();
  });
</script>


	<script type="text/javascript">
	 //prev_tab_id = '<?php //echo $this->content_id; ?>';
	 $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function()
	 {
	    if(store_showtitle != 0) {
	    	if($('profile_status')) {
			  	$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle())?><?php echo $this->translate(' &raquo; Map ');?></h2>";
	    	}
				if($('layout_map')) {
				  $('layout_map').style.display = 'block';
				}
	  	}
	  	if($('global_content').getElement('.layout_sitestore_photorecent_sitestore')) {
				$('global_content').getElement('.layout_sitestore_photorecent_sitestore').style.display = 'none';
			}
	    if($('global_content').getElement('.layout_sitestore_location_sitestore')) {
					 $('global_content').getElement('.layout_sitestore_location_sitestore').style.display = 'block';
			}
			if($('global_content').getElement('.layout_activity_feed')) {
				$('global_content').getElement('.layout_activity_feed').style.display = 'none';
			}
			if($('global_content').getElement('.layout_core_profile_links')) {
				$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
			}
			if($('global_content').getElement('.layout_sitestore_info_sitestore')) {
				$('global_content').getElement('.layout_sitestore_info_sitestore').style.display = 'none';
			}
			if($('global_content').getElement('.layout_sitestore_overview_sitestore')) {
				$('global_content').getElement('.layout_sitestore_overview_sitestore').style.display = 'none';
		  }
	  	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
	    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
	      $('global_content').getElement('.'+ prev_tab_class).style.display = 'none';
	    }
	    prev_tab_id = '<?php echo $this->content_id; ?>';
	  	prev_tab_class = 'layout_sitestore_location_sitestore';

	    if(store_showtitle == 1 ) {
setLeftLayoutForStore(); 
	    } else if(store_showtitle == 0 ) {
		    if ($$('.layout_left')){
		      $$('.layout_left').setStyle('display', 'none');
		      if($('thumb_icon')) {
		       $('thumb_icon').style.display = 'block';
		      }
		    }		if ($$('.layout_right')){
      $$('.layout_right').setStyle('display', 'none');
      if($('thumb_icon')) {
       $('thumb_icon').style.display = 'block';
      }
    }		

	    }
	  });
	</script>
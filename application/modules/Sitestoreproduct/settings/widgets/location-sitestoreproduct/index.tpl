<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">
    var myLatlng;
    function initialize() {
        var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
        var myOptions = {
            zoom: <?php echo $this->location->zoom; ?>,
            center: myLatlng,
            navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("sitestoreproduct_view_map_canvas"), myOptions);

var contentString = '<div id="content">'+'  <ul class="sitestores_locationdetails"><li>'+

		'<div class="sitestores_locationdetails_info_title">'+
		'<?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->string()->escapeJavascript($this->sitestoreproduct->getTitle())); ?>'+

		'<div class="fright">'+
		'<span >'+
					<?php if ($this->sitestoreproduct->featured == 1): ?>
							'<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
							'</span>'+
								'<span>'+
					<?php if ($this->sitestoreproduct->sponsored == 1): ?>
							'<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
					<?php endif; ?>
				'</span>'+
			'</div>'+
		'<div class="clr"></div>'+
		'</div>'+

		'<div class="sitestores_locationdetails_photo" >'+
		'<?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->itemPhoto($this->sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))); ?>'+
		'</div>'+
		'<div class="sitestores_locationdetails_info">'+
					'<div class="sitestores_locationdetails_info_date">'+
							'<?php echo $this->htmlLink("https://maps.google.com/?daddr=".urlencode($this->location->location), $this->translate($this->location->location), array('target' => 'blank')) ?>'
					'</div>'+
					'</div>'+
					'<div class="clr"></div>'+
					' </li></ul>'+
					'</div>';
        var infowindow = new google.maps.InfoWindow({
            size: new google.maps.Size(250, 50),
            content: contentString

        });

        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: "<?php echo str_replace('"', ' ', $this->sitestoreproduct->getTitle()) ?>"

        });

        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });

        $$('.tab_layout_sitestoreproduct_location_sitestoreproduct').addEvent('click', function() {
            google.maps.event.trigger(map, 'resize');
            map.setZoom(<?php echo $this->location->zoom; ?>);
            map.setCenter(myLatlng);
        });

        google.maps.event.addListener(map, 'click', function() {
            infowindow.close();
            google.maps.event.trigger(map, 'resize');
            map.setZoom(<?php echo $this->location->zoom; ?>);
            map.setCenter(myLatlng);
        });
    }
</script>

<div class="sitestoreproduct_profile_map b_dark clr">
    <ul class="sitestoreproduct_profile_location">
        <li class="seaocore_map">
            <div id="sitestoreproduct_view_map_canvas"></div>
            <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
            <?php if (!empty($siteTitle)) : ?>
                <div class="seaocore_map_info">
                    <?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?>
                </div>
            <?php endif; ?>
        </li>
    </ul>
</div>	

<div class='profile_fields clr'>
    <h4>
        <span><?php echo$this->translate('Location Information') ?></span>
    </h4>
    <ul>
        <li>
            <span><?php echo $this->translate('Location:'); ?> </span>
            <span><b><?php echo $this->location->location; ?></b> - <b>
                    <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->location->product_id, 'resouce_type' => 'sitestoreproduct_product'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?>
                </b></span>
        </li>
        <?php if (!empty($this->location->formatted_address)): ?>
            <li>
                <span><?php echo $this->translate('Formatted Address:'); ?> </span>
                <span><?php echo $this->location->formatted_address; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->address)): ?>
            <li>
                <span><?php echo $this->translate('Street Address:'); ?> </span>
                <span><?php echo $this->location->address; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->city)): ?>
            <li>
                <span><?php echo $this->translate('City:'); ?></span>
                <span><?php echo $this->location->city; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->zipcode)): ?>
            <li>
                <span><?php echo $this->translate('Zipcode:'); ?></span>
                <span><?php echo $this->location->zipcode; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->state)): ?>
            <li>
                <span><?php echo $this->translate('State:'); ?></span>
                <span><?php echo $this->location->state; ?></span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->country)): ?>
            <li>
                <span><?php echo $this->translate('Country:'); ?></span>
                <span><?php echo $this->location->country; ?></span>
            </li>
        <?php endif; ?>
    </ul>
</div>

<script type="text/javascript" >
    function owner(thisobj) {
        var Obj_Url = thisobj.href;
        Smoothbox.open(Obj_Url);
    }
</script>

<script type="text/javascript">
    window.addEvent('domready', function() {
        initialize();
    });
</script>
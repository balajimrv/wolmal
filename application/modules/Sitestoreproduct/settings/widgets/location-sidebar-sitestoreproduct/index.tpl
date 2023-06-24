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
    function initializeSidebarMap() {
        var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
        var myOptions = {
            zoom: <?php echo $this->location->zoom; ?>,
            center: myLatlng,
            navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("sitestoreproduct_view_map_canvas_sidebar"), myOptions);

        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: "<?php echo str_replace('"', ' ', $this->sitestoreproduct->getTitle()) ?>"

        });

        google.maps.event.addListener(marker, 'click', function() {
            //infowindow.open(map,marker);
        });

        $$('.tab_layout_sitestoreproduct_location_sidebar_sitestoreproduct').addEvent('click', function() {
            google.maps.event.trigger(map, 'resize');
            map.setZoom(<?php echo $this->location->zoom; ?>);
            map.setCenter(myLatlng);
        });

        google.maps.event.addListener(map, 'click', function() {
            //infowindow.close();
            google.maps.event.trigger(map, 'resize');
            map.setZoom(<?php echo $this->location->zoom; ?>);
            map.setCenter(myLatlng);
        });
    }
</script>

<div class="sitestoreproduct_profile_map b_dark clr">
    <ul>
        <li class="seaocore_map">
            <div id="sitestoreproduct_view_map_canvas_sidebar" style="height:<?php echo $this->height; ?>px"></div>
        </li>
    </ul>
</div>	

<div class='clr o_hidden'>
    <ul class="sitestoreproduct_side_widgets sitestoreproduct_profile_product_info">
        <li class="clr">
            <div class="sitestoreprooduct_listings_stats">
              <i class="sitestoreproduct_icon_strip sitestoreproduct_icon sitestoreproduct_icon_location" title="<?php echo $this->translate("Location") ?>"></i>
                <div class="o_hidden f_small">
                    <?php echo $this->location->location; ?> - <b>
                        <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->location->product_id, 'resouce_type' => 'sitestoreproduct_product'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?></b>
                </div>
            </div>
        </li>
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
        initializeSidebarMap();
    });
</script>
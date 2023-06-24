<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editaddress.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
        ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>
<div class="sitestoreproduct_form_popup">
  <?php echo $this->form->render($this); ?>
</div>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    if (document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
      var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
    }
  });

    window.addEvent('load', function() { 
        var preLocation = '<?php echo $this->sitestoreproduct->location; ?>';
        var locationId = '<?php echo $this->locationId; ?>';
        if(preLocation == '' && locationId) {
            var locationEl = document.getElementById('location');
            var locationValue = '<?php echo $this->locationDetails->location; ?>';
            var latitudeValue = '<?php echo $this->locationDetails->latitude; ?>';
            var longitudeValue = '<?php echo $this->locationDetails->longitude; ?>';
            var formattedAddressValue = '<?php echo $this->locationDetails->formatted_address; ?>';
            var addressValue = '<?php echo $this->locationDetails->address; ?>';
            var countryValue = '<?php echo $this->locationDetails->country; ?>';
            var stateValue = '<?php echo $this->locationDetails->state; ?>';
            var zipcodeValue = '<?php echo $this->locationDetails->zipcode; ?>';
            var cityValue = '<?php echo $this->locationDetails->city; ?>';
            var locationParams = '{"location" :"' + locationValue + '","latitude" :"' + latitudeValue + '","longitude":"' + longitudeValue + '","formatted_address":"' + formattedAddressValue + '","address":"' + addressValue + '","country":"' + countryValue + '","state":"' + stateValue + '","zip_code":"' + zipcodeValue + '","city":"' + cityValue + '"}';
            document.getElementById('location').value = locationValue;
            document.getElementById('locationParams').value = locationParams;
      }
    });
  
</script>


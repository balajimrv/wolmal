<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-location.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="settings global_form_popup sitestoreproduct_admin_popup_form">
  
  <?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
  <a href="javascript: void(0);" onclick="return addAnotherRegion();" id="addRegionLink"><?php echo $this->translate("Add More Regions") ?></a>
  <script type="text/javascript">
    //<!--
    en4.core.runonce.add(function() {//alert('dfdf');
      var maxRegions = 100;
      var regions = <?php echo Zend_Json::encode($this->regions) ?>;
      var regionParent = $('regions').getParent();
//      document.getElementById('country-label').style.display = 'block';
//      document.getElementById('regions-label').style.display = 'block';
      
      var addAnotherRegion = window.addAnotherRegion = function (dontFocus, label) {
        if (maxRegions && $$('input.ratingRegionInput').length >= maxRegions) {
          return !alert(new String('<?php echo $this->string()->escapeJavascript($this->translate("A maximum of %s regions are permitted at single time.")) ?>').replace(/%s/, maxRegions));
          return false;
        }

        var regionElement = new Element('input', {
          'type': 'text',
          'name': 'regionsArray[]',
          'class': 'ratingRegionInput',
          'value': label
        });
        
        if( dontFocus ) {
          regionElement.inject(regionParent);
        } else {
          regionElement.inject(regionParent).focus();
        }

        $('addRegionLink').inject(regionParent);

        if( maxRegions && $$('input.ratingRegionInput').length >= maxRegions ) {
          $('addRegionLink').destroy();
        }
      }
      
      // Do stuff
      if( $type(regions) == 'array' && regions.length > 0 ) {
        regions.each(function(label) {
          addAnotherRegion(true, label);
        });
        if( regions.length == 1 ) {
          addAnotherRegion(true);
        }
      } else {
        // display two boxes to start with
        addAnotherRegion(true);
        //addAnotherRegion(true);
      }
    });
    // -->
  </script>  
  
</div>

<script type="text/javascript">  
  function allRegion(){
    if(document.getElementById('all_regions-1').checked){
      document.getElementById('regions-wrapper').style.display = 'none';
    }else {
      document.getElementById('regions-wrapper').style.display = 'block';
    }
  }
  
  window.addEvent('domready', function() {
    allRegion();
  });
  
</script>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>

<style type="text/css">
	.form-label{
		width:95px !important;
	}
	#regions-element input{
		margin-bottom:5px;
	}
	#regions-element a{
		float:left;
	}
	select{
		max-width:300px;
	}
</style>
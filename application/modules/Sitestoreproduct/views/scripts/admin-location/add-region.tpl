<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-region.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="settings sr_sitestoreproduct_parameters_popup">
  
  <?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
  <a href="javascript: void(0);" onclick="return addAnotherRegion();" id="addRegionLink"><?php echo $this->translate("Add More Regions / States") ?></a>
  <script type="text/javascript">
    //<!--
    en4.core.runonce.add(function() {//alert('dfdf');
      var maxRegions = 100;
      var regions = <?php echo Zend_Json::encode($this->regions) ?>;
      var regionParent = $('regions').getParent();
//      document.getElementById('country-label').style.display = 'block';
//      document.getElementById('regions-label').style.display = 'block';
      
      var addAnotherRegion = window.addAnotherRegion = function (dontFocus, label) {//alert('dsd');
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

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
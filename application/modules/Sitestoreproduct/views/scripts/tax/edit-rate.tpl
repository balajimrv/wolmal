<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-rate.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">
var setRegion = null;
window.addEvent('domready', function() {
  document.getElementById('tax_rate-wrapper').style.display = 'none';
  <?php if ($this->flag_region != 0): ?>
    setRegion = <?php echo $this->flag_region ?>;
  <?php endif; ?>
  <?php  if (!empty($this->flagAllCountries)): ?>
    document.getElementById('state-wrapper').style.display = 'none';
  <?php  endif; ?>
  showPriceType();
});
  
function showPriceType(){
  if(document.getElementById('handling_type')){
    if(document.getElementById('handling_type').value == 1) {
      document.getElementById('tax_price-wrapper').style.display = 'none';
      document.getElementById('tax_rate-wrapper').style.display = 'block';

    } else{
      document.getElementById('tax_price-wrapper').style.display = 'block';
      document.getElementById('tax_rate-wrapper').style.display = 'none';
    }
  }
}
</script>

<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>
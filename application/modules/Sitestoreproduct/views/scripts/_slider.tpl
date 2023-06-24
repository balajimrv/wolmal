<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _slider.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/slider.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/slider.js');
?>

<?php
$currency_symbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;

if (empty($currency_symbol)) {
  $currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
  
}
$minPrice = ($this->minPrice) ? $this->minPrice : 0;
$minPrice = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($minPrice, 1, 0);

$maxPrice = ($this->maxPrice) ? $this->maxPrice : 999;
$maxPrice = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($maxPrice, 1, 0);


$searchMinPrice = Zend_Controller_Front::getInstance()->getRequest()->getParam('minPrice');
 
$searchMaxPrice = Zend_Controller_Front::getInstance()->getRequest()->getParam('maxPrice');
 

?>
<li>
  <span id="minmax_slider-label" style="margin-bottom:7px;"><label><?php echo $this->translate('Price Range'); ?></label></span>
  <div class="slide_container" >
    <div class="slider_gutter" id="minmax_slider" >
      <div id="slider_minmax_gutter_l" class="slider_gutter_item iconsprite_controls"></div>
      <div id="slider_minmax_gutter_m" class="slider_gutter_item gutter iconsprite_controls">
        <div id="slider_bkg_img"></div>
        <div id="slider_minmax_minKnobA" class="knob"></div>
        <div id="slider_minmax_maxKnobA" class="knob"></div>
      </div>
      <div id="slider_minmax_gutter_r" class="slider_gutter_item iconsprite_controls"> </div>
    </div>
    <div id="slider_minmax_min" class="fleft price_range_txt"> </div>
    <div id="slider_minmax_max" class="fright price_range_txt"> </div>
    <div class="clearfix"></div>
  </div>
</li>
<!-- End Slider with two  knobs-->

<script type="text/javascript" >
// ON LOAD 
  en4.core.runonce.add(function() {
    var mySlideA = new Slider($('slider_minmax_gutter_m'), $('slider_minmax_minKnobA'), $('slider_bkg_img'), {
      start: <?php echo $minPrice ?>,
      end: <?php echo $maxPrice ?>,
      offset: 8,
      snap: false,
      onChange: function(pos) {

        $('minPrice').value = pos.minpos;
        $('maxPrice').value = pos.maxpos;
        <?php if( $this->currencySymbolPosition == 'left' ) : ?>
          $('slider_minmax_min').innerHTML = '<?php echo $currency_symbol; ?> ' + pos.minpos;
          $('slider_minmax_max').innerHTML = '<?php echo $currency_symbol; ?> ' + pos.maxpos;
        <?php elseif( $this->currencySymbolPosition == 'right' ) : ?>
          $('slider_minmax_min').innerHTML = pos.minpos + ' <?php echo $currency_symbol; ?>';
          $('slider_minmax_max').innerHTML = pos.maxpos + ' <?php echo $currency_symbol; ?>';
        <?php endif; ?>
      }
    },
    $('slider_minmax_maxKnobA')).setMin(<?php echo ($searchMinPrice) ? $searchMinPrice : $minPrice ?>).setMax(<?php echo ($searchMaxPrice) ? $searchMaxPrice : $maxPrice ?>);
  });
</script>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: combination-create.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproductform.css')
?>
<?php if(!empty($this->error_message)) :?>
<div class="tip">
  <span>
    <?php echo $this->error_message ;?>
    </span>
  </div>
<?php else: ?>
<div class="clr">
  <?php echo $this->form->render($this) ?>
</div>
<?php foreach ($this->field_ids as $field_id) : ?>
  <span id="attribute_price_<?php echo $field_id; ?>" style="display: none;"></span>
<?php endforeach; ?>

<script type="text/javascript">

  window.addEvent('domready', function() {
<?php foreach ($this->field_ids as $field_id) : ?>
      var is_post = '<?php echo $this->post ; ?>';
      var price_field_id = 'price_' + '<?php echo $field_id; ?>';
      var price_incr_field_id = 'price_increment_' + '<?php echo $field_id; ?>';
      var select_id = 'select_' + '<?php echo $field_id ;?>';
      if (is_post != 1) {
        if ($(price_field_id + '-wrapper')) {
          $(price_field_id + '-wrapper').setStyle('display', 'none');
        }
        if ($(price_incr_field_id + '-wrapper')) {
          $(price_incr_field_id + '-wrapper').setStyle('display', 'none');
        }
      }
      else{
        if($(select_id).value == 0){
            $(price_field_id + '-wrapper').setStyle('display', 'none');
            $(price_incr_field_id + '-wrapper').setStyle('display', 'none');
        }else if($('existed_' + '<?php echo $field_id; ?>').value == 1){
            $('attribute_price_' + '<?php echo $field_id; ?>').innerHTML = $('select_response_' + '<?php echo $field_id; ?>').value;
            $('attribute_price_' + '<?php echo $field_id; ?>').setStyle('display', 'block');
            $('attribute_price_' + '<?php echo $field_id; ?>').inject($('select_' + '<?php echo $field_id; ?>' + '-wrapper'), 'after');
            $(price_field_id + '-wrapper').setStyle('display', 'none');
            $(price_incr_field_id + '-wrapper').setStyle('display', 'none');
        }else{
            $(price_field_id + '-wrapper').setStyle('display', 'block');
            $(price_incr_field_id + '-wrapper').setStyle('display', 'block');
        }
      }
<?php endforeach; ?>
  });

  function showPrice(element, field_id, product_id)
  {
    if (element.value != null) {
      var url = en4.core.baseUrl + 'sitestoreproduct/siteform/get-attribute-price';
      $('attribute_price_' + field_id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
      $('attribute_price_' + field_id).inject($('select_' + field_id + '-wrapper'), 'after');
      $('attribute_price_' + field_id).setStyle('display', 'block');
      temp = new Request({
        method: 'post',
        'url': url,
        'data': {
          'format': 'json',
          'combination_attribute_id': element.value,
          'field_id': field_id,
          'product_id': product_id,
        },
        onSuccess: function(responseJSON) {
          if (responseJSON != null && responseJSON != '') {
            $('attribute_price_' + field_id).innerHTML = responseJSON;
            $('attribute_price_' + field_id).setStyle('display', 'block');
            $('attribute_price_' + field_id).inject($('select_' + field_id + '-wrapper'), 'after');
            $('price_' + field_id).value = 1;
            $('select_response_' + field_id).value = responseJSON;
            $('existed_' + field_id).value = 1;
            if ($('price_' + field_id + '-wrapper'))
              $('price_' + field_id + '-wrapper').setStyle('display', 'none');
            if ($('price_increment_' + field_id + '-wrapper'))
              $('price_increment_' + field_id + '-wrapper').setStyle('display', 'none');
          }
          else {
            if ($('attribute_price_' + field_id))
              $('attribute_price_' + field_id).setStyle('display', 'none');
            $('select_response_' + field_id).value = 0;
            $('existed_' + field_id).value = 0;
            $('price_' + field_id).value = parseFloat("0.00").toFixed(2);
            $('price_' + field_id + '-wrapper').setStyle('display', 'block');
            $('price_increment_' + field_id + '-wrapper').setStyle('display', 'block');
          }
        }
      });
      temp.send();
    }
  }

</script>

<?php endif; ?>
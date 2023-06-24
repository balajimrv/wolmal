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
                if (document.getElementById(price_field_id + '-wrapper')) {
                    document.getElementById(price_field_id + '-wrapper').setStyle('display', 'none');
                }
                if (document.getElementById(price_incr_field_id + '-wrapper')) {
                    document.getElementById(price_incr_field_id + '-wrapper').setStyle('display', 'none');
                }
            }
            else{
                if(document.getElementById(select_id).value == 0){
                    document.getElementById(price_field_id + '-wrapper').setStyle('display', 'none');
                    document.getElementById(price_incr_field_id + '-wrapper').setStyle('display', 'none');
                }else if(document.getElementById('existed_' + '<?php echo $field_id; ?>').value == 1){
                    document.getElementById('attribute_price_' + '<?php echo $field_id; ?>').innerHTML = document.getElementById('select_response_' + '<?php echo $field_id; ?>').value;
                    document.getElementById('attribute_price_' + '<?php echo $field_id; ?>').setStyle('display', 'block');
                    document.getElementById('attribute_price_' + '<?php echo $field_id; ?>').inject(document.getElementById('select_' + '<?php echo $field_id; ?>' + '-wrapper'), 'after');
                    document.getElementById(price_field_id + '-wrapper').setStyle('display', 'none');
                    document.getElementById(price_incr_field_id + '-wrapper').setStyle('display', 'none');
                }else{
                    document.getElementById(price_field_id + '-wrapper').setStyle('display', 'block');
                    document.getElementById(price_incr_field_id + '-wrapper').setStyle('display', 'block');
                }
            }
            <?php endforeach; ?>
        });

        function showPrice(element, field_id, product_id)
        {
            if (element.value != null) {
                var url = '<?php echo $baseUrl;?>' + 'sitestoreproduct/siteform/get-attribute-price';
                document.getElementById('attribute_price_' + field_id).innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
                document.getElementById('attribute_price_' + field_id).inject(document.getElementById('select_' + field_id + '-wrapper'), 'after');
                document.getElementById('attribute_price_' + field_id).setStyle('display', 'block');
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
                            document.getElementById('attribute_price_' + field_id).innerHTML = responseJSON;
                            document.getElementById('attribute_price_' + field_id).setStyle('display', 'block');
                            document.getElementById('attribute_price_' + field_id).inject(document.getElementById('select_' + field_id + '-wrapper'), 'after');
                            document.getElementById('price_' + field_id).value = 1;
                            document.getElementById('select_response_' + field_id).value = responseJSON;
                            document.getElementById('existed_' + field_id).value = 1;
                            if (document.getElementById('price_' + field_id + '-wrapper'))
                                document.getElementById('price_' + field_id + '-wrapper').setStyle('display', 'none');
                            if (document.getElementById('price_increment_' + field_id + '-wrapper'))
                                document.getElementById('price_increment_' + field_id + '-wrapper').setStyle('display', 'none');
                        }
                        else {
                            if (document.getElementById('attribute_price_' + field_id))
                                document.getElementById('attribute_price_' + field_id).setStyle('display', 'none');
                            document.getElementById('select_response_' + field_id).value = 0;
                            document.getElementById('existed_' + field_id).value = 0;
                            document.getElementById('price_' + field_id).value = parseFloat("0.00").toFixed(2);
                            document.getElementById('price_' + field_id + '-wrapper').setStyle('display', 'block');
                            document.getElementById('price_increment_' + field_id + '-wrapper').setStyle('display', 'block');
                        }
                    }
                });
                temp.send();
            }
        }

    </script>

<?php endif; ?>
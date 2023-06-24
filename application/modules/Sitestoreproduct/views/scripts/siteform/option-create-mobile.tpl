<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: field-create.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl
        . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproductform.css')
?>

<?php if(!empty($this->error_message)) : ?>
    <div class="tip">
        <span> <?php echo $this->error_message; ?> </span>
    </div>
<?php else :?>
    <div class="clr">
        <?php echo $this->form->render($this) ?>
    </div>

    <script type="text/javascript">

        window.addEvent('domready', function(){
            if($('quantity_unlimited-1')){
                $('quantity-wrapper').setStyle('display', ($('quantity_unlimited-1').checked ?'none':'block'));
            }

            if($('price')){
                $('price').value = parseFloat("0.00").toFixed(2);
            }
        });

        function showStock(){
            var radios = document.getElementsByName("quantity_unlimited");
            var radioValue;
            if (radios[0].checked) {
                radioValue = radios[0].value;
            }else {
                radioValue = radios[1].value;
            }
            if(radioValue == 1) {
                document.getElementById('quantity-wrapper').style.display="none";
            } else{
                document.getElementById('quantity-wrapper').style.display="block";
            }
        }
    </script>

<?php endif; ?>
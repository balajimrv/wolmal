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

<?php if ($this->form): ?>
    <?php echo $this->form->render($this) ?>
<?php else: ?>
    <div class="global_form_popup_message">
        <?php echo $this->translate("Changes saved.") ?>
    </div>
<?php endif; ?>

<script type="text/javascript">

    //  window.addEvent('domready', function(){
    //        //$('quantity-wrapper').setStyle('display', ($('quantity_unlimited-1').checked ?'none':'block'));
    //		});

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
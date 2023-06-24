<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  window.addEvent('domready', function() {
//    document.getElementById('product_type-wrapper').style.display = 'none';
//    document.getElementById('max_product-wrapper').style.display = 'none';
//    document.getElementById('comission_handling-wrapper').style.display = 'none';
//    document.getElementById('comission_rate-wrapper').style.display = 'none';
//    document.getElementById('comission_fee-wrapper').style.display = 'none';
//    document.getElementById('allow_selling_products-wrapper').style.display = 'none';
//    document.getElementById('online_payment_threshold-wrapper').style.display = 'none';
//    document.getElementById('transfer_threshold-wrapper').style.display = 'none';
//    document.getElementById('sitestoreproduct_main_files-wrapper').style.display = 'none';
//    document.getElementById('sitestoreproduct_sample_files-wrapper').style.display = 'none';          
//    document.getElementById('filesize_main-wrapper').style.display = 'none';
//    document.getElementById('filesize_sample-wrapper').style.display = 'none';
    showComissionType();
    showSellingOptions();
	});  
  
  function showComissionType(){
    if(document.getElementById('comission_handling')){
      if(document.getElementById('comission_handling').value == 1) {
        document.getElementById('comission_fee-wrapper').style.display = 'none';
        document.getElementById('comission_rate-wrapper').style.display = 'block';		
      } else{
        document.getElementById('comission_fee-wrapper').style.display = 'block';
        document.getElementById('comission_rate-wrapper').style.display = 'none';
      }
    }
  }
  
  function isDownloadable() {
    if( document.getElementById('product_type-downloadable').checked ){
      document.getElementById('sitestoreproduct_main_files-wrapper').style.display = 'block';
      document.getElementById('sitestoreproduct_sample_files-wrapper').style.display = 'block';
      document.getElementById('filesize_main-wrapper').style.display = 'block';
      document.getElementById('filesize_sample-wrapper').style.display = 'block';
    }else {
      document.getElementById('sitestoreproduct_main_files-wrapper').style.display = 'none';
      document.getElementById('sitestoreproduct_sample_files-wrapper').style.display = 'none';
      document.getElementById('filesize_main-wrapper').style.display = 'none';
      document.getElementById('filesize_sample-wrapper').style.display = 'none';
    }
  }
   
 function showStoreSettings(value) {
    if(document.getElementById('enable-0').checked){
      document.getElementById('product_type-wrapper').style.display = 'none';
      document.getElementById('max_product-wrapper').style.display = 'none';
      document.getElementById('comission_handling-wrapper').style.display = 'none';
      document.getElementById('comission_rate-wrapper').style.display = 'none';
      document.getElementById('comission_fee-wrapper').style.display = 'none';
      if(document.getElementById('allow_selling_products-wrapper'))
        document.getElementById('allow_selling_products-wrapper').style.display = 'none';
      document.getElementById('online_payment_threshold-wrapper').style.display = 'none';
      document.getElementById('transfer_threshold-wrapper').style.display = 'none';
      document.getElementById('sitestoreproduct_main_files-wrapper').style.display = 'none';
      document.getElementById('sitestoreproduct_sample_files-wrapper').style.display = 'none';          
      document.getElementById('filesize_main-wrapper').style.display = 'none';
      document.getElementById('filesize_sample-wrapper').style.display = 'none';
    }else {
      document.getElementById('product_type-wrapper').style.display = 'block';
      document.getElementById('max_product-wrapper').style.display = 'block';
      document.getElementById('comission_handling-wrapper').style.display = 'block';
      if(document.getElementById('allow_selling_products-wrapper'))
        document.getElementById('allow_selling_products-wrapper').style.display = 'block';
      document.getElementById('online_payment_threshold-wrapper').style.display = 'block';
      document.getElementById('transfer_threshold-wrapper').style.display = 'block';
      showComissionType();
      isDownloadable();
    }
 }
</script>
<?php if( !empty($this->siteStoreproductEnable) ): ?>
<script type="text/javascript">
    window.addEvent('domready', function() {
    showComissionType();
    if(document.getElementById('modules-sitestoreproduct')){
      storeEnable();
      document.getElementById('modules-sitestoreproduct').addEvent('click',function() {
        storeEnable();
      });
    }
	});   
  
  function storeEnable(){
    if (document.getElementById('modules-sitestoreproduct').checked == true) {
          document.getElementById('max_product-wrapper').style.display = 'block';
          document.getElementById('comission_handling-wrapper').style.display = 'block';
          if(document.getElementById('allow_selling_products-wrapper'))
            document.getElementById('allow_selling_products-wrapper').style.display = 'block';
          document.getElementById('online_payment_threshold-wrapper').style.display = 'block';
          document.getElementById('transfer_threshold-wrapper').style.display = 'block';
          showComissionType();
        } else {
          document.getElementById('max_product-wrapper').style.display = 'none';
          document.getElementById('comission_handling-wrapper').style.display = 'none';
          document.getElementById('comission_rate-wrapper').style.display = 'none';
          document.getElementById('comission_fee-wrapper').style.display = 'none';
          if(document.getElementById('allow_selling_products-wrapper'))
            document.getElementById('allow_selling_products-wrapper').style.display = 'none';
          document.getElementById('online_payment_threshold-wrapper').style.display = 'none';
          document.getElementById('transfer_threshold-wrapper').style.display = 'none';
        } 
  }
  function showComissionType(){
    if(document.getElementById('comission_handling')){
          if(document.getElementById('comission_handling').value == 1) {
            document.getElementById('comission_fee-wrapper').style.display = 'none';
            document.getElementById('comission_rate-wrapper').style.display = 'block';		
          } else{
            document.getElementById('comission_fee-wrapper').style.display = 'block';
            document.getElementById('comission_rate-wrapper').style.display = 'none';
          }
        }
  }
  
  
</script>
<?php endif; ?>
<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>

<?php if (count($this->navigation)) { ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php } ?>

<div class="sitestore_pakage_form">
	<div class="settings">
	  <?php echo $this->form->render($this) ?>
	</div>
</div>	

<script type="text/javascript">
  function setRenewBefore(){

    if($('duration-select').value=="forever"|| $('duration-select').value=="lifetime" || ($('recurrence-select').value!=="forever" && $('recurrence-select').value!=="lifetime")){
      $('renew-wrapper').setStyle('display', 'none');
      $('renew_before-wrapper').setStyle('display', 'none');
    }else{
      $('renew-wrapper').setStyle('display', 'block');
      if($('renew').checked)
        $('renew_before-wrapper').setStyle('display', 'block');
      else
        $('renew_before-wrapper').setStyle('display', 'none');
    }
  }
  $('duration-select').addEvent('change', function(){
    setRenewBefore();
  });
  window.addEvent('domready', function() {
    setRenewBefore();
  });
  
  function showSellingOptions(){
    if(document.getElementById('allow_selling_products-wrapper') && $('allow_selling_products-1').checked){
      showComissionType();
      $('sale_to_access_levels-wrapper').style.display = 'block';
      $('comission_handling-wrapper').style.display = 'block';
      $('online_payment_threshold-wrapper').style.display = 'block';
      $('transfer_threshold-wrapper').style.display = 'block';
      $('allow_non_selling_product_price-wrapper').style.display = 'none';
    }else{
      $('sale_to_access_levels-wrapper').style.display = 'none';
      $('comission_handling-wrapper').style.display = 'none';
      $('comission_fee-wrapper').style.display = 'none';
      $('comission_rate-wrapper').style.display = 'none';
      $('online_payment_threshold-wrapper').style.display = 'none';
      $('transfer_threshold-wrapper').style.display = 'none';
      $('allow_non_selling_product_price-wrapper').style.display = 'block';
    }
  }
</script>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  window.addEvent('domready', function() {
    <?php if( empty($this->isEnabledPackage) ) : ?>
    createStore();
    showComissionType();
    showSellingOptions();
    isDownloadable();
    <?php endif; ?>
//    disableElement();
	});  
  
  function createStore() {
    if( document.getElementById('allow_store_create-0').checked ){
      document.getElementById('allow_selling_products-wrapper').style.display = 'none';
      document.getElementById('online_payment_threshold-wrapper').style.display = 'none';
      document.getElementById('transfer_threshold-wrapper').style.display = 'none';
      document.getElementById('product_type-wrapper').style.display = 'none';
      document.getElementById('sitestoreproduct_main_files-wrapper').style.display = 'none';
      document.getElementById('sitestoreproduct_sample_files-wrapper').style.display = 'none';
      document.getElementById('filesize_main-wrapper').style.display = 'none';
      document.getElementById('filesize_sample-wrapper').style.display = 'none';         
      document.getElementById('max_product-wrapper').style.display = 'none';
      document.getElementById('comission_handling-wrapper').style.display = 'none';
      document.getElementById('comission_fee-wrapper').style.display = 'none';
      document.getElementById('comission_rate-wrapper').style.display = 'none';
    }else {
      document.getElementById('allow_selling_products-wrapper').style.display = 'block';
      document.getElementById('online_payment_threshold-wrapper').style.display = 'block';
      document.getElementById('transfer_threshold-wrapper').style.display = 'block';
      document.getElementById('product_type-wrapper').style.display = 'block';
      document.getElementById('sitestoreproduct_main_files-wrapper').style.display = 'block';
      document.getElementById('sitestoreproduct_sample_files-wrapper').style.display = 'block';
      document.getElementById('filesize_main-wrapper').style.display = 'block';
      document.getElementById('filesize_sample-wrapper').style.display = 'block';         
      document.getElementById('max_product-wrapper').style.display = 'block';
      document.getElementById('comission_handling-wrapper').style.display = 'block';
      document.getElementById('comission_fee-wrapper').style.display = 'block';
      document.getElementById('comission_rate-wrapper').style.display = 'block';
    }
  }
  
//  function disableElement() {
//    if( document.getElementById('allow_buy-0').checked ){
//      document.getElementById('allow_check-wrapper').style.display = 'none';      
//    }else {
//      document.getElementById('allow_check-wrapper').style.display = 'block';
//      if( document.getElementById('allow_check-0').checked ) {
//      }      
//    }
//  }
  
  function isDownloadable() {
    if(document.getElementById('product_type-downloadable')) {
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
  }
  
  function showComissionType(){
    // if(document.getElementById('comission_handling')){
          if(document.getElementById('comission_handling').value == 1) {
            document.getElementById('comission_fee-wrapper').style.display = 'none';
            document.getElementById('comission_rate-wrapper').style.display = 'block';		
          } else{
            document.getElementById('comission_fee-wrapper').style.display = 'block';
            document.getElementById('comission_rate-wrapper').style.display = 'none';
          }
       //  }
  }
  
  
</script>
<script type="text/javascript">
  <?php $user = Engine_Api::_()->user()->getViewer(); ?>
	window.addEvent('domready', function() {
		contactoption('<?php echo Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $user, 'contact') ?>');
	});

	var fetchLevelSettings =function(level_id){
		window.location.href= en4.core.baseUrl+'admin/sitestore/level/index/id/'+level_id;
	}

	function contactoption(option) {
		if(option == 1) {
			if($('contact_detail-wrapper')) {
				$('contact_detail-wrapper').style.display = 'block';
			}
		} 
		else {
			if($('contact_detail-wrapper')) {
				$('contact_detail-wrapper').style.display = 'none';
			}
		}
	}
</script>
<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'level'), $this->translate('Stores'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'settings','action'=>'level'), $this->translate('Products'), array())
    ?>
    </li>
  </ul>
</div>

<div class='clear seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<script type="text/javascript">
  function showSellingOptions(){
    if($('allow_selling_products-1').checked){
      showComissionType();
      $('comission_handling-wrapper').style.display = 'block';
      $('online_payment_threshold-wrapper').style.display = 'block';
      $('transfer_threshold-wrapper').style.display = 'block';
    }else{
      $('comission_handling-wrapper').style.display = 'none';
      $('comission_fee-wrapper').style.display = 'none';
      $('comission_rate-wrapper').style.display = 'none';
      $('online_payment_threshold-wrapper').style.display = 'none';
      $('transfer_threshold-wrapper').style.display = 'none';
    }
  }
</script>
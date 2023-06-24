<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-cart.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sr_sitestoreproduct_dashboard_content">
  <?php if( !empty($this->sitestoreproduct) && !empty($this->sitestore) ): ?>
    <?php echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header.tpl', array('sitestoreproduct'=>$this->sitestoreproduct, 'sitestore'=>$this->sitestore)); ?>
  <?php endif; ?>
  <ul class="sr_sitestoreproduct_browse_list">
  <?php if( !empty($this->bundleConfigProductsForm) ) : ?>
    <?php $productIdsArray = array(); ?>
    <?php foreach($this->bundleConfigProductsForm as $product_id => $configProductsForm) : ?>
    <li>
      <?php $productIdsArray[] = $product_id; ?>
      <?php  $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id); ?>
      <div class="sr_sitestoreproduct_browse_list_photo b_medium">
        <?php echo $this->htmlLink($sitestoreproduct->getHref(),$this->itemPhoto($sitestoreproduct, 'thumb.normal', $sitestoreproduct->getTitle())) ?>
      </div>
      <div class="sr_sitestoreproduct_browse_list_info">
        <div class="sr_sitestoreproduct_browse_list_info_header o_hidden">
          <div class="sr_sitestoreproduct_list_title">
            <?php echo $this->htmlLink($sitestoreproduct->getHref(), $sitestoreproduct->getTitle()); ?>
          </div>
        </div>

      <!-- SHOW CONFIGURATIONS PRE-FILLED VALUES -->
      <?php if( !empty($this->bundle_product_attributes[$product_id]) ) : ?>
        <div id="product_filled_attribute_<?php echo $product_id ?>">
          <?php $makeFieldValueArray = Engine_Api::_()->sitestoreproduct()->makeFieldValueArray($this->bundle_product_attributes[$product_id]); ?>
          <div class="sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light mtop10">
            <?php foreach($makeFieldValueArray as $key => $makeFieldValue) : ?>
              <?php echo "<b>$key</b>:  $makeFieldValue<br/>"; ?>
            <?php endforeach; ?>
          </div>
          <div class="sr_sitestoreproduct_browse_list_options clr">
            <a href="javascript:void(0)" onclick="reconfigProductAttribute(<?php echo $product_id ?>, 0);" class="buttonlink seaocore_icon_edit"><?php echo $this->translate("Edit Attributes") ?></a>
          </div>
        </div>
      <?php endif; ?>

      <!-- SHOW CONFIGURATIONS FORM TO FILL VALUES -->
      <div id="product_blank_attribute_<?php echo $product_id ?>" <?php if( !empty($this->bundle_product_attributes[$product_id]) ) : ?> style="display:none" <?php endif; ?> >
        <div id="configProductFormError_<?php echo $product_id ?>" style="display:none;" class="seaocore_txt_red">
          <?php echo $this->translate("Please specify product's option(s) before saving attributes for this product.") ?>
        </div>
        <div class="clr mtop10">
          <?php echo $configProductsForm->render($this); ?> 
        </div>
        
        <?php if( !empty($this->bundle_product_attributes[$product_id]) ) : ?>
          <div class="clr mtop5">
            <a href="javascript:void(0)" onclick="reconfigProductAttribute(<?php echo $product_id ?>, 1)">
              <?php echo $this->translate("Cancel") ?>
            </a>
          </div>
        <?php endif; ?>
      </div>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>

    <?php $productIdsString = @implode(",", $productIdsArray) ?>

    <div id="saveProductAttributeButton" class='buttons mbot10 clr' <?php if( !empty($this->bundle_product_attributes) ) : ?> style="display:none" <?php endif; ?>>
      <button id="bundle_product_attribute_save" type='button' onclick="checkBundleProductAttributes('<?php echo $productIdsString ?>');"><?php echo $this->translate("Save Attributes") ?></button>
      <div id="bundle_product_attribute_save_loading" class="mleft5" style="display: none;">
        <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/spinner.gif" />
      </div>
    </div>

  <script type="text/javascript">

  function reconfigProductAttribute(product_id, tempFlag)
  {
    if( tempFlag )
    {
      $("product_filled_attribute_"+product_id).style.display = 'block';
      $("product_blank_attribute_"+product_id).style.display = 'none';
    }
    else
    {
      $("product_filled_attribute_"+product_id).style.display = 'none';
      $("product_blank_attribute_"+product_id).style.display = 'block';
    }
    
    var productIdsArray = '<?php echo $productIdsString ?>'.split(',');

      for ( index = 0 ; index < productIdsArray.length; index++ )
      {
        if( ($("product_blank_attribute_"+productIdsArray[index]).style.display == 'block') )
        {
          $("saveProductAttributeButton").style.display = 'block';
          return;
        }
      }
      $("saveProductAttributeButton").style.display = 'none';
  }

  var isProductAttributeError = false;
  function checkBundleProductAttributes(productIdsString)
  {
    var productIdsArray = productIdsString.split(',');
    
    for ( index = 0 ; index < productIdsArray.length; index++ )
    {
      $("configProductFormError_"+productIdsArray[index]).style.display = 'none';
      if( $("product_blank_attribute_" + productIdsArray[index]).style.display == 'none' )
        continue;
      $('bundle_product_config_'+productIdsArray[index]).getElements('input, select, textarea, radio, checkbox', true).each(function(el){
        if(el.type != 'hidden' && (el.value == '' || el.value == null)) {
          isProductAttributeError = true;
          $("configProductFormError_"+productIdsArray[index]).style.display = 'block';
        }
      });
    }

    if( !isProductAttributeError )
      saveBundleProductAttributes(productIdsString);
  }

  function saveBundleProductAttributes(productIdsString)
  {
    var productIdsArray = productIdsString.split(',');
    var bundleProductConfigurations = '';

    for ( index = 0 ; index < productIdsArray.length; index++ )
    {
      bundleProductConfigurations += 'product_id_'+index+'=' + productIdsArray[index] + '&' + $("bundle_product_config_"+productIdsArray[index]).toQueryString();

      if( index != (productIdsArray.length - 1) )
        bundleProductConfigurations += '&';
    }

    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitestoreproduct/product/save-bundle-product-attribute',
      method: 'POST',
      onRequest: function(){
        $("bundle_product_attribute_save_loading").style.display = 'inline-block';
      },
      data : {
        format : 'json',
        product_id : '<?php echo $this->product_id ?>',
        bundleProductConfigurations : bundleProductConfigurations
      },    
      onSuccess : function(responseJson) {
        $("bundle_product_attribute_save_loading").style.display = 'none';
        window.location = '<?php echo $this->url(array('action' => 'bundle-product-attributes', 'product_id' => $this->product_id), 'sitestoreproduct_product_general', true) ?>';
      }
      }));
  }

  </script>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("No product found for adding product attributes.") ?>
      </span>
    </div>
  <?php endif; ?>
</div>
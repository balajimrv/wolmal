<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) :
  $closed = empty($this->sitestoreproduct->closed);
else:
  $closed = 1;
endif;
$viewer_email = empty($this->viewer_id) ? '' : $this->viewer->email;
?>
<?php
$reviewApi = Engine_Api::_()->sitestoreproduct();
$expirySettings = $reviewApi->expirySettings();
$approveDate = null;
if ($expirySettings == 2):
  $approveDate = $reviewApi->adminExpiryDuration();
endif;

$compare = $this->compareButtonSitestoreproduct($this->sitestoreproduct, $this->identity);

$show_msg = $tempVATvalues = $isFixed = 0;

$priceAfterDisocunt = Engine_Api::_()->sitestoreproduct()->getProductDiscount($this->sitestoreproduct, true, $this->productPriceOptions, 1);
$getPriceOfProductsAfterVAT = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($this->sitestoreproduct);
if (!empty($getPriceOfProductsAfterVAT) && !empty($priceAfterDisocunt) && isset($getPriceOfProductsAfterVAT['vat']) && !empty($getPriceOfProductsAfterVAT['vat']))
  $priceAfterDisocunt = @(float) $priceAfterDisocunt + @(float) $getPriceOfProductsAfterVAT['vat'];
if (!empty($getPriceOfProductsAfterVAT) && isset($getPriceOfProductsAfterVAT['show_msg']) && !empty($getPriceOfProductsAfterVAT['show_msg']))
  $show_msg = 1;


?>

<?php if ($approveDate && $this->sitestoreproduct->approved_date && $approveDate > $this->sitestoreproduct->approved_date): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('This product has beed expired.'); ?>
    </span>
  </div>
<?php endif; ?>

<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0) && !empty($this->sitestoreproduct->closed)) : ?>
  <div class="tip"> 
    <span> <?php echo $this->translate('This product has been closed by the owner.'); ?> </span>
  </div>
<?php endif; ?>

<div class="clr sr_sitestoreproduct_profile_info">
  <div  class="" style="width: 100%">
    <?php if (!empty($this->sitestoreproduct->newlabel)): ?>
      <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('NEW'); ?>"></i>
    <?php endif; ?>
    <div class="iscroll_carousal photo-carousal-wrapper" data-width="300" data-height="300">
      <div class='iscroll_carousal_wrapper prelative photo-carousal b_dark' data-itemcount="<?php echo $this->total_images ?>">
        <div class="iscroll_carousal_scroller" style="width: <?php echo $this->total_images * 300 ?>px">
          <?php $i = 0; ?>
          <ul class="">
            <li>
              <?php if ($this->sitestoreproduct->photo_id): ?>
                <?php $photo = $this->sitestoreproduct->getPhoto($this->sitestoreproduct->photo_id); ?>
                  <a href="<?php echo $photo->getHref() ?>" data-linktype='photo-gallery' class="photo-carousal-photo">
                    <span id="product_profile_picture" style='background-image:url(<?php echo $this->sitestoreproduct->getPhotoUrl(); ?>)'></span>
                  </a>
                <?php $i++; ?>
              <?php else: ?>
                <?php echo $this->itemPhoto($this->sitestoreproduct, 'thumb.main', '', array('align' => 'center')); ?>
              <?php endif; ?>
            </li>
            <?php foreach ($this->photo_paginator as $photo): ?>
              <?php
              if ($photo->file_id == $this->sitestoreproduct->photo_id):
                continue;
              endif;
              ?>
              <li class="liPhoto">            
                <a href="<?php echo $photo->getHref() ?>" data-linktype='photo-gallery' class="photo-carousal-photo">
                  <span  style='background-image:url(<?php echo $photo->getPhotoUrl(); ?>)'></span>
                </a>
              </li>
              <?php $i++; ?>
            <?php endforeach; ?>
          </ul>
        </div>       
      </div>
      <div class="iscroll_carousal_nav clr">
        <?php if($i>1): ?>
        <span class="iscroll_carousal_prev ui-icon ui-icon-caret-left" ></span>
        <span class="iscroll_carousal_next ui-icon ui-icon-caret-right" ></span>
        <ul class="iscroll_carousal_indicator" id="indicator">
          <?php for ($j = 1; $j <= $i; $j++): ?>
            <li class="<?php echo $j == 1 ? 'active' : '' ?>"><?php echo $j; ?></li>
          <?php endfor; ?>
        </ul>
        <?php endif; ?>
      </div> 
    </div>
  </div>

  <div class="ui-page-content clr" >
    <div class="clr">
      <b>
        <?php echo $this->sitestoreproduct->getTitle(); ?>
      </b>
    </div>
    <div class="cont-sep b_medium t_l"></div>
   
    
    <!--Store Information Block-->
 <?php if(!empty ($this->storeInfo)) : ?>
    <div class="sm-ui-cont-head clr">
      <div class="sm-ui-cont-author-photo">
        <?php echo $this->htmlLink($this->storeObj->getHref(), $this->itemPhoto($this->storeObj, 'thumb.icon')); ?>
      </div>
      <div class="sm-ui-cont-cont-info">
        <div class="sm-ui-cont-author-name"><?php echo $this->htmlLink($this->storeObj->getHref(), $this->storeObj->getTitle()); ?> </div>
        <div class="sm-ui-cont-cont-date">
          <?php echo $this->translate("(Store) - "); ?>
          <?php echo $this->translate(array('%s like', '%s likes', $this->storeObj->like_count), $this->locale()->toNumber($this->storeObj->like_count)) ?>
        </div>

        <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') && ($this->storeObj->rating > 0)): ?>
          <span class="clr dblock seaocore_txt_light">
            <?php
            $currentRatingValue = $this->storeObj->rating;
            $difference = $currentRatingValue - (int) $currentRatingValue;
            if ($difference < .5) {
              $finalRatingValue = (int) $currentRatingValue;
            } else {
              $finalRatingValue = (int) $currentRatingValue + .5;
            }
            ?>

            <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
              <?php for ($x = 1; $x <= $this->storeObj->rating; $x++): ?>
                <span class="rating_star_generic rating_star" ></span>
              <?php endfor; ?>
              <?php if ((round($this->storeObj->rating) - $this->storeObj->rating) > 0): ?>
                <span class="rating_star_generic rating_star_half" ></span>
              <?php endif; ?>
            </span>

            <?php echo $this->translate(array('%s review', '%s reviews', $this->storeObj->review_count), $this->locale()->toNumber($this->storeObj->review_count)) ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
    <div class="cont-sep b_medium t_l"></div>
    <?php if (strip_tags($this->sitestoreproduct->body)): ?>
      <div class="sm-ui-cont-cont-des">
        <?php echo $this->viewMore(strip_tags($this->sitestoreproduct->body), 300, 5000) ?>
      </div>
      <div class="cont-sep b_medium t_l"></div>
    <?php endif; ?>

    <div class="profile-price-info o_hidden">
      <?php echo Engine_Api::_()->sitestoreproduct()->getProductDiscount($this->sitestoreproduct); ?>

      <!--MANAGE STOCK-->
      <div class="clr t_l">
        <?php if (!empty($this->sitestoreproduct->stock_unlimited) || $this->sitestoreproduct->in_stock >= $this->sitestoreproduct->min_order_quantity) : ?>
          <div class="stock_a sitestoreproduct_item_availability"> <b><?php echo $this->translate("In Stock") ?> </b></div>
          <div>
            <?php
            if (empty($this->sitestoreproduct->stock_unlimited)) :
              echo $this->translate(array('%s Item left', '%s Items left', $this->sitestoreproduct->in_stock), $this->locale()->toNumber($this->sitestoreproduct->in_stock));
            endif;
            ?>
          </div>
        <?php else: ?>
          <div class="stock_a t_red"> <b><?php echo $this->translate("Out of Stock.") ?> </b></div>
        <?php endif; ?>

        <?php if ($expirySettings == 2): $exp = $this->sitestoreproduct->getExpiryTime(); ?>
          <div class="clr profile-price-info-stats">
            <?php echo $exp ? $this->translate("Expiry On: %s", $this->locale()->toDate($exp, array('size' => 'medium'))) : '' ?></div>
        <?php elseif ($expirySettings == 1 && $this->sitestoreproduct->end_date && $this->sitestoreproduct->end_date != '0000-00-00 00:00:00'): ?>
          <div class="clr profile-price-info-stats">
            <?php echo $this->translate("On Sale Till: %s", $this->locale()->toDate(strtotime($this->sitestoreproduct->end_date), array('size' => 'medium'))) ?></div>
        <?php endif; ?>
      </div>  
    </div>
      
    <div class="cont-sep b_medium t_l"></div>

    <?php if (empty($this->errorMessage) && empty($this->doNotRenderForm) && $this->option_id && ($this->sitestoreproduct->product_type == 'configurable' || $this->sitestoreproduct->product_type == 'virtual')): ?>

      <?php
      /* Include the common user-end field switching javascript */
      //echo $this->partial('_jsSwitchConfigurable.tpl', 'sitestoreproduct', array(
      //'topLevelId' => (int) @$this->topLevelId,
      //'topLevelValue' => (int) @$this->topLevelValue
      // ))
      ?>

      <div class="sitestoreproduct_pcbox clr">
          <?php if( empty($this->notAllowSelling) ) : ?>
        <div class='sitestoreproduct_pcbox_f'>
          <?php echo $this->form->render($this) ?>
        </div>
           <?php else: ?>
             <div class="tip"><span><?php echo $this->translate("This product is currently not available for purchase.") ?></span></div>
       <?php endif; ?>
      </div>
    <?php elseif ($this->doNotRenderForm): ?> 
      <div class="sitestoreproduct_pcbox clr">
        <?php if (!empty($this->out_of_stock_action)) : ?>
          <div id="notify_to_seller" class="mbot10">
            <div>
              <?php echo $this->translate("Notify me when this product is in stock:"); ?>
            </div>
            <span id="notify_to_me_email_error" class="t_red mtop5" style="display:none">
              <?php echo $this->translate("Please enter a valid Email Address") ?>
            </span>
            <div class="clr mtop10">
              <?php echo $this->translate("Email:"); ?>
              <input type="text" id="notify_to_seller_email" value="<?php echo $viewer_email ?>" />
              <button class="notify_btn" type="button" onclick="notifyToSeller(<?php echo $this->sitestoreproduct->product_id ?>)">
                <?php echo $this->translate("Notify Me") ?>
              </button>
              <span id="notify_to_me_loading" style="display: inline-block;"></span>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>     
      <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/_listInfoProductType.tpl'; ?>
    </div>
  </div>
<?php

echo $this->partial('_jsSwitch.tpl', 'fields', array(
))
?>

  <script type="text/javascript">
       var seao_dateFormat = '<?php echo Engine_Api::_()->seaocore()->getLocaleDateFormat(); ?>';
  var discounted_price;
  var combination_attribute_id = {};
  var variation_attribute_ids = {};
  var parent_attribute_id = {};
  var show_msg;
  var last_order = 0;
  var stock_unlimited;
  var in_stock;
  var isFixed;
  var vatValue;
  var show_item_left;
  var tempStarttimeFlag = 1;
  var tempEndtimeFlag = 1;

      
      
      
  function showChildOptions(element, field_id, product_id, order, price_array, max_order) {
    if (element.value != 0) {
//      $("#configuration_price_loading").innerHTML = '<img src="<?php// echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
//      $('#select_' + field_id).options[0].disabled = true;

      if (order != max_order) {
        combination_attribute_id[field_id] = element.value;
        if (show_item_left == 1)
            $("#items_left").html("");
      }
      else {
        combination_attribute_id[field_id] = element.value;
        if (show_item_left == 1)
          showItemLeft(combination_attribute_id);
      }
      //price_array = JSON.parse(price_array);
      if (order == 0)
        selectedFields = {};

      if (price_array[element.value] != null) {
        selectedFields[field_id] = price_array[element.value];
      }
      else {
        selectedFields[field_id] = 0.00;
      }

      var x;
      var configurable_price = 0;

      if (last_order == order)
        delete parent_attribute_id[order];

      if (order < last_order) {
        if (parent_attribute_id.length != 0) {
          for (var k in parent_attribute_id) {
            if (k == order || k > order)
              delete parent_attribute_id[k];
          }
        }
      }

      if (order == 0)
        parent_attribute_id = {};

      var url = sm4.core.baseUrl + 'sitestoreproduct/index/get-attribute-child';
      
      show_msg = <?php echo $show_msg; ?>;
    $.ajax({ 
        type: 'post', 
        url: url, 
        data: {
          'format': 'json',
          'combination_attribute_id': element.value,
          'parent_attribute_ids': parent_attribute_id,
          'field_id': field_id,
          'product_id': product_id,
          'order': order,
          'isUserEnd': true
        },
        success: function (responseJSON) { 
           var values = $.parseJSON(responseJSON.attributeArray);
          if (values != null) {
            var x;
            var count = 0;
            for (x = 0; x < values.length; x++) {
              var select = $('#select_' + values[x].field_id);
              selectedFields[values[x].field_id] = '0.00';
              if (values[x].order == order + 1) {

$('#select_' + values[x].field_id).append("<option value='"+values[x].value+"'>"+values[x].label+"</option>");
                count++;
              }
              else {
                select.empty();
                var opt = $('option');
                 opt.val("0");
                opt.html(sm4.core.language.translate('-- Please Select --'));
                select.append(opt);
              }
            }
          }
          
          for (x in selectedFields) {
            configurable_price += parseFloat(selectedFields[x]);
          }
          
//          if (show_msg == 1) {
//            if (!isFixed) {
//              configurable_price = configurable_price + vatValue;
//            } else {
//              configurable_price = configurable_price + ((vatValue * configurable_price) / 100);
//            }
//          }
          configurable_price += parseFloat(discounted_price);
         
          var url = sm4.core.baseUrl + 'sitestoreproduct/index/configuration-price';
          
          
          $.ajax({ 
        type: 'post', 
        url: url, 
        data: {
          'format': 'json',
              'price': configurable_price,
              show_msg: show_msg,
        },
        onSuccess: function(responseJSON) {
              $("configuration_price_loading").html("");
              $('.sr_sitestoreproduct_profile_price')[0].html(responseJSON);
            }
          });
       }
      });
      parent_attribute_id[order] = element.value;

      if (order != 0)
        last_order = order;
      else
        last_order = 0;
    }
  }

      
      

<?php if (!empty($this->otherInfo->discount) && empty($this->otherInfo->discount_permanant)) : ?>
  setTimeout(function() {
    var id = $.mobile.activePage.attr('id');
    var totalSeconds =<?php echo @strtotime($this->otherInfo->discount_end_date) - time() ?>;
    window.setInterval(function() {
      if (totalSeconds < 0)
        return;
      totalSeconds--;
      if (id !== $.mobile.activePage.attr('id'))
        return;
      var seconds = totalSeconds;
      var days = Math.floor(seconds / 86400);
      seconds -= days * 86400;

      var hours = Math.floor(seconds / 3600);
      seconds -= hours * (3600);

      var minutes = Math.floor(seconds / 60);
      seconds -= minutes * (60);

      //  en4.core.language.translate(array('%s day', '%s days', days), Number(days));
      var timerStr = ((days > 0) ? days + sm4.core.language.translate(" days ") : "") + LeadingZero(hours) + ":" + LeadingZero(minutes) + ":" + LeadingZero(seconds) + sm4.core.language.translate(" hrs");
      $.mobile.activePage.find('#discount_price_timer').html(timerStr);


      function LeadingZero(time)
      {
        return (time < 10) ? "0" + time : +time;
      }
    }, 1000);
  }, 200);
<?php endif; ?>



sm4.core.runonce.add(function() {



  if ($.mobile.activePage.find('#add_to_cart').length)
  {
    $.mobile.activePage.find('#add_to_cart').on('submit', function(event) {
      var errorMessage = '<?php echo $this->errorMessage; ?>';

      if (errorMessage != '') {
        if (!$.mobile.activePage.find('#add_to_cart_message').length) {
          var wrapperDiv = $('<div id="add_to_cart_message" class="review_error sitestoreproduct_form_error" />');
          wrapperDiv.html('<span><?php echo $this->string()->escapeJavascript($this->errorMessage); ?></span>');
          wrapperDiv.insertBefore($.mobile.activePage.find('#add_to_cart .form-elements'));
          //, 'before');
        }

        return false;
      }
      var radioErrorFlag = false;
           if (($(this).attr('type') == 'radio')) {
            var radios = $(el);
            for (i = 0; i < radios.length; i++) {
              if (radios[i].checked) {
                radioErrorFlag = 1;
                break;
              }
            }
          }

      var form_error = 0;
      $.mobile.activePage.find('#add_to_cart').find('input, select, textarea, radio, checkbox').each(function(el) {
      
        if ($(this).attr('type') != 'hidden' && ($(this).val() == '' || $(this).val() == null || $(this).val() == 0)) {
          if (!$.mobile.activePage.find('#add_to_cart_message').length) {
            var wrapperDiv = $('<div id="add_to_cart_message" class="review_error sitestoreproduct_form_error" />');
            wrapperDiv.html('<span><?php echo $this->string()->escapeJavascript($this->translate('Please specify product\'s option(s) before adding this product into your cart.')); ?></span>');
            wrapperDiv.insertBefore($.mobile.activePage.find('#add_to_cart .form-elements'));
            //, 'before');
          }

          form_error = 1;
        }

//             if ((($(this).attr('type') == 'radio')) || ($(this).attr('type') != 'hidden' && ($(this).val() == '' || $(this).val() == null))) {
//            if (!$.mobile.activePage.find('#add_to_cart_message').length) {
//              var wrapperDiv =  $('<div id="add_to_cart_message" class="review_error sitestoreproduct_form_error" />');
//              wrapperDiv.html("<span><?php// echo $this->translate('Please select the desired product options from below before adding this product into your cart.'); ?></span>");
//              wrapperDiv.insertBefore($.mobile.activePage.find('#add_to_cart .form-elements'));
//            }
//
//            form_error = 1;
//          }
     });
      var stock_unlimited = <?php echo $this->sitestoreproduct->stock_unlimited; ?>;
      var in_stock = <?php echo $this->sitestoreproduct->in_stock; ?>;
      var quantity = $.mobile.activePage.find('#quantity').val();
      if (stock_unlimited == 0 && quantity > in_stock) {

        if (!$.mobile.activePage.find('#add_to_cart_message').length) {
          var wrapperDiv = $('<div id="add_to_cart_message" class="review_error sitestoreproduct_form_error" />');
          if (in_stock == 1)
            wrapperDiv.html('<span><?php echo $this->string()->escapeJavascript($this->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1.")); ?></span>');
          else
            wrapperDiv.html('<span><?php echo $this->string()->escapeJavascript($this->translate("Only %s quantities of this product are available in stock. Please enter the quantity less than or equal to %s.", $this->sitestoreproduct->in_stock, $this->sitestoreproduct->in_stock)); ?></span>');
          wrapperDiv.insertBefore($.mobile.activePage.find('#add_to_cart .form-elements'));
          //, 'before');
        }

        form_error = 1;
      }
      
  
      

      if (form_error == 1) {
        return false;
      }
    });
  }
});

  </script>
  <style type="text/css">
    .sitestoreproduct_form_error{
      color: red;
    }
  </style>

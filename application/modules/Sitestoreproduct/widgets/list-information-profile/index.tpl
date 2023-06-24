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

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/sitestoreproduct_zoom.js'); ?>

<?php if (!empty($this->like_button) && $this->like_button == 1) : ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php endif; ?>

<?php
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) :
  $closed = empty($this->sitestoreproduct->closed);
else:
  $closed = 1;
endif;
$viewer_email = empty($this->viewer_id) ? '' : $this->viewer->email;
?>
<script type="text/javascript">
  tempImageZoomWidth = 380;
  tempImageZoomHeight = 480;
  var seaocore_content_type = 'sitestoreproduct_product';
  var seaocore_like_url = en4.core.baseUrl + 'seaocore/like/like';
  var selectedFields = {};
  function preventDefault(e) {
    if (e.currentTarget.allowDefault) {
      return;
    }
    e.preventDefault();
  }
</script>

<?php
$reviewApi = Engine_Api::_()->sitestoreproduct();
$expirySettings = $reviewApi->expirySettings();
$approveDate = null;
if ($expirySettings == 2):
  $approveDate = $reviewApi->adminExpiryDuration();
endif;

$compare = $this->compareButtonSitestoreproduct($this->sitestoreproduct, $this->identity);
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
  <div id="profile_photo" class="sr_sitestoreproduct_profile_photo_wrapper b_medium">
    <?php if (!empty($this->sitestoreproduct->newlabel)): ?>
      <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('NEW'); ?>"></i>
<?php endif; ?>

    <div class='sr_sitestoreproduct_profile_photo prelative <?php if ($this->can_edit): ?>sr_sitestoreproduct_photo_edit_wrapper<?php endif; ?>'>
<?php if (!empty($this->can_edit)) : ?>
        <a class='sr_sitestoreproduct_photo_edit' href="<?php echo $this->url(array('action' => 'change-photo', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_dashboard", true) ?>">
          <i class="sr_sitestoreproduct_icon"></i>
        <?php echo $this->translate('Change Picture'); ?>        
        </a>
<?php endif; ?>
      <table>
        <tr>
          <td>
            <?php if ($this->sitestoreproduct->photo_id): ?>
              <?php $photo = $this->sitestoreproduct->getPhoto($this->sitestoreproduct->photo_id); ?>
  <?php if (empty($this->isQuickView) && !empty($photo)) : ?>
                <a id="sitestoreproduct_product_image_zoom" href="<?php echo $this->sitestoreproduct->getPhotoUrl(); ?>" <?php if (SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");
          return false;' <?php endif; ?>>
                  <img id="product_profile_picture" alt=""  src='<?php echo $this->sitestoreproduct->getPhotoUrl(); ?>' />
                </a>
    <?php $notShowImageInLightBox = true; ?>
  <?php else: ?>
                <a id="sitestoreproduct_product_image_zoom" href="<?php echo $this->sitestoreproduct->getPhotoUrl(); ?>" >
                  <img id="product_profile_picture" alt=""  src='<?php echo $this->sitestoreproduct->getPhotoUrl(); ?>' />
                </a>
                  <?php $notShowImageInLightBox = false; ?>
                <?php endif; ?>
              <div id="product_profile_magnify_message" class="f_small pabsolute txt_center" style="display: none">
              <?php echo $this->translate("Roll over image to magnify") ?>
              </div>
            <?php else: ?>
  <?php echo $this->itemPhoto($this->sitestoreproduct, 'thumb.main', '', array('align' => 'center')); ?>
<?php endif; ?>
          </td>
        </tr>
      </table>
    </div>

      <?php $widgetContent = $this->content()->renderWidget("sitestoreproduct.photos-carousel", array('includeInWidget' => $this->identity, 'minMum' => 2, 'itemCount' => 3, 'isQuickView' => $this->isQuickView)) ?>
      <?php if (strlen($widgetContent) > 15): ?>
      <div class="b_medium sr_sitestoreproduct_photoscarousel o_hidden">
      <?php echo $widgetContent ?>
      </div>
    <?php endif; ?>

        <?php if ($this->sitestoreproduct->photo_id && empty($this->isQuickView) && !empty($photo)): ?>
      <p class="mtop10 mbot5">
        <a href="<?php echo $photo->getHref(); ?>" <?php if (false): //(SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");
        return false;' <?php endif; ?>>
          <?php if (strlen($widgetContent) > 15): ?>
            <?php echo $this->translate('Click on above images to view full picture'); ?>
  <?php else: ?>
        <?php echo $this->translate('Click on above image to view full picture'); ?>
      <?php endif; ?>
        </a>
      </p>
<?php endif; ?>
  </div>

  <div class="sr_sitestoreproduct_profile_content">
    <div class="sr_sitestoreproduct_profile_title">
      <h2>
      <?php echo $this->sitestoreproduct->getTitle(); ?>
      </h2>

      <?php
      //LIKE BUTTON WORK
      if (empty($this->isQuickView)) :
        $viewer_id = $this->viewer_id;
        ?>
        <?php if (!empty($this->like_button) && $this->like_button == 1 && !empty($viewer_id)) : ?>
          <?php
          if (!empty($this->viewer)) {
            $resourceId = Engine_Api::_()->core()->getSubject()->getIdentity();
            $check_availability = Engine_Api::_()->getApi('like', 'seaocore')->hasLike('sitestoreproduct_product', $resourceId);
            if (is_array($check_availability)) {
              if (count($check_availability) > 0 && array_key_exists('like_id', $check_availability[0])) {
                $check_availability = $check_availability[0]['like_id'];
              } else {
                $check_availability = 0;
              }
            }
            if (!empty($check_availability)) {
              $label = 'Unlike this';
              $unlike_show = "display:inline-block;";
              $like_show = "display:none;";
              $like_id = $check_availability;
            } else {
              $label = 'Like this';
              $unlike_show = "display:none;";
              $like_show = "display:inline-block;";
              $like_id = 0;
            }
          }
          ?>
    <?php if (empty($this->sitestoreproduct_like)) {
      exit();
    } ?>
          <div class="seaocore_like_button" id="sitestoreproduct_product_unlikes_<?php echo $resourceId; ?>" style ='<?php echo $unlike_show; ?>' >
            <a href="javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resourceId; ?>', 'sitestoreproduct_product');">
              <i class="seaocore_like_thumbdown_icon"></i>
              <span><?php echo $this->translate('Unlike') ?></span>
            </a>
          </div>
          <div class="seaocore_like_button" id="sitestoreproduct_product_most_likes_<?php echo $resourceId; ?>" style ='<?php echo $like_show; ?>'>
            <a href="javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resourceId; ?>', 'sitestoreproduct_product');">
              <i class="seaocore_like_thumbup_icon"></i>
              <span><?php echo $this->translate('Like') ?></span>
            </a>
          </div>
          <input type ="hidden" id = "sitestoreproduct_product_like_<?php echo $resourceId; ?>" value = '<?php echo $like_id; ?>' />
  <?php elseif ($this->like_button == 2 && $this->success_showFBLikeButton) : ?>
    <?php echo $this->content()->renderWidget("Facebookse.facebookse-commonlike", array('module_current' => 'sitestoreproduct')); ?>
  <?php endif; ?>
    <?php endif; ?>
    <?php //LIKE BUTTON WORK ?>

    </div>

    <!--Store Information Block-->
<?php if (!empty($this->storeInfo)) : ?>
      <div class="clr sitestoreproduct_psi o_hidden mtop10 mbot5">
        <div class="fleft mright5">
            <?php echo $this->htmlLink($this->storeObj->getHref(), $this->itemPhoto($this->storeObj, 'thumb.icon')); ?>
        </div>
        <div class="o_hidden">
          <b><?php echo $this->htmlLink($this->storeObj->getHref(), $this->storeObj->getTitle()); ?> </b><br/>
          <span class="seaocore_txt_light">
            <?php echo $this->translate("(Store) - "); ?>
            <?php echo $this->translate(array('%s like', '%s likes', $this->storeObj->like_count), $this->locale()->toNumber($this->storeObj->like_count)) ?>
          </span>
          <span class="clr dblock seaocore_txt_light">
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') && ($this->storeObj->rating > 0)): ?>
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
      <?php endif; ?>
          </span>
        </div>
      </div>
      <?php endif; ?>

    <?php if (!empty($this->showDescription) && strip_tags($this->sitestoreproduct->body)): ?>
      <div class="sr_sitestoreproduct_profile_information_des clr">
      <?php echo $this->viewMore(strip_tags($this->sitestoreproduct->getDescription())) ?>
      </div>
    <?php endif; ?>

    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0) && !empty($this->sitestoreproduct->location) && !empty($this->isQuickView)) : ?>
      <div class="mbot10 clr">
        <?php  echo $this->translate("Location: "); echo $this->translate($this->sitestoreproduct->location); ?>
        - <b>
          <?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->sitestoreproduct->product_id, 'resouce_type' => 'sitestoreproduct_product'), $this->translate("Get Directions"), array('class' => 'smoothbox')) ; ?>
          </b>
        </div>
      <?php endif; ?>

<?php $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($this->sitestoreproduct->store_id);

$temp_non_selling_product_price = Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($this->sitestoreproduct->store_id);

if ((!empty($temp_allowed_selling) && !empty($this->sitestoreproduct->allow_purchase))|| !empty($temp_non_selling_product_price)):
  ?>
      <div class="sitestoreproduct_profile_price_info o_hidden">
          <?php echo Engine_Api::_()->sitestoreproduct()->getProductDiscount($this->sitestoreproduct, true, $this->productPriceOptions, null, $this->isQuickView); ?>

        <!--MANAGE STOCK-->
        <div class="right fleft">
            <?php if ((!empty($this->sitestoreproduct->stock_unlimited) || $this->sitestoreproduct->in_stock >= $this->sitestoreproduct->min_order_quantity ) && empty($this->out_of_stock)) : ?>
              <?php if (!empty($this->productInventory)) : ?>
              <div class="stock_a sitestoreproduct_item_availability"> <?php echo $this->translate("In Stock") ?> </div>
              <?php endif; ?>

            <div id="items_left">
            <?php
            if (empty($this->sitestoreproduct->stock_unlimited)) :
              echo $this->translate(array('%s Item left', '%s Items left', $this->sitestoreproduct->in_stock), $this->locale()->toNumber($this->sitestoreproduct->in_stock));
            endif;
            ?>
            </div>
          <?php else: ?>
            <div class="stock_a seaocore_txt_red"> <?php echo $this->translate("Out of Stock.") ?> </div>
          <?php endif; ?>

      <?php
      if ($expirySettings == 2): $exp = $this->sitestoreproduct->getExpiryTime();
        echo '<div class="sr_sitestoreproduct_profile_information_stats seaocore_txt_light">' . $exp ? $this->translate("Expiry On: %s", $this->locale()->toDate($exp, array('size' => 'medium'))) : '' . '</div>';
      elseif ($expirySettings == 1 && $this->sitestoreproduct->end_date && $this->sitestoreproduct->end_date != '0000-00-00 00:00:00'):
        echo '<div class="sr_sitestoreproduct_profile_information_stats seaocore_txt_light">' . $this->translate("On Sale Till: %s", $this->locale()->toDate(strtotime($this->sitestoreproduct->end_date), array('size' => 'medium'))) . '</div>';
      endif;
      ?>
        </div>  
      </div>    
    <?php endif; ?>

    <?php if (empty($this->errorMessage) && empty($this->doNotRenderForm) && $this->option_id && ($this->sitestoreproduct->product_type == 'configurable' || $this->sitestoreproduct->product_type == 'virtual')): ?>

        <?php
        /* Include the common user-end field switching javascript */
        echo $this->partial('_jsSwitchConfigurable.tpl', 'sitestoreproduct', array(
                //'topLevelId' => (int) @$this->topLevelId,
                //'topLevelValue' => (int) @$this->topLevelValue
        ))
        ?>

      <div class="sitestoreproduct_pcbox clr">
      <?php if (Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($this->sitestoreproduct->store_id)) : ?>
          <div class='sitestoreproduct_pcbox_f'>
            <div class="fleft"><?php echo $this->form->render($this) ?></div>
          </div>
        <!--WORK FOR SHOWING PAYMENT METHODS START-->
            <?php if (!empty($this->payWithString)): ?>
              <div class="mtop10 seaocore_txt_light clr">
                <?php echo $this->translate('Pay with:') ." ". $this->payWithString;
                $payWithString = ''
                ?>
              </div>
            <?php endif; ?>
            <!--WORK FOR SHOWING PAYMENT METHODS ENDS -->
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
            <span id="notify_to_me_email_error" class="seaocore_txt_red mtop5" style="display:none">
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
        <?php else: ?>
          <div class="tip"><span><?php echo $this->translate("This product is currently not available for purchase.") ?></span></div>
          <?php endif; ?>
        <?php if (!empty($compare) || !empty($this->create_review)): ?>
          <?php if (!empty($compare)): ?>
            <span class="btnlink comparelink"> 
              <?php echo $compare ?>
            </span>
          <?php endif; ?>
          <span class="btnlink wishlistlink">
        <?php echo $this->addToWishlistSitestoreproduct($this->sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '')); ?>
          </span>
        <?php if (!empty($this->create_review)): ?>
            <span class="btnlink reviewlink">
          <?php echo $this->content()->renderWidget("sitestoreproduct.review-button", array('product_guid' => $this->sitestoreproduct->getGuid(), 'product_profile_page' => 1, 'identity' => $this->identity, 'isProductProfile' => 1, 'isQuickView' => $this->isQuickView)) ?>
            </span>
        <?php endif; ?>
        </div>
      <?php endif; ?>

    <?php endif; ?>     
    <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_listInfoProductType.tpl'; ?>

    <?php if ($this->gutterNavigation && $this->actionLinks && empty($this->isQuickView)): ?>
      <?php
$options = array('ulClass' => 'sr_sitestoreproduct_information_gutter_options b_medium clr');
      echo ($this->navigation()->menu()->renderMenu($this->gutterNavigation, $options));
      
//      echo $this->navigation()
//              ->menu()
//              ->setContainer($this->gutterNavigation)
//              ->setUlClass('sr_sitestoreproduct_information_gutter_options b_medium clr')
//              ->render();
      ?>
<?php endif; ?>

<?php if (!empty($this->isQuickView)) : ?>
      <div class="clr mtop10">
        <b><?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->translate("Show More Details &raquo")); ?></b>
      </div>  
<?php endif; ?>
  </div>



</div>
<div class="clr widthfull"></div>

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

<?php if (!empty($this->otherInfo->discount) && empty($this->otherInfo->discount_permanant) && strtotime($this->otherInfo->discount_start_date) <= time()) : ?>
    setTimeout(function() {
      createTimer("discount_price_timer", <?php echo @strtotime($this->otherInfo->discount_end_date) - time() ?>)
    }, 200);
<?php endif; ?>

  en4.core.runonce.add(function() {
    stock_unlimited = <?php echo $this->sitestoreproduct->stock_unlimited; ?>;
    in_stock = <?php echo $this->sitestoreproduct->in_stock; ?>;
//    show_item_left = <?php //echo $this->show_items_left;  ?>;
    if ($('add_to_cart'))
    {
      $('submit_addtocart').innerHTML = '';
      new Element('span', {
        'html': '<?php echo $this->translate(" Add to Cart ") ?>'
      }).inject($('submit_addtocart'));
      document.getElementById('submit_addtocart').addClass('add_to_cart_button');

      $('add_to_cart').addEvent('submit', function(event) {
        var errorMessage = '<?php echo $this->errorMessage; ?>';

        if (errorMessage != '') {
          if (!$('add_to_cart_message')) {
            var wrapperDiv = document.createElement("div");
            wrapperDiv.id = 'add_to_cart_message';
            wrapperDiv.setAttribute("class", "review_error sitestoreproduct_form_error");
            wrapperDiv.innerHTML = '<span><?php echo $this->errorMessage; ?></span>';
            wrapperDiv.inject($('add_to_cart').getElement('.form-elements'), 'before');
          }
          return false;
        }

<?php if ($this->sitestoreproduct->product_type == 'virtual' && !empty($this->dateTimeSelector)) : ?>
          if (seao_dateFormat == 'dmy') {
            userSelectedStartTime = en4.seaocore.covertdateDmyToMdy($('starttime-date').value);
            userSelectedEndTime = en4.seaocore.covertdateDmyToMdy($('endtime-date').value);
          } else {
            userSelectedStartTime = $('starttime-date').value;
            userSelectedEndTime = $('endtime-date').value;
          }

          var selectedDateDifference = new Date(userSelectedStartTime).diff(new Date(userSelectedEndTime), 'day')
          if (selectedDateDifference < 0) {
            alert("To date should be greater than From date.");
            return false;
          }
<?php endif; ?>

        var form_error = 0;
        $('add_to_cart').getElements('radio, select, checkbox, multi_select, multi_checkbox', true).each(function(el) {
          var radioErrorFlag = 0;
          if (el.type != 'hidden' && el.name == 'quantity' && /*(el.value < 1 || el.value % 1 !== 0)*/ !(/^[1-9]\d*/.test(el.value))) {
            if (!$('add_to_cart_quantity_message')) {
              var wrapperDiv = document.createElement("div");
              wrapperDiv.id = 'add_to_cart_quantity_message';
              wrapperDiv.setAttribute("class", "review_error sitestoreproduct_form_error");
              wrapperDiv.innerHTML = "<span><?php echo $this->translate('Please enter a valid quantity.'); ?></span>";
              wrapperDiv.inject($('add_to_cart').getElement('.form-elements'), 'before');
            }
            form_error = 1;
          }

          if ((el.type == 'radio')) {
            var radios = document.getElementsByName(el.name);
            for (i = 0; i < radios.length; i++) {
              if (radios[i].checked) {
                radioErrorFlag = 1;
                break;
              }
            }
          }

          if ((el.type == 'select-one')) {
            var field_id = el.id.split("_")[1];
            combination_attribute_id[field_id] = el.value;
          }


          if (((el.type == 'radio') && (radioErrorFlag == 0)) || (el.type != 'hidden' && (el.value == '' || el.value == null || el.value == 0))) {
            if (!$('add_to_cart_message')) {
              var wrapperDiv = document.createElement("div");
              wrapperDiv.id = 'add_to_cart_message';
              wrapperDiv.setAttribute("class", "review_error sitestoreproduct_form_error");
              wrapperDiv.innerHTML = "<span><?php echo $this->translate('Please select the desired product options from below before adding this product into your cart.'); ?></span>";
              wrapperDiv.inject($('add_to_cart').getElement('.form-elements'), 'before');
            }

            form_error = 1;
          }
        });


        var stock_unlimited = <?php echo $this->sitestoreproduct->stock_unlimited; ?>;
        var in_stock = <?php echo $this->sitestoreproduct->in_stock; ?>;
        var quantity = $('quantity').value;

        if(!(/^[1-9]\d*/.test(quantity))){
            if (!$('add_to_cart_quantity_message')) {
              var wrapperDiv = document.createElement("div");
              wrapperDiv.id = 'add_to_cart_quantity_message';
              wrapperDiv.setAttribute("class", "review_error sitestoreproduct_form_error");
              wrapperDiv.innerHTML = "<span><?php echo $this->translate('Please enter a valid quantity.'); ?></span>";
              wrapperDiv.inject($('add_to_cart').getElement('.form-elements'), 'before');
            }
            form_error = 1;
        }else if (stock_unlimited == 0 && quantity > in_stock) {
          if (!$('add_to_cart_message')) {
            var wrapperDiv = document.createElement("div");
            wrapperDiv.id = 'add_to_cart_message';
            wrapperDiv.setAttribute("class", "review_error sitestoreproduct_form_error");
            if (in_stock == 1)
              wrapperDiv.innerHTML = '<span><?php echo $this->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1."); ?></span>';
            else
              wrapperDiv.innerHTML = '<span><?php echo $this->translate("Only %s quantities of this product are available in stock. Please enter the quantity less than or equal to %s.", $this->sitestoreproduct->in_stock, $this->sitestoreproduct->in_stock); ?></span>';
            wrapperDiv.inject($('add_to_cart').getElement('.form-elements'), 'before');
          }

          form_error = 1;
        }
        
        if (form_error == 1) {
          return false;
        }

        if (form_error != 1){
            event.preventDefault();
            var redirecturl = window.location.href;
            var product_id = '<?php echo $this->sitestoreproduct->product_id;  ?>';
            var url = en4.core.baseUrl + 'sitestoreproduct/index/show-combination-quantity';
                tempReq = new Request.JSON({
                method: 'post',
               'url': url,
                'data' : {
                  'format' : 'json',
                  'combination_attribute_ids' : combination_attribute_id,
                 'product_id': product_id,
                  'quantity': quantity
               },
            onSuccess : function(responseJSON) {
                  if(responseJSON.error_message != 0){
                    if(!$('add_to_cart_message')) {
                        var wrapperDiv = document.createElement("div");
                        wrapperDiv.id = 'add_to_cart_message';    
                        wrapperDiv.setAttribute("class", "review_error sitestoreproduct_form_error");    
                        wrapperDiv.innerHTML = '<span>' + responseJSON.error_message + '</span>';
                        wrapperDiv.inject($('add_to_cart').getElement('.form-elements'), 'before');
                    }
                 }else{
                     $('add_to_cart').submit();
                 }
               }
              });
              tempReq.send();
        }

      });
    }

<?php if ($this->sitestoreproduct->product_type == 'virtual' && !empty($this->dateTimeSelector)) : ?>
      if ($('starttime-minute'))
        $('starttime-minute').style.display = 'none';
      if ($('starttime-ampm'))
        $('starttime-ampm').style.display = 'none';
      if ($('starttime-hour'))
        $('starttime-hour').style.display = 'none';
      if ($('endtime-minute'))
        $('endtime-minute').style.display = 'none';
      if ($('endtime-ampm'))
        $('endtime-ampm').style.display = 'none';
      if ($('endtime-hour'))
        $('endtime-hour').style.display = 'none';

      initializeCalendarDate(seao_dateFormat, cal_starttime, cal_endtime, 'starttime', 'endtime');
      cal_starttime_onHideStart();
<?php endif; ?>

    if ($('sitestoreproduct_product_image_zoom')) {
      productProfileImg.src = $('sitestoreproduct_product_image_zoom').getProperty('href');
      setTimeout(function() {
        tempImgWidth = productProfileImg.width;
        tempImgHeight = productProfileImg.height;
        if (tempImgWidth >= 380 && tempImgHeight >= 480)
          $("product_profile_magnify_message").style.display = 'block';
        else
          $('sitestoreproduct_product_image_zoom').addEvent('click', function(e) {
            e.stop();
          });
      }, 100)


<?php if (empty($notShowImageInLightBox)) : ?>
        notShowImageInLightBox = true;
<?php else: ?>
        notShowImageInLightBox = false;
<?php endif; ?>
      tempFlag = true;
      $('sitestoreproduct_product_image_zoom').addEvent('mouseover', function() {
        sitestoreproductProfileImageMagnify();
      });
    }

<?php
$show_msg = $tempVATvalues = $isFixed = 0;

$priceAfterDisocunt = Engine_Api::_()->sitestoreproduct()->getProductDiscount($this->sitestoreproduct, true, $this->productPriceOptions, 1);
$getPriceOfProductsAfterVAT = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($this->sitestoreproduct);
if (!empty($getPriceOfProductsAfterVAT) && !empty($priceAfterDisocunt) && isset($getPriceOfProductsAfterVAT['vat']) && !empty($getPriceOfProductsAfterVAT['vat']))
  $priceAfterDisocunt = @(float) $priceAfterDisocunt + @(float) $getPriceOfProductsAfterVAT['vat'];
if (!empty($getPriceOfProductsAfterVAT) && isset($getPriceOfProductsAfterVAT['show_msg']) && !empty($getPriceOfProductsAfterVAT['show_msg']))
  $show_msg = 1;

$getPriceOfProductsAfterVAT = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($this->sitestoreproduct);

if (!empty($getPriceOfProductsAfterVAT)):
  $priceAfterDisocunt = $getPriceOfProductsAfterVAT['display_product_price'];
  $productTaxProduct = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct');
  $productTaxRateProduct = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');
  $storeVatDetail = $productTaxProduct->fetchRow(array('store_id = ?' => $this->storeObj->store_id, 'is_vat = ?' => 1));
  if (!empty($storeVatDetail)) {
    $vatId = $storeVatDetail->tax_id;
    $storeVatRateDetail = $productTaxRateProduct->fetchRow(array('tax_id = ?' => $vatId));
    if (!empty($storeVatRateDetail)) {
      $tempVATvalues = $storeVatRateDetail->tax_value;
      $isFixed = $storeVatRateDetail->handling_type;
    }
  }
  if (isset($getPriceOfProductsAfterVAT['show_msg']) && !empty($getPriceOfProductsAfterVAT['show_msg'])):
    $show_msg = 1;
  endif;
else:
  $priceAfterDisocunt = $priceAfterDisocunt = Engine_Api::_()->sitestoreproduct()->getProductDiscount($this->sitestoreproduct, true, $this->productPriceOptions, 1);
endif;
?>
    discounted_price = '<?php echo $priceAfterDisocunt; ?>';
    show_msg = <?php echo $show_msg; ?>;
    isFixed = <?php echo $isFixed; ?>;
    vatValue = '<?php echo $tempVATvalues; ?>';

  });

<?php if ($this->sitestoreproduct->product_type == 'virtual' && !empty($this->dateTimeSelector)) : ?>
  <?php if (!empty($this->priceRangeText)) : ?>
    <?php if ($this->priceRangeText == 'per_day') : ?>
      <?php $dateDiffBasedon = 'day'; ?>
    <?php elseif ($this->priceRangeText == 'weekly') : ?>
      <?php $dateDiffBasedon = 'week'; ?>
    <?php elseif ($this->priceRangeText == 'monthly') : ?>
      <?php $dateDiffBasedon = 'month'; ?>
    <?php elseif ($this->priceRangeText == 'yearly') : ?>
      <?php $dateDiffBasedon = 'year'; ?>
    <?php endif; ?>
  <?php endif; ?>

    var cal_starttime_onHideStart = function() {
      cal_starttimeDate_onHideStart(seao_dateFormat, cal_starttime, cal_endtime, 'starttime', 'endtime');
  <?php if (!empty($this->showQuantityBox) && !empty($dateDiffBasedon)) : ?>
        checkDateDifference('<?php echo $dateDiffBasedon ?>');
  <?php endif; ?>

      if (tempStarttimeFlag != 1) {
        $("calendar_output_span_starttime-date").style.display = 'block';
      } else {
        tempStarttimeFlag = 2;
      }

      // check end date and make it the same date if it's too
      cal_endtime.calendars[0].start = new Date($('starttime-date').value);
      // redraw calendar
      cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
      cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
    };

    var cal_endtime_onHideStart = function() {
  <?php if (!empty($this->showQuantityBox) && !empty($dateDiffBasedon)) : ?>
        checkDateDifference('<?php echo $dateDiffBasedon ?>');
  <?php endif; ?>

      if (tempEndtimeFlag != 1) {
        $("calendar_output_span_endtime-date").style.display = 'block';
      } else {
        tempEndtimeFlag = 2;
      }

      cal_starttime.calendars[0].end = new Date($('endtime-date').value);
      // redraw calendar
      cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
      cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
    };

    function checkDateDifference(priceRangeBasis) {
      var dateDifference = new Date($("starttime-date").value).diff(new Date($("endtime-date").value), priceRangeBasis);
      var tempDateDifference;

      if (priceRangeBasis == 'day') {
        if (dateDifference && dateDifference > 0)
          $('quantity').value = dateDifference;
        else if (dateDifference == 0)
          $('quantity').value = 1;
        return;
      }

      if (priceRangeBasis == 'week') {
        tempDateDifference = new Date($("starttime-date").value).diff(new Date($("endtime-date").value), 'day');
        if (tempDateDifference % 7 != 0) {
          $('quantity').value = Math.floor(tempDateDifference / 7) + 1;
          return;
        } else if (dateDifference == 0)
          $('quantity').value = 1;
      }
      if (priceRangeBasis == 'month') {
        tempDateDifference = new Date($("starttime-date").value).diff(new Date($("endtime-date").value), 'day');
        if (tempDateDifference % 30 != 0) {
          $('quantity').value = Math.floor(tempDateDifference / 30) + 1;
          return;
        } else if (dateDifference == 0)
          $('quantity').value = 1;
      }
      if (priceRangeBasis == 'year') {
        tempDateDifference = new Date($("starttime-date").value).diff(new Date($("endtime-date").value), 'day');
        if (tempDateDifference % 365 != 0) {
          $('quantity').value = Math.floor(tempDateDifference / 365) + 1;
          return;
        } else if (dateDifference == 0)
          $('quantity').value = 1;
      }
    }
<?php endif; ?>

//  window.addEvent('load', function(){
//    <?php
//  $show_msg = 0;
//    $priceAfterDisocunt =  Engine_Api::_()->sitestoreproduct()->getProductDiscount($this->sitestoreproduct, true, $this->productPriceOptions, 1);
//    $getPriceOfProductsAfterVAT =  Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($this->sitestoreproduct);
//    if(!empty($getPriceOfProductsAfterVAT) && !empty($priceAfterDisocunt) && isset($getPriceOfProductsAfterVAT['vat']) && !empty($getPriceOfProductsAfterVAT['vat']))
//      $priceAfterDisocunt = @(float)$priceAfterDisocunt + @(float)$getPriceOfProductsAfterVAT['vat'];
//    if(!empty($getPriceOfProductsAfterVAT) && isset($getPriceOfProductsAfterVAT['show_msg']) && !empty($getPriceOfProductsAfterVAT['show_msg']))
//      $show_msg = 1;
?>//
//    discounted_price = '<?php //echo $priceAfterDisocunt;  ?>';
//    show_msg = <?php //echo $show_msg;  ?>;
//  });

  if ($('sitestoreproduct_product_image_zoom')) {
    $('sitestoreproduct_product_image_zoom').addEvent('mouseover', function() {
      sitestoreproductProfileImageMagnify();
    });
  }

//  function changeDateTimeFiledValue() {
//    <?php if (!empty($this->productQuantityBox) && !empty($this->dateTimeSelector)) : ?>
  //        if( seao_dateFormat == 'dmy' ) {
  //    cal_starttime_date = en4.seaocore.covertdateDmyToMdy($(starttime+'-date').value);
  //        } else {
  //          cal_starttime_date = $("endtime-date").value;
  //        }
  //      $("endtime-date").value = new Date($("endtime-date").value).increment('day', $('quantity').value).format("%m/%d/%Y");
  //      alert($("endtime-date").value);
  //    <?php endif; ?>
//  }

  function showChildOptions(element, field_id, product_id, order, price_array, max_order) {
    if (element.value != 0) {
      document.getElementById("configuration_price_loading").innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />';
      $('select_' + field_id).options[0].disabled = true;

      if (order != max_order) {
        combination_attribute_id[field_id] = element.value;
        if (show_item_left == 1)
          document.getElementById("items_left").innerHTML = '';
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

      var url = en4.core.baseUrl + 'sitestoreproduct/index/get-attribute-child';
      temp = new Request.JSON({
        method: 'post',
        'url': url,
        'data': {
          'format': 'json',
          'combination_attribute_id': element.value,
          'parent_attribute_ids': parent_attribute_id,
          'field_id': field_id,
          'product_id': product_id,
          'order': order,
          'isUserEnd': true
        },
        onSuccess: function(responseJSON) {
          var values = JSON.parse(responseJSON.attributeArray);
          if (values != null) {
            var x;
            var count = 0;
            for (x = 0; x < values.length; x++) {
              var select = document.getElementById('select_' + values[x].field_id);
              selectedFields[values[x].field_id] = '0.00';
              if (values[x].order == order + 1) {
                if (count == 0) {
                  select.empty();
                  var opt = document.createElement('option');
                  opt.value = "0";
                  opt.innerHTML = en4.core.language.translate('-- Please Select --');
                  select.appendChild(opt);
                }
                var opt = document.createElement('option');
                opt.value = values[x].value;
                opt.innerHTML = values[x].label;
                select.appendChild(opt);
                count++;
              }
              else {
                select.empty();
                var opt = document.createElement('option');
                opt.value = "0";
                opt.innerHTML = en4.core.language.translate('-- Please Select --');
                select.appendChild(opt);
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
         
          var url = en4.core.baseUrl + 'sitestoreproduct/index/configuration-price';
          tempReq = new Request({
            method: 'post',
            'url': url,
            'data': {
              'format': 'json',
              'price': configurable_price,
              show_msg: show_msg,
            },
            onSuccess: function(responseJSON) {
              document.getElementById("configuration_price_loading").innerHTML = '';
              document.getElementsByClassName('sr_sitestoreproduct_profile_price')[0].innerHTML = responseJSON;
            }
          });
          tempReq.send();

        }
      });
      temp.send();

      parent_attribute_id[order] = element.value;

      if (order != 0)
        last_order = order;
      else
        last_order = 0;
    }
  }

  function checkQuantity() {

    var quantity = $('quantity').value;
    var form_error = 0;
    if (!(/^[1-9]\d*/.test(quantity))) {

      var wrapperDiv = document.createElement("div");
      wrapperDiv.id = 'cart_quantity_message';
      wrapperDiv.setAttribute("class", "review_error sitestoreproduct_form_error");
      wrapperDiv.innerHTML = "<span><?php echo $this->translate('Please enter a valid quantity.'); ?></span>";
      wrapperDiv.inject($('quantity'), 'before');
      form_error = 1;
    }
    if (stock_unlimited == 0 && quantity > in_stock) {
      var wrapperDiv = document.createElement("div");
      wrapperDiv.id = 'cart_quantity_message';
      wrapperDiv.setAttribute("class", "review_error sitestoreproduct_form_error");
      if (in_stock == 1)
        wrapperDiv.innerHTML = '<span><?php echo $this->translate("Only 1 quantity of this product is available in stock. Please enter the quantity as 1."); ?></span>';
      else
        wrapperDiv.innerHTML = '<span><?php echo $this->translate("Only %s quantities of this product are available in stock. Please enter the quantity less than or equal to %s.", $this->sitestoreproduct->in_stock, $this->sitestoreproduct->in_stock); ?></span>';
      wrapperDiv.inject($('quantity'), 'before');

    }

    if (form_error == 1)
      return false;
  }

  function showItemLeft(combination_attribute_ids) {
    var product_id = '<?php echo $this->sitestoreproduct->product_id; ?>';
    var url = en4.core.baseUrl + 'sitestoreproduct/index/show-combination-quantity';
    tempReq = new Request.JSON({
      method: 'post',
      'url': url,
      'data': {
        'format': 'json',
        'combination_attribute_ids': combination_attribute_ids,
        'product_id': product_id
      },
      onSuccess: function(responseJSON) {
        document.getElementById("items_left").innerHTML = responseJSON.combination_quantity + ' Items left';
      }
    });
    tempReq.send();
  }

</script>
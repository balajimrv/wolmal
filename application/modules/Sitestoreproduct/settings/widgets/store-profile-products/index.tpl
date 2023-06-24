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
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_rating.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css')
?>
<?php $latitude = $this->settings->getSetting('sitestoreroduct.map.latitude', 0); ?>
<?php $longitude = $this->settings->getSetting('sitestoreroduct.map.longitude', 0); ?>
<?php $defaultZoom = $this->settings->getSetting('sitestoreroduct.map.zoom', 1); ?>
<?php if ($this->enableLocation): ?>
  <?php
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
  ?>
<?php endif; ?>

<script type="text/javascript" >
  var identity = <?php echo $this->identity; ?>;
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }

  OrderproductselectArray['<?php echo $this->identity; ?>'] = [];
  en4.core.runonce.add(function()
  {


    if (typeof OrderproductselectArray['<?php echo $this->identity; ?>'] != 'undefined') {
      OrderproductselectArray['<?php echo $this->identity; ?>']['tab'] = '<?php echo $this->identity; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['temp_layouts_views'] = '<?php echo $this->temp_layouts_views; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['temp_is_subject'] = '<?php echo $this->temp_is_subject; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['itemCount'] = '<?php echo $this->itemCount; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['add_to_cart'] = '<?php echo $this->showAddToCart; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['in_stock'] = '<?php echo $this->showInStock; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['categoryRouteName'] = '<?php echo $this->categoryRouteName; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['ratingType'] = '<?php echo $this->ratingType; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['title_truncation'] = '<?php echo $this->title_truncation; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['title_truncationGrid'] = '<?php echo $this->title_truncationGrid; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['postedby'] = '<?php echo $this->postedby; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['columnWidth'] = '<?php echo $this->columnWidth; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['columnHeight'] = '<?php echo $this->columnHeight; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['tempStatistics'] = '<?php echo empty($this->statistics) ? 'none' : implode(",", $this->statistics); ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['temp_product_types'] = '<?php echo $this->temp_product_types; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['searchByOptions'] = '<?php echo json_encode($this->searchByOptions) ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['bottomLine'] = '<?php echo $this->bottomLine; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['viewType'] = '<?php echo $this->viewType; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['layouts_order'] = '<?php echo $this->layouts_order; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['showClosed'] = '<?php echo $this->showClosed; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['section'] = '<?php if (!empty($this->section)) echo $this->section;
else echo "0"; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['downpayment'] = '<?php if (!empty($this->directPayment) && !empty($this->isDownPaymentEnable) && !empty($this->downpayment)) echo $this->downpayment;
else echo 0; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['category'] = '<?php if (!empty($this->category)) echo $this->category;
else echo "0"; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['orderby'] = '<?php echo $this->orderby; ?>';
      OrderproductselectArray['<?php echo $this->identity; ?>']['temViewType'] = '<?php echo $this->temViewType; ?>';

    }
  });
</script> 

<?php if ($this->ajaxView && empty($this->isajax)): ?>
  <div id="tmp_layout_sitestoreproduct_store_profile_products<?php echo $this->identity ?>"></div>
  <script type="text/javascript">
    $("tmp_layout_sitestoreproduct_store_profile_products<?php echo $this->identity ?>").getParent('.layout_sitestoreproduct_store_profile_products').addClass('layout_sitestoreproduct_store_profile_products<?php echo $this->identity ?>');
    var params = {
      requestParams:<?php echo json_encode($this->params) ?>,
      responseContainer: $$('.layout_sitestoreproduct_store_profile_products<?php echo $this->identity ?>'),
        requestUrl: en4.core.baseUrl+'<?php  echo ($this->user_layout) ? 'sitestore/widget' :'widget'; ?>'
    };
    en4.sitestoreproduct.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
  </script>
<?php endif; ?>

<?php if (empty($this->isajax)) : ?>
  <script type="text/javascript">
    var tempCurrentViewType = <?php echo $this->temViewType ?>;
  </script>
<?php endif; ?>

<?php if (false) : ?>
  <script type="text/javascript" >
    window.addEvent('load', function() {
      $('sitestoreproduct_root_<?php echo $this->identity; ?>').innerHTML = '<div class="seaocore_content_loader"></div>';
      var request = new Request.HTML({
        url: en4.core.baseUrl + 'widget/index/mod/sitestoreproduct/name/store-profile-products',
        method: 'get',
        data: {
          format: 'html',
          'load_content': 1,
          'identity': '<?php echo $this->identity ?>',
          'ajaxView': <?php echo $this->ajaxView; ?>,
          'store_id': <?php echo $this->store_id; ?>,
          'tab': '<?php echo $this->content_id ?>',
          'temViewType': <?php echo $this->temViewType; ?>,
          'temp_layouts_views': '<?php echo $this->temp_layouts_views; ?>',
          'temp_is_subject': <?php echo $this->is_subject ?>,
          'itemCount': <?php echo $this->itemCount ?>,
          'add_to_cart': <?php echo $this->showAddToCart; ?>,
          'in_stock': <?php echo $this->showInStock; ?>,
          'categoryRouteName': '<?php echo $this->categoryRouteName; ?>',
          'ratingType': '<?php echo $this->ratingType; ?>',
          'title_truncation': <?php echo $this->title_truncation; ?>,
          'title_truncationGrid': <?php echo $this->title_truncationGrid; ?>,
          'postedby': <?php echo $this->postedby; ?>,
          'bottomLine': <?php echo $this->bottomLine; ?>,
          'viewType': <?php echo $this->viewType; ?>,
          'layouts_order': <?php echo $this->layouts_order; ?>,
          'showClosed': <?php echo $this->showClosed; ?>,
          'orderby': '<?php echo $this->orderby; ?>',
          'columnWidth': '<?php echo $this->columnWidth; ?>',
          'columnHeight': '<?php echo $this->columnHeight; ?>',
          'section': <?php if (!empty($this->section)) echo $this->section;
  else echo "0"; ?>,
          'category': <?php if (!empty($this->category)) echo $this->category;
  else echo "0"; ?>,
          'tempStatistics': '<?php echo empty($this->statistics) ? 'none' : implode(",", $this->statistics); ?>',
          'temp_product_types': '<?php echo $this->temp_product_types; ?>'

        },
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

          $('sitestoreproduct_root_<?php echo $this->identity; ?>').innerHTML = responseHTML;
          switchview(OrderproductselectArray[identity]['temViewType'], '<?php echo $this->identity; ?>');
          // switchview(tempCurrentViewType);          
          // switchview(1);
        }
      });
      request.send();
    });
  </script>
<?php endif; ?>

<?php if (!empty($this->showContent)) : ?>
  <script type="text/javascript">
    var productsSearchText = '<?php echo $this->search ?>';
    var pageProductPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
    en4.core.runonce.add(function()
    {
  <?php if (empty($this->ajaxView) && ($this->current_count > 0)): ?>
        switchview(OrderproductselectArray['<?php echo $this->identity; ?>']['temViewType'], '<?php echo $this->identity; ?>');
  <?php endif; ?>

      //opacity / display fix
      $$('.sitestoreproduct_tooltip').setStyles({
        opacity: 0,
        display: 'block'
      });

      var anchor = $('sitestoreproduct_search_<?php echo $this->identity; ?>').getParent();

      //put the effect in place
      $$('.jq-sitestoreproduct_tooltip li').each(function(el, i) {
        el.addEvents({
          'mouseenter': function() {
            el.getElement('div').fade('in');
          },
          'mouseleave': function() {
            el.getElement('div').fade('out');
          }
        });
      });

      $('sitestoreproduct_products_search_input_text_<?php echo $this->identity ?>').removeEvents('keyup').addEvent('keyup', function(e) {
        if (e.key != 'enter')
          return;
        if ($('sitestoreproduct_products_search_input_checkbox') && $('sitestoreproduct_products_search_input_checkbox').checked == true) {
          var checkbox_value = 1;
        }
        else {
          var checkbox_value = 0;
        }
        if ($('sitestoreproduct_search_<?php echo $this->identity; ?>') != null) {
          if ($('sr_sitestoreproduct_browse_lists_view_options_b_medium_<?php echo $this->identity; ?>'))
            $('sr_sitestoreproduct_browse_lists_view_options_b_medium_<?php echo $this->identity; ?>').style.display = 'none';
          $('sitestoreproduct_search_<?php echo $this->identity; ?>').innerHTML = '<div class="seaocore_content_loader"></div></center>';
        }
        var tempSection_<?php echo $this->identity; ?> = 0;
        if ($('sitestoreproduct_products_sections_selectbox_<?php echo $this->identity ?>')) {
          tempSection_<?php echo $this->identity; ?> = $('sitestoreproduct_products_sections_selectbox_<?php echo $this->identity ?>').value;
        }
        var downpayment_<?php echo $this->identity; ?> = 0;
        if ($('sitestoreproduct_products_downpayment_<?php echo $this->identity ?>')) {
          downpayment_<?php echo $this->identity; ?> = $('sitestoreproduct_products_downpayment_' + identity).value;
        }
        var sitestoreproduct_products_search_input_text_<?php echo $this->identity ?> = '';
        if ($('sitestoreproduct_products_search_input_text_<?php echo $this->identity ?>'))
          sitestoreproduct_products_search_input_text_<?php echo $this->identity ?> = $('sitestoreproduct_products_search_input_text_<?php echo $this->identity ?>').value;

        var sitestoreproduct_products_location_input_text_<?php echo $this->identity ?> = '';
        if ($('sitestoreproduct_products_location_input_text_<?php echo $this->identity ?>'))
          sitestoreproduct_products_location_input_text_<?php echo $this->identity ?> = $('sitestoreproduct_products_location_input_text_<?php echo $this->identity ?>').value;

        var sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity ?> = '';
        if ($('sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity ?>'))
          sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity ?> = $('sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity ?>').value;

        var sitestoreproduct_products_categories_selectbox_<?php echo $this->identity ?> = '';
        if ($('sitestoreproduct_products_categories_selectbox_<?php echo $this->identity ?>'))
          sitestoreproduct_products_categories_selectbox_<?php echo $this->identity ?> = $('sitestoreproduct_products_categories_selectbox_<?php echo $this->identity ?>').value;
        var temViewType = OrderproductselectArray[<?php echo $this->identity ?>]['temViewType'];
        en4.core.request.send(new Request.HTML({
          url: en4.core.baseUrl + '<?php echo ($this->user_layout) ? 'sitestore/widget' : 'widget'; ?>',
          'data': {
            'format': 'html',
            'subject': en4.core.subject.guid,
            'identity': identity,
            'content_id': identity,
            'search': sitestoreproduct_products_search_input_text_<?php echo $this->identity; ?>,
            'location': sitestoreproduct_products_location_input_text_<?php echo $this->identity ?>,
            'selectbox': sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity; ?>,
            'section': tempSection_<?php echo $this->identity; ?>,
            'downpayment': downpayment_<?php echo $this->identity; ?>,
            'category': sitestoreproduct_products_categories_selectbox_<?php echo $this->identity; ?>,
            'checkbox': checkbox_value,
            'isajax': '1',
            'tab': '<?php echo $this->content_id ?>',
            'temViewType': OrderproductselectArray['<?php echo $this->identity; ?>']['temViewType'],
            'temp_layouts_views': '<?php echo $this->temp_layouts_views; ?>',
            'temp_is_subject': <?php echo $this->is_subject ?>,
            'itemCount': <?php echo $this->itemCount ?>,
            'add_to_cart': <?php echo $this->showAddToCart; ?>,
            'in_stock': <?php echo $this->showInStock; ?>,
            'categoryRouteName': '<?php echo $this->categoryRouteName; ?>',
            'ratingType': '<?php echo $this->ratingType; ?>',
            'title_truncation': <?php echo $this->title_truncation; ?>,
            'title_truncationGrid': <?php echo $this->title_truncationGrid; ?>,
            'postedby': <?php echo $this->postedby; ?>,
            'columnWidth': '<?php echo $this->columnWidth; ?>',
            'columnHeight': '<?php echo $this->columnHeight; ?>',
            'tempStatistics': '<?php echo empty($this->statistics) ? 'none' : implode(",", $this->statistics); ?>',
            'temp_product_types': '<?php echo $this->temp_product_types; ?>',
            'searchByOptions': OrderproductselectArray[identity]['searchByOptions']
          }, onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('sitestoreproduct_search_' + <?php echo $this->identity; ?>).innerHTML = responseHTML;
            switchview(temViewType, identity);
            // switchview(5);
          }
        }), {
          // 'element' : $('id_' + <?php // echo $this->content_id      ?>),
          //  'force': true          
        });
      });

      if ($('sitestoreproduct_products_location_input_text_<?php echo $this->identity ?>')) {
        $('sitestoreproduct_products_location_input_text_<?php echo $this->identity ?>').removeEvents('keyup').addEvent('keyup', function(e) {
          if (e.key != 'enter')
            return;
          if ($('sitestoreproduct_products_search_input_checkbox') && $('sitestoreproduct_products_search_input_checkbox').checked == true) {
            var checkbox_value = 1;
          }
          else {
            var checkbox_value = 0;
          }
          if ($('sitestoreproduct_search_<?php echo $this->identity; ?>') != null) {
            if ($('sr_sitestoreproduct_browse_lists_view_options_b_medium_<?php echo $this->identity; ?>'))
              $('sr_sitestoreproduct_browse_lists_view_options_b_medium_<?php echo $this->identity; ?>').style.display = 'none';
            $('sitestoreproduct_search_<?php echo $this->identity; ?>').innerHTML = '<div class="seaocore_content_loader"></div></center>';
          }
          var tempSection_<?php echo $this->identity; ?> = 0;
          if ($('sitestoreproduct_products_sections_selectbox_<?php echo $this->identity ?>')) {
            tempSection_<?php echo $this->identity; ?> = $('sitestoreproduct_products_sections_selectbox_<?php echo $this->identity ?>').value;
          }
          var downpayment_<?php echo $this->identity; ?> = 0;
          if ($('sitestoreproduct_products_downpayment_<?php echo $this->identity ?>')) {
            downpayment_<?php echo $this->identity; ?> = $('sitestoreproduct_products_downpayment_' + identity).value;
          }
          var sitestoreproduct_products_search_input_text_<?php echo $this->identity ?> = '';
          if ($('sitestoreproduct_products_search_input_text_<?php echo $this->identity ?>'))
            sitestoreproduct_products_search_input_text_<?php echo $this->identity ?> = $('sitestoreproduct_products_search_input_text_<?php echo $this->identity ?>').value;

          var sitestoreproduct_products_location_input_text_<?php echo $this->identity ?> = '';
          if ($('sitestoreproduct_products_location_input_text_<?php echo $this->identity ?>'))
            sitestoreproduct_products_location_input_text_<?php echo $this->identity ?> = $('sitestoreproduct_products_location_input_text_<?php echo $this->identity ?>').value;

          var sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity ?> = '';
          if ($('sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity ?>'))
            sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity ?> = $('sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity ?>').value;

          var sitestoreproduct_products_categories_selectbox_<?php echo $this->identity ?> = '';
          if ($('sitestoreproduct_products_categories_selectbox_<?php echo $this->identity ?>'))
            sitestoreproduct_products_categories_selectbox_<?php echo $this->identity ?> = $('sitestoreproduct_products_categories_selectbox_<?php echo $this->identity ?>').value;
          var temViewType = OrderproductselectArray[<?php echo $this->identity ?>]['temViewType'];
          url = en4.core.baseUrl + '<?php echo ($this->user_layout) ? 'sitestore/widget' : 'widget'; ?>';
          en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + '<?php echo ($this->user_layout) ? 'sitestore/widget' : 'widget'; ?>',
            'data': {
              'format': 'html',
              'subject': en4.core.subject.guid,
              'identity': identity,
              'content_id': identity,
              'search': sitestoreproduct_products_search_input_text_<?php echo $this->identity; ?>,
              'location': sitestoreproduct_products_location_input_text_<?php echo $this->identity ?>,
              'selectbox': sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity; ?>,
              'section': tempSection_<?php echo $this->identity; ?>,
              'downpayment': downpayment_<?php echo $this->identity; ?>,
              'category': sitestoreproduct_products_categories_selectbox_<?php echo $this->identity; ?>,
              'checkbox': checkbox_value,
              'isajax': '1',
              'tab': '<?php echo $this->content_id ?>',
              'temViewType': OrderproductselectArray['<?php echo $this->identity; ?>']['temViewType'],
              'temp_layouts_views': '<?php echo $this->temp_layouts_views; ?>',
              'temp_is_subject': <?php echo $this->is_subject ?>,
              'itemCount': <?php echo $this->itemCount ?>,
              'add_to_cart': <?php echo $this->showAddToCart; ?>,
              'in_stock': <?php echo $this->showInStock; ?>,
              'categoryRouteName': '<?php echo $this->categoryRouteName; ?>',
              'ratingType': '<?php echo $this->ratingType; ?>',
              'title_truncation': <?php echo $this->title_truncation; ?>,
              'title_truncationGrid': <?php echo $this->title_truncationGrid; ?>,
              'postedby': <?php echo $this->postedby; ?>,
              'columnWidth': '<?php echo $this->columnWidth; ?>',
              'columnHeight': '<?php echo $this->columnHeight; ?>',
              'tempStatistics': '<?php echo empty($this->statistics) ? 'none' : implode(",", $this->statistics); ?>',
              'temp_product_types': '<?php echo $this->temp_product_types; ?>',
              'searchByOptions': OrderproductselectArray[identity]['searchByOptions']
            }, onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
              $('sitestoreproduct_search_' + <?php echo $this->identity; ?>).innerHTML = responseHTML;
              switchview(temViewType, identity);
              // switchview(5);
            }
          }), {
            // 'element' : $('id_' + <?php // echo $this->content_id      ?>),
            //  'force': true          
          });
        });
      }
    });



    function Orderproductselect(identity)
    {
      var temViewType = OrderproductselectArray[identity]['temViewType'];
      var productsSearchSelectbox = '<?php echo $this->selectbox ?>';
      var pageProductPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
      if ($('sitestoreproduct_products_search_input_checkbox') && $('sitestoreproduct_products_search_input_checkbox').checked == true) {
        var checkbox_value = 1;
      }
      else {
        var checkbox_value = 0;
      }
      if ($('sitestoreproduct_search_' + identity)) {
        if ($('sr_sitestoreproduct_browse_lists_view_options_b_medium_' + identity))
          $('sr_sitestoreproduct_browse_lists_view_options_b_medium_' + identity).style.display = 'none';
        $('sitestoreproduct_search_' + identity).innerHTML = '<div class="seaocore_content_loader"></div>';
      }
      var tempSection = 0;
      if ($('sitestoreproduct_products_sections_selectbox_' + identity)) {
        tempSection = $('sitestoreproduct_products_sections_selectbox_' + identity).value;
      }

      var downpayment = 0;
      if ($('sitestoreproduct_products_downpayment_' + identity)) {
        downpayment = $('sitestoreproduct_products_downpayment_' + identity).value;
      }

      var sitestoreproduct_products_search_input_text = '';
      if ($('sitestoreproduct_products_search_input_text_' + identity))
        sitestoreproduct_products_search_input_text = $('sitestoreproduct_products_search_input_text_' + identity).value;

      var sitestoreproduct_products_search_input_selectbox = '';
      if ($('sitestoreproduct_products_search_input_selectbox_' + identity))
        sitestoreproduct_products_search_input_selectbox = $('sitestoreproduct_products_search_input_selectbox_' + identity).value;

      var sitestoreproduct_products_categories_selectbox = '';
      if ($('sitestoreproduct_products_categories_selectbox_' + identity))
        sitestoreproduct_products_categories_selectbox = $('sitestoreproduct_products_categories_selectbox_' + identity).value;

      en4.core.request.send(new Request.HTML({
        url: en4.core.baseUrl + '<?php echo ($this->user_layout) ? 'sitestore/widget' : 'widget'; ?>',
        'data': {
          'format': 'html',
          'subject': en4.core.subject.guid,
          'identity': identity,
          'content_id': identity,
          'search': sitestoreproduct_products_search_input_text,
          'selectbox': sitestoreproduct_products_search_input_selectbox,
          'section': tempSection,
          'downpayment': downpayment,
          'category': sitestoreproduct_products_categories_selectbox,
          'checkbox': checkbox_value,
          'isajax': '1',
          'tab': OrderproductselectArray[identity]['tab'],
          'temViewType': OrderproductselectArray[identity]['temViewType'],
          'temp_layouts_views': OrderproductselectArray[identity]['temp_layouts_views'],
          'temp_is_subject': OrderproductselectArray[identity]['temp_is_subject'],
          'itemCount': OrderproductselectArray[identity]['itemCount'],
          'add_to_cart': OrderproductselectArray[identity]['add_to_cart'],
          'in_stock': OrderproductselectArray[identity]['in_stock'],
          'categoryRouteName': OrderproductselectArray[identity]['categoryRouteName'],
          'ratingType': OrderproductselectArray[identity]['ratingType'],
          'title_truncation': OrderproductselectArray[identity]['title_truncation'],
          'title_truncationGrid': OrderproductselectArray[identity]['title_truncation'],
          'postedby': OrderproductselectArray[identity]['postedby'],
          'columnWidth': OrderproductselectArray[identity]['columnWidth'],
          'columnHeight': OrderproductselectArray[identity]['columnHeight'],
          'tempStatistics': OrderproductselectArray[identity]['tempStatistics'],
          'temp_product_types': OrderproductselectArray[identity]['temp_product_types'],
          'searchByOptions': OrderproductselectArray[identity]['searchByOptions']
        }, onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
          $('sitestoreproduct_search_' + identity).innerHTML = responseHTML;
          switchview(temViewType, identity);
        }
      }), {
      });
    }
  </script>
  <?php endif; ?>

  <?php if (!empty($this->ajaxView) && empty($this->load_content) && empty($this->isajax)): ?>
  <div id="sitestoreproduct_root_<?php echo $this->identity; ?>"></div>
    <?php return; ?>
  <?php endif; ?>

  <?php if (empty($this->isajax)) : ?>
  <div id="id_<?php echo $this->identity; ?>">
  <?php endif; ?>

  <?php
  $this->headLink()
          ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
          ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_rating.css')
          ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
  $this->headScript()
          ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
  ?>
  <?php
  $ratingValue = $this->ratingType;
  $ratingShow = 'small-star';
  if ($this->ratingType == 'rating_editor') {
    $ratingType = 'editor';
  } elseif ($this->ratingType == 'rating_avg') {
    $ratingType = 'overall';
  } else {
    $ratingType = 'user';
  }
  ?>

  <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>

  <?php $doNotShowTopContent = 0; ?>
<?php if ($this->categoryName && !empty($this->categoryObject->top_content)): ?>

    <h4 class="sr_sitestoreproduct_browse_lists_view_options_head mbot10" style="display: inherit;">
  <?php echo $this->translate($this->categoryName); ?>
    </h4>

  <?php $doNotShowTopContent = 1; ?>
    <?php endif; ?>

  <div id="sitestoreproduct_header_<?php echo $this->identity; ?>">



      <?php if (!empty($this->paginator) && $this->paginator->count() > 0 && (empty($this->search) && empty($this->checkbox) && empty($this->selectbox)) || !empty($this->is_subject) && !empty($this->viewer_id) && !empty($this->can_edit) && $this->authValue == 2): ?>


      <?php if (empty($this->isajax) && !empty($this->is_subject) && !empty($this->viewer_id) && !empty($this->can_edit)): ?>
        <div class="mbot10">
        <?php if (empty($this->quota) || (!empty($this->quota) && $this->current_count < $this->quota)): ?> 
            <a href='<?php echo $this->url(array('action' => 'create', 'store_id' => $this->store_id, 'tab' => $this->identity), 'sitestoreproduct_general', true) ?>' class='buttonlink seaocore_icon_add mright10'><?php echo $this->translate('Create Product'); ?>
            </a>
          <?php endif; ?>
          <a href='<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id), 'sitestore_dashboard', true); ?>' class='buttonlink sitestoreproduct_icon_store'><?php echo $this->translate('Manage Products'); ?></a>
        </div>
  <?php endif; ?>
        <?php if (!empty($this->searchByOptions)): ?>

    <?php if (empty($this->isajax)): ?>
          <div class="sitestoreproduct_product_list_filters">
    <?php endif; ?>
          <?php if ((empty($this->isajax) && ($this->paginator->count() > 0 || !empty($this->temp_tab)) && (empty($this->search) && empty($this->checkbox) && empty($this->selectbox)))): ?>
            <form id='filter_form_sitestoreproduct_<?php echo $this->identity; ?>' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general", true) ?>' style='display: none;'>
              <input type="hidden" id="page" name="page"  value=""/>
            </form>
      <?php if (in_array('1', $this->searchByOptions)): ?>
              <div class="sitestoreproduct_product_list_filters_field">
                <label><?php echo $this->translate("Search: "); ?></label>
                <input id="sitestoreproduct_products_search_input_text_<?php echo $this->identity ?>" type="text" value="<?php echo $this->search; ?>" />
              </div>
                <?php endif; ?>
                <?php if (in_array('2', $this->searchByOptions)): ?>
              <div class="sitestoreproduct_product_list_filters_field">
                <label><?php echo $this->translate('Browse by:'); ?>	</label>
                <select name="default_visibility" id="sitestoreproduct_products_search_input_selectbox_<?php echo $this->identity; ?>" onchange = "Orderproductselect('<?php echo $this->identity; ?>');">
                  <?php if ($this->selectbox == 'creation_date'): ?>
                    <option value="creation_date" selected='selected'><?php echo $this->translate("Most Recent"); ?></option>
                  <?php else: ?>
                    <option value="creation_date"><?php echo $this->translate("Most Recent"); ?></option>
                  <?php endif; ?>
                  <?php if ($this->selectbox == 'newlabel'): ?>
                    <option value="newlabel" selected='selected'><?php echo $this->translate("New Arrivals"); ?></option>
                  <?php else: ?>
                    <option value="newlabel"><?php echo $this->translate("New Arrivals"); ?></option>
                  <?php endif; ?>
                  <?php if ($this->selectbox == 'selling_price_count'): ?>
                    <option value="selling_price_count" selected='selected'><?php echo $this->translate("Most Sold Products (Price)"); ?></option>
                  <?php else: ?>
                    <option value="selling_price_count"><?php echo $this->translate("Most Sold Products (Price)"); ?></option>
                  <?php endif; ?>	
                  <?php if ($this->selectbox == 'selling_item_count'): ?>
                    <option value="selling_item_count" selected='selected'><?php echo $this->translate("Most Sold Products (Quantity)"); ?></option>
                  <?php else: ?>
                    <option value="selling_item_count"><?php echo $this->translate("Most Sold Products (Quantity)"); ?></option>
                  <?php endif; ?>
                  <?php if ($this->selectbox == 'discount_amount'): ?>
                    <option value="discount_amount" selected='selected'><?php echo $this->translate("Most Discounted Products"); ?></option>
                  <?php else: ?>
                    <option value="discount_amount"><?php echo $this->translate("Most Discounted Products"); ?></option>
                  <?php endif; ?>
                  <?php //if ($this->selectbox == 'comment_count'):  ?>
        <!--              <option value="comment_count" selected='selected'><?php //echo $this->translate("Most Commented"); ?></option>-->
                  <?php //else: ?>
        <!--              <option value="comment_count"><?php //echo $this->translate("Most Commented");  ?></option>-->
                  <?php //endif; ?>		
                  <?php //if ($this->selectbox == 'view_count'):  ?>
        <!--              <option value="view_count" selected='selected'><?php //echo $this->translate("Most Viewed"); ?></option>-->
                  <?php //else: ?>
        <!--              <option value="view_count"><?php //echo $this->translate("Most Viewed");  ?></option>-->
                  <?php //endif; ?>		
                  <?php if ($this->selectbox == 'like_count'): ?>
                    <option value="like_count" selected='selected'><?php echo $this->translate("Most Liked"); ?></option>
                  <?php else: ?>
                    <option value="like_count"><?php echo $this->translate("Most Liked"); ?></option>
                  <?php endif; ?>
                  <?php if ($this->selectbox == 'sponsored'): ?>
                    <option value="sponsored" selected='selected'><?php echo $this->translate("Sponsored"); ?></option>
                  <?php else: ?>
                    <option value="sponsored"><?php echo $this->translate("Sponsored"); ?></option>
                  <?php endif; ?>
                  <?php if ($this->selectbox == 'featured'): ?>
                    <option value="featured" selected='selected'><?php echo $this->translate("Featured"); ?></option>
        <?php else: ?>
                    <option value="featured"><?php echo $this->translate("Featured"); ?></option>
              <?php endif; ?>
              <?php if ($this->selectbox == 'highlighted'): ?>
                    <option value="highlighted" selected='selected'><?php echo $this->translate("Highlighted"); ?></option>
        <?php else: ?>
                    <option value="highlighted"><?php echo $this->translate("Highlighted"); ?></option>
        <?php endif; ?>
                </select>
              </div>
                <?php endif; ?>
                <?php if (in_array('3', $this->searchByOptions)): ?>
        <?php if (!empty($this->categoryCount)) : ?>
                <div class="sitestoreproduct_product_list_filters_field">
                  <label><?php echo $this->translate('Browse by Category:'); ?>	</label>
                  <select name="category" id="sitestoreproduct_products_categories_selectbox_<?php echo $this->identity; ?>" onchange = "Orderproductselect('<?php echo $this->identity; ?>');">
                    <option value="0"></option>
                <?php foreach ($this->categories as $sections) : ?>
                      <option value="<?php echo $sections->category_id; ?>" <?php if ($this->section == $sections->category_id) : ?>selected='selected' <?php endif; ?>><?php echo $this->translate($sections->category_name); ?></option>
          <?php endforeach; ?>
                  </select>              
                </div>
                  <?php endif; ?>
                <?php endif; ?>
                <?php if (in_array('4', $this->searchByOptions)): ?>
        <?php if (!empty($this->sectionCount)) : ?>
                <div class="sitestoreproduct_product_list_filters_field">
                  <label><?php echo $this->translate('Browse by Section:'); ?>	</label>
                  <select name="section" id="sitestoreproduct_products_sections_selectbox_<?php echo $this->identity; ?>" onchange = "Orderproductselect('<?php echo $this->identity; ?>');">
                    <option value="0"></option>
                <?php foreach ($this->sections as $sections) : ?>
                      <option value="<?php echo $sections->section_id; ?>" <?php if ($this->section == $sections->section_id) : ?>selected='selected' <?php endif; ?>><?php echo $this->translate($sections->section_name); ?></option>
          <?php endforeach; ?>
                  </select>              
                </div>
        <?php endif; ?>
      <?php endif; ?>

            <?php if (in_array('4', $this->searchByOptions) && !empty($this->directPayment) && !empty($this->isDownPaymentEnable)): ?>
              <div class="sitestoreproduct_product_list_filters_field">
                <label><?php echo $this->translate('Downpayment:'); ?></label>
                <select name="downpayment" id="sitestoreproduct_products_downpayment_<?php echo $this->identity; ?>" onchange = "Orderproductselect('<?php echo $this->identity; ?>');">
                  <option value="0"></option>
                  <option value="1" <?php if ($this->downpayment == 1) : ?>selected='selected' <?php endif; ?>><?php echo $this->translate('Yes'); ?></option>
                  <option value="2" <?php if ($this->downpayment == 2) : ?>selected='selected' <?php endif; ?>><?php echo $this->translate('No'); ?></option>
                </select>
              </div>
            <?php endif; ?>

            <?php if (in_array('6', $this->searchByOptions) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0)): ?>
              <div class="sitestoreproduct_product_list_filters_field">
                <label><?php echo $this->translate("Location: "); ?></label>
                <input id="sitestoreproduct_products_location_input_text_<?php echo $this->identity ?>" type="text" value="<?php echo $this->search; ?>" />
              </div>
      <?php endif; ?>

        <?php endif; ?>
        <?php if (empty($this->isajax)): ?>
          </div>
    <?php endif;
  endif;
  ?>
<?php endif; ?>
  </div>

  <div id="sitestoreproduct_search_<?php echo $this->identity; ?>">
<?php if (!empty($this->paginator) && $this->paginator->count() > 0): ?>

      <script type="text/javascript">
        var pageAction = function(page) {

          var form;
          if ($('filter_form')) {
            form = document.getElementById('filter_form');
          } else if ($('filter_form_sitestoreproduct_<?php echo $this->identity; ?>')) {
            form = $('filter_form_sitestoreproduct_<?php echo $this->identity; ?>');
          }
          form.elements['page'].value = page;

          form.submit();
        };
      </script>

  <?php if (($this->list_view && $this->grid_view) || ($this->grid_view) || ($this->list_view) || ($this->map_view)): ?>
        <div class="sr_sitestoreproduct_browse_lists_view_options b_medium" id="sr_sitestoreproduct_browse_lists_view_options_b_medium_<?php echo $this->identity; ?>">
          <div class="fleft"> 
    <?php if ($this->categoryName && $doNotShowTopContent != 1): ?>
              <h4 class="sr_sitestoreproduct_browse_lists_view_options_head">
      <?php echo $this->translate($this->categoryName); ?>
              </h4>
          <?php endif; ?>
          <?php echo $this->translate(array('%s product found.', '%s product found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
          </div>

    <?php if ($this->grid_view): ?>
            <span class="seaocore_tab_select_wrapper fright">
      <!--              <div class="seaocore_tab_select_view_tooltip"><?php //echo $this->translate("Grid View"); ?></div>-->
              <span class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview(1, '<?php echo $this->identity; ?>');" ></span>
            </span>
    <?php endif; ?>
    <?php if ($this->list_view): ?>
            <span class="seaocore_tab_select_wrapper fright">
      <!--              <div class="seaocore_tab_select_view_tooltip"><?php //echo $this->translate("List View"); ?></div>-->
              <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0, '<?php echo $this->identity; ?>');" ></span>
            </span>
        <?php endif; ?>
        <?php if ($this->map_view && $this->enableLocation): ?>
            <span class="seaocore_tab_select_wrapper fright">
      <!--              <div class="seaocore_tab_select_view_tooltip"><?php //echo $this->translate("List View");  ?></div>-->
              <span class="seaocore_tab_icon tab_icon_map_view" onclick="switchview(2, '<?php echo $this->identity; ?>');" ></span>
            </span>
    <?php endif; ?>
        </div>
          <?php endif; ?>

          <?php if ($this->list_view): ?>

        <div id="grid_view_<?php echo $this->identity; ?>"  style="<?php if (!$this->temViewType): echo "display: block;";
          else: echo "display: none;";
          endif; ?>">

                <?php if (empty($this->viewType)): ?>

            <ul class="sr_sitestoreproduct_browse_list seaocore_browse_list" id="sr_sitestoreproduct_browse_list_<?php echo $this->identity; ?>">
                  <?php foreach ($this->paginator as $sitestoreproduct): ?>
                    <?php $getIntrestedMemberCount = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->getNotifyEmail($sitestoreproduct->product_id, 'COUNT(notifyemail_id)')->query()->fetchColumn(); ?>
                    <?php if (!empty($sitestoreproduct->sponsored)): ?>
                  <li class="sitestoreproduct_q_v_wrap list_sponsered b_medium <?php echo!empty($sitestoreproduct->highlighted) ? 'lists_highlight' : ''; ?>">
                    <?php else: ?>
                  <li class="sitestoreproduct_q_v_wrap b_medium <?php echo!empty($sitestoreproduct->highlighted) ? 'lists_highlight' : ''; ?>">
                    <?php endif; ?>
                  <div class='sr_sitestoreproduct_browse_list_photo b_medium'>
                    <?php $product_id = $sitestoreproduct->product_id; ?>
                    <?php $quickViewButton = true; ?>
                    <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                      <?php if ($sitestoreproduct->featured): ?>
                        <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
                           <?php endif; ?>
                             <?php if ($sitestoreproduct->newlabel): ?>
                        <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                      <?php endif; ?>
                    <?php endif; ?>

                  <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))) ?>

                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
          <?php if (!empty($sitestoreproduct->sponsored)): ?>
                        <div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505');
            ;
            ?>">
                          <?php echo $this->translate('SPONSORED'); ?>                 
                        </div>
          <?php endif; ?>
                            <?php endif; ?>
                  </div>
                          <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 3)): ?>
                    <div class="sr_sitestoreproduct_browse_list_rating">
                            <?php if (is_array($this->statistics) && in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                        <div class="clr">	
                          <div class="sr_sitestoreproduct_browse_list_rating_stats">
                            <?php echo $this->translate("Editor Rating"); ?>
                          </div>
            <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'editor', $sitestoreproduct->getType()); ?>
                          <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                            <span class="sr_sitestoreproduct_browse_list_rating_stars">
                              <span class="fleft">
            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', 'big-star'); ?>
                              </span>
                                  <?php if (count($ratingData) > 1): ?>
                                <i class="fright arrow_btm"></i>
                                    <?php endif; ?>
                            </span>

                                    <?php if (count($ratingData) > 1): ?>
                              <div class="sr_sitestoreproduct_ur_show_rating br_body_bg b_medium">
                                <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                      <?php foreach ($ratingData as $reviewcat): ?>

                                    <div class="o_hidden">
                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                        <div class="parameter_title">
                                        <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                        </div>
                                        <div class="parameter_value">
                                      <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'small-box', $reviewcat['ratingparam_name']); ?>
                                        </div>
                                <?php else: ?>
                                        <div class="parameter_title">
                  <?php echo $this->translate("Overall Rating"); ?>
                                        </div>	
                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                              <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'big-star'); ?>
                                        </div>
                                <?php endif; ?> 
                                    </div>

                              <?php endforeach; ?>
                                </div>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div> 
                        <?php endif; ?>
                        <?php if (is_array($this->statistics) && in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_users) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_users')): ?>
                        <div class="clr">
                          <div class="sr_sitestoreproduct_browse_list_rating_stats">
                                <?php echo $this->translate("User Ratings"); ?><br />
                                <?php
                                $totalUserReviews = $sitestoreproduct->review_count;
                                if ($sitestoreproduct->rating_editor) {
                                  $totalUserReviews = $sitestoreproduct->review_count - 1;
                                }
                                ?>
                            <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
                          </div>
            <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'user', $sitestoreproduct->getType()); ?>
                          <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                            <span class="sr_sitestoreproduct_browse_list_rating_stars">
                              <span class="fleft">
            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', 'big-star'); ?>
                              </span>
                                  <?php if (count($ratingData) > 1): ?>
                                <i class="fright arrow_btm"></i>
                                    <?php endif; ?>
                            </span>

                                    <?php if (count($ratingData) > 1): ?>
                              <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                      <?php foreach ($ratingData as $reviewcat): ?>

                                    <div class="o_hidden">
                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                        <div class="parameter_title">
                                        <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                        </div>
                                        <div class="parameter_value">
                                      <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                        </div>
                                <?php else: ?>
                                        <div class="parameter_title">
                  <?php echo $this->translate("Overall Rating"); ?>
                                        </div>	
                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                              <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                        </div>
                <?php endif; ?> 
                                    </div>

                              <?php endforeach; ?>
                                </div>
                              </div> 
                          <?php endif; ?> 
                          </div>
                        </div>  
                              <?php endif; ?>

                            <?php if (!empty($sitestoreproduct->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                        <div class="clr">
                          <div class="sr_sitestoreproduct_browse_list_rating_stats">
            <!--	                    <?php //echo $this->translate("Overall Rating");    ?><br />-->

                            <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count)) ?>
                          </div>
            <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, null, $sitestoreproduct->getType()); ?>
                          <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                            <span class="sr_sitestoreproduct_browse_list_rating_stars">
                              <span class="fleft">
            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_avg, $ratingType, 'big-star'); ?>
                              </span>
                                  <?php if (count($ratingData) > 1): ?>
                                <i class="fright arrow_btm"></i>
                                    <?php endif; ?>
                            </span>

                                    <?php if (count($ratingData) > 1): ?>
                              <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                                <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                      <?php foreach ($ratingData as $reviewcat): ?>

                                    <div class="o_hidden">
                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                        <div class="parameter_title">
                                        <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                        </div>
                                        <div class="parameter_value">
                                      <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], $ratingType, 'small-box', $reviewcat['ratingparam_name']); ?>
                                        </div>
                                <?php else: ?>
                                        <div class="parameter_title">
                  <?php echo $this->translate("Overall Rating"); ?>
                                        </div>	
                                        <div class="parameter_value" style="margin: 0px 0px 5px;">
                            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                        </div>
                <?php endif; ?> 
                                    </div>

              <?php endforeach; ?>
                                </div>
                              </div> 
            <?php endif; ?> 
                          </div>
                        </div>  
          <?php endif; ?>
                    </div>
                        <?php endif; ?>

                  <div class='sr_sitestoreproduct_browse_list_info'>  

                    <div class='sr_sitestoreproduct_browse_list_info_header o_hidden'>
                      <div class="sr_sitestoreproduct_list_title">
                    <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())); ?>
                      </div>
                      <div class="clear"></div>
                    </div>
                    <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                      <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "" . $this->categoryRouteName . ""); ?>"> 
                    <?php echo $sitestoreproduct->getCategory()->getTitle(true) ?>
                      </a>
                    </div>

                      <?php if (!empty($sitestoreproduct->location) && $this->enableLocation && $this->showLocation): ?>
                        <?php $locationId = Engine_Api::_()->getDbTable('locations', 'sitestoreproduct')->getLocationId($sitestoreproduct->product_id, $sitestoreproduct->location); ?>
                        <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $sitestoreproduct->product_id, 'resouce_type' => 'sitestoreproduct_product', 'location_id' => $locationId, 'flag' => 'map'), $this->translate($sitestoreproduct->location), array('class' => 'smoothbox')); ?>
                      <?php endif; ?>

                      <?php
                      // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                      echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock, true);
                      ?>

                    <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                    <?php
                    if (is_array($this->statistics) && in_array('likeCount', $this->statistics)) {
                      echo $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)) . '<br />';
                    }
                    if (!empty($getIntrestedMemberCount)):
                      echo $this->translate(array('%s buyer intrested.', '%s buyers intrested.', $getIntrestedMemberCount), $this->locale()->toNumber($getIntrestedMemberCount)) . '<br />';
                    endif;
                    ?>
                    </div>

                    <!--							<div class='sr_sitestoreproduct_browse_list_info_blurb'>
        <?php //if($this->bottomLine):   ?>
        <?php //echo $this->viewMore($sitestoreproduct->getBottomLine(), 125, 5000); ?>
                      <?php //else:   ?>
                      <?php //echo $this->viewMore(strip_tags($sitestoreproduct->body), 125, 5000); ?>
                      <?php //endif;   ?>
                                  </div>-->
                    <div class="sr_sitestoreproduct_browse_list_info_footer clr o_hidden">
                      <div>
                      <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct, $this->identity); ?>
                      </div>
                      <div>
                      <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '')); ?>
                      </div>
                    </div>
                    <div class='sr_sitestoreproduct_browse_list_options clr'>
                      <?php
                      if ($sitestoreproduct->owner_id == $this->viewer_id || $this->can_edit == 1):
                        echo $this->htmlLink($sitestoreproduct->getHref(), $this->translate('View Product'), array('class' => 'buttonlink item_icon_sitestoreproduct'));
                        echo $this->htmlLink(array('route' => 'sitestoreproduct_specific', 'action' => 'edit', 'product_id' => $sitestoreproduct->product_id), $this->translate('Dashboard'), array('class' => 'buttonlink seaocore_icon_edit'));
                        echo $this->htmlLink($this->url(array('action' => 'delete', 'product_id' => $sitestoreproduct->product_id), "sitestoreproduct_general", true), $this->translate('Delete Product'), array('class' => 'buttonlink seaocore_icon_delete'));
                        if (empty($sitestoreproduct->highlighted)):
                          echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'highlighted', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Highlighted'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_highlighted'));

                        else:

                          echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'highlighted', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Un-highlighted'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_unhighlighted'));
                        endif;
                      endif;

                      if (!empty($this->viewer_level_id) && ($this->viewer_level_id == 1)):
                        if (empty($sitestoreproduct->featured)):
                          echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'featured', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Featured'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_featured'));
                        else:
                          echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'featured', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Un-featured'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_unfeatured'));
                        endif;


                        if (empty($sitestoreproduct->sponsored)):
                          echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'sponsored', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Sponsored'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_sponsored'));
                        else:
                          echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'sponsored', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Un-sponsored'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_unsponsored'));
                        endif;
                      endif;
                      ?>
                    </div>
                  </div>
                </li>
                <?php endforeach; ?>
            </ul>

                <?php else: ?>

            <ul class="sr_sitestoreproduct_browse_list seaocore_browse_list" id="sr_sitestoreproduct_browse_list_<?php echo $this->identity; ?>">
                  <?php foreach ($this->paginator as $sitestoreproduct): ?>
                    <?php $getIntrestedMemberCount = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->getNotifyEmail($sitestoreproduct->product_id, 'COUNT(notifyemail_id)')->query()->fetchColumn(); ?>
                    <?php if (!empty($sitestoreproduct->sponsored)): ?>
                  <li class="sitestoreproduct_q_v_wrap list_sponsered b_medium <?php echo!empty($sitestoreproduct->highlighted) ? 'lists_highlight' : ''; ?>">
                    <?php else: ?>
                  <li class="sitestoreproduct_q_v_wrap b_medium <?php echo!empty($sitestoreproduct->highlighted) ? 'lists_highlight' : ''; ?>">
                    <?php endif; ?>
                  <div class='sr_sitestoreproduct_browse_list_photo b_medium'>
                    <?php $product_id = $sitestoreproduct->product_id; ?>
                    <?php $quickViewButton = true; ?>
                      <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
                      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                      <?php if ($sitestoreproduct->featured): ?>
                        <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></span>
                      <?php endif; ?>
          <?php if ($sitestoreproduct->newlabel): ?>
                        <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                      <?php endif; ?>
        <?php endif; ?>
                        <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.normal', '', array('align' => 'center'))) ?>
                        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.fs.markers', 1)): ?>
                        <?php if (!empty($sitestoreproduct->sponsored)): ?>
                        <div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505'); ?>">
            <?php echo $this->translate('SPONSORED'); ?>                 
                        </div>
                              <?php endif; ?>
                            <?php endif; ?>
                  </div>
                  <div class="sr_sitestoreproduct_browse_list_rating">
                          <?php if (is_array($this->statistics) && in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                      <div class="clr">	
                        <div class="sr_sitestoreproduct_browse_list_rating_stats">
                          <?php echo $this->translate("Editor Rating"); ?>
                        </div>
          <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'editor', $sitestoreproduct->getType()); ?>
                        <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                          <span class="sr_sitestoreproduct_browse_list_rating_stars">
                            <span class="fleft">
          <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', 'big-star'); ?>
                            </span>
                                <?php if (count($ratingData) > 1): ?>
                              <i class="fright arrow_btm"></i>
                                  <?php endif; ?>
                          </span>

                                  <?php if (count($ratingData) > 1): ?>
                            <div class="sr_sitestoreproduct_ur_show_rating br_body_bg b_medium">
                              <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                    <?php foreach ($ratingData as $reviewcat): ?>

                                  <div class="o_hidden">
                                      <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                      <div class="parameter_title">
                                      <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                      </div>
                                      <div class="parameter_value">
                                    <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'small-box', $reviewcat['ratingparam_name']); ?>
                                      </div>
                              <?php else: ?>
                                      <div class="parameter_title">
                <?php echo $this->translate("Overall Rating"); ?>
                                      </div>	
                                      <div class="parameter_value" style="margin: 0px 0px 5px;">
                            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'editor', 'big-star'); ?>
                                      </div>
                              <?php endif; ?> 
                                  </div>

                            <?php endforeach; ?>
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div> 
                      <?php endif; ?>
                      <?php if (is_array($this->statistics) && in_array('viewRating', $this->statistics) && !empty($sitestoreproduct->rating_users) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_users')): ?>
                      <div class="clr">
                        <div class="sr_sitestoreproduct_browse_list_rating_stats">
                              <?php echo $this->translate("User Ratings"); ?><br />
                              <?php
                              $totalUserReviews = $sitestoreproduct->review_count;
                              if ($sitestoreproduct->rating_editor) {
                                $totalUserReviews = $sitestoreproduct->review_count - 1;
                              }
                              ?>
                          <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
                        </div>
          <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'user', $sitestoreproduct->getType()); ?>
                        <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                          <span class="sr_sitestoreproduct_browse_list_rating_stars">
                            <span class="fleft">
          <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', 'big-star'); ?>
                            </span>
                                <?php if (count($ratingData) > 1): ?>
                              <i class="fright arrow_btm"></i>
                                  <?php endif; ?>
                          </span>

                                  <?php if (count($ratingData) > 1): ?>
                            <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                              <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                    <?php foreach ($ratingData as $reviewcat): ?>

                                  <div class="o_hidden">
                                      <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                      <div class="parameter_title">
                                      <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                      </div>
                                      <div class="parameter_value">
                                    <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                      </div>
                              <?php else: ?>
                                      <div class="parameter_title">
                <?php echo $this->translate("Overall Rating"); ?>
                                      </div>	
                                      <div class="parameter_value" style="margin: 0px 0px 5px;">
                            <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                      </div>
              <?php endif; ?> 
                                  </div>

                            <?php endforeach; ?>
                              </div>
                            </div> 
                        <?php endif; ?> 
                        </div>
                      </div>  
                            <?php endif; ?>

                          <?php if (!empty($sitestoreproduct->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                      <div class="clr">
                        <div class="sr_sitestoreproduct_browse_list_rating_stats">
          <!--	                    <?php //echo $this->translate("Overall Rating");    ?><br />-->

                          <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $sitestoreproduct->review_count), $this->locale()->toNumber($sitestoreproduct->review_count)) ?>
                        </div>
          <?php $ratingData = $this->ratingTable->ratingbyCategory($sitestoreproduct->product_id, null, $sitestoreproduct->getType()); ?>
                        <div class="sr_sitestoreproduct_ur_show_rating_star fnone o_hidden">
                          <span class="sr_sitestoreproduct_browse_list_rating_stars">
                            <span class="fleft">
          <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_avg, $ratingType, 'big-star'); ?>
                            </span>
                                <?php if (count($ratingData) > 1): ?>
                              <i class="fright arrow_btm"></i>
                                  <?php endif; ?>
                          </span>

                                  <?php if (count($ratingData) > 1): ?>
                            <div class="sr_sitestoreproduct_ur_show_rating  br_body_bg b_medium">
                              <div class="sr_sitestoreproduct_profile_rating_parameters sr_sitestoreproduct_ur_show_rating_box">

                                    <?php foreach ($ratingData as $reviewcat): ?>

                                  <div class="o_hidden">
                                      <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                      <div class="parameter_title">
                                      <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                      </div>
                                      <div class="parameter_value">
                                    <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], $ratingType, 'small-box', $reviewcat['ratingparam_name']); ?>
                                      </div>
                              <?php else: ?>
                                      <div class="parameter_title">
                <?php echo $this->translate("Overall Rating"); ?>
                                      </div>	
                                      <div class="parameter_value" style="margin: 0px 0px 5px;">
                <?php echo $this->showRatingStarSitestoreproduct($reviewcat['avg_rating'], $ratingType, 'big-star'); ?>
                                      </div>
              <?php endif; ?> 
                                  </div>

                            <?php endforeach; ?>
                              </div>
                            </div> 
          <?php endif; ?> 
                        </div>
                      </div>  
                    <?php endif; ?>
                  </div>

                  <div class="sr_sitestoreproduct_browse_list_info">
                    <div class="sr_sitestoreproduct_browse_list_info_header">
                      <div class="sr_sitestoreproduct_list_title">
                    <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncation), array('title' => $sitestoreproduct->getTitle())); ?>
                      </div>
                    </div>  

                    <!--                  <div class='sr_sitestoreproduct_browse_list_info_blurb'>
                        <?php //if($this->bottomLine):  ?>
                        <?php //echo $this->viewMore($sitestoreproduct->getBottomLine(), 125, 5000); ?>
        <?php //else:  ?>
        <?php //echo $this->viewMore(strip_tags($sitestoreproduct->body), 125, 5000); ?>
                    <?php //endif;    ?>
                                      </div>-->

                    <!--                  <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                    <?php //echo $this->timestamp(strtotime($sitestoreproduct->creation_date)) ?><?php // if($this->postedby): ?> <?php // echo $this->translate('created by'); ?>
        <?php // echo $this->htmlLink($sitestoreproduct->getOwner()->getHref(), $sitestoreproduct->getOwner()->getTitle())     ?><?php //endif;     ?>
                                      </div>-->
                    <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                      <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "" . $this->categoryRouteName . ""); ?>"> 
                      <?php echo $sitestoreproduct->getCategory()->getTitle(true) ?>
                      </a>
                    </div>

                      <?php
                      // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                      echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock, true);
                      ?>

                    <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                        <?php
                        if (is_array($this->statistics) && in_array('likeCount', $this->statistics)) {
                          echo $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)) . '<br />';
                        }
                        if (!empty($getIntrestedMemberCount)):
                          echo $this->translate(array('%s buyer intrested.', '%s buyers intrested.', $getIntrestedMemberCount), $this->locale()->toNumber($getIntrestedMemberCount)) . '<br />';
                        endif;
                        ?>
                    </div>

                    <div class="mtop10 sr_sitestoreproduct_browse_list_info_footer clr o_hidden">
                      <div>
                        <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct); ?>
                      </div>
                      <div>
                        <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '')); ?>
                      </div>
                      <div class='sitestorevideo_profile_options'>
                        <?php
                        if ($sitestoreproduct->owner_id == $this->viewer_id || $this->can_edit == 1):
                          echo $this->htmlLink($sitestoreproduct->getHref(), $this->translate('View Product'), array('class' => 'buttonlink item_icon_sitestoreproduct'));
                          echo $this->htmlLink(array('route' => 'sitestoreproduct_specific', 'action' => 'edit', 'product_id' => $sitestoreproduct->product_id), $this->translate('Dashboard'), array('class' => 'buttonlink seaocore_icon_edit'));
                          echo $this->htmlLink($this->url(array('action' => 'delete', 'product_id' => $sitestoreproduct->product_id), "sitestoreproduct_general", true), $this->translate('Delete Product'), array('class' => 'buttonlink seaocore_icon_delete'));
                          if (empty($sitestoreproduct->highlighted)):
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'highlighted', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Highlighted'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_highlighted'));
                          else:
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'highlighted', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Un-highlighted'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_highlighted'));
                          endif;
                        endif;

                        if (!empty($this->viewer_level_id) && ($this->viewer_level_id == 1)):
                          if (empty($sitestoreproduct->featured)):
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'featured', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Featured'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_featured'));
                          else:
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'featured', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Un-featured'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_featured'));
                          endif;


                          if (empty($sitestoreproduct->sponsored)):
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'sponsored', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Sponsored'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_sponsored'));
                          else:
                            echo $this->htmlLink(array('route' => 'sitestoreproduct_manage', 'action' => 'sponsored', 'product_id' => $sitestoreproduct->product_id, 'tab' => $this->identity, 'is_store' => $this->is_subject), $this->translate('Make Un-sponsored'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_sponsored'));
                          endif;
                        endif;
                        ?>
                      </div>
                    </div>
                </li>
              <?php endforeach; ?>
            </ul>

    <?php endif; ?>

        </div>

              <?php endif; ?>

                <?php if ($this->grid_view): ?>
        <div id="image_view_<?php echo $this->identity; ?>" class="sr_sitestoreproduct_container" style="<?php if ($this->temViewType) : echo "display: block;";
              else: "display: none;";
              endif; ?>">
          <ul class="sitestoreproduct_grid_view mtop10">
                    <?php $isLarge = ($this->columnWidth > 170); ?>
                    <?php foreach ($this->paginator as $sitestoreproduct): ?>    
                      <?php $getIntrestedMemberCount = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->getNotifyEmail($sitestoreproduct->product_id, 'COUNT(notifyemail_id)')->query()->fetchColumn(); ?>
              <li class="sitestoreproduct_q_v_wrap g_b <?php if ($isLarge): ?>largephoto<?php endif; ?>" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
                <div>
                      <?php if ($sitestoreproduct->newlabel): ?>
                    <i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
      <?php endif; ?>
                  <div class="sitestoreproduct_grid_view_thumb_wrapper">
                    <?php $product_id = $sitestoreproduct->product_id; ?>
                    <?php $quickViewButton = true; ?>
      <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>        
                    <a href="<?php echo $sitestoreproduct->getHref() ?>" class ="sitestoreproduct_grid_view_thumb">
      <?php
      $url = $sitestoreproduct->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');


      if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_normal.png';
      endif;
      ?>
                      <span style="background-image: url(<?php echo $url; ?>); <?php if ($isLarge): ?> height:160px; <?php endif; ?>"></span>
                    </a>
                  </div>  
                  <div class="sitestoreproduct_grid_title">
                    <?php echo $this->htmlLink($sitestoreproduct->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getTitle(), $this->title_truncationGrid), array('title' => $sitestoreproduct->getTitle())) ?>
                  </div>
                  <div class="sitestoreproduct_grid_stats clr">
                    <a href="<?php echo $this->url(array('category_id' => $sitestoreproduct->category_id, 'categoryname' => $sitestoreproduct->getCategory()->getCategorySlug()), "" . $this->categoryRouteName . ""); ?>"> <?php echo $this->translate($sitestoreproduct->getCategory()->getTitle(true)) ?> </a>
                  </div>
      <?php
      // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
      echo $this->getProductInfo($sitestoreproduct, $this->identity, 'grid_view', $this->showAddToCart, $this->showinStock);
      ?>

                  <div class='sr_sitestoreproduct_browse_list_info_stat seaocore_txt_light'>
                    <?php
                    if (is_array($this->statistics) && in_array('likeCount', $this->statistics)) {
                      echo $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)) . '<br />';
                    }
                    if (!empty($getIntrestedMemberCount)):
                      echo $this->translate(array('%s buyer intrested.', '%s buyers intrested.', $getIntrestedMemberCount), $this->locale()->toNumber($getIntrestedMemberCount)) . '<br />';
                    endif;
                    ?>
                  </div>

                        <?php if (is_array($this->statistics) && in_array('viewRating', $this->statistics)): ?>
                    <div class="sitestoreproduct_grid_rating">                     
                          <?php if ($ratingValue == 'rating_both'): ?>
                            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
                            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
                          <?php else: ?>
                            <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
        <?php endif; ?>
                    </div>
      <?php endif; ?>
                  <div class="sitestoreproduct_grid_view_list_btm">
                    <div class="sitestoreproduct_grid_view_list_footer b_medium">
      <?php echo $this->compareButtonSitestoreproduct($sitestoreproduct); ?>
                      <span class="fright">
              <?php if ($sitestoreproduct->sponsored == 1): ?>
                          <i class="sr_sitestoreproduct_icon seaocore_icon_sponsored" title="<?php echo $this->translate('Sponsored'); ?>"></i>
          <?php endif; ?>
          <?php if ($sitestoreproduct->featured == 1): ?>
                          <i class="sr_sitestoreproduct_icon seaocore_icon_featured" title="<?php echo $this->translate('Featured'); ?>"></i>
          <?php endif; ?>
          <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'icon_wishlist_add', 'classLink' => 'sr_sitestoreproduct_wishlist_link', 'text' => '')); ?>
                      </span>
                    </div>
                  </div>  
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

  <?php if ($this->map_view): ?> 
          <?php if ($this->enableLocation): ?>              
          <div id="siteevent_map_canvas_view_browse" <?php if ($this->defaultLayout != 'map_view'): ?> style="display: none;" <?php endif; ?>>
            <div class="seaocore_map clr" style="overflow:hidden;">
              <div id="siteevent_browse_map_canvas" class="siteevent_list_map" style="height:250px;"> </div>
              <div class="clear mtop10"></div>
                <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
                <?php if (!empty($siteTitle)) : ?>
                <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
                <?php endif; ?>
            </div>
          </div>
    <?php endif; ?>
          <?php endif; ?>
      <div class="clear"></div>
      <div class="seaocore_pagination">
  <?php // echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true,));    ?>
        <div>
            <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
            <div id="store_table_rate_previous_<?php echo $this->identity; ?>" class="paginator_previous">
              <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                  'onclick' => "paginationProductPrevious('$this->identity')",
                  'class' => 'buttonlink icon_previous'
              ));
              ?>
              <span id="tablerate_spinner_prev_<?php echo $this->identity; ?>"></span>
            </div>
      <?php endif; ?>
  <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
            <div id="store_table_rate_next_<?php echo $this->identity; ?>" class="paginator_next">
              <span id="tablerate_spinner_next_<?php echo $this->identity; ?>"></span>
    <?php
    echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
        'onclick' => "paginationProductNext('$this->identity')",
        'class' => 'buttonlink_right icon_next'
    ));
    ?>
            </div>
  <?php endif; ?>
        </div>
      </div>	
<?php elseif (isset($this->params['tag_id']) || isset($this->params['category_id'])): ?>
      <br/>
      <div class="tip mtip10">
        <span> <?php echo $this->translate('Nobody has created a product with that criteria.'); ?>
        </span> 
      </div>
<?php else: ?>
      <div class="tip mtop10"> 
        <span> 
      <?php echo $this->translate('No products have been created yet in this store.'); ?>
        </span>
      </div>
      <div class="clear"></div>
      <div class="seaocore_pagination">
        <div>
          <div id="store_table_rate_previous_<?php echo $this->identity; ?>">
            <span id="tablerate_spinner_prev_<?php echo $this->identity; ?>"></span>
          </div>
          <div id="store_table_rate_next_<?php echo $this->identity; ?>">
            <span id="tablerate_spinner_next_<?php echo $this->identity; ?>"></span>
          </div>
        </div>
      </div>	
<?php endif; ?>
  </div>
<?php if (empty($this->isajax)) : ?>
  </div>
<?php endif; ?>

<script type="text/javascript" >
  function switchview(flage, identity) {
    if (flage == 1) {
      if ($('image_view_' + identity)) {
        if ($('sr_sitestoreproduct_map_canvas_view_browse'))
          $('sr_sitestoreproduct_map_canvas_view_browse').style.display = 'none';
        if ($("siteevent_map_canvas_view_browse"))
          $("siteevent_map_canvas_view_browse").style.display = 'none';
        if ($('grid_view_' + identity))
          $('grid_view_' + identity).style.display = 'none';
        $('image_view_' + identity).style.display = 'block';
      }
    } else if (flage == 0) {
      if ($('grid_view_' + identity)) {
        if ($('sr_sitestoreproduct_map_canvas_view_browse'))
          $('sr_sitestoreproduct_map_canvas_view_browse').style.display = 'none';
        if ($("siteevent_map_canvas_view_browse"))
          $("siteevent_map_canvas_view_browse").style.display = 'none';
        $('grid_view_' + identity).style.display = 'block';
        if ($('image_view_' + identity))
          $('image_view_' + identity).style.display = 'none';
      }
    } else if (flage == 2) {
      if ($("siteevent_map_canvas_view_browse")) {
        if ($('sr_sitestoreproduct_map_canvas_view_browse'))
          $('sr_sitestoreproduct_map_canvas_view_browse').style.display = 'none';
        if ($('grid_view_' + identity))
          $('grid_view_' + identity).style.display = 'none';
        $('image_view_' + identity)
        $('image_view_' + identity).style.display = 'none';
        $("siteevent_map_canvas_view_browse").style.display = 'block';
<?php if ($this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>
          google.maps.event.trigger(map, 'resize');
          map.setZoom(<?php echo $defaultZoom ?>);
          map.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
<?php endif; ?>
      }
    }
    tempCurrentViewType = flage;
  }

  function paginationProductNext(identity) {
    var temViewType = OrderproductselectArray[identity]['temViewType'];
    var tempSection = 0;
    if ($('sitestoreproduct_products_sections_selectbox_' + identity)) {
      tempSection = $('sitestoreproduct_products_sections_selectbox_' + identity).value;
    }
    var downpayment = 0;
    if ($('sitestoreproduct_products_downpayment_' + identity)) {
      downpayment = $('sitestoreproduct_products_downpayment_' + identity).value;
    }
    var sitestoreproduct_products_search_input_text = '';
    if ($('sitestoreproduct_products_search_input_text_' + identity))
      sitestoreproduct_products_search_input_text = $('sitestoreproduct_products_search_input_text_' + identity).value;

    var sitestoreproduct_products_location_input_text = '';
    if ($('sitestoreproduct_products_location_input_text_' + identity))
      sitestoreproduct_products_location_input_text = $('sitestoreproduct_products_location_input_text_' + identity).value;

    var sitestoreproduct_products_search_input_selectbox = '';
    if ($('sitestoreproduct_products_search_input_selectbox_' + identity))
      sitestoreproduct_products_search_input_selectbox = $('sitestoreproduct_products_search_input_selectbox_' + identity).value;

    var sitestoreproduct_products_categories_selectbox = '';
    if ($('sitestoreproduct_products_categories_selectbox_' + identity))
      sitestoreproduct_products_categories_selectbox = $('sitestoreproduct_products_categories_selectbox_' + identity).value;
    $('tablerate_spinner_next_' + identity).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + '<?php echo ($this->user_layout) ? 'sitestore/widget' : 'widget'; ?>',
      data: {
        format: 'html',
        subject: en4.core.subject.guid,
        isajax: 1,
        page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
        'search': sitestoreproduct_products_search_input_text,
        'location': sitestoreproduct_products_location_input_text,
        'selectbox': sitestoreproduct_products_search_input_selectbox,
        'section': tempSection,
        'downpayment': downpayment,
        'category': sitestoreproduct_products_categories_selectbox,
        'load_content': 1,
        'identity': identity,
        'content_id': identity,
        'ajaxView': '<?php echo $this->ajaxView ?>',
        'store_id': <?php echo $this->store_id; ?>,
        'tab': OrderproductselectArray[identity]['tab'],
        'temViewType': OrderproductselectArray[identity]['temViewType'],
        'temp_layouts_views': OrderproductselectArray[identity]['temp_layouts_views'],
        'temp_is_subject': OrderproductselectArray[identity]['temp_is_subject'],
        'itemCount': OrderproductselectArray[identity]['itemCount'],
        'add_to_cart': OrderproductselectArray[identity]['add_to_cart'],
        'in_stock': OrderproductselectArray[identity]['in_stock'],
        'categoryRouteName': OrderproductselectArray[identity]['categoryRouteName'],
        'ratingType': OrderproductselectArray[identity]['ratingType'],
        'title_truncation': OrderproductselectArray[identity]['title_truncation'],
        'title_truncationGrid': OrderproductselectArray[identity]['title_truncation'],
        'postedby': OrderproductselectArray[identity]['postedby'],
        'columnWidth': OrderproductselectArray[identity]['columnWidth'],
        'columnHeight': OrderproductselectArray[identity]['columnHeight'],
        'tempStatistics': OrderproductselectArray[identity]['tempStatistics'],
        'temp_product_types': OrderproductselectArray[identity]['temp_product_types'],
        'searchByOptions': OrderproductselectArray[identity]['searchByOptions'],
        'bottomLine': OrderproductselectArray[identity]['bottomLine'],
        'viewType': OrderproductselectArray[identity]['viewType'],
        'layouts_order': OrderproductselectArray[identity]['layouts_order'],
        'showClosed': OrderproductselectArray[identity]['showClosed'],
        'orderby': OrderproductselectArray[identity]['orderby'],
        'section' : OrderproductselectArray[identity]['section'],
                'category' : OrderproductselectArray[identity]['category']
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('tablerate_spinner_next_' + identity).innerHTML = '';
        $('sitestoreproduct_search_' + identity).innerHTML = responseHTML;
        switchview(temViewType, identity);
      }
    }));
  }

  function paginationProductPrevious(identity) {
    var temViewType = OrderproductselectArray[identity]['temViewType'];
    $('tablerate_spinner_prev_' + <?php echo $this->identity ?>).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
    var tempSection = 0;
    if ($('sitestoreproduct_products_sections_selectbox_' + identity)) {
      tempSection = $('sitestoreproduct_products_sections_selectbox_' + identity).value;
    }
    var downpayment = 0;
    if ($('sitestoreproduct_products_downpayment_' + identity)) {
      downpayment = $('sitestoreproduct_products_downpayment_' + identity).value;
    }
    var sitestoreproduct_products_search_input_text = '';
    if ($('sitestoreproduct_products_search_input_text_' + identity))
      sitestoreproduct_products_search_input_text = $('sitestoreproduct_products_search_input_text_' + identity).value;

    var sitestoreproduct_products_location_input_text = '';
    if ($('sitestoreproduct_products_location_input_text_' + identity))
      sitestoreproduct_products_location_input_text = $('sitestoreproduct_products_location_input_text_' + identity).value;

    var sitestoreproduct_products_search_input_selectbox = '';
    if ($('sitestoreproduct_products_search_input_selectbox_' + identity))
      sitestoreproduct_products_search_input_selectbox = $('sitestoreproduct_products_search_input_selectbox_' + identity).value;

    var sitestoreproduct_products_categories_selectbox = '';
    if ($('sitestoreproduct_products_categories_selectbox_' + identity))
      sitestoreproduct_products_categories_selectbox = $('sitestoreproduct_products_categories_selectbox_' + identity).value;

    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + '<?php echo ($this->user_layout) ? 'sitestore/widget' : 'widget'; ?>',
      data: {
        format: 'html',
        subject: en4.core.subject.guid,
        isajax: 1,
        temp_is_subject: <?php echo $this->is_subject ?>,
        page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>,
        'search': sitestoreproduct_products_search_input_text,
        'location': sitestoreproduct_products_location_input_text,
        'selectbox': sitestoreproduct_products_search_input_selectbox,
        'section': tempSection,
        'downpayment': downpayment,
        'category': sitestoreproduct_products_categories_selectbox,
        'load_content': 1,
        'identity': identity,
        'content_id': identity,
        'ajaxView': '<?php echo $this->ajaxView ?>',
        'store_id': <?php echo $this->store_id; ?>,
        'tab': OrderproductselectArray[identity]['tab'],
        'temViewType': OrderproductselectArray[identity]['temViewType'],
        'temp_layouts_views': OrderproductselectArray[identity]['temp_layouts_views'],
        'temp_is_subject': OrderproductselectArray[identity]['temp_is_subject'],
                'itemCount': OrderproductselectArray[identity]['itemCount'],
        'add_to_cart': OrderproductselectArray[identity]['add_to_cart'],
        'in_stock': OrderproductselectArray[identity]['in_stock'],
        'categoryRouteName': OrderproductselectArray[identity]['categoryRouteName'],
        'ratingType': OrderproductselectArray[identity]['ratingType'],
        'title_truncation': OrderproductselectArray[identity]['title_truncation'],
        'title_truncationGrid': OrderproductselectArray[identity]['title_truncation'],
        'postedby': OrderproductselectArray[identity]['postedby'],
        'columnWidth': OrderproductselectArray[identity]['columnWidth'],
        'columnHeight': OrderproductselectArray[identity]['columnHeight'],
        'tempStatistics': OrderproductselectArray[identity]['tempStatistics'],
        'temp_product_types': OrderproductselectArray[identity]['temp_product_types'],
        'searchByOptions': OrderproductselectArray[identity]['searchByOptions'],
        'bottomLine': OrderproductselectArray[identity]['bottomLine'],
        'viewType': OrderproductselectArray[identity]['viewType'],
        'layouts_order': OrderproductselectArray[identity]['layouts_order'],
        'showClosed': OrderproductselectArray[identity]['showClosed'],
        'orderby': OrderproductselectArray[identity]['orderby'],
        'section' : OrderproductselectArray[identity]['section'],
                'category' : OrderproductselectArray[identity]['category']
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('tablerate_spinner_prev_' + identity).innerHTML = '';
        $('sitestoreproduct_search_' + identity).innerHTML = responseHTML;
        switchview(temViewType, identity);
      }
    }));
  }
</script>
<?php if ($this->enableLocation): ?>
  <script type="text/javascript">

    var gmarkers = [];
  // global "map" variable
    var map = null;
    var infowindow = [];
    function srInitializeMap(element_id) {
      // create the map
      var myOptions = {
        zoom: <?php echo $defaultZoom; ?>,
        center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
        navigationControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }

      map = new google.maps.Map(document.getElementById("siteevent_browse_map_canvas"), myOptions);
      google.maps.event.addListener(map, 'click', function() {
        infowindow.close();
        google.maps.event.trigger(map, 'resize');
      });

      infowindow = new google.maps.InfoWindow(
              {
                size: new google.maps.Size(250, 50)
              });
    }

    function setSRMarker(element_id, latlng, bounce, html, title_list) {
      var contentString = html;
      if (bounce == 0) {
        var marker = new google.maps.Marker({
          position: latlng,
          map: map
  //                            title: title_list,
  //                            animation: google.maps.Animation.DROP,
  //                            zIndex: Math.round(latlng.lat() * -100000) << 5
        });
      }
      else {
        var marker = new google.maps.Marker({
          position: latlng,
          map: map,
          title: title_list,
          draggable: false,
          animation: google.maps.Animation.BOUNCE
        });
      }
      gmarkers.push(marker);

      google.maps.event.addListener(marker, 'click', function() {
        infowindow.setContent(contentString);
        google.maps.event.trigger(map, 'resize');

        infowindow.open(map, marker);
      });
    }
    en4.core.runonce.add(function() {
      srInitializeMap("<?php echo $this->identity ?>");
  <?php if (!empty($this->locations) && $this->enableLocation): ?>
    <?php foreach ($this->locations as $location) : ?>
          var point = new google.maps.LatLng(<?php echo $location->latitude ?>,<?php echo $location->longitude ?>);
          var contentString = '<div id="content">' +
                  '<div id="siteNotice">' +
                  '</div>' + '  <ul class="sitestores_locationdetails"><li>' +
                  '<div class="sitestores_locationdetails_info_title">' +
                  '<?php echo $this->htmlLink($this->locationsProduct[$location->product_id]->getHref(), $this->locationsProduct[$location->product_id]->getTitle()); ?>' +
                  '<div class="fright">' +
                  '<span >' +
      <?php if ($this->locationsProduct[$location->product_id]->featured == 1): ?>
            '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>' + <?php endif; ?>
          '</span>' +
                  '<span>' +
      <?php if ($this->locationsProduct[$location->product_id]->sponsored == 1): ?>
            '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>' +
      <?php endif; ?>
          '</span>' +
                  '</div>' +
                  '<div class="clr"></div>' +
                  '</div>' +
                  '<div class="sitestores_locationdetails_photo" >' +
                  '<?php echo $this->htmlLink($this->locationsProduct[$location->product_id]->getHref(), $this->itemPhoto($this->locationsProduct[$location->product_id], 'thumb.normal', '', array('align' => 'center'))); ?>' +
                  '</div>' +
                  '<div class="sitestores_locationdetails_info">' +
      <?php if (!empty($this->statistics)) : ?>
            '<div class="sitestores_locationdetails_info_date">' +
        <?php
        $statistics = '';

        if (in_array('likeCount', $this->statistics)) {
          $statistics .= $this->string()->escapeJavascript($this->translate(array('%s like', '%s likes', $this->locationsProduct[$location->product_id]->like_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->like_count))) . ', ';
        }

        if (in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
          $statistics .= $this->string()->escapeJavascript($this->translate(array('%s review', '%s reviews', $this->locationsProduct[$location->product_id]->review_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->review_count))) . ', ';
        }

        if (in_array('commentCount', $this->statistics)) {
          $statistics .= $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->locationsProduct[$location->product_id]->comment_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->comment_count))) . ', ';
        }


        if (in_array('viewCount', $this->statistics)) {
          $statistics .= $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $this->locationsProduct[$location->product_id]->view_count), $this->locale()->toNumber($this->locationsProduct[$location->product_id]->view_count))) . ', ';
        }


        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');
        ?>
            '<?php echo $statistics; ?>' +
                    '</div>' +
      <?php endif; ?>
          '<div class="sitestores_locationdetails_info_date">' +
                  '<?php echo $this->htmlLink("http://maps.google.com/?daddr=" . urlencode($location->location), $this->translate($location->location), array('target' => 'blank')) ?>' +
                  '</div>' +
                  '</div>' +
                  '<div class="clr"></div>' +
                  '</li> </ul>' +
                  '</div>';
          setSRMarker(<?php echo $this->identity ?>, point, 0, contentString, "<?php echo $this->locationsProduct[$location->product_id]->getTitle() ?>");
    <?php endforeach; ?>
  <?php endif; ?>
    });
  </script>
<?php endif; ?>
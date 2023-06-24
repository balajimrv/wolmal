<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: account.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
 $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); 
 $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/follow.js'); ?>

<?php

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
      $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css');
    }
    $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');  
    $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/styles/style_sitestore.css');

$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css');
  // $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/scripts/core.js');
  $myStoreMneus = $this->myStoreMenus;
  $getBaseUrl = $this->url(array('action' => 'account'), 'sitestoreproduct_general', true);
  $tempAddressUrl = $this->url(array('action' => 'manage-address') , 'sitestoreproduct_general', true);
  $tempOrderUrl = $this->url(array('action' => 'my-order'), 'sitestoreproduct_product_general', true);
  $tempOrderViewUrl = $this->url(array('action' => 'order-view', 'order_id' => $this->orderId, 'page_viewer' => 1), 'sitestoreproduct_general', true);
  $tempOrderShipUrl = $this->url(array('action' => 'order-ship', 'order_id' => $this->orderId, 'page_viewer' => 1), 'sitestoreproduct_general', true);

  
  $tempDownloadproductUrl = $this->url(array('action' => 'download-products', 'order_id' => $this->orderId), 'sitestoreproduct_product_general', true);
 $tempWishlistUrl = $this->url(array('action' => 'my-wishlists', 'hide_follow' => 1), 'sitestoreproduct_wishlist_general', true);
 //$tempWishlistUrl = $tempWishlistUrl . '?search_wishlist=my_wishlists';

   $storesILike = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mylike.show', 1); 
   $tempMyStoresUrl = $this->url(array('action' => 'manage'), 'sitestore_general', true);
   $tempStoreLikeUrl = $this->url(array('action' => 'mylikes'), 'sitestore_like', true);
   $tempStoreAdminUrl = $this->url(array('action' => 'my-stores'), 'sitestore_manageadmins', true);
  
  $tempOnloadUrl = '';
  
  switch($this->showMenu) {
    case 'my-address': $tempOnloadUrl = $tempAddressUrl; break;
    case 'my-orders': 
      $tempOnloadUrl = $tempOrderUrl; 
      if( !empty($this->showSubMenu) )
      {
        if( $this->showSubMenu == 'order-view' )
        {
          $tempOnloadUrl = $tempOrderViewUrl;
        }
        if( $this->showSubMenu == 'order-shipment' )
        {
          $tempOnloadUrl = $tempOrderShipUrl;
        }
      }
      
    break;
    case 'my-wishlists': $tempOnloadUrl = $tempWishlistUrl; break;
    case 'my-downloadable-products': $tempOnloadUrl = $tempDownloadproductUrl; break;
    
    default: $tempOnloadUrl = $tempMyStoresUrl; break;
  }
?>
<script type="text/javascript">
  var getBaseUrl = '<?php echo $getBaseUrl; ?>/menuType/';
  // var getBaseParam = '';
  var getMethod = '/method/';
  var activeTabId = '<?php echo $this->showMenu ?>';
  window.addEvent('domready', function(){
    myAccountUrl('<?php echo $this->showMenu; ?>', '<?php echo $this->showSubMenu; ?>', <?php echo $this->orderId; ?>, '<?php echo $tempOnloadUrl ?>')
});

  myAccountUrl = function(id, subId, orderId, url) {
    var BaseUrl = getBaseUrl + id;

      if( subId != '' ) {
        BaseUrl = BaseUrl + '/subMenuType/' + subId ;
      }
      if( orderId != 0 ){
        BaseUrl = BaseUrl + '/orderId/'+ orderId;
      }

    if(BaseUrl && typeof history.pushState != 'undefined') {
      history.pushState( {}, document.title, BaseUrl );
    }
    
    // getBaseUrl = getBaseUrl;
    if($('dynamic_menus_content') != null) {
      $('dynamic_menus_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/spinner_temp.gif" /></center>';
    }

    if ($type($('sitestoreproduct_menu_' + activeTabId))) {
      $('sitestoreproduct_menu_' + activeTabId).erase('class');
    }

    if($('sitestoreproduct_menu_' + id))
      $('sitestoreproduct_menu_' + id).set('class', 'selected');
    
    activeTabId = id;
    
    var showappinfo = new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'isajax' : 1,
        'flag_display' : 2
        // 'method' : actionName
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {

        $('dynamic_menus_content').innerHTML = responseHTML;
        Smoothbox.bind($('dynamic_menus_content'));
        en4.core.runonce.trigger();
      }
    });

    showappinfo.send();
  }

</script>

  <?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/navigation_views.tpl'; ?>

<div class="seaocore_db_tabs">
  <ul>
   
    <?php if($this->canViewWishlist): ?>
      <li><a href="javascript:void(0);" onclick = "myAccountUrl('my-wishlists', '', 0, '<?php echo $tempWishlistUrl; ?>');" id="sitestoreproduct_menu_my-wishlists" class="<?php if($this->showMenu == 'my-wishlists'){ echo 'selected';} ?>"><?php echo $this->translate("My Wishlists"); ?></a></li>      
    <?php endif; ?>
    
    <li><a href="javascript:void(0);" onclick = "myAccountUrl('my-orders', '', 0, '<?php echo $tempOrderUrl; ?>');" id="sitestoreproduct_menu_my-orders" class="<?php if($this->showMenu == 'my-orders'){ echo 'selected';} ?>"><?php echo $this->translate("My Orders"); ?></a></li>   
    <?php if( !empty($this->isAnyDownloadableProduct) ) : ?>
      <li><a href="javascript:void(0);" onclick = "myAccountUrl('my-downloadable-products', '', 0, '<?php echo $tempDownloadproductUrl; ?>');" id="sitestoreproduct_menu_my-downloadable-products" class="<?php if($this->showMenu == 'my-downloadable-products'){ echo 'selected';} ?>"><?php echo $this->translate("My Downloadable Products"); ?></a></li>  
    <?php endif; ?>
    
    <?php if($this->countUserStores): ?>  
      <li><a href="javascript:void(0);" onclick = "myAccountUrl('my-stores', '', 0, '<?php echo $tempMyStoresUrl; ?>');" id="sitestoreproduct_menu_my-stores" class="<?php if($this->showMenu == 'my-stores'){ echo 'selected';} ?>"><?php echo $this->translate("My Stores"); ?></a></li>     
    <?php endif; ?>
      
    <?php if($this->getCountUserAsAdmin): ?>
      <li><a href="javascript:void(0);" onclick = "myAccountUrl('stores-i-admin', '', 0, '<?php echo $tempStoreAdminUrl; ?>');" id="sitestoreproduct_menu_stores-i-admin" class="<?php if($this->showMenu == 'stores-i-admin'){ echo 'selected';} ?>"><?php echo $this->translate("Stores I Admin"); ?></a></li>     
    <?php endif; ?>
    
    <?php if($storesILike && $this->getLikeCounts):?>
      <li><a href="javascript:void(0);" onclick = "myAccountUrl('stores-i-like', '', 0, '<?php echo $tempStoreLikeUrl; ?>');" id="sitestoreproduct_menu_stores-i-like" class="<?php if($this->showMenu == 'stores-i-like'){ echo 'selected';} ?>"><?php echo $this->translate("Stores I Like"); ?></a></li>     
    <?php endif; ?>
    
      <li><a href="javascript:void(0);" onclick = "myAccountUrl('my-address', '', 0, '<?php echo $tempAddressUrl; ?>');" class="<?php if($this->showMenu == 'my-address'){ echo 'selected';} ?>" id="sitestoreproduct_menu_my-address"><?php echo $this->translate("My Addresses"); ?></a></li> 

  </ul>
  
  <?php if($this->canCreate):?>
    <div class="clr">
      <?php echo $this->content()->renderWidget('sitestoreproduct.store-startup-link'); ?>
    </div>
  <?php endif; ?>
</div>

<div class="seaocore_tbs_cont" id="dynamic_menus_content"></div>
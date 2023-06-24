<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit_tabs.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$sitestore = Engine_Api::_()->getItem('sitestore_store', $this->store_id);
$baseUrl = $this->layout()->staticBaseUrl;

$this->headLink()
        ->appendStylesheet($baseUrl . 'externals/calendar/styles.css')
        ->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/calendar/styles.css')
        ->appendStylesheet($baseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore_dashboard.css')
        ->appendStylesheet($baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css')
        ->appendStylesheet($baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css');

$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_rating.css')
        ->prependStylesheet($baseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');

$this->headScript()
        ->appendFile($baseUrl . 'externals/moolasso/Lasso.js')
        ->appendFile($baseUrl . 'externals/moolasso/Lasso.Crop.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($baseUrl . 'externals/calendar/calendar.compat.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js')
        ->appendFile($baseUrl . 'application/modules/Core/externals/scripts/composer.js')
        ->appendFile($baseUrl . 'application/modules/Sitestore/externals/scripts/core.js')
        ->appendFile($baseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');

?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl'; ?>
<?php
$tempURLOptions = $tempRedirectUrl = null;
$routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores");

  $tempRedirectUrl .=!empty($this->store_id) ? $this->store_id . '/' : '';
  $tempRedirectUrl .=!empty($this->showType) ? $this->showType . '/' : '';
  $tempRedirectUrl .=!empty($this->showMenu) ? $this->showMenu . '/' : '';
  $tempRedirectUrl .=!empty($this->showMethod) ? $this->showMethod . '/' : '';

  $tempURLOptions .=!empty($this->order_id) ? 'order_id/' . $this->order_id . '/' : '';
  $tempURLOptions .=!empty($this->tax_id) ? 'tax_id/' . $this->tax_id . '/' : '';
  $tempURLOptions .=!empty($this->storeNo) ? 'storeno/' . $this->storeNo . '/' : '';
  $tempURLOptions .=!empty($this->admin_calling) ? 'admin_calling/' . $this->admin_calling . '/' : '';
  $tempURLOptions .=!empty($this->notice) ? 'notice/1/' : '';  
  $tempURLOptions .=!empty($this->method_id) ? 'method_id/' . $this->method_id . '/' : '';
  $tempURLOptions .=!empty($this->month) ? 'month/' . $this->month . '/year/' . $this->year . '/' : '';
  $tempURLOptions .=!empty($this->tab) ? 'tab/' . $this->tab . '/' : '';
  $tempURLOptions .=!empty($this->task) ? 'task/' . $this->task . '/' : '';
  $tempURLOptions .=!empty($this->sections) ? 'sections/' . $this->sections . '/' : '';
  $tempURLOptions .=!empty($this->tag_id) ? 'tag_id/' . $this->tag_id . '/' : '';

  $tempRedirectUrl .= $tempURLOptions;
  $tempURLOptions = @rtrim($tempURLOptions, '/');
  $senRequestControllerAction = !empty($tempURLOptions)? $this->showMethod . '/' . $tempURLOptions :  $this->showMethod;

  ?>

  <?php if (!empty($this->showMethod)): ?>
    <script type="text/javascript">
      en4.core.runonce.add(function() {
        manage_store_dashboard(<?php echo $this->sitestores_view_menu ?>,'<?php echo $senRequestControllerAction; ?>','<?php echo $this->showType ?>', '<?php echo $tempRedirectUrl; ?>');
      });  
    </script>
  <?php endif; ?>
    
  <script type="text/javascript">
    
  function sectionselectAll(mainCheckbox, tempCheckbox)
    {
      var i;
     var multidelete_form = $('multichange_form');
      var inputs = multichange_form.elements;
      var tempFlag;
      
      if($(mainCheckbox).checked){
        tempFlag = 1;        
      }else {
        tempFlag = 0;
      }
      
      $(tempCheckbox).checked = tempFlag;
      for (i = 1; i < inputs.length; i++) {
                
                if($("change_" + inputs[i].value)) {                  
                  $("change_" + inputs[i].value).checked = tempFlag;        
                }
      }
    }
    
    var toggleMenus = function(id) {
      var className = '';
      if(id == 52){
        className = 'store_dashboard_tax_menu';
      }
      
      
      if(document.getElementById('store_dashboard_tax_menu')) {
        if( className == 'store_dashboard_tax_menu' ) {
          document.getElementById('store_dashboard_tax_menu').style.display = 'block';
        }else {
          document.getElementById('store_dashboard_tax_menu').style.display = 'none';
        }
      }    
    }
    
    var manage_store_dashboard = function (id, actionName, controller, tempURL) {
      new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);
      // IT'S THE VARIABLE WHICH SEND TO SITESTORE CONTROLLERS FOR GETTING REQUIRED RESULT ACCORDING TO REQUEST. WHERE 'actionName' and 'controller' IS THE VARIABLE, WHICH HAVE THE INFORMATION OF SITESTORE PLUGIN CONTROLLERS AND ACTION.
      var tempStoreDeshboardUrl = en4.core.baseUrl + 'sitestoreproduct/' + controller + '/' + actionName + '/' + 'store_id/<?php echo $this->store_id ?>/type/' + controller + '/menuId/' + id;

      // WE ARE MAKING THE URL, WHICH WILL SHOW TO ADDRESS BAR AND REQUIRED ON STORE REFRESH.
      if( !tempURL ){
        var tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/' + controller + '/' + id + '/' +  actionName;
      }else {
        var tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + tempURL;
      }

      if(tempStoreURL && typeof history.pushState != 'undefined') {
        history.pushState( {}, document.title, tempStoreURL );
      }

      ShowDashboardStoreContent(tempStoreDeshboardUrl, '', '', '<?php echo $this->store_id ?>', id);
      toggleMenus(id);
    }

  </script>
<?php
$ManageCategorySettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoremember.category.settings', 1);
$sitestoreMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');

$contactSpicifyFileds = 0;
$offerPrivacy = 0;
$formPrivacy = 0;
$badgePrivacy = 0;

//OFFER PRIVACY
$sitestoreofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
if ($sitestoreofferEnabled) {
  $offerPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');
}

//FORM PRIVACY
$sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
if ($sitestoreFormEnabled) {
  $formPrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'form');
}

//SPECIFIC CONTENT DETAILS FIELDS
$storeOwner = Engine_Api::_()->user()->getUser($sitestore->owner_id);
$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitestore_store', $storeOwner, 'contact_detail');
$availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email',);
$options_create = array_intersect_key($availableLabels, array_flip($view_options));

if (!empty($options_create)) {
  $contactSpicifyFileds = 1;
}

//BADGE PRIVACY
$badgeCount = 0;
$sitestoreBadgeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge');
if (!empty($sitestoreBadgeEnabled)) {
  $badgePrivacy = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'badge');
  if (!empty($badgePrivacy)) {
    $badgeCount = Engine_Api::_()->sitestorebadge()->badgeCount();
  }
}

$wishlistCount = 0;
$sitestorewishlistEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorewishlist');
if ($sitestorewishlistEnabled) {
  $wishlistCount = Engine_Api::_()->getDbtable('stores', 'sitestorewishlist')->wishlistCount($sitestore->store_id);
}
?>

<?php $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.multiple.location', 1); ?>
<?php $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showurl.column', 1); ?>
<?php $edit_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.edit.url', 0); ?>

<div class="seaocore_db_tabs">
  
<!-- START SHOW DASHBOARD TABS WORK -->
  <?php $dashboard_tabs = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_dashboard');  ?>
  <?php $allowSellingProducts = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($sitestore->store_id, false); ?>
  <?php if( !empty($dashboard_tabs) ) : ?>
    <ul>
      <?php foreach( $dashboard_tabs as $storeTab ) : ?>
        <?php $tempStyle = ""; ?>
        <?php if( empty($allowSellingProducts) ) : ?>
          <?php if( $storeTab->name == 'sitestore_dashboard_manageorders' || $storeTab->name == 'sitestore_dashboard_shippingmethods' || $storeTab->name == 'sitestore_dashboard_taxes' || $storeTab->name == 'sitestore_dashboard_paymentaccount' || $storeTab->name == 'sitestore_dashboard_paymentmethod' || $storeTab->name == 'sitestore_dashboard_paymentrequests' || $storeTab->name == 'sitestore_dashboard_yourbill' || $storeTab->name == 'sitestore_dashboard_transactions' || $storeTab->name == 'sitestore_dashboard_salesstatistics' || $storeTab->name == 'sitestore_dashboard_graphstatistics' || $storeTab->name == 'sitestore_dashboard_salesreports') : ?>
      <?php if(!Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($sitestore->store_id) && $storeTab->name != 'sitestore_dashboard_taxes'): ?>
            <?php $tempStyle = "style='display:none'"; ?>
      <?php endif;?>
          <?php endif; ?>
        <?php endif; ?>
        <?php $attribs = array_diff_key(array_filter($storeTab->toArray()), array_flip(array(
          'reset_params', 'route', 'module', 'controller', 'action', 'type',
          'visible', 'label', 'href'))); ?>
        <li id="id_<?php echo $storeTab->tab ?>" <?php if( $this->sitestores_view_menu == $storeTab->tab ) : echo "class='selected'"; endif; ?> <?php echo $tempStyle; ?>>
          <?php echo $this->htmlLink($storeTab->getHref(), $this->translate($storeTab->getLabel()), $attribs) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
<!-- END SHOW DASHBOARD TABS WORK -->

  <div class="dashboard_info">
    <div class="dashboard_info_image">
<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $this->itemPhoto($sitestore, 'thumb.profile')) ?>
    </div>
    <center>
      <span>
    <?php if ($sitestore->declined == 0): ?>
      <?php if ($sitestore->featured == 1): ?>
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
  <?php endif; ?>
  <?php if ($sitestore->sponsored == 1): ?>
    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
  <?php endif; ?>
  <?php if (empty($sitestore->approved) && empty($sitestore->declined)): ?>
    <?php $approvedtitle = 'Not approved';
    if (empty($sitestore->aprrove_date)): $approvedtitle = "Approval Pending";
    endif; ?>
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_approved0.gif', '', array('class' => 'icon', 'title' => $this->translate($approvedtitle))) ?>
  <?php endif; ?>
  <?php if ($sitestore->closed): ?>
    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
  <?php endif; ?>
<?php endif; ?>
      <?php if ($sitestore->declined == 1): ?>
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/declined.gif', '', array('class' => 'icon', 'title' => $this->translate('Declined'))) ?>
<?php endif; ?>
      </span>
    </center>

<?php if (Engine_Api::_()->sitestore()->hasPackageEnable()): ?>
      <div>
        <b><?php echo $this->translate('Package: ') ?></b>
        <a href='javascript:void(0)' onclick="Smoothbox.open('<?php echo $this->url(array("action" => "detail", 'id' => $sitestore->package_id), 'sitestore_packages', true) ?>');" title="<?php echo $this->translate(ucfirst($sitestore->getPackage()->title)) ?>"><?php echo $this->translate(ucfirst($sitestore->getPackage()->title)); ?></a>
      </div>
  <?php if (!$sitestore->getPackage()->isFree()): ?>
        <div>
          <b><?php echo $this->translate('Payment: ') ?></b>
          <?php
          if ($sitestore->status == "initial"):
            echo $this->translate("Not made");
          elseif ($sitestore->status == "active"):
            echo $this->translate("Yes");
          else:
            echo $this->translate(ucfirst($sitestore->status));
          endif;
          ?>
        </div>
  <?php endif ?>
<?php endif ?>
    <div>
      <b><?php echo $this->translate('Status: ') . Engine_Api::_()->sitestore()->getStoreStatus($sitestore) ?></b>
    </div>
<?php if (!empty($sitestore->aprrove_date)): ?>
      <div style="color: chocolate">
  <?php echo $this->translate('Approved ') . $this->timestamp(strtotime($sitestore->aprrove_date)) ?>
      </div>
  <?php if (Engine_Api::_()->sitestore()->hasPackageEnable()): ?>
        <div style="color: green;">
    <?php
    $expiry = Engine_Api::_()->sitestore()->getExpiryDate($sitestore);
    if ($expiry !== "Expired" && $expiry !== $this->translate('Never Expires'))
      echo $this->translate("Expiration Date: ");
    echo $expiry;
    ?>
        </div>
  <?php endif; ?>
<?php endif ?>


<?php if (Engine_Api::_()->sitestore()->canShowPaymentLink($sitestore->store_id)): ?>
      <div class="tip center mtop5">
        <span class="db_payment_link">
          <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitestore->store_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
          <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitestore_session_payment', true) ?>">
            <input type="hidden" name="store_id_session" id="store_id_session" />
          </form>
        </span>
      </div>
<?php endif; ?>
<?php if (Engine_Api::_()->sitestore()->canShowRenewLink($sitestore->store_id)): ?>
      <div class="tip mtop5">
        <span style="margin:0px;"> <?php echo $this->translate("Please click "); ?>
          <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitestore->store_id ?>)"><?php echo $this->translate('here'); ?></a><?php echo $this->translate(' to renew store.'); ?>
          <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitestore_session_payment', true) ?>">
            <input type="hidden" name="store_id_session" id="store_id_session" />
          </form>
        </span>
      </div>
<?php endif; ?>
  </div>
</div>
<?php if (Engine_Api::_()->sitestore()->canShowPaymentLink($sitestore->store_id)): ?>
  <div class="sitestore_edit_content">
    <div class="tip">
      <span>
  <?php echo $this->translate('The package for your Store requires payment. You have not fulfilled the payment for this Store.'); ?>
        <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitestore->store_id ?>)"><?php echo $this->translate('Make payment now!'); ?></a>
        <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitestore_session_payment', true) ?>">
          <input type="hidden" name="store_id_session" id="store_id_session" />
        </form>
      </span>
    </div>
  </div>
<?php endif; ?>
<?php if (Engine_Api::_()->sitestore()->canShowRenewLink($sitestore->store_id)): ?>
  <div class="sitestore_edit_content">
    <div class="tip">
      <span>
  <?php if ($sitestore->expiration_date <= date('Y-m-d H:i:s')): ?>
    <?php echo $this->translate("Your package for this Store has expired and needs to be renewed.") ?>
  <?php else: ?>
    <?php echo $this->translate("Your package for this Store is about to expire and needs to be renewed.") ?>
  <?php endif; ?>
  <?php echo $this->translate(" Click "); ?>
        <a href='javascript:void(0);' onclick="submitSession(<?php echo $sitestore->store_id ?>)"><?php echo $this->translate('here'); ?></a><?php echo $this->translate(' to renew it.'); ?>
        <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitestore_session_payment', true) ?>">
          <input type="hidden" name="store_id_session" id="store_id_session" />
        </form>
      </span>
    </div>
  </div>
<?php endif;?>

<script type="text/javascript">
  var Show_Tab_Selected = "<?php echo $this->sitestores_view_menu; ?>";
  var sitestore_dismiss_shipping = 0;
  var sitestore_dismiss_payment = 0;
  
  <?php if( !empty($_COOKIE['sitestore_dismiss_shipping']) ): ?>
    sitestore_dismiss_shipping = 1;
  <?php endif; ?>
      
  <?php if( !empty($_COOKIE['sitestore_dismiss_payment']) ): ?>
    sitestore_dismiss_payment = 1;
  <?php endif; ?>
  
  function submitSession(id){
    document.getElementById("store_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }

  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
  
  //WORK FOR CLOSING THE FACEBOOK POPUP WHILE LINKING FACEBOOK PAGE
  if (window.opener!= null) {
  
    <?php if (!empty($_GET['redirect_fb'])) : ?>
                window.opener.location.reload(false);
               close();
             
    <?php endif; ?>
}

  function dismiss(modName, dismissType) {
    document.cookie= modName + "_dismiss_" + dismissType + "=" + 1;
    $('dismiss_' + dismissType).style.display = 'none';
    if(dismissType == 'shipping'){
        sitestore_dismiss_shipping = 1;
    }else if(dismissType == 'payment'){
        sitestore_dismiss_payment = 1;
    }
  }
  
  var initializeCalendar = function() { 
  // check end date and make it the same date if it's too
  cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
  // redraw calendar
  cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
  cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);

  // check start date and make it the same date if it's too		
  cal_starttime.calendars[0].start = new Date( $('starttime-date').value );
  // redraw calendar
  cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
  cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
}
var cal_starttime_onHideStart = function() { 
  // check end date and make it the same date if it's too
  cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
  // redraw calendar
  cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
  cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);

  //CHECK IF THE END TIME IS LESS THEN THE START TIME THEN CHANGE IT TO THE START TIME.
  var startdatetime = new Date($('starttime-date').value);
  var enddatetime = new Date($('endtime-date').value);
  if(startdatetime.getTime() > enddatetime.getTime()) {
    $('endtime-date').value = $('starttime-date').value;
    $('calendar_output_span_endtime-date').innerHTML = $('endtime-date').value;
    cal_endtime.changed(cal_endtime.calendars[0]);
  }
}

if($$('.ajax_dashboard_enabled')) {
  en4.core.runonce.add(function() {
    $$('.ajax_dashboard_enabled').addEvent('click',function(event) {
      var element = $(event.target);
      var show_url = '<?php echo $show_url; ?>';
      var edit_url = '<?php echo $edit_url; ?>';
      event.stop();
//      var href = this.href; 
      var ulel=this.getParent('ul');

      ulel.getElements('li').removeClass('selected');

      if( element.tagName.toLowerCase() == 'a' ) {
        element = element.getParent('li');
      }

      element.addClass('selected');
      
      var tempId = element.id;
      var tab_id = tempId.split("_");
      Show_Tab_Selected = tab_id[1];

      // START WORK FOR CHANGE THE BROWSER URL
      var tempStoreURL;
      if( this.name == 'sitestore_dashboard_manageproducts' )
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/product/62/manage';
      else if( this.name == 'sitestore_dashboard_managesections' )
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/index/88/sections';
      else if( this.name == 'sitestore_dashboard_manageorders' )
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/index/55/manage-order';
      else if( this.name == 'sitestore_dashboard_shippingmethods' )
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/index/51/shipping-methods';
      else if( this.name == 'sitestore_dashboard_taxes' ) {
        <?php $taxActionName = 'index'; ?>
        <?php $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0); ?>
        <?php if( !empty($isVatAllow) ) : ?>
          <?php $vatCreator = false; //Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.product.vat.creator', 0); ?>
          <?php if( empty($vatCreator) ) : ?>
            <?php $taxActionName = 'vat'; ?>
          <?php endif; ?>
        <?php endif;?>
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/tax/52/<?php echo $taxActionName ?>';
      }
      else if( this.name == 'sitestore_dashboard_paymentmethod' || this.name == 'sitestore_dashboard_paymentaccount' )
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/product/53/payment-info';
      else if( this.name == 'sitestore_dashboard_transactions' ) {
        <?php $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable(); ?>
        <?php if( empty($directPayment) ) : ?>
          tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/product/56/transaction';
        <?php else: ?>
          tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/product/56/store-transaction';
        <?php endif; ?>
      }
      else if( this.name == 'sitestore_dashboard_paymentrequests' )
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/product/54/payment-to-me';
      else if( this.name == 'sitestore_dashboard_yourbill' )
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/product/54/your-bill';
      else if( this.name == 'sitestore_dashboard_salesstatistics' )
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/product/60/store-dashboard';
      else if( this.name == 'sitestore_dashboard_importproducts' )
        tempStoreURL = '<?php echo $routeStartP ?>/dashboard/store/' + '<?php echo $this->store_id ?>/import/89/index';
      else 
        tempStoreURL = this.href;

      new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);

      if(tempStoreURL && typeof history.pushState != 'undefined') {
        history.pushState( {}, document.title, tempStoreURL );
      }
      // END WORK FOR CHANGE THE BROWSER URL
      
      ShowDashboardStoreContent(this.href, show_url, edit_url, '<?php echo $this->store_id ?>', 0);
    });
  });
}

</script>
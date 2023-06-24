<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _DashboardNavigation.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css') ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css');
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl. 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl. 'externals/moolasso/Lasso.Crop.js')
?>

<?php
$sitestoreproduct = $this->sitestoreproduct;
$viewer = Engine_Api::_()->user()->getViewer();

$allowStyle = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('sitestoreproduct_product', $viewer->level_id, "style");

$allowOverview = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "overview");

$allowCreation = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "create");

$allowDocument = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.document.enable', 0);

$allowEdit = $this->sitestoreproduct->authorization()->isAllowed($viewer, 'edit');

$allowVideoUpload = Engine_Api::_()->sitestoreproduct()->allowVideo($this->sitestoreproduct, $viewer);
$allowPhotoUpload = Engine_Api::_()->authorization()->isAllowed($this->sitestoreproduct, $viewer, "photo");

$allowContactDetailsUpload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "contact");

$allowMetaKeywords = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "metakeyword");

$allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);

$params['product_type_title'] = Zend_Registry::get('Zend_Translate')->_('Products');
$params['dashboard'] = $this->translate('Dashboard');
//SET META TITLE
Engine_Api::_()->sitestoreproduct()->setMetaTitles($params);

$tempProductInfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->product_id, 'product_info');
if( !empty($tempProductInfo) ) {
  $productInfo = unserialize($tempProductInfo);
  if( !empty($productInfo) && $sitestoreproduct->product_type == 'bundled' ){
    $bundleProductType = $productInfo['bundle_product_type'];
    if( @in_array('configurable', $bundleProductType) || @in_array('virtual', $bundleProductType) )
      $isConfigurationTabRequired = true;
  } 
}
$isSitestorereservationModuleExist = Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist();

$this->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");
include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/navigation_views.tpl';
?>
<div class="layout_middle">
<div class='seaocore_db_tabs'>
   <!-- START SHOW DASHBOARD TABS WORK -->
  <?php $dashboard_tabs = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_dashboard');  ?>
  <?php if( !empty($dashboard_tabs) ) : ?>
    <ul>
      <?php foreach( $dashboard_tabs as $storeTab ) : ?>
        <?php $tempStyle = ""; ?>
        <?php $attribs = array_diff_key(array_filter($storeTab->toArray()), array_flip(array(
          'reset_params', 'route', 'module', 'controller', 'action', 'type',
          'visible', 'label', 'href')));?>
      
        <li id="id_<?php echo $storeTab->tab ?>" <?php if( $this->sitestores_view_menu == $storeTab->tab ) : echo "class='selected'"; endif; ?> <?php echo $tempStyle; ?>>
          <?php echo $this->htmlLink($storeTab->getHref(), $this->translate($storeTab->getLabel()), $attribs) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
<!-- END SHOW DASHBOARD TABS WORK --> 

  <div class="sr_sitestoreproduct_dashboard_info clr">
    <div class="sr_sitestoreproduct_dashboard_info_image prelative">
			<?php if($this->sitestoreproduct->newlabel):?>
				<i class="sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
			<?php endif;?>
      <?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->itemPhoto($this->sitestoreproduct, 'thumb.profile')) ?>
    </div>
    <center class="clr">
      <span>
        <?php if ($this->sitestoreproduct->sponsored == 1): ?>
          <i title="<?php echo $this->translate('Sponsored');?>" class="sr_sitestoreproduct_icon seaocore_icon_sponsored"></i>
        <?php endif; ?>
        <?php if ($this->sitestoreproduct->featured == 1): ?>
          <i title="<?php echo $this->translate('Featured');?>" class="sr_sitestoreproduct_icon seaocore_icon_featured"></i>
        <?php endif; ?>
      </span>
    </center>
  </div> 
 
  <?php if( $this->sitestoreproduct->store_id ) : ?>
    <?php $store = Engine_Api::_()->getItem('sitestore_store', $this->sitestoreproduct->store_id); ?>
    <div class="clr sitestoreproduct_psi o_hidden mtop10 mbot5">
      <div class="fleft mright5">
        <?php echo $this->htmlLink($store->getHref(), $this->itemPhoto($store, 'thumb.icon')); ?>
      </div>
      <div class="o_hidden">
        <b><?php echo $this->htmlLink($store->getHref(), $store->getTitle()); ?> </b><br/>
        <span class="seaocore_txt_light">
          <?php echo $this->translate("(Store) - "); ?>
          <?php echo $this->translate(array('%s like', '%s likes', $store->like_count), $this->locale()->toNumber($store->like_count)) ?>
        </span>
      </div>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
  var miniLoadingImage = 0;
	function showAjaxBasedContent(url) {
		if (history.pushState) {
			history.pushState( {}, document.title, url );
		} else {
			window.location.hash = url;
		}
		$('global_content').getElement('.sr_sitestoreproduct_dashboard_content').innerHTML = '<div class="seaocore_content_loader"></div>'; 
		en4.core.request.send(new Request.HTML({
			url : url,
			'method' : 'get',
			data : {
				format : 'html',
				'isajax' : 1
			},onSuccess :  function(responseTree, responseElements, responseHTML, responseJavaScript)  {
					$('global_content').innerHTML = responseHTML;
          Smoothbox.bind($('global_content'));
          en4.core.runonce.trigger();
					if (window.InitiateAction) {
						InitiateAction ();
					}
				}
		}));
	}

var requestActive = false;
window.addEvent('load', function() {
  InitiateAction();
});

var InitiateAction = function () {
  formElement = $$('.global_form')[0];
  if (typeof formElement != 'undefined' ) {
    formElement.addEvent('submit', function(event) {
      if (typeof submitformajax != 'undefined' && submitformajax == 1) {
        submitformajax = 0;
        event.stop();
        Savevalues();
      }
    })
  }
}

var Savevalues = function() {
  if( requestActive ) return;

  requestActive = true;
  var pageurl = $('global_content').getElement('.global_form').action;
 
  currentValues = formElement.toQueryString();
  if( miniLoadingImage )
    $('show_tab_content_child').innerHTML = '<div><center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/spinner.gif" /></center></div>';
  else
    $('show_tab_content_child').innerHTML = '<div class="seaocore_content_loader"></div>';
  if (typeof page_url != 'undefined') {
    var param = (currentValues ? currentValues + '&' : '') + 'isajax=1&format=html&page_url=' + page_url;
  }
  else {
    var param = (currentValues ? currentValues + '&' : '') + 'isajax=1&format=html';
  }

  var request = new Request.HTML({
    url: pageurl,
    onSuccess :  function(responseTree, responseElements, responseHTML, responseJavaScript)  {
      $('global_content').innerHTML =responseHTML;
      InitiateAction (); 
      requestActive = false;
    }
  });
  request.send(param);
}

if($$('.ajax_dashboard_enabled')) {
  en4.core.runonce.add(function() {
    $$('.ajax_dashboard_enabled').addEvent('click',function(event) {
      var element = $(event.target);
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
      if( this.name == 'sitestoreproduct_dashboard_changephoto' ){
        <?php $url = $this->url(array('action' => 'change-photo', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_dashboard", true) ?>
        tempStoreURL = '<?php echo $url; ?>';
        }
      else if( this.name == 'sitestoreproduct_dashboard_contact' ){
        <?php $url = $this->url(array('action' => 'contact', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_dashboard", true) ?>
        tempStoreURL = '<?php echo $url ;?>';
       }
      else if( this.name == 'sitestoreproduct_album_editphotos' ){
        <?php $url = $this->url(array('product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_albumspecific", true) ?>
        tempStoreURL = '<?php echo $url ;?>';
      }
      else if( this.name == 'sitestoreproduct_videoedit_edit' ){
        <?php $url = $this->url(array('product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_videospecific", true) ?>
        tempStoreURL = '<?php echo $url ;?>';
      }
      else if( this.name == 'sitestoreproduct_dashboard_productdocument' ) {
        <?php $url = $this->url(array('action' => 'product-document', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_dashboard", true) ?>
        tempStoreURL = '<?php echo $url ;?>';
      }
      else if( this.name == 'sitestoreproduct_dashboard_metadetail'){
        <?php $url = $this->url(array('action' => 'meta-detail', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_dashboard", true) ?>
        tempStoreURL = '<?php echo $url ;?>';
      }
      else if( this.name == 'sitestoreproduct_index_editstyle' ) {
        <?php $url = $this->url(array('action' => 'editstyle', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_specific", true) ?>
          tempStoreURL = '<?php echo $url ;?>';
      }
      else if( this.name == 'sitestoreproduct_printingtag_printtag' ){
        <?php 
          $url = $this->url(array('action' => 'print-tag', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_tag", true) ?>
        tempStoreURL = '<?php echo $url ;?>';
       }
      else if( this.name == 'sitestoreproduct_dashboard_producthistory' ){
        <?php $url = $this->url(array('action' => 'product-history', 'product_id' => $this->sitestoreproduct->product_id), "sitestoreproduct_dashboard", true) ?>
        tempStoreURL = '<?php echo $url ;?>';
        }
//      else if( this.name == 'sitestoreproduct_dashboard_productbooking' ){
//        <?php //$url = $this->url(array('action' => 'product-booking', 'product_id' => $this->sitestoreproduct->product_id), "sitestorereservation_dashboard", true) ?>
//        tempStoreURL = '<?php //echo $url ;?>';
//        }
      else if( this.name == 'sitestoreproduct_files_index' ){
        <?php $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($this->sitestoreproduct->product_id); ?>
        <?php $url = $this->url(array('action' => 'index','option_id' => $option_id,'product_id' => $this->sitestoreproduct->product_id),'sitestoreproduct_files', true) ?>
        tempStoreURL = '<?php echo $url ;?>';
        }
      else if( this.name == 'sitestoreproduct_dashboard_editlocation' ){
        <?php $url = $this->url(array('action' => 'editlocation','product_id' => $this->sitestoreproduct->product_id),'sitestoreproduct_dashboard', true) ?>
        tempStoreURL = '<?php echo $url ;?>';
        }
     else if( this.name == 'sitestoreproduct_product_bundleproductattributes' ){
        <?php $url = $this->url(array('action' => 'bundle-product-attributes','product_id' => $this->sitestoreproduct->product_id),'sitestoreproduct_product_general', true) ?>
        tempStoreURL = '<?php echo $url ;?>';
        }
      else 
        tempStoreURL = this.href;

      new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);

      if(tempStoreURL && typeof history.pushState != 'undefined') {
        history.pushState( {}, document.title, tempStoreURL );
      }
      // END WORK FOR CHANGE THE BROWSER URL
      
      showAjaxBasedContent(tempStoreURL);
    });
  });
}

</script>
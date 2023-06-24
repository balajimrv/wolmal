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

<script type="text/javascript">
  var fetchPackagesFromStore = function(store_name) {
    window.location.href = en4.core.baseUrl + 'admin/sitestoreproduct/settings/index/store/' + store_name;
  }
</script>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>

<?php if (count($this->navigationStore)): ?>
	<div class='seaocore_admin_tabs'>
		<?php echo $this->navigation()->menu()->setContainer($this->navigationStore)->render() ?>
	</div>
<?php endif; ?>

<?php if( count($this->navigationStoreGlobal) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigationStoreGlobal)->render()
    ?>
  </div>
<?php endif; ?>

<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>

  <?php if(empty($this->isAnyCountryEnable)):?>
    <div class="seaocore_tip">
      <span>
        <?php echo $this->translate("You have not configured the shipping locations for the Stores on your site. Please %s.", $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'location', 'action' => 'index'), $this->translate("click here"))) ?>
      </span>
    </div>
  <?php endif; ?>

  <?php if(!$this->hasLanguageDirectoryPermissions):?>
    <div class="seaocore_tip">
      <span>
        <?php echo "Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory for change the setting 'Singular Product Title' and 'Plural Product Title'." ?>
      </span>
    </div>
  <?php endif; ?>

<?php if(empty($this->isAnyGatewayEnable)):?>
<div class="seaocore_tip">
  <span>
    <?php echo $this->translate("You have not configured to payment information for your site. Please %s.", $this->htmlLink(array('route' => 'admin_default', 'module' => 'payment', 'controller' => 'gateway', 'action' => 'index'), $this->translate("click here"))) ?>
  </span>
</div>
<?php endif; ?>

<div class='seaocore_settings_form'>
	<div class='settings'>    
    <?php 
      if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.isActivate', null)) {
        $this->form->setDescription($this->translate('These settings affect all members in your community.'));
        $this->form->getDecorator('Description')->setOption('escape', false);
      }
      echo $this->form->render($this); 
    ?>
  </div>
</div>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

<script type="text/javascript">

	window.addEvent('domready', function() {
		showDefaultNetwork('<?php echo $settings->getSetting('sitestoreproduct.network', 0) ?>');
//    $('sitestoreproduct_check_combination_quantity-wrapper').setStyle('display', ($('sitestoreproduct_combination-1').checked ?'block':'none'));
	});
  
//  $('sitestoreproduct_combination-1').addEvent('click', function(){
//				$('sitestoreproduct_check_combination_quantity-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
//		});
//		$('sitestoreproduct_combination-0').addEvent('click', function(){
//				$('sitestoreproduct_check_combination_quantity-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
//		});

	function showDefaultNetwork(option) {
		if($('sitestoreproduct_default_show-wrapper')) {
			if(option == 0) {
				$('sitestoreproduct_default_show-wrapper').style.display='block';
         showDefaultNetworkType($('sitestoreproduct_default_show-1').checked);
			}
			else{
         showDefaultNetworkType(1);
				$('sitestoreproduct_default_show-wrapper').style.display='none';
			}
		}
	}
  function showDefaultNetworkType(option) {
    if($('sitestoreproduct_networks_type-wrapper')) {
      if(option == 1) {
        $('sitestoreproduct_networks_type-wrapper').style.display='block';
      }else{
        $('sitestoreproduct_networks_type-wrapper').style.display='none';
      }
    }
  }
  
</script>

<script type="text/javascript">

	window.addEvent('domready', function() {
    //toogleLaguagePhase('none');
		showOverviewText('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.overview', 1) ?>');
    hideOwnerReviews('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2);?>');
    showDescription('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.bodyallow', 1);?>');  
    showCheckboxSettings('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);?>');
       var expiry=0;

       if($('sitestoreproduct_expiry-2'))
       {
        if($('sitestoreproduct_expiry-2').checked){
          expiry=2;
        }
       }

      showExpiryDuration(expiry);
	});

  function showOverviewText(option) {
    if($('sitestoreproduct_overviewcreation-wrapper')) {
      if(option == 1) {
        $('sitestoreproduct_overviewcreation-wrapper').style.display = "block";
      } else {
        $('sitestoreproduct_overviewcreation-wrapper').style.display = "none";
      }
    }
  }

  function hideOwnerReviews(option) {
    if($('sitestoreproduct_allowownerreview-wrapper')) {
      if(option == 2 || option == 3) {
        $('sitestoreproduct_allowownerreview-wrapper').style.display='block';
      }else{
        $('sitestoreproduct_allowownerreview-wrapper').style.display='none';
      }
    }
  }

  function showExpiryDuration(option) {
    if($('sitestoreproduct_adminexpiryduration-wrapper')) {
      if(option == 2) {
        $('sitestoreproduct_adminexpiryduration-wrapper').style.display='block';
      }else{
        $('sitestoreproduct_adminexpiryduration-wrapper').style.display='none';
      }
    }
  }

  function showDescription(option) {
    if($('sitestoreproduct_bodyrequired-wrapper')) {
      if(option == 1) {
        $('sitestoreproduct_bodyrequired-wrapper').style.display='block';
      } else{
        $('sitestoreproduct_bodyrequired-wrapper').style.display='none';
      }
    }
  }
  

	if($('sitestoreproduct_multilanguage-1')) {
		$('sitestoreproduct_multilanguage-1').addEvent('click', function(){
				$('sitestoreproduct_languages-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
		});
		$('sitestoreproduct_multilanguage-0').addEvent('click', function(){
				$('sitestoreproduct_languages-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
		});
		window.addEvent('domready', function() {
			$('sitestoreproduct_languages-wrapper').setStyle('display', ($('sitestoreproduct_multilanguage-1').checked ?'block':'none'));
		});
	}
  
  function showCheckboxSettings(option) {
    if($('sitestoreproduct_show_checkbox_vat_inclusive-wrapper')) {
      if(option == 1) {
        $('sitestoreproduct_show_checkbox_vat_inclusive-wrapper').style.display='block';
      } else{
        $('sitestoreproduct_show_checkbox_vat_inclusive-wrapper').style.display='none';
      }
    }
    if($('sitestoreproduct_show_checkbox_net_prices-wrapper')) {
      if(option == 1) {
        $('sitestoreproduct_show_checkbox_net_prices-wrapper').style.display='block';
      } else{
        $('sitestoreproduct_show_checkbox_net_prices-wrapper').style.display='none';
      }
    }
  }

</script>
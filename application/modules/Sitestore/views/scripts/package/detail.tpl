<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: package-detail.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$package_view = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.view', 1);
$packageInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.information',array('price', 'billing_cycle', 'duration', 'featured', 'sponsored', 'tellafriend', 'print', 'overview', 'map', 'insights', 'contactdetails', 'sendanupdate', 'apps', 'description', 'twitterupdates', 'ads'));
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<div class="sitestore_package_store global_form_popup" style="margin:10px 0 0 10px;">
  <ul class="sitestore_package_list">
    <li style="width:650px;">
      <div class="sitestore_package_list_title">
        <div class="sitestore_create_link">
          <a href="javascript:void(0);" onclick="createAD()"><?php echo $this->translate("Open a Store"); ?> &raquo;</a>
        </div>
        <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->package->title; ?></h3>
      </div>
       <?php $item=$this->package;?>
       <?php $this->detailPackage=1; ?>
       <?php include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/package/_packageInfo.tpl'; ?>
      <button onclick='javascript:parent.Smoothbox.close()' class="fright"><?php echo $this->translate('Close'); ?></button>
    </li>
  </ul>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
<script type="text/javascript">

  function createAD() {
		var url='<?php echo $this->url(array("action"=>"create" ,'id' => $this->package->package_id), 'sitestore_general', true) ?>';

		parent.window.location.href=url;
		parent.Smoothbox.close();
  }
</script>
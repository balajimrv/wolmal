<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: packge-detail.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Communityad/externals/styles/style_communityad.css');
	$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>
<div class="sitestore_package_store global_form_popup">
  <ul class="sitestore_package_list">
    <li>
      <div class="sitestore_package_list_title">
       
        <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->package->title; ?></h3>
      </div>
      <?php $item=$this->package;?>
      <?php $this->detailPackage=1; ?>
      <?php include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/package/_packageInfo.tpl'; ?>
      <button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo $this->translate('Close'); ?></button>
    </li>
  </ul>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
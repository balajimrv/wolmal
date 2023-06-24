<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: process.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div>
  <div>
    <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" /></center>
  </div>
  <div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
    <?php echo $this->translate("Processing Request. Please wait .....") ?>
  </div>
</div>

<form method="<?php echo $this->transactionMethod == 'GET' ? 'GET' : 'POST' ?>" action="<?php echo $this->transactionUrl ?>" data-ajax="false" id="sitestoreproduct_payment_process" style="display: none;">
  <?php foreach ($this->transactionData as $key => $value): ?>
    <input type="hidden" name="<?php echo $key ?>"  value="<?php echo $value; ?>"/>
  <?php endforeach; ?>
</form>
<script type="text/javascript">
  sm4.core.runonce.add(function() {
    $('#sitestoreproduct_payment_process').submit();
  });
</script>

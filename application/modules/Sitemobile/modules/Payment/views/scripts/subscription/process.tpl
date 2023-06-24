<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: process.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>
<div>
	<div>
	  <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemobile/modules/Core/externals/images/loading.gif" /></center>
	</div>
	<div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
	  <?php echo $this->translate("Processing Request. Please wait .....")?>
	</div>
</div>
<form method="post" action="<?php echo $this->transactionUrl ?>"id="subscription_payment_process" style="display: none;">
  <?php foreach ($this->transactionData as $key => $value):?>
  <input type="hidden" name="<?php echo $key?>"  value="<?php echo $value;?>"/>
  <?php  endforeach; ?>
</form>
<script type="text/javascript">
 $('#subscription_payment_process').submit();
</script>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
  sm4.core.runonce.add(function() {
    var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'utility', 'action' => 'advertisement'), 'default', true) ?>';
    var processClick = $(window).processClick = function(adcampaign_id, ad_id) {
			$.ajax({
				url : url,
				type: "POST",
				dataType: "json",
				data : {
					format : 'json',
					adcampaign_id : adcampaign_id,
					ad_id : ad_id
				},
			});}
  });
</script>

<div onclick="javascript:processClick(<?php echo $this->campaign->adcampaign_id.", ".$this->ad->ad_id?>)">
  <?php echo $this->ad->html_code; ?>
</div>
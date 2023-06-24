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

<div class="cadmc_statistics">
		<div>
	    <p>
	      <?php echo $this->translate("Use the below filters to graphically observe various metrics of your store over different time periods.") ?>
	    </p>
			<div class="cadmc_statistics_search">
				<?php echo $this->formFilter->render($this) ?>
			</div>
			
		  <div class="cadmc_statistics_nav">
		    <a id="admin_stats_offset_previous"  class='buttonlink icon_previous' onclick="processStatisticsPage(-1);" href="javascript:void(0);" style="float:left;"><?php echo $this->translate("Previous") ?></a>
		    <a id="admin_stats_offset_next" class='buttonlink_right icon_next' onclick="processStatisticsPage(1);" href="javascript:void(0);" style="display:none;float:right;"><?php echo $this->translate("Next") ?></a>
		  </div>

		  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/swfobject/swfobject.js"></script>
		  <script type="text/javascript">
		    var prev = '<?php echo $this->prev_link ?>';
        var storeId = '<?php echo $this->store_id ?>';
		    var currentArgs = {};
		    var processStatisticsFilter = function(formElement) {
		      var vals = formElement.toQueryString().parseQueryString();
		      vals.offset = 0;
		      buildStatisticsSwiff(vals);
		      return false;
		    }
		    var processStatisticsPage = function(count) {
		      var args = $merge(currentArgs);
		      args.offset += count;
		      buildStatisticsSwiff(args);
		    }
		    var buildStatisticsSwiff = function(args) {

		      var earliest_date = '<?php echo $this->earliest_ad_date ?>';
		      var startObject = '<?php echo $this->startObject ?>';

		      if(args.offset < 0) {
						switch(args.period) {
							case 'ww':
							startObject = startObject - (Math.abs(args.offset)*7*86400);
							break;
							
							case 'MM':
							startObject = startObject - (Math.abs(args.offset)*31*86400);
							break;

							case 'y':
							startObject = startObject - (Math.abs(args.offset)*366*86400);
							break;
						}
						$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
		      }
		      else if(args.offset > 0) {
						$('admin_stats_offset_previous').setStyle('display', 'block');
		      }
		      else if(args.offset == 0) {
						switch(args.period) {
							case 'ww':
							if (typeof args.prev_link != 'undefined') {
									$('admin_stats_offset_previous').setStyle('display', (args.prev_link >= 1 ? '' : 'none')); 
							}
							else {
									$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
							}
							break;
							
							case 'MM':
								startObject = '<?php echo mktime(0, 0, 0, date('m', $this->startObject), 1, date('Y', $this->startObject)) ?>';
								$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
								break;

							case 'y':
								startObject = '<?php echo mktime(0, 0, 0, 1, 1, date('Y', $this->startObject)) ?>';
								$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
								break;
						}
		      }
		  
		      currentArgs = args;

		      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

		      var url = new URI('<?php echo $this->url(array('action' => 'chart-data')) ?>');
		      url.setData(args);
		      
		      //$('my_chart').empty();
		      swfobject.embedSWF(
						"<?php echo $this->baseUrl() ?>/externals/open-flash-chart/open-flash-chart.swf",
						"my_chart",
						"850",
						"400",
						"9.0.0",
						"expressInstall.swf",
						{
							"data-file" : escape(url.toString()),
							'id' : 'mooo'
						}
		      );
		    }	    

		    window.addEvent('load', function() {
		      buildStatisticsSwiff({
					'type' : 'all',
					'mode' : 'normal',
					'chunk' : 'dd',
					'period' : 'ww',
					'start' : 0,
					'offset' : 0,
					'store_id' : storeId,
					'prev_link' : prev
					});
		    });
		  </script>

				<div id="my_chart">
					<center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin:10px 0;' /></center>
				</div>
			</div>
</div>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$this->headScript()
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">

// filter the dropdown of form used to filter graphical stats
function filterDropdown(element) {
    var optn1 = document.createElement("OPTION");
		optn1.text = '<?php echo $this->translate("By Week") ?>';
		optn1.value = '<?php echo Zend_Date::WEEK; ?>';
    var optn2 = document.createElement("OPTION");
		optn2.text = '<?php echo $this->translate("By Month") ?>';
		optn2.value = '<?php echo Zend_Date::MONTH; ?>';

    switch(element.value) {
      case 'ww':
			removeOption('ww');
			removeOption('MM');
      break;

      case 'MM':
			addOption(optn1,'ww' );
			removeOption('MM');
      break;

      case 'y':
			addOption(optn1,'ww' );
			addOption(optn2,'MM' );
      break;
    }
  }

	// add an option to the dropdown
  function addOption(option,value )
  {
    var addoption = false;
		for (var i = ($('chunk').options.length-1); i >= 0; i--) {
			var val = $('chunk').options[ i ].value; 
			if (val == value) {
				addoption = true;
				break; 
			}
		}
		if(!addoption) {
			$('chunk').options.add(option);
		}
  }

	// remove an option from the dropdown
	function removeOption(value) 
  {
    for (var i = ($('chunk').options.length-1); i >= 0; i--) 
    { 
      var val = $('chunk').options[ i ].value; 
      if (val == value) {
				$('chunk').options[i] = null;
				break; 
      }
    } 
  }
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<div class="layout_middle">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; ?>
  <div class="sitestore_edit_content">
		<div class="sitestore_edit_header">
			<?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()),$this->translate('VIEW_STORE')) ?>
			<h3><?php echo $this->translate('Dashboard: ').$this->sitestore->title; ?></h3>
		</div>
  <div id="show_tab_content">
		<div class="sitestore_edit_insights_table">
	    <table>
				<thead>
					<tr>
					  <th><?php echo $this->translate('Total Views') ?></th>
					  <th><?php echo $this->translate('Total Likes') ?></th>
						<?php if(!empty($this->show_comments)) : ?>
							<th><?php echo $this->translate('Total Comments') ?></th>
						<?php endif; ?>
						<th><?php echo $this->translate('Monthly Active Users') ?></th>
					</tr> 
				</thead>
	      <tbody>
					<tr>
					  <td><?php echo number_format($this->total_views); ?></td>
					  <td><?php echo number_format($this->total_likes); ?></td>
						<?php if(!empty($this->show_comments)) : ?>
							<td><?php echo number_format($this->total_comments); ?></td>
						<?php endif; ?>
					  <td><?php echo number_format($this->total_users); ?></td>
				  </tr>
	      </tbody>
	    </table>
	  </div>
		<div class="sitestore_edit_insights">
			<div>
		    <h4><?php echo $this->translate("Store Insights") ?></h4>
		    <p>
		      <?php echo $this->translate("Use the below filter to observe various metrics of your store over different time periods.") ?>
		    </p>
				<div class="sitestore_edit_insights_search">
					<?php echo $this->filterForm->render($this) ?>
				</div>	
		    <div class="sitestore_statistics">
		      <div class="sitestore_edit_insights_nav">
						<a id="admin_stats_offset_previous"  class='icon_previous' onclick="processStatisticsStore(-1);"><?php echo $this->translate("Previous") ?></a>
						<a id="admin_stats_offset_next"   class='icon_next' onclick="processStatisticsStore(1);" style="display: none;"><?php echo $this->translate("Next") ?></a>
		      </div>

			      <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl  ?>externals/swfobject/swfobject.js"></script>
			      <script type="text/javascript">
							var prev = '<?php echo $this->prev_link ?>';
							var currentArgs = {};
							var processStatisticsFilter = function(formElement) {
							  var vals = formElement.toQueryString().parseQueryString();
							  vals.offset = 0;
							  buildStatisticsSwiff(vals);
							  return false;
							}
							var processStatisticsStore = function(count) {
							  var args = $merge(currentArgs);
							  args.offset += count;
							  buildStatisticsSwiff(args);
							}
							var buildStatisticsSwiff = function(args) {

								var creation_date = '<?php echo $this->creation_date ?>';
								var startObject = '<?php echo $this->startObject ?>';

								// Check if previous link should come in all the cases
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
									$('admin_stats_offset_previous').setStyle('display', (startObject > creation_date ? '' : 'none'));
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
												$('admin_stats_offset_previous').setStyle('display', (startObject > creation_date ? '' : 'none'));
											}
										break;
									
										case 'MM':
											startObject = '<?php echo mktime(0, 0, 0, date('m', $this->startObject), 1, date('Y', $this->startObject)) ?>';
											$('admin_stats_offset_previous').setStyle('display', (startObject > creation_date ? '' : 'none'));
										break;

										case 'y':
											startObject = '<?php echo mktime(0, 0, 0, 1, 1, date('Y', $this->startObject)) ?>';
											$('admin_stats_offset_previous').setStyle('display', (startObject > creation_date ? '' : 'none'));
										break;
									}
								}
							  currentArgs = args;
							  $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));
						
							  var url = new URI('<?php echo $this->url(array('action' => 'chart-data')) ?>');
							  url.setData(args);
							  
							  //$('my_chart').empty();
							  swfobject.embedSWF(
							    "<?php echo $this->layout()->staticBaseUrl ?>externals/open-flash-chart/open-flash-chart.swf",
							    "my_chart",
							    "100%",
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
									'prev_link' : prev
							  });
							});
		    		</script>
	    		<div id="my_chart"></div>
	    	</div>	
			</div>
		</div>
  </div>
 </div>  
</div>
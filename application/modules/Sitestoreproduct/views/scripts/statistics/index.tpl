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

<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
?>

<?php 
  include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl';
  include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl';
?>


	<div class="layout_middle">
		<?php
		$this->headScript()
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
		?>
		<?php
			/* Include the common user-end field switching javascript */
			echo $this->partial('_jsSwitch.tpl', 'fields', array())
		?>

		<div class="sitestore_edit_content">
			<div class="sitestore_edit_header">
				<a href='<?php echo $this->url(array('store_url' => $this->siteStoreUrl), 'sitestore_entry_view', true) ?>' class="sitestoreproduct_buttonlink"><?php echo $this->translate('View Store'); ?></a>
				<h3><?php echo $this->translate('Dashboard: ').$this->sitestore->title; ?></h3>
			</div>
			<div id="show_tab_content">
        <div class="sitestoreproduct_manage_store sitestoreproduct_statistics_graph_search">
          <div>
            <p>
              <?php echo $this->translate("Use the below filters to graphically observe various metrics of your store over different time periods.") ?>
            </p>
            <div class="sitestoreproduct_statistics_search">
              <?php echo $this->formFilter->render($this) ?>
            </div>
            <div class="sitestoreproduct_statistics_paging clr">
              <a id="admin_stats_offset_previous"  class='buttonlink icon_previous' onclick="processStatisticsPage(-1);" href="javascript:void(0);" style="float:left;"><?php echo $this->translate("Previous") ?></a>
              <a id="admin_stats_offset_next" class='buttonlink_right icon_next' onclick="processStatisticsPage(1);" href="javascript:void(0);" style="display:none;float:right;"><?php echo $this->translate("Next") ?></a>
            </div>
              <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/swfobject/swfobject.js"></script>
		  
      <script type="text/javascript">
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

		      var earliest_date = '<?php echo $this->earliest_order_date ?>';
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
						$('admin_stats_offset_previous').setStyle('display', ((startObject > earliest_date && earliest_date) ? '' : 'none'));
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
									$('admin_stats_offset_previous').setStyle('display', ((startObject > earliest_date && earliest_date) ? '' : 'none'));
							}
							break;
							
							case 'MM':
								startObject = '<?php echo mktime(0, 0, 0, date('m', $this->startObject), 1, date('Y', $this->startObject)) ?>';
								$('admin_stats_offset_previous').setStyle('display', ((startObject > earliest_date && earliest_date) ? '' : 'none'));
								break;

							case 'y':
								startObject = '<?php echo mktime(0, 0, 0, 1, 1, date('Y', $this->startObject)) ?>';
								$('admin_stats_offset_previous').setStyle('display', ((startObject > earliest_date && earliest_date) ? '' : 'none'));
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
          <div class="sitestoreproduct_statistics_graph b_medium">
            <div id="my_chart">
              <center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin:10px 0;' /></center>
            </div>
          </div>  
        </div>
      </div>
    </div>
	</div>	
</div>
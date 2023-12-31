
<h2>
  <?php echo $this->translate("Activity Points") ?> &raquo; 
  &nbsp;<a href="<?php echo $this->url(array('module' => 'activitypoints', 'controller' => 'manage'),'admin_default', true) ?>"><?php echo $this->translate("Members") ?></a> &raquo; 
  &nbsp;<a href="<?php echo $this->user->getHref() ?>"><?php echo $this->user->getTitle() ?></a> (<?php echo $this->user->username ?>)
</h2>

<br>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
      
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("100016068") ?>
</p>

<br />

<div class="admin_search">
  <div class="search">
    <?php echo $this->filterForm->render($this) ?>
  </div>
</div>

<br />



<div class="admin_statistics">
  <div class="admin_statistics_nav">
    <a id="admin_stats_offset_previous" onclick="processStatisticsPage(-1);"><?php echo $this->translate("Previous") ?></a>
    <a id="admin_stats_offset_next" onclick="processStatisticsPage(1);" style="display: none;"><?php echo $this->translate("Next") ?></a>
  </div>

  <script type="text/javascript" src="externals/swfobject/swfobject.js"></script>
  <script type="text/javascript">
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
      currentArgs = args;

      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

      var url = new URI('<?php echo $this->url(array('action' => 'chart-data')) ?>');
      url.setData(args);
      
      $('my_chart').empty();
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
    
    /* OFC */
    var ofcIsReady = false;
    function ofc_ready()
    {
      ofcIsReady = true;
    }
    var save_image = function() {
      //window.location = 'data:image/png;base64,' + $('my_chart').get_img_binary();
      
      var img_src = "<img src='data:image/png;base64," + $('my_chart').get_img_binary() + "' />";
      var img_win = window.open('', 'Charts: Export as Image');
      img_win.document.write("<html><head><title>Charts: Export as Image</title></head><body>" + img_src + "</body></html>");

      return;
      
      // Can't get the stupid call back to work right
      var url = '<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart-image-upload')) ?>';
      $('my_chart').post_image(url, 'onImageUploadComplete', false);
    }
    var onImageUploadComplete = function() {

    }
    

    window.addEvent('load', function() {
      buildStatisticsSwiff({
        'type' : 'earned_vs_spent',
        'mode' : 'normal',
        'chunk' : 'dd',
        'period' : 'ww',
        'start' : 0,
        'offset' : 0,
        'user_id' : '<?php echo $this->user->getIdentity() ?>'
      });
    });
  </script>
  <div id="my_chart"></div>
</div>

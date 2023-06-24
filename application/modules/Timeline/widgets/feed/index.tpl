<?php if( (!empty($this->feedOnly) || !$this->endOfFeed ) &&
    (empty($this->getUpdate) && empty($this->checkUpdate)) ): ?>
  <script type="text/javascript">
    if (typeof(ScrollLoaderVar) == 'undefined') {
        var  ScrollLoaderVar;
    }
    var ViewMoreActive = false;
    en4.core.runonce.add(function() {
      
      var activity_count = <?php echo sprintf('%d', $this->activityCount) ?>;
      var next_id = <?php echo sprintf('%d', $this->nextid) ?>;
      var subject_guid = '<?php echo $this->subjectGuid ?>';
      var endOfFeed = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;

      var activityViewMore = window.activityViewMore = function(next_id, subject_guid) {
        if (ViewMoreActive)  return;
        else ViewMoreActive = true;
        if( en4.core.request.isRequestActive() ) return;

        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';
        $('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = '';

        var request = new Request.HTML({
          url : url,
          data : {
            format : 'html',
            'maxid' : next_id,
            'feedOnly' : true,
            'nolayout' : true,
            'subject' : subject_guid,
            'timeline' : true
           // getUpdate : 1
          },
          evalScripts : true,
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            ViewMoreActive = false;
            Elements.from(responseHTML).inject($('activity-feed'));
            en4.core.runonce.trigger();
            Smoothbox.bind($('activity-feed'));
            start_side();
          }
        });
       request.send();
      }

      if( next_id > 0 && !endOfFeed ) {
          if (ScrollLoaderVar != null) {
            ScrollLoaderVar.detach();
          }
          ScrollLoaderVar = new ScrollLoader({
                onScroll: function(){
                    activityViewMore(next_id, subject_guid);
                }
          });
        $('feed_viewmore').style.display = '';
        $('feed_loading').style.display = 'none';
        $('feed_viewmore_link').removeEvents('click').addEvent('click', function(event){
          event.stop();
          activityViewMore(next_id, subject_guid);
        });
      } else {
        ScrollLoaderVar.detach();
        $('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = 'none';
      }

    });
  </script>
<?php endif; ?>

<?php if( !empty($this->feedOnly) && empty($this->checkUpdate)): // Simple feed only for AJAX
  echo $this->timelineActivityLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'getUpdate' => $this->getUpdate,
    'feedOnly' => $this->feedOnly,
    'show_feature_toggle' => $this->show_feature_toggle,
    'features' => $this->features
  ));
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->checkUpdate) ): // if this is for the live update
  if ($this->activityCount)
  echo "<script type='text/javascript'>
          document.title = '($this->activityCount) ' + activityUpdateHandler.title;
          activityUpdateHandler.options.next_id = ".$this->firstid.";
        </script>

        <div class='tip'>
          <span>
            <a href='javascript:void(0);' onclick='javascript:activityUpdateHandler.getFeedUpdate(".$this->firstid.");$(\"feed-update\").empty();'>
              {$this->translate(array(
                  '%d new update is available - click this to show it.',
                  '%d new updates are available - click this to show them.',
                  $this->activityCount),
                $this->activityCount)}
            </a>
          </span>
        </div>";
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->getUpdate) ): // if this is for the get live update ?>
   <script type="text/javascript">
     activityUpdateHandler.options.last_id = <?php echo sprintf('%d', $this->firstid) ?>;
   </script>
<?php endif; ?>

<?php if( $this->enableComposer ): ?>
  <div class="activity-post-container">

    <form method="post" action="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>" class="activity" enctype="application/x-www-form-urlencoded" id="activity-form">
      <textarea id="activity_body" cols="1" rows="1" name="body"></textarea>
      <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
      <?php if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
        <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
      <?php endif; ?>
      <?php if( $this->formToken ): ?>
        <input type="hidden" name="token" value="<?php echo $this->formToken ?>" />
      <?php endif ?>
      <div id="compose-menu" class="compose-menu">
        <button id="compose-submit" type="submit"><?php echo $this->translate("Share") ?></button>
      </div>
    </form>

    <?php
      $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
    ?>

    <script type="text/javascript">
      var composeInstance;
      en4.core.runonce.add(function() {
          if( !DetectMobileQuick() && !DetectIpad() ) {
              composeInstance = new Composer('activity_body', {
                            menuElement : 'compose-menu',
                            baseHref : '<?php echo $this->baseUrl() ?>',
                            lang : {
                              'Post Something...' : '<?php echo $this->string()->escapeJavascript($this->translate('Post Something...')) ?>'
                            }
                          });
              var tabs_feed = $$('li.tab_layout_timeline_feed').getLast();
              if (typeOf (tabs_feed) == 'element') {
                    if (!tabs_feed.hasClass('active')) {
                        tabs_feed.addEvent('click', function(){
                            composeInstance.elements.body.fireEvent('blur');
                        });
                    }                    
              }
          }
      });
    </script>

    <?php foreach( $this->composePartials as $partial ): ?>
      <?php echo $this->partial($partial[0], $partial[1]) ?>
    <?php endforeach; ?>

  </div>
<?php endif; ?>

<?php if ($this->updateSettings && !$this->action_id): // wrap this code around a php if statement to check if there is live feed update turned on ?>
  <script type="text/javascript">
    var activityUpdateHandler;
    en4.core.runonce.add(function() {
      try {
          activityUpdateHandler = new ActivityUpdateHandler({
            'baseUrl' : en4.core.baseUrl,
            'basePath' : en4.core.basePath,
            'identity' : 4,
            'delay' : <?php echo $this->updateSettings;?>,
            'last_id': <?php echo sprintf('%d', $this->firstid) ?>,
            'subject_guid' : '<?php echo $this->subjectGuid ?>'
          });
          setTimeout("activityUpdateHandler.start()",1250);
          //activityUpdateHandler.start();
          window._activityUpdateHandler = activityUpdateHandler;
      } catch( e ) {
        //if( $type(console) ) console.log(e);
      }
    });
  </script>
<?php endif;?>

<?php if( $this->post_failed == 1 ): ?>
  <div class="tip">
    <span>
      <?php $url = $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'privacy'), 'default', true) ?>
      <?php echo $this->translate('The post was not added to the feed. Please check your %1$sprivacy settings%2$s.', '<a href="'.$url.'">', '</a>') ?>
    </span>
  </div>
<?php endif; ?>

<?php // If requesting a single action and it doesn't exist, show error ?>
<?php if( !$this->activity ): ?>
  <?php if( $this->action_id ): ?>
    <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
    <p>
      <?php echo $this->translate("The page you have attempted to access could not be found.") ?>
    </p>
  <?php return; else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("Nothing has been posted here yet - be the first!") ?>
      </span>
    </div>
  <?php return; endif; ?>
<?php endif; ?>

<div class="feed-update_ul">
<div id="feed-update"></div>
<div class="v_line"></div>

<?php echo $this->timelineActivityLoop($this->activity, array(
  'action_id' => $this->action_id,
  'viewAllComments' => $this->viewAllComments,
  'viewAllLikes' => $this->viewAllLikes,
  'getUpdate' => $this->getUpdate,
  'feedOnly' => $this->feedOnly,
  'show_feature_toggle' => $this->show_feature_toggle,
  'features' => $this->features
)) ?>


<div class="feed_viewmore" id="feed_viewmore" style="display: none;clear:both;">
  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
    'id' => 'feed_viewmore_link',
    'class' => 'buttonlink icon_viewmore'
  )) ?>
</div>
<div class="feed_viewmore" id="feed_loading" style="display: none;clear:both;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left;margin-right: 2px;' />
  <?php echo $this->translate("Loading ...") ?>
</div>
<div class="user_signup_date"><span><?php echo $this->translate("User Sign up Date:") ?></span> <?php echo $this->Sigh_up_Date ?></div>




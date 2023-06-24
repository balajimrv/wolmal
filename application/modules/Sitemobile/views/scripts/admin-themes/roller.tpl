<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: roller.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
 $this->headLink()
                ->appendStylesheet(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/css/tr.layout.css' )
         ->appendStylesheet(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/css/jquery.ui.css' )
         ->appendStylesheet(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/css/farbtastic.css' )
         ->appendStylesheet(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/css/tr.panel.css' )
        ;
$this->headScript()
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/js/lib/jquery.js')
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/js/lib/jquery.ui.js')
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/js/lib/jquery.ui.tabs.paging.js')
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/js/lib/jquery.color.js')
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/js/lib/json2.js')
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/js/lib/farbtastic.js')
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/js/app.js')
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/js/panel.js')
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/js/ui.js')
        ->appendFile(rtrim($this->baseUrl(), '/') . '/application/modules/Sitemobile/externals/theme-roller/jqm/panel.js')
;
?>
<script type="text/javascript">
  var changeThemeFile = function(file) {
    if(file=='theme.css'){
      var url = '<?php echo $this->url() ?>?file=' + file;
    }else{
      var url = '<?php echo $this->url(array('action' => 'index')) ?>?file=' + file;
    }
    window.location.href = url;
  }
</script>
<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl'; ?>
<h3>
  <?php echo $this->translate("Theme Roller Editor") ?>
</h3>
      <p>
        <?php echo $this->translate('SITEMOBILE_VIEWS_SCRIPTS_ADMINTHEMES_THEMES_ROLLER_DESCRIPTION'); ?>
      </p>
<br/>
<div class="sm-help-links">
	<a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'themes', 'action' => 'guidelines'), 'admin_default', true) ?>/#create-theme" class="buttonlink icon_help" ><?php echo $this->translate("Guidelines for creating a new theme."); ?></a>
  <a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'themes', 'action' => 'guidelines'), 'admin_default', true) ?>/#edit-theme" class="buttonlink icon_help"><?php echo $this->translate("Guidelines for editing active theme."); ?></a>
</div>
<br/>
<?php if ($this->writeable[$this->activeTheme->name]): ?>
  <div class="admin_theme_editor_edit_wrapper">

    <div class="admin_theme_editor_selected">
      <?php $theme = $this->activeTheme; ?>
      <?php
      // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
      $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
      if (!empty($this->manifest[$theme->name]['package']['thumb'])) {
        $thumb = $this->manifest[$theme->name]['package']['thumb'];
      }
      ?>
      <div class="theme_wrapper_selected"><img src="<?php echo $thumb ?>" alt="<?php echo $theme->name ?>"></div>
      <div class="theme_selected_info">
        <h3><?php echo $theme->title ?></h3>
        <?php if (!empty($this->manifest[$theme->name]['package']['version'])): ?>
          <h4 class="version">v<?php echo $this->manifest[$theme->name]['package']['version'] ?></h4>
        <?php endif; ?>
        <?php if (!empty($this->manifest[$theme->name]['package']['author'])): ?>
          <h4><?php echo $this->translate('by %s', $this->manifest[$theme->name]['package']['author']) ?></h4>
        <?php endif; ?>
        <div class="theme_edit_file">
          <h4>
            <?php echo $this->translate("Editing File:") ?>
          </h4>
          <?php echo $this->formSelect('choosefile', $this->activeFileName, array('onchange' => 'changeThemeFile(this.value);'), $this->activeFileOptions) ?>
        </div>
      </div>
    </div>
    <div>
      <div id="load-mask">

        <div id="load-spinner"></div>

      </div>


      <div id="interface">
        <div id="welcome" class="dialog" title=" ">


          <div class="buttonpane">
            <div class="separator"></div>

            <div id="colors">
              <div class="color-drag disabled" style="background-color: #C1272D"></div>
              <div class="color-drag disabled" style="background-color: #ED1C24"></div>
              <div class="color-drag disabled" style="background-color: #F7931E"></div>
              <div class="color-drag disabled" style="background-color: #FFCC33"></div>
              <div class="color-drag disabled" style="background-color: #FCEE21"></div>
              <div class="color-drag disabled" style="background-color: #D9E021"></div>
              <div class="color-drag disabled" style="background-color: #8CC63F"></div>
              <div class="color-drag disabled" style="background-color: #009245"></div>
              <div class="color-drag disabled" style="background-color: #006837"></div>
              <div class="color-drag disabled" style="background-color: #00A99D"></div>
              <div class="color-drag disabled" style="background-color: #33CCCC"></div>
              <div class="color-drag disabled" style="background-color: #33CCFF"></div>
            </div>
          </div>
        </div>


        <div id="share" class="dialog" title=" ">
          <h1><strong>Save</strong> Theme</h1>

          <div class="input-wrapper">
            <span class="loading-text">
              <img src="./application/modules/Sitemobile/externals/theme-roller/images/ajax-load-black.gif" />
              Loading...
            </span>
          </div>
        </div>


        <div id="toolbar">
          <div id="tr-logo"></div>
          <div id="button-block-1">
            <div id="fix-buttons">
              <div id="undo">
                <img src="./application/modules/Sitemobile/externals/theme-roller/images/undo.png" alt="Undo" />
                <span>undo</span>
              </div>
              <div id="redo">
                <img src="./application/modules/Sitemobile/externals/theme-roller/images/redo.png" alt="Redo" />
                <span>redo</span>
              </div>
            </div>
            <div class="tb-button" id="inspector-button">
              <img src="./application/modules/Sitemobile/externals/theme-roller/images/inspector.png" alt=" "/>
              <span>Inspector <strong>off</strong></span>
            </div>
          </div>
          <div id="button-block-2">
            <div class="tb-button" id="share-button">
              <div class="tb-button-inner">
                 <img src="./application/modules/Sitemobile/externals/theme-roller/images/save.png" alt=" "/>
                <div class="text">
                  <span class="big">Save</span>
                  <span>theme</span>
                </div>
              </div>
            </div>


          </div>
        </div>

        <div id="colorpicker"></div>

        <div id="tr_panel">
          <div id="tabs">
            <ul>
              <!--Tabs and tab panels go here-->
            </ul>
          </div>
        </div>

        <div id="wrapper">
          <div id="header-wrapper">
            <div id="header">
              <div id="quickswatch">
                <h2>Drag a color onto an element below</h2>
                <div class="colors">
                  <div class="color-drag" style="background-color: #FFFFFF"></div>
                  <div class="color-drag" style="background-color: #F2F2F2"></div>
                  <div class="color-drag" style="background-color: #E6E6E6"></div>
                  <div class="color-drag" style="background-color: #CCCCCC"></div>
                  <div class="color-drag" style="background-color: #808080"></div>
                  <div class="color-drag" style="background-color: #4D4D4D"></div>
                  <div class="color-drag" style="background-color: #000000"></div>
                  <div class="color-drag" style="background-color: #C1272D"></div>
                  <div class="color-drag" style="background-color: #ED1C24"></div>
                  <div class="color-drag" style="background-color: #F7931E"></div>
                  <div class="color-drag" style="background-color: #FFCC33"></div>
                  <div class="color-drag" style="background-color: #FCEE21"></div>
                  <div class="color-drag" style="background-color: #D9E021"></div>
                  <div class="color-drag" style="background-color: #8CC63F"></div>
                  <div class="color-drag" style="background-color: #009245"></div>
                  <div class="color-drag" style="background-color: #006837"></div>
                  <div class="color-drag" style="background-color: #00A99D"></div>
                  <div class="color-drag" style="background-color: #33CCCC"></div>
                  <div class="color-drag" style="background-color: #33CCFF"></div>
                  <div class="color-drag" style="background-color: #29ABE2"></div>
                  <div class="color-drag" style="background-color: #0071BC"></div>
                  <div class="color-drag" style="background-color: #2E3192"></div>
                  <div class="color-drag" style="background-color: #662D91"></div>
                  <div class="color-drag" style="background-color: #93278F"></div>
                  <div class="color-drag" style="background-color: #D4145A"></div>
                  <div class="color-drag" style="background-color: #ED1E79"></div>
                  <div class="color-drag" style="background-color: #C7B299"></div>
                  <div class="color-drag" style="background-color: #736357"></div>
                  <div class="color-drag" style="background-color: #C69C6D"></div>
                  <div class="color-drag" style="background-color: #8C6239"></div>
                  <div class="color-drag" style="background-color: #603813"></div>
                </div>
                <div id="sliders">
                  <img src="./application/modules/Sitemobile/externals/theme-roller/images/target.png" alt=" "/>
                  <span>LIGHTNESS</span><div id="lightness_slider"></div>
                  <span>SATURATION</span><div id="saturation_slider"></div>
                </div>
              </div>
              <?php
//					    if( isset($kuler_markup) ) {
//					        echo $kuler_markup;
//					    }
              ?>
              <div id="most-recent-colors">
                <div class="picker">
                  <h2>Recent Colors</h2>
                  <div class="compact">
                    <a id="recent-color-picker" href="#">colors...</a>
                    <input type="text" class="colorwell-toggle" value="#FFFFFF" data-name="recent" style="display: none" />
                  </div>
                </div>
                <div class="clear"></div>
                <div class="colors">
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                  <div class="color-drag disabled" style="background-color: #ddd"></div>
                </div>
              </div>

            </div>
          </div>

          <div id="content">
            <iframe id="frame" src="<?php echo $this->url(array('module'=>'sitemobile','controller'=>'theme-roller','action'=>'preview'),'default','true'); ?>" onload="TR.iframeLoadCallback();">
            </iframe>
          </div>

          <?php
          if (isset($JQM_VERSION)) {
            echo '<div id="version">' . $JQM_VERSION . '</div>';
          }
          ?>

          <?php
          //	if( isset($style) || isset($style_id) ) {
          echo '<div style="display: none" id="imported-style">true</div>';
          //	}
          ?>

          <div id="style"><?php
        echo $this->activeFileContents;
          ?>
          </div>

        </div>
      </div>
    </div>

  </div>

<?php else: ?>
  <div class="admin_theme_editor_edit_wrapper">
    <div class="tip">
      <span>

        <?php echo $this->translate('SITEMOBILE_VIEWS_SCRIPTS_ADMINTHEMES_INDEX_STYLESHEETSPERMISSION', $this->activeTheme->name) ?>

      </span>
    </div>
  </div>
<?php endif; ?>
<style type="text/css">
  #global_content{
    width:99%;
  }
</style>

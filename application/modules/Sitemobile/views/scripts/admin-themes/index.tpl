<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var modifications = [];
  window.onbeforeunload = function() {
    if( modifications.length > 0 ) {
      return '<?php echo $this->translate("If you leave the page now, your changes will be lost. Are you sure you want to continue?") ?>';
    }
  }
  var pushModification = function(type) {
    modifications.push(type);
  }
  var removeModification = function(type) {
    modifications.erase(type);
  }
  var changeThemeFile = function(file) {
 /*  if(file !='theme.css'){
      var url = '<?php echo $this->url() ?>?file=' + file;
    }else{
      var url = '<?php echo $this->url(array('action'=>'roller')) ?>?file=' + file;
    }*/
    window.location.href = '<?php echo $this->url() ?>?file=' + file;
  }
  var saveFileChanges = function() {
    var request = new Request.JSON({
      url : '<?php echo $this->url(array('action' => 'save')) ?>',
      data : {
        'theme_id' : $('theme_id').value,
        'file' : $('file').value,
        'body' : $('body').value,
        'format' : 'json'
      },
      onComplete : function(responseJSON) {
        if( responseJSON.status ) {
          removeModification('body');
          $$('.admin_themes_header_revert').setStyle('display', 'inline');
          alert('<?php echo $this->string()->escapeJavascript($this->translate("Your changes have been saved!")) ?>');
        } else {
          alert('<?php echo $this->string()->escapeJavascript($this->translate("An error has occurred. Changes could NOT be saved.")) ?>');
        }
      }
    });
    request.send();
  }
  var revertThemeFile = function() {
    var answer = confirm('<?php echo $this->string()->escapeJavascript($this->translate("SITEMOBILE_VIEWS_SCRIPTS_ADMINTHEMES_INDEX_REVERTTHEMEFILE")) ?>');
    if( !answer ) {
      return;
    }

    var request = new Request.JSON({
      url : '<?php echo $this->url(array('action' => 'revert')) ?>',
      data : {
        'theme_id' : '<?php echo $this->activeTheme->theme_id ?>',
        'format' : 'json'
      },
      onComplete : function() {
        removeModification('body');
        window.location.replace( window.location.href );
         window.location.reload();
      }
    });
    request.send();
  }
</script>

<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl'; ?>
<h3>
  <?php echo $this->translate("Mobile / Tablet Theme Editor") ?>
</h3>
<p>
  <?php echo $this->translate('Customize your community\'s overall look and feel in mobile and tablet by editing your current theme. Your theme consists of several CSS files. If you want to make custom changes to these files, select the one you want to edit from the "Editing File" pull-down below.') ?>
</p>
<!--Links for guideline-->
<br/>
<div class="sm-help-links">

    <!--Code Commented Related To Theme Customization Work-->

<!--  <a href="<?php// echo $this->url(array('module' => 'sitemobile', 'controller' => 'themes', 'action' => 'customization'), 'admin_default', true) ?>" class="buttonlink icon_help" ><?php// echo $this->translate("Customize Theme."); ?></a>-->
	<a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'themes', 'action' => 'guidelines'), 'admin_default', true) ?>/#create-theme" class="buttonlink icon_help" ><?php echo $this->translate("Guidelines for creating a new theme."); ?></a>
  <a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'themes', 'action' => 'guidelines'), 'admin_default', true) ?>/#edit-theme" class="buttonlink icon_help"><?php echo $this->translate("Guidelines for editing the active theme."); ?></a>
</div>	
<div class="admin_theme_editor_wrapper">
  <form action="<?php echo $this->url(array('action' => 'save')) ?>" method="post">
    <div class="admin_theme_edit">

      <div class="admin_theme_header_controls">
        <h3>
          <?php echo $this->translate('Active Theme') ?>
        </h3>
        <div>
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Revert'), array(
             'class' => 'buttonlink admin_themes_header_revert',
             'onclick' => 'revertThemeFile();',
             'style' => !empty($this->modified[$this->activeTheme->name]) ? '':'display:none;')) ?>
          <?php echo $this->htmlLink(array('route'=>'admin_default', 'module'=>'sitemobile', 'controller'=>'themes', 'action'=>'export','name'=>$this->activeTheme->name),
            $this->translate('Export'), array(
            'class' => 'buttonlink admin_themes_header_export',
            )) ?>
          <?php echo $this->htmlLink(array('route'=>'admin_default', 'module'=>'sitemobile', 'controller'=>'themes', 'action'=>'clone', 'name'=>$this->activeTheme->name),
            $this->translate('Clone'), array(
            'class' => 'buttonlink admin_themes_header_clone',
            )) ?>
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Save Changes'), array(
            'onclick' => 'saveFileChanges();return false;',
            'class' => 'buttonlink admin_themes_header_save',
          )) ?>
        </div>
      </div>


      <?php if( $this->writeable[$this->activeTheme->name] ): ?>
        <div class="admin_theme_editor_edit_wrapper sm_admin_theme_editor_edit_wrapper">

          <div class="admin_theme_editor_selected">
            <?php foreach( $this->themes as $theme ):?>
              <?php
              // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
              $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
              if( !empty($this->manifest[$theme->name]['package']['thumb']) ) {
                $thumb = $this->manifest[$theme->name]['package']['thumb'];
              }
              if ($theme->name === $this->activeTheme->name): ?>
                <div class="theme_wrapper_selected"><img src="<?php echo $thumb ?>" alt="<?php echo $theme->name?>"></div>
                <div class="theme_selected_info">
                  <h3><?php echo $theme->title?></h3>
                  <?php if ( !empty($this->manifest[$theme->name]['package']['version'])): ?>
                      <h4 class="version">v<?php echo $this->manifest[$theme->name]['package']['version'] ?></h4>
                  <?php endif; ?>
                  <?php if ( !empty($this->manifest[$theme->name]['package']['author'])): ?>
                    <h4><?php echo $this->translate('by %s', $this->manifest[$theme->name]['package']['author']) ?></h4>
                  <?php endif; ?>
                  <div class="theme_edit_file">
                    <h4>
                      <?php echo $this->translate("Editing File:") ?>
                    </h4>
                    <?php echo $this->formSelect('choosefile', $this->activeFileName, array('onchange' => 'changeThemeFile(this.value);'), $this->activeFileOptions) ?>
                  </div>
                </div>
              <?php break; endif; ?>
            <?php endforeach; ?>
          </div>

          <div class="admin_theme_editor">
            <?php echo $this->formTextarea('body', $this->activeFileContents, array('onkeypress' => 'pushModification("body")', 'spellcheck' => 'false')) ?>
          </div>
          <button class="activate_button" onclick="saveFileChanges();return false;"><?php echo $this->translate("Save Changes") ?></button>

          <?php echo $this->formHidden('file', $this->activeFileName, array()) ?>
          <?php echo $this->formHidden('theme_id', $this->activeTheme->theme_id, array()) ?>

        </div>
      <?php else: ?>
        <div class="admin_theme_editor_edit_wrapper">
          <div class="tip">
            <span>

              <?php echo 'The stylesheets for your current theme are not writeable. Please set full permissions recursively (CHMOD -R 0777) on "application/themes/sitemobile_tablet/'.$this->activeTheme->name.'" and try again.' ?>

            </span>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </form>


  <div class="admin_theme_chooser">

    <div class="admin_theme_header_controls">
      <h3>
        <?php echo $this->translate("Available Themes") ?>
      </h3>
      <div>
        <?php // echo $this->htmlLink(array('route'=>'admin_default', 'module'=>'sitemobile', 'controller'=>'themes','action'=>'upload'), $this->translate("Upload New Theme"), array('class'=>'buttonlink admin_themes_header_import')) ?>
        <!--
        <a class="admin help" href="http://support.socialengine.com/questions/128/Creating-Your-Own-Theme" target="_blank" style="margin-left: 10px; margin-right: 0px;"> </a>
        -->
      </div>
    </div>


    <div class="admin_theme_editor_chooser_wrapper">
      <ul class="admin_themes">
        <?php
        // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
        $alt_row = true;
        foreach( $this->themes as $theme ):
          $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
          if( !empty($this->manifest[$theme->name]['package']['thumb']) )
            $thumb = $this->manifest[$theme->name]['package']['thumb'];
          ?>
          <li <?php echo ($alt_row) ? ' class="alt_row"' : "";?>>
            <div class="theme_wrapper"><img src="<?php echo $thumb ?>" alt="<?php echo $theme->name?>"></div>
            <div class="theme_chooser_info">
                  <h3><?php echo $theme->title?></h3>
                    <?php if ( !empty($this->manifest[$theme->name]['package']['version'])): ?>
                        <h4 class="version">v<?php echo $this->manifest[$theme->name]['package']['version'] ?></h4>
                    <?php endif; ?>
                    <?php if ( !empty($this->manifest[$theme->name]['package']['author'])): ?>
                      <h4><?php echo $this->translate('by %s', $this->manifest[$theme->name]['package']['author']) ?></h4>
                    <?php endif; ?>
                    <?php if ($theme->name !== $this->activeTheme->name):?>
                            <form action="<?php echo $this->url(array('action' => 'change')) ?>" method="post">
                                    <button class="activate_button"><?php echo $this->translate('Activate Theme') ?></button>
                                    <?php echo $this->formHidden('theme', $theme->name, array('id'=>'')) ?>
                            </form>
                    <?php else:?>
                            <div class="current_theme">
                              (<?php echo $this->translate("this is your current theme") ?>)
                            </div>
                    <?php endif;?>
            </div>
          </li>
          <?php $alt_row = !$alt_row; ?>
        <?php endforeach; ?>
      </ul>
    </div>

  </div>

</div>

<script type="text/javascript">
//<![CDATA[
var updateCloneLink = function(){
  var value = $$('.theme_name input:checked');
  if (!value)
    return;
  else
    var newValue = value[0].value;
  var link = $$('a.admin_themes_header_clone');
  if (link.length) {
    link.set('href', link[0].href.replace(/\/name\/[^\/]+/, '/name/'+newValue));
  }
}
//]]>
</script>
<style type="text/css">
.icon_help{
 background-image: url(application/modules/Seaocore/externals/images/help.gif);
}
</style>
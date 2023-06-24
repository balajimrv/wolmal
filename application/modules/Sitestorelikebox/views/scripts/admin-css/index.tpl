<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>
<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>
<div class='tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer( $this->navigation )->render()
  ?>
</div>
<h2>
  <?php echo $this->translate("Style Editor for Color Schemes") ?>
</h2>

<p>
  <?php echo $this->translate("Configure the 2 color schemes (Light: light.css and Dark: dark.css) according to your requirement by modifying the class attribute values in the CSS below. You can configure the color schemes to match your site’s theme. To see the color code for a color, click on the rainbow icon below and select the desired color in the color picker. You can also see the preview for your chosen CSS configuration.") ?>
</p>

<script type="text/javascript">
  var modifications = [];
  window.onbeforeunload = function() {
    if( modifications.length > 0 ) {
      return '<?php echo $this->translate("If you leave the store now, your changes will be lost. Are you sure you want to continue?") ?>';
    }
  }
  var pushModification = function(type) {
    modifications.push(type);
  }
  var removeModification = function(type) {
    modifications.erase(type);
  }
  var changeThemeFile = function(file) {
    var url = '<?php echo $this->url() ?>?file=' + file;
    window.location.href = url;
  }
  var saveFileChanges = function() {
    var request = new Request.JSON({
      url : '<?php echo $this->url(array('action' => 'save')) ?>',
      data : {
        'file' : $('file').value,
        'body' : $('body').value,
        'format' : 'json'
      },
      onComplete : function(responseJSON) {
        if( responseJSON.status ) {
          removeModification('body');
          $$('.admin_themes_header_revert').setStyle('display', 'inline');
          alert('<?php echo $this->string()->escapeJavascript($this->translate("Your changes have been saved!")) ?>');
          window.location.reload(true);
        } else {
          alert('<?php echo $this->string()->escapeJavascript($this->translate("Your CSS changes for the color scheme could not be saved. Please give write permission (777) to the folder: /application/modules/Sitestorelikebox")) ?>');
        }
      }
    });
    request.send();
  }
  var revertThemeFile = function() {
    var answer = confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure that you want to revert all the changes you have made to this color scheme CSS ? If yes, the original color scheme CSS will be restored immediately and your changes lost.")) ?>');
    if( !answer ) {
      return;
    }

    var request = new Request.JSON({  
      url : '<?php echo $this->url(array('action' => 'revert')) ?>',
      data : {
       'file' : $('file').value, 
        'format' : 'json'
      },
      onComplete : function() {
        removeModification('body');
        window.location.replace( window.location.href );
      }
    });
    request.send();
  }
</script>


<div class="admin_theme_editor_wrapper">
  <form action="<?php echo $this->url(array('action' => 'save')) ?>" method="post">
    <div class="admin_theme_edit">
      <div class="admin_theme_header_controls">
        <h3>
          <?php echo $this->translate('Color Scheme Editing') ?>
        </h3>
      </div>
      <?php if( $this->writeable ): ?>
        <div class="admin_theme_editor_edit_wrapper" style="width:570px;">
          <div class="admin_theme_editor_selected fleft">
						<div class="theme_selected_info">
							<div class="theme_edit_file">
								<h4>
									<?php echo $this->translate("Choose Color Scheme CSS:") ?>
								</h4>
								<?php echo $this->formSelect('choosefile', $this->activeFileName, array('onchange' => 'changeThemeFile(this.value);'), $this->activeFileOptions) ?>
							</div>
						</div>
          </div>
          <?php
						$filterForm = new Sitestorelikebox_Form_Admin_Manage_Filter();
						echo $filterForm->sitestorelikebox_color; ?>
          <div class="admin_theme_editor clear">
            <?php echo $this->formTextarea('body', $this->activeFileContents, array('onkeypress' => 'pushModification("body")', 'spellcheck' => 'false')) ?>
          </div>
          <button class="activate_button" onclick="saveFileChanges();return false;"><?php echo $this->translate("Save Changes") ?></button>
          <?php echo $this->formHidden('file', $this->activeFileName, array()) ?>
        </div>
        <div class="admin_layoutbox_footnotes" style="width:550px;">
					<?php echo $this->translate("<strong class='bold'>Note:</strong> If you do not see your Color Scheme CSS changes in the Preview, then please clear your browser's cache and refresh the store.") ?>
				</div>
      <?php else: ?>
        <div class="admin_theme_editor_edit_wrapper" style="width:550px;">
          <div class="tip">
            <span>
              <?php echo $this->translate('CORE_VIEWS_SCRIPTS_ADMINTHEMES_INDEX_STYLESHEETSPERMISSION', $this->activeTheme->name) ?>
            </span>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </form>
	<div class="admin_likebox_wrapper">
		<h3>
			<?php echo $this->translate('Color Scheme Preview'); ?>
		</h3>
		<?php if(!empty($this->modified)) : ?>
			<div class="sitestorelikebox_style_revert">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Default'), array(
				'class' => 'buttonlink admin_themes_header_revert',
				'onclick' => 'revertThemeFile();',
				'style' => !empty($this->modified) ? '':'display:none;')) ?>
			</div>
		<?php endif; ?>
		<div class="admin_likebox_admin_preview">
			<iframe src='<?php echo $this->url(array('action' => 'dummy', 'file' => $this->activeFileName )) ?>' width="300" height="660" style="border-width:0px;"></iframe>
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
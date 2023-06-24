<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
$songs = $this->playlist->getSongs();
?>
<script type="text/javascript">

  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile() || DetectAllIos()) {
      $.mobile.activePage.find('#form-upload-music').css('display', 'none');
      $.mobile.activePage.find('#show_supported_message').css('display', 'block');
    } else {
      $.mobile.activePage.find('#form-upload-music').css('display', 'block');
      $.mobile.activePage.find('#show_supported_message').css('display', 'none');
    } 
  });

</script>


<?php echo $this->form->render($this) ?>

<div style="display:none;">
  <?php if (!empty($songs)): ?>
    <ul id="music_songlist">
      <?php foreach ($songs as $song): ?>
      <li id="song_item_<?php echo $song->song_id ?>" class="file file-success">
        <a href="javascript:void(0)" class="song_action_remove file-remove"><?php echo $this->translate('Remove') ?></a>
        <span class="file-name">
          <?php echo $song->getTitle() ?>
        </span>
        (<a href="javascript:void(0)" class="song_action_rename file-rename"><?php echo $this->translate('rename') ?></a>)
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<div style="display:none" id="show_supported_message" class='tip'>

  <span><?php echo $this->translate("Sorry, due to copyright laws and Apple restrictions, music cannot be uploaded from your device. You can edit a playlist from your Desktop."); ?><span>

</div>
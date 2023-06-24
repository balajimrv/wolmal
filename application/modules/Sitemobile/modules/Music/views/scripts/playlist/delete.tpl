<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: delete.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>

<div class='global_form_popup'>
  <?php if ($this->success): ?>
    <div class="global_form_popup_message">
      <?php echo $this->translate('The selected playlist has been deleted.') ?>
    </div>

  <?php else: // success == false ?>

  <form method="POST" action="<?php echo $this->url() ?>">
    <div>
      <h3><?php echo $this->translate('Delete Playlist?') ?></h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to delete the selected playlist? This action cannot be undone.') ?>
      </p>

      <p>&nbsp;</p>

      <p>
        <input type="hidden" name="playlist_id" value="<?php echo $this->playlist_id?>"/>
        <button type='submit' data-theme="b" data-inline="true"><?php echo $this->translate('Delete') ?></button>
        <?php echo $this->translate("or") ?> 
          <a href="#" data-rel="back" data-role="button" data-inline="true">
            <?php echo $this->translate('Cancel') ?>
          </a>
      </p>
    </div>
  </form>
  <?php endif; // success ?>

</div>



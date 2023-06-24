<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: delete.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div class='global_form_popup'>
  <form method="POST" action="<?php echo $this->url() ?>">
    <div>
      <h3>
        <?php echo $this->translate('Delete Message(s)?') ?>
      </h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to delete the selected message(s)? This action cannot be undone.') ?>
      </p>

      <div class="ui-page-content">
        <input type="hidden" name="message_ids" value="<?php echo $this->message_ids?>"/>
        <input type="hidden" name="place" value="<?php echo $this->place?>"/>
        <button type='submit' data-theme="b"><?php echo $this->translate('Delete') ?></button> 
        <?php echo $this->translate("or");?> 
				<a href="#" data-role="button" data-rel="back"><?php echo $this->translate("Cancel");?></a>
      </div>
    </div>
  </form>
</div>
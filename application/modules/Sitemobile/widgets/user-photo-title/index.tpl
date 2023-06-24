<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>


<!--Cover Photo Start Here-->
<div class="db-cover-photo-wrap">
  <a href="<?php echo $this->viewer()->getHref() ?>" data-title ="<?php echo $this->viewer()->getTitle()?>">
    <?php if ($this->addCoverPhoto && $this->photo): ?>
      <div class="db-cover-photo">
        <?php echo $this->itemPhoto($this->photo, 'thumb.cover','',array('data-load'=>'force')) ?>
        <div class="db-cover-profile-photo-cover"></div>
      </div>
    <?php endif; ?>
    <div class="db-cover-profile">
      <div class="db-cover-profile-photo">
        <?php echo $this->itemPhoto($this->viewer(), 'thumb.icon','',array('data-loaded'=>'force')) ?>
      </div>
      <div class="db-cover-profile-info">
        <strong><?php echo $this->translate('Hi %1$s!', $this->viewer()->getTitle()); ?></strong>
        <p></p>
      </div>
    </div>
  </a>
</div>
<!--Cover Photo End Here-->
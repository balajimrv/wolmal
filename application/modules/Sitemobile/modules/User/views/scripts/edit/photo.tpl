<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: photo.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="headline">
  <h2>
    <?php if ($this->viewer->isSelf($this->user)): ?>
      <?php echo $this->translate('Edit My Profile'); ?>
    <?php else: ?>
      <?php echo $this->translate('%1$s\'s Profile', $this->htmlLink($this->user->getHref(), $this->user->getTitle())); ?>
    <?php endif; ?>
  </h2>
  <div class="tabs">
    <?php
    // Render the menu
    echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
    ?>
  </div>
</div>
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
  <?php
  $winow_supported = 1;
// Windows is (generally) not a mobile OS
  if (isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone') || preg_match('/' . 'iPad' . '/i', $_SERVER['HTTP_USER_AGENT']))) {
    if (preg_match('/' . 'iPad' . '/i', $_SERVER['HTTP_USER_AGENT'])) {
      $version = preg_replace("/(.*) OS ([0-9]*)_(.*)/", "$2", $_SERVER['HTTP_USER_AGENT']);

// for example you use it this way

      if ($version < 6) {
        $winow_supported = 0;
      }
    } else {
      $winow_supported = 0;
    }
  }
  ?>


  <?php if (!$winow_supported): ?>
    <?php
    $this->form->done->setLabel("Sorry, the browser you are using does not support Photo uploading. You can edit your profile picture from your desktop.");
    $this->form->done->setAttribs(array('data-role' => 'none', 'onclick' => '', 'type' => '', 'class' => 'tip', 'style' => 'width:100%;'));
    $this->form->removeElement('Filedata');
    ?>
  <?php else: ?>
    <?php
    $this->form->done->setLabel("Save Photo");
    $this->form->done->setAttribs(array('type' => 'submit'));
    ?>
  <?php endif; ?>

  <div class="ui-page-content">
    <div class="ui-page-edit-user-photo">
  <?php echo $this->form->render($this) ?>
    </div>
  </div>
<?php else: ?>
  <form class="change-photo-form global_form" action="<?php echo $this->form->getAction(); ?>">
    <div id="current_profile_photo_wrapper">
      <div class="form-elements">
        <div id="current-wrapper" class="form-wrapper change-photo-wrapper">
          <div id="current-label" class="form-label">
            <label for="current" class="optional"><?php echo $this->translate("Current Photo") ?></label>
          </div>
          <div id="current-element" class="form-element">
            <?php echo $this->itemPhoto($this->subject(), 'thumb.profile', "", array('id' => 'lassoImg')) ?>
          </div>
        </div>
      </div>
      <div <?php echo $this->dataHtmlAttribs("navigation", array('data-role' => "navbar", 'data-inset' => "true")); ?>>
        <ul>
          <li>
            <a data-role="button" onclick="smappcore.userProfilePhoto.capturePhoto();" ><?php echo $this->translate("Capture") ?></a></li>
          <li><a data-role="button" onclick="smappcore.userProfilePhoto.getPhoto(smappcore.userProfilePhoto.pictureSource.PHOTOLIBRARY);" >
  <?php echo $this->translate("Gallery") ?></a></li>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteusercoverphoto')): ?>
          <li><?php echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', 'user_id' => $this->user->user_id, 'recent' => 1, 'special' => 'profile'), 'siteusercoverphoto_profilepage', true), $this->translate('My Photos'), array(' class' => 'seaocore_profile_cover_icon_photo_view smoothbox')); ?></li>
          <?php endif; ?>
              <?php if ($this->form->remove): ?>
            <li>
              <a data-role="button" href="<?php echo $this->url(array('action' => 'remove-photo')) ?>" class="smoothbox">
    <?php echo $this->translate($this->form->remove->getLabel()) ?></a></li>
              <?php endif; ?>
        </ul>
      </div>
    </div>
    <div id="preview_profile_photo_wrapper" style="display: none;">
      <div id="preview-wrapper-user-photo" class="form-element" >
        <div><?php echo $this->itemPhoto($this->subject(), 'thumb.profile', "", array('id' => 'preview-image-user')) ?>
        </div>
        <div>
          <a data-role="button" data-theme="b" id="profile_photo_user_save"><?php echo $this->translate("Save photo") ?> </a>
          <a data-role="button"  onclick="smappcore.userProfilePhoto.hidePreview()"><?php echo $this->translate("Cancel") ?> </a>
        </div>
      </div>
    </div>

  </form>
<?php endif; ?>
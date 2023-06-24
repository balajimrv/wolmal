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
<?php if (!Engine_Api::_()->sitemobile()->isApp() ||1): ?>
<?php
$winow_supported = 1;
// Windows is (generally) not a mobile OS
if (isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone') || preg_match('/' . 'iPad' . '/i', $_SERVER['HTTP_USER_AGENT']))) {

  $winow_supported = 0;
  if (preg_match('/' . 'iPad' . '/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/OS [2-5]_\d(_\d)? like Mac OS X/i', $_SERVER['HTTP_USER_AGENT'])) {
    $winow_supported = 1;
  }
}
?>

<?php if (!$winow_supported): ?>
  <?php
  if ($this->form->done) {
    $this->form->done->setLabel("Sorry, the browser you are using does not support Photo uploading, so please skip this step for now. You can upload a profile picture from your desktop.");
    $this->form->done->setAttribs(array('onclick' => '', 'type' => '', 'class' => 'tip'));
  } else {

    $this->form->addElement('Button', 'done', array(
        'label' => 'Sorry, the browser you are using does not support Photo uploading, so please skip this step for now. You can upload a profile picture from your desktop.',
        'type' => 'submit',
        'onclick' => 'javascript:finishForm();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if ($settings->getSetting('user.signup.photo', 0) == 0) {
      $this->form->addElement('Cancel', 'skip-link', array(
          'label' => 'skip',
          'prependText' => ' or ',
          'link' => true,
          'href' => 'javascript:void(0);',
          'onclick' => 'skipForm(); return false;',
          'decorators' => array(
              'ViewHelper',
          ),
      ));
    }
  }

  if (isset($this->form->Filedata))
    $this->form->removeElement('Filedata');
  ?>
<?php else: ?>
  <?php
  $this->form->Filedata->setAttrib('accept', 'image/*');
  if ($this->form->done) {
    $this->form->done->setLabel("Save Photo");
    $this->form->done->setAttribs(array('type' => 'submit', 'onclick' => 'javascript:finishForm();'));
  } else {

    $this->form->addElement('Button', 'done', array(
        'label' => 'Save Photo',
        'type' => 'submit',
        'onclick' => 'javascript:finishForm();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if ($settings->getSetting('user.signup.photo', 0) == 0) {
      $this->form->addElement('Cancel', 'skip-link', array(
          'label' => 'skip',
          'prependText' => ' or ',
          'link' => true,
          'href' => 'javascript:void(0);',
          'onclick' => 'skipForm(); return false;',
          'decorators' => array(
              'ViewHelper',
          ),
      ));
    }
  }
  ?>
<?php endif; ?>

<div class="ui-page-content">
  <div class="ui-page-edit-user-photo">
<?php echo $this->form->render($this) ?>
  </div>
</div>
<script type="text/javascript">
  function skipForm() {
    $('#skip').attr('value', 'skipForm');
    $.mobile.activePage.find('#SignupForm').submit();
  }

</script>
<?php else: ?>
  <form class="change-photo-form global_form" action="<?php echo $this->form->getAction(); ?>" id="SignupForm"  method="post">
    <div id="current_profile_photo_wrapper">
      <div class="form-elements">
        <div id="current-wrapper" class="form-wrapper change-photo-wrapper">
          <div id="current-label" class="form-label">
            <label for="current" class="optional"><?php echo $this->translate("Current Photo") ?></label>
          </div>
          <div id="current-element" class="form-element">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/User/externals/images/nophoto_user_thumb_profile.png' id="lassoImg" />
          </div>
        </div>
      </div>
      <div <?php echo $this->dataHtmlAttribs("navigation", array('data-role' => "navbar", 'data-inset' => "true")); ?>>
        <ul data-theme="a" >
          <li>
            <a data-role="button" onclick="smappcore.userProfilePhoto.capturePhoto();" ><?php echo $this->translate("Capture") ?></a></li>
          <li><a data-role="button" onclick="smappcore.userProfilePhoto.getPhoto(smappcore.userProfilePhoto.pictureSource.PHOTOLIBRARY);" >
  <?php echo $this->translate("Choose From Gallery") ?></a></li>
              <?php if ($this->form->remove): ?>
            <li>
              <a data-role="button" href="<?php echo $this->url(array('action' => 'remove-photo')) ?>" class="smoothbox">
    <?php echo $this->translate($this->form->remove->getLabel()) ?></a></li>
              <?php endif; ?>
        </ul>
      </div>
      <div>
      <a data-role="button"  onclick="javascript:skipForm();return false;" href="javascript:void(0);" ><?php echo $this->translate("Skip") ?> </a>
      
    </div>
    </div>
    <div id="preview_profile_photo_wrapper" style="display: none;">
      <div id="preview-wrapper-user-photo" class="form-element" >
        <div><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/User/externals/images/nophoto_user_thumb_profile.png' id="preview-image-user" />
        </div>
          <a data-role="button" data-theme="b" id="profile_photo_user_save" ><?php echo $this->translate("Save photo") ?> </a>
          <a data-role="button"  onclick="smappcore.userProfilePhoto.hidePreview()"><?php echo $this->translate("Cancel") ?> </a>
      </div>
    </div>
    

<input type="hidden" id="uploadPhoto" value="" name="uploadPhoto">

<input type="hidden" id="nextStep" value="" name="nextStep">

<input type="hidden" id="skip" value="" name="skip">
  </form>
<script type="text/javascript">
  function skipForm() {
    $('#skip').attr('value', 'skipForm');
    $.mobile.activePage.find('#SignupForm').submit();
  }

</script>
<?php endif; ?>
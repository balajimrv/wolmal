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

<?php if($this->logo && $this->image_type=='full'): ?>
  <div class="sm_splash_screen">
    <?php echo $this->htmlImage($this->logo, array('alt' => ''));?>
  </div>
<?php else: ?>
  <div class="sm_startup_cont">
    <div class="sitetitle">
      <?php
      if ($this->logo):
        echo $this->htmlImage($this->logo, array('alt' => ''), array('height' => $this->height, 'width' => $this->width));
      else : ?>
      <?php $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core'); 
      echo $this->translate($coreSettingsApi->getSetting('sitemobile.site.title', $coreSettingsApi->getSetting('core_general_site_title'))); ?>
      <?php endif; ?>
    </div>
    <div class="copyrighttxt">
      <?php echo $this->translate('Copyright &copy;%s', date('Y')) ?>
    </div>
  </div>
  <div class="loader">
    <div class="spinner">
      <div class="bounce1"></div>
      <div class="bounce2"></div>
      <div class="bounce3"></div>
    </div>
  </div>
<?php endif; ?>

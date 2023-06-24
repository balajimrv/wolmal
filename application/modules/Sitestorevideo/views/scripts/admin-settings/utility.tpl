<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: utility.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
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
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestorevideo','controller'=>'settings','action'=>'index'), $this->translate('Stores'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'settings','action'=>'show-video'), $this->translate('Products'), array())
    ?>
    </li>			
  </ul>
</div>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<h3>
  <?php echo $this->translate('Store Video Utilities'); ?>
</h3>
<p>
  <?php echo $this->translate("This store contains utilities to help configure and troubleshoot the store video plugin.") ?>
</p>
<br/>

<div class="settings">
  <form>
    <div>
      <h3><?php echo $this->translate("Ffmpeg Version") ?></h3>
      <p class="form-description"><?php echo $this->translate("This will display the current installed version of ffmpeg.") ?></p>
      <textarea><?php echo $this->version; ?></textarea><br/><br/><br/>

      <h3><?php echo $this->translate("Supported Video Formats") ?></h3>
      <p class="form-description"><?php echo $this->translate('This will run and show the output of "ffmpeg -formats". Please see this store for more info.') ?></p>
      <textarea><?php echo $this->format; ?></textarea><br/><br/>
      <?php if (TRUE): ?>
      <?php else: ?>
      <?php endif; ?>
    </div>
  </form>
</div>
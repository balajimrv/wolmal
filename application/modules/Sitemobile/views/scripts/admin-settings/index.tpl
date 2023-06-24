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

<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl';?>
<?php if(count($this->notIntegratedModules)>0):?>
<div>
  <div class="seaocore-notice">
		<div class="seaocore-notice-icon">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
		</div>
<!--    <div style="float:right;">
      <button onclick="dismissNote();"><?php echo $this->translate('Dismiss'); ?></button>
    </div>-->
    <div class="seaocore-notice-text">
      <?php echo $this->translate("Note: It seems that some modules on your site are not compatible with this plugin. Please find the list of such plugins below and click on \"Enable This Module\" link to enable the compatibility of those modules with this plugin:"); ?></br>
      <ul>
      <?php foreach( $this->notIntegratedModules as $item ): ?>
              <li><b><?php echo  $item->title ?> </b>
                <?php echo $this->htmlLink(array('reset' => true,'module'=>'sitemobile','controller'=>'module', 'action' => 'enable-mobile', 'enable_mobile' =>'1', 'name' => $item->name,'integrated'=>$item->integrated), 'Enable This Module')?>
              </li>    
        <?php     endforeach; ?>
      </ul>
      <?php //echo $this->translate("If you want to integrated these plugins for 'Mobile / Tablet' plugin, then please click on 'Add' button."); ?>
    </div>
  </div>
</div>
<?php endif; ?>
<?php if( !empty($this->getHostTypeArray) ): ?>
<div >
  <div class="seaocore-notice">
		<div class="seaocore-notice-icon">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
		</div>
    <div style="float:right;">
      <button onclick="dismissNote();"><?php echo $this->translate('Dismiss'); ?></button>
    </div>
    <div class="seaocore-notice-text">
      <?php echo $this->translate("Note: It seems that this plugin has been used at multiple domains, because of which this plugin may not work properly on domain configures to use this plugin. Please find the list of other domains below :"); ?></br>
      <ul>
      <?php foreach( $this->getHostTypeArray as $getHostName ):
              if( $this->viewAttapt != $getHostName && !empty($getHostName)):
                echo '<li><b>' . $getHostName . '</b></li>';
              endif;      
             endforeach;
      ?>
      </ul>
      <?php echo $this->translate("1) If you do not want to use this plugin on Multiple Domains, then please click on 'Dismiss' button.<br/> 2) If above is not the case and you want to use this plugin on multiple domains, then please file a support ticket from your SocialEngineAddOns <a href='http://www.socialengineaddons.com/user/login' target='_blank'>client area</a>."); ?>
    </div>
  </div>
</div>
<?php
  endif;
  include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; 
?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
  function dismissNote() {
    $('is_remove_note').value = 1;
    document.getElementById("sitemobile_global").submit();
  }
</script>
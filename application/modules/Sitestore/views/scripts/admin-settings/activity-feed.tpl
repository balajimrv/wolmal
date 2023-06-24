<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: activity-feed.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<?php if(!$this->isAAFModule):?>
<div class="tip">
  <span>
    <?php echo $this->translate('If you have the "%s" installed on your website, then you are saved from many of the configuration options below as that plugin automatically takes care of them. That plugin also provides many more advanced and useful features for activity feeds.',"<a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank' >Advanced Activity Feeds / Wall Plugin</a>");?>
  </span>
</div>
<?php endif;?>


  <div class='clear sitestore_settings_form'>
    <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
  window.addEvent('domready', function() {

    showEditingOptions("sitestorefeed_likestore_dummy-wrapper",'<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feed.type', 0); ?>');
    var feedTypeVale='<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorefeed.likestore.dummy', 'c') ?>';
    if(feedTypeVale=='a'){
      showTips('tip_like_widget','like');
    }else if(feedTypeVale=='b' || feedTypeVale=='c'){
      showTips('tip_like_faq','like');
        if(feedTypeVale=='c' && $('faq_37'))
         $('faq_37').style.display = 'block';
    }

  });
  function showEditingOptions(id,value) {
    if($(id)) {
      if(value==0)
        $(id).style.display = 'none';
      else
        $(id).style.display = 'block';
    }
  }

  function showTips(id,type){
    if($(id)) {     
        $('faq_37').style.display = 'none';
        $('tip_like_widget').style.display = 'none';
        $('tip_like_faq').style.display = 'none';
        $(id).style.display = 'block';
    }
  }


  function openSmoothboxFeed(url){    
    Smoothbox.open( url);

  }
</script>

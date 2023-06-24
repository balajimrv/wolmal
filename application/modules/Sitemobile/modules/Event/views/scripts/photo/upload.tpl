<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: upload.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
 <?php if(!Engine_Api::_()->sitemobile()->isApp()):?>
<div data-role="navbar" role="navigation" data-iconpos="right">
  <ul>
    <li><a data-icon="arrow-r" href="<?php echo $this->event->getHref(); ?>"><?php echo $this->event->getTitle(); ?></a></li>
    <li><a data-icon="arrow-d" class="ui-btn-active ui-state-persist"><?php echo $this->translate('Photos'); ?></a></li>
  </ul>
</div>
<?php endif;?>


<?php echo $this->form->render($this); ?>


<script type="text/javascript">

  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#form-upload').css('display', 'none');
      $.mobile.activePage.find('#show_supported_message').css('display', 'block');
    } else {
      $.mobile.activePage.find('#form-upload').css('display', 'block');
      $.mobile.activePage.find('#show_supported_message').css('display', 'none');
    } 
  });
</script>


<div style="display:none" id="show_supported_message" class='tip'>

  <span><?php echo $this->translate("Sorry, the browser you are using does not support Photo uploading. We recommend you to create an Event from your mobile / tablet without uploading a main photo for it. You can later upload the main photo from your Desktop."); ?><span>

</div>
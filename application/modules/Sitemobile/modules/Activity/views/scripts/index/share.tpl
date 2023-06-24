<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: share.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div id="feedshare" class="sm-share-popup">
  <div class="sm-share-popup-wrapper">

    <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
    <br />
    <?php if (!Engine_API::_()->sitemobile()->isApp()): ?>
    <div class="sharebox">
      <?php if ($this->attachment->getPhotoUrl()): ?>
        <div class="sharebox_photo">
          <?php echo $this->htmlLink($this->attachment->getHref(), $this->itemPhoto($this->attachment, 'thumb.icon'), array('target' => '_parent')) ?>
        </div>
      <?php endif; ?>
      <div>
        <div class="sharebox_title">
          <?php echo $this->htmlLink($this->attachment->getHref(), $this->attachment->getTitle(), array('target' => '_parent')) ?>
        </div>
        <div class="sharebox_description">
          <?php echo $this->attachment->getDescription() ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<script type="text/javascript"> 
  //<![CDATA[
  var toggleFacebookShareCheckbox = function(){
    $('span.composer_facebook_toggle').toggleClass('composer_facebook_toggle_active');
    var checkBox = $('#post_to_facebook');
    if (checkBox.attr("checked") == 'checked') { 
      checkBox.removeAttr('Checked');
    }
    else {
      checkBox.attr("checked", "checked");
    }
  }
  var toggleTwitterShareCheckbox = function(){
    $('span.composer_twitter_toggle').toggleClass('composer_twitter_toggle_active');
    var checkBox = $('#post_to_twitter');
    if (checkBox.attr("checked") == 'checked') { 
      checkBox.removeAttr('Checked');
    }
    else {
      checkBox.attr("checked", "checked");
    }
    
  }
  //]]>
</script>
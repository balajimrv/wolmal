<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: share.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php
$coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
?>
<?php $this->headScriptSM()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/smSocialActivity.js'); ?>
<div class="sm-ui-popup-top ui-header ui-bar-a">
  <a href="javascript:void(0);" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-icon-right" onclick="$('.ui-page-active').removeClass('dnone');$('#feedsharepopup').remove();$(window).scrollTop(parentScrollTop)"></a>
  <h2 class="ui-title" id="count-feedcomments"><?php echo $this->translate($coreSettingsApi->getSetting('sitemobile.site.title', $coreSettingsApi->getSetting('core_general_site_title'))); ?></h2>
</div>
<div class="sm-share-popup-wrapper">
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  
</div>

<!--<form method="post" action="/sev4v_new/messages/compose" class="global_form" enctype="application/x-www-form-urlencoded" id="messages_compose">
  <div>
    <div>
          <h3>Compose Message</h3>
          <p class="form-description">Create your new message with the form below. Your message can be addressed to up to 10 recipients.</p>
    <div class="form-elements">
        <div class="form-wrapper" id="to-wrapper">
            <div class="form-label" id="to-label">
                <label class="optional ui-input-text" for="to">Send To</label>
            </div>
            <div class="form-element" id="to-element">
                <div class="ui-input-text ui-shadow-inset ui-corner-all ui-btn-shadow ui-body-c">
                    <input type="text" placeholder="Start typing..." autocomplete="off" value="" id="to" name="to" class="ui-input-text ui-body-c ui-autocomplete-input">
                        <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>

                </div>
            </div>
        </div>
        <div class="form-wrapper" id="toValues-wrapper" style="display: none;">
            <div class="form-label" id="toValues-label">
                <label class="required" for="toValues">Send To</label>
            </div>
            <div class="form-element" id="toValues-element">
                <input type="hidden" id="toValues" value="" name="toValues">
            </div>
        </div>
        <div class="form-wrapper" id="title-wrapper">
            <div class="form-label" id="title-label">
                <label class="optional ui-input-text" for="title">Subject</label>
            </div>
            <div class="form-element" id="title-element">
                <div class="ui-input-text ui-shadow-inset ui-corner-all ui-btn-shadow ui-body-c">
                    <input type="text" value="" id="title" name="title" class="ui-input-text ui-body-c">
                </div>
            </div>
        </div>
        <div class="form-wrapper" id="body-wrapper">
          <div class="form-label" id="body-label">
            <label class="required ui-input-text" for="body">
              Message
            </label>
          </div>
          <div class="form-element" id="body-element">
              <textarea rows="6" cols="45" id="body" name="body" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset">

              </textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>-->
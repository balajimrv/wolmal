<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: invitepreview.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/styles/style_sitestoreinvite.css');
?>
<div class="global_form_popup">
  <h3><?php echo $this->translate("Invite to your Store") ?></h3>


  <div class="sitestoreinvite_popup_heading"><?php echo $this->translate("Preview your invitation") ?></div>


  <?php if (!empty($this->is_suggenabled)) : ?>

    <?php echo $this->translate("To the invitees who are already on %s, a suggestion for your Store will be sent.", $this->site_title) ?>
    <?php echo '<div class="sitestoreinvite_popup_suggestion"><span class="sitestore_notification"><a href="javascript:void(0);" >' . $this->viewer_name . '</a>' . $this->translate(" has suggested you to visit and explore the Store: ") . '<a href="javascript:void(0);" >' . $this->sitestore->title . '</a>.</span></div>'; ?>
    <div class="sitestoreinvite_popup_suggestion_preview">
      <ul class="requests">
        <li style="margin-bottom:0px;">
          <?php echo $this->itemPhoto($this->sitestore, 'thumb.icon', ''); ?>
          <div>
            <div>
              <?php echo '<a href="javascript:void(0);">' . $this->viewer_name . '</a>' . $this->translate(" has sent you a store suggestion:") ?> <a href="javascript:void(0);"><?php echo $this->sitestore->title; ?> </a>
            </div>
            <div>
              <button type="submit">
                <?php echo $this->translate('View this Store'); ?>
              </button>
              <?php echo $this->translate('or'); ?> <a href="javascript:void(0);"> <?php echo $this->translate('ignore request'); ?>  </a>
            </div>
          </div>	
        </li>
      </ul>
    </div>	
  <?php else : ?>

    <?php echo $this->translate("To the invitees who are already on %s, a suggestion notification for your Store will be sent.", $this->site_title) ?>
    <?php echo '<div class="sitestoreinvite_popup_suggestion"><span class="sitestore_notification"><a href="javascript:void(0);" >' . $this->viewer_name . '</a>' . $this->translate(" has suggested you to visit and explore the Store: ") . '<a href="javascript:void(0);" >' . $this->sitestore->title . '</a>.</span></div>'; ?>

  <?php endif; ?>
  <br />
  <?php echo $this->translate("Additionally, an email will also be sent to the invitees who are not on %s.", $this->site_title) ?>
  <div class="sitestoreinvite_popup_email_preview">
    <?php echo $this->bodyHtmlTemplate; ?>
  </div>
  <div class="buttons" >
    <button name="invitepreview"  id="invitepreview" onclick="parent.inviteFriends('<?php echo $this->servicetype;?>');" ><?php echo $this->translate("Send"); ?></button> 
    <?php echo $this->translate(" or"); ?>  <a onclick="parent.Smoothbox.close();" href="javascript:void(0);" id="cancel" name="cancel"><?php echo $this->translate("cancel"); ?></a>
  </div>
</div>	

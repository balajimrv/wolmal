<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: inviteusers.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreinvite/externals/styles/style_sitestoreinvite.css');
?>
<?php
$inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
        . $_SERVER['HTTP_HOST']
        . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
            'sitestore_id' => $this->storeinvite_id,
            'user_id' => $this->storeinvite_userid
                ), 'sitestoreinvite_invitefriends', true);
?>

<?php if (empty($this->successmessage)) : ?>
  <div id="invite_form">
    <form method="post" action='<?php echo $inviteUrl ?>' class="global_form_popup global_form" enctype="application/x-www-form-urlencoded">
      <div>
        <div>
          <h3><?php echo $this->translate('Invite & Promote'); ?></h3>
          <p class="form-description"><?php echo $this->translate('Tell your friends, fans and customers about this store and make it popular. Enter up to 10 email addresses in the text fields given below.'); ?></p>
            <?php if (!empty($this->is_error)) : ?>
              <ul class="form-errors">
                <li>
                  <?php echo $this->translate('To'); ?>
                  <ul class="errors">
                    <li><?php echo $this->is_error; ?></li>
                  </ul>
                </li>
              </ul>
            <?php endif; ?>
          <div class="form-elements">

            <div class="form-wrapper">
              <div class="form-label">
                <label class="required"><?php echo $this->translate('To'); ?></label>
              </div>
              <div class="form-element">
                <div id="parent_email">
                  <?php if (empty($this->invite_emails)) : ?>
                    <div class="inputbigouter" id='message_new_input0'>
                      <input type="text" class="input-txt" name="invite_email[]" id="message_new_input_0" value='' />
                    </div>
                  <?php else: ?>

                    <!--{foreach from=$emails item=email_array key=u}-->
                    <?php foreach ($this->invite_emails as $key => $email): ?>

                      <div class="inputbigouter" id='message_new_input<?php echo $key; ?>'>
                        <input type="text" class="input-txt" name="invite_email[]" id="message_new_input_<?php echo $key; ?>" value='<?php echo $email; ?>' onBlur="removecat('<?php echo $key; ?>')"/>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>

                <div id="message_block"></div>
                <div class="form_desc clr fleft" id= 'showlink'>
                  <a href="javascript:void(0);" onClick="message_box();"><?php echo $this->translate('Add another email address (10 Max)'); ?></a>
                </div>
              </div>
            </div>

            <div class="form-wrapper">
              <div class="form-label">
                <label class="required"><?php echo $this->translate('Message'); ?></label>
              </div>
              <div class="form-element">
                <p class="description"><?php echo $this->translate('Enter your message below.'); ?></p>
                <textarea name='invite_message' class="textarea" rows='6' cols='45'><?php echo $this->invite_message; ?></textarea>

              </div>
            </div>

            <div class="form-wrapper">
              <div class="form-label">&nbsp;</div>
              <div class="form-element">
                <button type="submit" id="submit" name="submit" value='submit'><?php echo $this->translate("Send Invites") ?></button>
                  <?php echo $this->translate('or'); ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
<?php endif; ?>
<script type="text/javascript">

  var total_prefill = 0;
<?php if ($this->total_prefill): ?>
      total_prefill = '<?php echo $this->total_prefill; ?>';
<?php endif; ?>
    var j = total_prefill;
    var div_counts = j;
    var counts = j;
    function message_box () {
      if(counts == 10) {
        $('showlink').style.display= 'none';
      }
      if (counts <=9) {
        counts++;		
        var modificationsarea_div = $('message_block');
        var newdiv = document.createElement('div');
        newdiv.id = 'message_new_input_' + counts;
        newdiv.innerHTML ='<div><input type="text" maxlength="60" id="message_new_input_' + counts + '"  name="invite_email[]" value="" class="addinputbox" /></div>';
        modificationsarea_div.appendChild(newdiv);
        var message_new_inputs = $('message_new_input_' + counts);
        message_new_inputs.focus();
      }
    }
    // THIS FUNCTION REMOVES A CATEGORY FROM THE STORE
    function removecat(messageid) {
      if ($('message_new_input_'+messageid).value == '' && messageid != 0 ) { 
        --j;
        var messagediv = $('message_new_input'+messageid); 
        var messarea = messagediv.parentNode;
        messarea.removeChild(messagediv);
      }
		
    }
</script>
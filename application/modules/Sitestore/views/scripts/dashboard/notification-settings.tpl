<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contact.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >
  var submitformajax = 1;
  //var manage_admin_formsubmit = 1;

  var maxRecipients = 10;
var storeAdminNamesAutocomplete;
function removeFromToValue(id)
  {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if ($('toValues').value==""){
      $('toValues-wrapper').setStyle('display', 'none');
    }
    else {
      $('toValues-wrapper').setStyle('display', 'block');
    }

storeAdminNamesAutocomplete.setOptions({
          'postData' : {'store_admin_ids': $('toValues').value}
      });
    $('to').disabled = false;
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    $('toValues').value = toValueArray.join();
  }
  
//  var packageRequest;

  en4.core.runonce.add(function() {

if ($('toValues').value==""){
				$('toValues-wrapper').setStyle('display', 'none');
      }
      
     storeAdminNamesAutocomplete = new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('module' => 'sitestore', 'controller' => 'dashboard', 'action' => 'suggest-store-admin-names', 'store_id' => $this->store_id), 'default', true) ?>', {
        'postData' : {'store_admin_ids': $('toValues').value},
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': true,
        'className': 'tag-autosuggest seaocore-autosuggest',
        'filterSubset' : true,
        'tokenFormat' : 'object',
        'tokenValueKey' : 'label',
        'injectChoice': function(token){

				var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
	      new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
	      choice.inputValue = token.label;
	      this.addChoiceEvents(choice).inject(this.choices);
	      choice.store('autocompleteChoice', token);
            
        },
        onPush : function(){
          if( $('toValues').value.split(',').length >= maxRecipients ){
            $('to').disabled = true;
          }
					if ($('toValues').value==""){
						$('toValues-wrapper').setStyle('display', 'none');
					}
					else {
						$('toValues-wrapper').setStyle('display', 'block');
					}
          storeAdminNamesAutocomplete.setOptions({
          'postData' : {'store_admin_ids': $('toValues').value}
      });
        }
      });
})

</script>

<?php if (empty($this->is_ajax)) : ?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
  <div class="layout_middle">
    <?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; ?>
    <div class="sitestore_edit_content">
      <div class="sitestore_edit_header">
        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()),$this->translate('VIEW_STORE')) ?>
        <h3><?php echo $this->translate('Dashboard: ') . $this->sitestore->title; ?></h3>
      </div>

      <div id="show_tab_content">
      <?php endif; ?>
			<h3> <?php echo $this->translate('Manage Notifications'); ?> </h3>
			<p class="form-description"><?php echo $this->translate("Below you can manage settings for receiving notifications for people's various activities in your store.") ?></p>
      <?php
      echo $this->form->render($this);
      ?>
      <br />
      <div id="show_tab_content_child">
      </div>

      <?php if (empty($this->is_ajax)) : ?>
      </div>
    </div>
  </div>
<?php endif; ?>
<script type="text/javascript">
en4.core.runonce.add(function() {
		<?php if (!empty($this->notification)) : ?>
			notificationEmail('block');
		<?php else : ?>
			notificationEmail('none');
		<?php endif; ?>
		
});
  window.addEvent('domready', function() {
		<?php if (!empty($this->notification)) : ?>
			notificationEmail('block');
		<?php else : ?>
			notificationEmail('none');
		<?php endif; ?>
		//en4.core.runonce.trigger();
  });
 
  
  function showNotificationAction() {
		if($('notification').checked == true) {
			notificationEmail('block');
			$('action_notification-posted').checked = true;
			$('action_notification-created').checked = true;
			$('action_notification-follow').checked = true;
			$('action_notification-like').checked = true;
			$('action_notification-comment').checked = true;
		} else {
			notificationEmail('none');
			$('action_notification-posted').checked = false;
			$('action_notification-created').checked = false;
			$('action_notification-follow').checked = false;
			$('action_notification-like').checked = false;
			$('action_notification-comment').checked = false;
		}
  }
  
  function notificationEmail(display) {
		if($('action_notification-wrapper')) {
			$('action_notification-wrapper').style.display=display;
		} 
		
		/*else {
			//$('action_notification-wrapper').style.display='none';
		//}*/
  }
</script>
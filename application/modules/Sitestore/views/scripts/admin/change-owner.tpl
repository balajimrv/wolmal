<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: changeowner.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function check_submit()
  { 
    if(document.getElementById('user_id').value == '' ) 
    {
      return false;
    }
    else 
    {
      return true;
    }
  }
</script>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    var contentAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('module' => 'sitestore', 'controller' => 'admin', 'action' => 'get-owner', 'store_id' => $this->store_id), 'default', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'sitestore_categories-autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : false,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
	
      }
    });
    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
    });
	
  });
</script>
<div class="sitestore_admin_popup">
  <div>
    <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  </div>
</div>
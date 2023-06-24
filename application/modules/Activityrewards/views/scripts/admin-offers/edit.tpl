
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',

      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });
</script>


<h2>
  <a href="<?php echo $this->url(array('module'  => 'activitypoints', 'controller' => 'settings','action'=>'index'), 'admin_default',true) ?>"><?php echo $this->translate("Activity Points Plugin") ?></a> &raquo; <a href="<?php echo $this->url(array('module'  => 'activityrewards', 'controller' => 'offers','action'=>'index'), 'admin_default',true) ?>"><?php echo $this->translate("Offers") ?></a> &raquo; <?php echo $this->translate("Edit Offer") ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>





<div class='clear'>
  <div class='settings'>

      <div class="global_form" id="admin_settings_form">
      <?php if ($this->form->saved_successfully): ?><h3 class="slowfade"><?php echo $this->translate("Settings were saved successfully.") ?></h3><?php endif; ?>

      <?php echo $this->form->render($this); ?>
      
      </div>

  </div>
</div>


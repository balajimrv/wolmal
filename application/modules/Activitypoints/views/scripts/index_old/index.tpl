<?php

  $this->headScript()

    ->appendFile($this->baseUrl() . '/application/modules/Activitypoints/externals/scripts/activitypoints.js')

    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')

    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')

    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')

    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js');



?>
<script>

var recipient_id = <?php echo isset($this->toValues) ? $this->toValues : '0'; ?>;



function up_sendpoints_to_friend() {

  SEMods.B.hide("up_sendpoints_success");

  SEMods.B.hide("up_sendpoints_fail");

  SEMods.B.show("up_sendpoints_main");

  SEMods.B.toggle("up_sendpoints");

}



function up_select_friend(obj) {

  recipient_id = obj.i;

}



function up_sendpoints() {

  SEMods.B.show("up_sendpoints_progress");

  SEMods.B.hide("up_sendpoints_main");

  SEMods.B.hide("up_sendpoints_success");

  SEMods.B.hide("up_sendpoints_fail");



  var ajax = new SEMods.Ajax( up_sendpoints_onajaxsuccess, up_sendpoints_onajaxfail );

  var params = "format=json&points_recipient_id="+recipient_id+"&points_amount="+SEMods.B.ge("points_amount").value;

  ajax.post( en4.core.baseUrl + 'activitypoints/index/' + 'sendpoints', params );

}



function up_sendpoints_onajaxsuccess(a, responseText) {

  var r = [];

  try {

    r = eval('('+responseText+')');

    

  } catch(e) {

    r.status = 1;

    r.msg = 'Internal Error';

  };



  SEMods.B.hide("up_sendpoints_progress");

  if(r.status == 0) {

    SEMods.B.ge("up_sendpoints_success_inner").innerHTML = r.msg;

    SEMods.B.ge("userpoints_balance").innerHTML = r.balance;

    SEMods.B.show("up_sendpoints_success");

  } else {

    SEMods.B.ge("up_sendpoints_fail_inner").innerHTML = r.msg;

    SEMods.B.show("up_sendpoints_fail");

    SEMods.B.show("up_sendpoints_main");

  }

    

}



function up_sendpoints_onajaxfail(a, responseText) {

  

  

}

</script>
<?php if( count($this->navigation) ): ?>

<div class="headline">
  <h2> <?php echo $this->translate('Bridges');?> </h2>
  <div class="tabs">
    <?php

	  // Render the menu

	  echo $this->navigation()

		->menu()

		->setContainer($this->navigation)

		->render();

	?>
  </div>
</div>
<?php endif; ?>
<div class="vbpage_main sesbasic_bxs sesbasic_clearfix">
  <p class="vbpage_des"><?php echo $this->translate('100016706') ?></p>
  <div class="vbpage_points_row">
    <div class='activitypoints_vault_balance'>
      <div class='activitypoints_vault_balance_title'> <strong>Balance Bridges</strong> <br>
      </div>
      <img class='activitypoints_vault_balance_coin' src="application/modules/Activitypoints/externals/images/userpoints_coins32.png">
      <div class='activitypoints_vault_balance_text'> <span id='userpoints_balance'><?php echo $this->user_points ?></span> <?php echo $this->translate('100016708') ?> </div>
      <br>
      <?php if($this->allow_transfer): ?>
      <script type="text/javascript">
  
    var maxRecipients = 1;
  
    
  
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
  
        $('toValues-wrapper').setStyle('height', '0');
  
      }
  
  
  
      //$('to').disabled = false;
  
      $('to-wrapper').setStyle('display','block');
  
    recipient_id = 0;
  
    }
  
  
  
    function removeToValue(id, toValueArray){
  
      for (var i = 0; i < toValueArray.length; i++){
  
        if (toValueArray[i]==id) toValueIndex =i;
  
      }
  
  
  
      toValueArray.splice(toValueIndex, 1);
  
      $('toValues').value = toValueArray.join();
  
    }
  
  
  
    en4.core.runonce.add(function() {
  
        new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('module' => 'activitypoints', 'controller' => 'index', 'action' => 'suggest'), 'default', true) ?>', {
  
          'minLength': 1,
  
          'delay' : 250,
  
          'selectMode': 'pick',
  
          'autocompleteType': 'message',
  
          'multiple': false,
  
          'className': 'message-autosuggest',
  
          'filterSubset' : true,
  
          'tokenFormat' : 'object',
  
          'tokenValueKey' : 'label',
  
          'injectChoice': function(token){
  
            if(token.type == 'user'){
  
              var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
  
              new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
  
              this.addChoiceEvents(choice).inject(this.choices);
  
              choice.store('autocompleteChoice', token);
  
            }
  
            else {
  
              var choice = new Element('li', {'class': 'autocompleter-choices friendlist', 'id':token.label});
  
              new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
  
              this.addChoiceEvents(choice).inject(this.choices);
  
              choice.store('autocompleteChoice', token);
  
            }
  
              
  
          },
  
          onPush : function(){
  
            if( $('toValues').value.split(',').length >= maxRecipients ){
  
        $('to-wrapper').setStyle('display','none');
  
              //$('to').disabled = true;
  
            }
  
        recipient_id = $('toValues').value.split(',').pop();
  
          }
  
        });
  
  
  
        <?php if( isset($this->toUser) && $this->toUser->getIdentity() ): ?>
  
  
  
        var toID = <?php echo $this->toUser->getIdentity() ?>;
  
        var name = '<?php echo $this->toUser->getTitle() ?>';
  
        var myElement = new Element("span");
  
        myElement.id = "tospan" + toID;
  
        myElement.setAttribute("class", "tag");
  
        myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+toID+"\");'>x</a>";
  
        $('toValues-element').appendChild(myElement);
  
        $('toValues-wrapper').setStyle('height', 'auto');
  
        
  
        <?php endif; ?>
  
  
  
        <?php if( isset($this->multi)): ?>
  
  
  
        var multi_type = '<?php echo $this->multi; ?>';
  
        var toIDs = '<?php echo $this->multi_ids; ?>';
  
        var name = '<?php echo $this->multi_name; ?>';
  
        var myElement = new Element("span");
  
        myElement.id = "tospan_"+name+"_"+toIDs;
  
        myElement.setAttribute("class", "tag tag_"+multi_type);
  
        myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+toIDs+"\");'>x</a>";
  
        $('toValues-element').appendChild(myElement);
  
        $('toValues-wrapper').setStyle('height', 'auto');
  
  
  
        <?php endif; ?>
  
  
  
      });
  
  
  
  
  
    en4.core.runonce.add(function(){
  
      new OverText($('to'), {'textOverride':'<?php echo $this->translate('Start typing...');?>','element':'label'});
  
    });
  
  </script> 
      <a href="#" onclick="up_sendpoints_to_friend();return false" alt="<?php echo $this->translate('100016709') ?>"> <?php echo $this->translate('100016709') ?> </a>
      <div id="up_sendpoints" style="<?php if( !isset($this->toUser) || !$this->toUser->getIdentity() ): ?>display:none;<?php endif; ?>padding-top: 3px;">
        <div id="up_sendpoints_success" style="display:none;"> <img src='application/modules/Activitypoints/externals/images/success.gif' border='0'> <span id="up_sendpoints_success_inner"></span> </div>
        <div id="up_sendpoints_fail" style="display:none;"> <img src='application/modules/Activitypoints/externals/images/error.gif' border='0'> <span id="up_sendpoints_fail_inner"></span> </div>
        <div id="up_sendpoints_progress" style="display:none;">
          <table cellpadding='0' cellspacing='0' style="padding-left: 5px">
            <tr>
              <td><img src="application/modules/Activitypoints/externals/images/semods_ajaxprogress1.gif"></td>
              <td style="padding-left:5px"><?php echo $this->translate('100016719') ?></td>
            </tr>
          </table>
        </div>
        <div id="up_sendpoints_main" style="display:block">
          <table cellpadding='0' cellspacing='0' style="padding-left: 5px">
            <tr>
              <td><span style="font-weight: bold"> <?php echo $this->translate('100016710') ?> </span></td>
              <td class='activitypoints_browse_field'><div id="to-wrapper" <?php if( isset($this->toUser) && $this->toUser->getIdentity() ): ?>style="display:none;"<?php endif; ?>>
                  <div id="to-element">
                    <input type="text" name="to" id="to" value="" autocomplete="off" />
                  </div>
                </div>
                <div id="toValues-wrapper">
                  <div id="toValues-element">
                    <input type="hidden" id="toValues" value="<?php echo isset($this->toValues) ? $this->toValues : ''; ?>" name="toValues">
                  </div>
                </div></td>
            </tr>
            <tr>
              <td><span style="font-weight: bold"> <?php echo $this->translate('100016711') ?> </span></td>
              <td class='activitypoints_browse_field'><input type="text" class="text" name="points_amount"></td>
            </tr>
            <tr>
              <td></td>
              <td class='browse_field'><button class="button" onclick="up_sendpoints()"> <?php echo $this->translate('100016715') ?> </button></td>
            </tr>
          </table>
          <input type="hidden" name="task" value="sendpoints">
        </div>
      </div>
      <?php endif; ?>
    </div>
    
    <div class="activitypoints_vault_total">
      <div class="activitypoints_vault_total_title"> <strong>Total Bridges Gained</strong> <br>
      </div>
      <img class="activitypoints_vault_total_coin" src="application/modules/Activitypoints/externals/images/userpoints_coins32.png">
      <div class="activitypoints_vault_total_text"> <strong> <?php echo $this->user_points_totalearned ?> </strong> <?php echo $this->translate('100016708') ?> </div>
    </div>
    <div style="clear:both"> </div>
  </div>
  <?php if ($this->userpoints_enable_topusers): ?>
  <div class="activitypoints_vault_bottom">
    <div class="activitypoints_vault_rating">
      <div class="activitypoints_vault_rating_title"> <strong> <?php echo $this->translate('100016722') ?> </strong> <br>
      </div>
      <img class="activitypoints_vault_rating_star" src="application/modules/Activitypoints/externals/images/star-32x32.png">
      <div class="activitypoints_vault_rating_text">
        <?php if ($this->user_points_totalearned != 0): ?>
        <strong> <?php echo $this->user_rank ?> </strong> <a href="<?php echo $this->url(array(), 'topusers') ?>"> <?php echo $this->translate('100016723') ?> </a>
        <?php else: ?>
        <?php echo $this->translate('100016724') ?>
        <?php endif; ?>
      </div>
    </div>
    <div style="clear:both"> </div>
  </div>
  <?php endif; ?>
</div>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
?>

<div class="layout_middle">
  <div class='global_form'>
    <?php echo $this->form->render($this) ?>
    <a href="javascript: void(0);" onclick="addAnotherOption(false,'');" id="addOptionLink"><?php echo $this->translate("Add another option") ?></a>

    <script type="text/javascript">
      
      var addAnotherOption ='';
      sm4.core.runonce.add(function() {
        var maxOptions = <?php echo $this->maxOptions ?>;
        var options = <?php echo Zend_Json::encode($this->options) ?>;
        var optionParent = $('#options').parent();
        addAnotherOption = $(document).ready = function (dontFocus, label) {  
          if (maxOptions && $('#pollOptionInput').length >= maxOptions) {
            return !alert(new String('<?php echo $this->string()->escapeJavascript($this->translate("A maximum of %s options are permitted.")) ?>').replace(/%s/, maxOptions));
          
            return false;
          }
         // 
         
          var optionElement = new $('<input />', {
            'type': 'text',
            'name': 'optionsArray[]',
            'id': 'pollOptionInput',
            'value': label,
            'data-mini': true,
           //'data-role':none,
            'events': {
              'keydown': function(event) {
                if (event.key == 'enter') {
                  if (this.get('value').trim().length > 0) {
                    addAnotherOption();
                    return false;
                  } else
                    return true;
                } else
                  return true;
              } // end keypress event
            } // end events
          });

          if( dontFocus ) {
            optionParent.append(optionElement);
          } else {
            optionParent.append(optionElement).focus();
          }

          optionParent.append($('#addOptionLink'));
          optionParent.trigger("create");

          if( maxOptions && $('#pollOptionInput').length >= maxOptions ) {
            $('#addOptionLink').remove();
          }
        }//end of anotherOption function
      
        // Do stuff
        if( $.type(options) == 'array' && options.length > 0 ) {
          options.each(function(label) {
            addAnotherOption(true, label);
          });
          if( options.length == 1 ) {
            addAnotherOption(true);
          }
        } else {
          // display two boxes to start with
          addAnotherOption(true,'');
          addAnotherOption(true,'');
        }
      });//end of runonce function
      // -->
    </script>
  </div>
</div> 


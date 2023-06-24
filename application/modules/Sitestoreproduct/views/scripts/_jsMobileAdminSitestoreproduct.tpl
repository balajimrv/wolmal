<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _jsAdminSitestoreproduct.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<script type="text/javascript">

  var fieldType = '<?php echo $this->fieldType ?>';
  var topLevelFieldId = '<?php echo sprintf('%d', $this->topLevelFieldId) ?>';
  var topLevelOptionId = '<?php echo sprintf('%d', $this->topLevelOptionId) ?>';
  var product_id = '<?php echo sprintf('%d', $this->product_id) ?>';
  var logging = true;
  // var sortablesInstance;
  var urls = {
    order : '<?php echo $this->layout()->staticBaseUrl; ?>sitestoreproduct/siteform/order',
  };

  // Register all events
  var registerEvents = function() {
    // Attach options activator
    $$('.field_extraoptions > a').removeEvents().addEvent('click', uiToggleOptions);
  }

  window.addEvent('domready', function() {
    registerEvents();
  });

  // Read the parent-option-child identifiers
  var readIdentifiers = function(string, throwException) {
    var m;

    // Find in ID
    m = string.match(/([0-9]+)_([0-9]+)_([0-9]+)(_([0-9]+))?/);
    if( $type(m) && $type(m[2]) ) {
      var dat = new Hash({
        parent_id : m[1],
        option_id : m[2],
        child_id : m[3]
      });
      if( $type(m[5]) ) {
        dat.set('suboption_id', m[5]);
      }
      return dat;
    }

    // Find in CLASS
    m = string.match(/parent_([0-9]+).+option_([0-9]+).+child_([0-9]+)/);
    if( $type(m) && $type(m[2]) ) {
      return new Hash({
        parent_id : m[1],
        option_id : m[2],
        child_id : m[3]
      });
    }

    // Not found
    if( !$type(throwException) || throwException ) {
      throw '<?php echo $this->string()->escapeJavascript($this->translate("Unable to find identifiers in text:")) ?> ' + string;
    } else {
      return false;
    }
  }

  var consoleLog = function() {
    //if( logging && typeof(console) != 'undefined' && console != null ) {
    if( logging ) {
      //if( typeof(console) !== 'undefined' && console != null ) {
      //  console.log(arguments);
      //console.log.apply(null, arguments);
      //}
    }
  }

  var saveOrder = function() {
    // $$('.admin_fields_options_saveorder')[0].setStyle('display', 'none');

    // Generate order structure
    var fieldOrder = [];
    var optionOrder = [];

    // Fields (maps) order
    $$('.admin_field').each(function(el) {
      var ids = readIdentifiers(el.get('id'));
      fieldOrder.push(ids.getClean());
    });

    // Options order
    $$('.field_option_select').each(function(el) {
      var ids = readIdentifiers(el.get('id'));
      optionOrder.push(ids.getClean());
    });

    // Send request
    var request = new Request.JSON({
      'url' : urls.order,
      'data' : {
        'fieldType' : fieldType,
        'format' : 'json',
        'fieldOrder' : fieldOrder,
        'optionOrder' : optionOrder
      },
      onSuccess : function(responseJSON, responseHTML) {
        //alert('Order saved!');
      }
    });

    request.send();
  }

  // handle moving fields
  function moveField(parent_id, flag){
    var parent = $("#" + parent_id);
    if(!parent || typeof parent == "undefined"){
      return;
    }

    // moving up
    if(flag == "up"){
      if(parent.prev().size() <= 0){
        return;
      }

      parent.insertBefore(parent.prev());
    }

    // moving down
    if(flag == "down"){
      if(parent.next().size() <= 0){
        return;
      }

      parent.insertAfter(parent.next());
    }

    // request to save order
    saveOrder();
  }

  /* --------------------------- OPTION - GENERAL --------------------------- */
  var uiToggleOptions = function(spec, forceClose) {
    if( $type(spec) == 'event' || $type(spec) == 'domevent' ) {
      element = spec.target;
    } else if( $type(spec) == 'element' || $type(spec) == 'domelement' ) {
      element = spec;
    } else {
      throw '<?php echo $this->string()->escapeJavascript($this->translate("cannot toggle, no event or element")) ?>';
    }

    element = element.getParent('.admin_field').getElement('.field_extraoptions');
    var isOpening = element.hasClass('active');

    // hide all openings...
    $$(".field_extraoptions").each(function(el){
      if(el.hasClass('active')){
        el.removeClass('active');
      }
    });

    // ...but not itself
    if(isOpening){
      element.addClass('active');
    }

    var targetState = !element.hasClass('active');
    if( $type(forceClose) && !forceClose ) targetState = false;
    !targetState ? element.removeClass('active') : element.addClass('active');
    OverText.update();
  }
</script>
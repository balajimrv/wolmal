<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->viewer_id): ?>

  <script type="text/javascript">
    sm4.core.runonce.add(function(){
      $('#rsvp_options input[type=radio]').bind('click', function(){
        var option_id = $(this).val();
//        $('#event_radio_' + option_id).addClass("event_radio_loading");
        $.mobile.loading().loader("show");
        $.ajax({
          type: "POST", 
          dataType: "json", 
          url: '<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $this->subject()->getGuid()), 'default', true); ?>',
          data: {
            format: 'json',
            'event_id': <?php echo $this->subject()->event_id ?>,
            'option_id' : option_id
          }
        }).done(function ( data ) {
//          $('#event_radio_' + option_id).removeClass("event_radio_loading").addClass("event_radio");
        $.mobile.loading().loader("hide");
       });
      });
    });
  </script>
 <div data-role="collapsible" id="collapsible" data-mini="true" data-collapsed='false'>
    <h3 id="your_rsvp"><?php echo $this->translate('Your RSVP'); ?></h3>
    <form class="event_rsvp_form" action="<?php echo $this->url() ?>" method="post" onsubmit="return false;">
      <fieldset data-role="controlgroup" data-mini="true" class="events_rsvp" id="rsvp_options">
        <input type="radio" name="rsvp_options" id="rsvp_option_2" value="2" <?php if ($this->rsvp == 2): ?> checked="true" <?php endif; ?> />
        <label for="rsvp_option_2"><?php echo $this->translate('Attending'); ?></label>	
        <input type="radio"  class="rsvp_options" name="rsvp_options" id="rsvp_option_1" value="1" <?php if ($this->rsvp == 1): ?> checked="true" <?php endif; ?> />
        <label for="rsvp_option_1"><?php echo $this->translate('Maybe Attending'); ?></label>
        <input type="radio"  class="rsvp_options" name="rsvp_options" id="rsvp_option_0" value="0" <?php if ($this->rsvp == 0): ?> checked="true" <?php endif; ?> />
        <label for="rsvp_option_0"><?php echo $this->translate('Not Attending'); ?></label>	
      </fieldset>
    </form>
  </div>
<?php endif; ?>
 


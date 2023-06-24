<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: append.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */ 
?>
<div class='global_form_popup'>
    <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
  function updateTextFields() {
    if ($.mobile.activePage.find('#playlist_id').val() == 0) {
      $.mobile.activePage.find('#title-wrapper').show();
    } else {
      $.mobile.activePage.find('#title-wrapper').hide();
    }
  }
  sm4.core.runonce.add(updateTextFields);
</script>
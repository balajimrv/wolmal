<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: messageowner.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>
<style type="text/css">
  .global_form > div{
    width: 600px;
  }
  .global_form div.form-label {
    width: 50px;
  }
  .global_form_popup #submit-wrapper, .global_form_popup #cancel-wrapper{
    float:none;
  }
  .global_form input[type="text"] {width:304px;}
</style>
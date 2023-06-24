<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(false):?>
<script type="text/javascript">
   window.addEvent('domready', function() {
     hide_Others();
   });
function hide_Others()
{
  if($('icon').value == 'others')
   document.getElementById('others-wrapper').style.display = 'block';
  else
     document.getElementById('others-wrapper').style.display = 'none';
}
 </script>
<?php endif;?>
 
<?php if( $this->form ): ?>

  <?php echo $this->form->render($this) ?>

<?php elseif( $this->status ): ?>

  <div><?php echo $this->translate("Your changes have been saved.") ?></div>

  <script type="text/javascript">
    setTimeout(function() {
      parent.window.location.replace( '<?php echo $this->url(array('action' => 'index', 'name' => $this->selectedMenu->name)) ?>' )
    }, 500);
  </script>

<?php endif; ?>
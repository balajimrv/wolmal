<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php 
if( !empty($this->showTinyMce) ):
$this->tinyMCESEAO()->addJS();
?>
<script type="text/javascript">
addTinyMCE("description");
function addTinyMCE(element_id) {
      <?php echo $this->tinyMCESEAO()->render(array('element_id' => 'element_id',
      'language' => $this->language,
      'directionality' => $this->directionality)); ?>'
        });
}
</script>
<?php endif; ?>
<h2>
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin') ?>
</h2>

<?php ?>
<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected FAQ?") ?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<p style="display:block;">
	<?php
            echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'startup'), $this->translate("Back to Store Startup Page"), array('class'=>'seaocore_icon_back buttonlink'));
	?>
	<br style="clear:both;" />
</p>
<br />
<?php if( empty($this->faqCheck) ){ ?>
<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<style type="text/css">
.defaultSkin iframe
{
	width:600px !important;
	height:350px !important;
}  
</style>
<?php } ?>


<style type="text/css">
.settings .form-element .description{max-width:600px;}
</style>
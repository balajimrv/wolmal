<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $description = "Below you can select the type of layout for the main profile of the directory items / stores on your site. You can choose from amongst 2 attractive AJAX based layouts.";
  if(Engine_Api::_()->getDbtable('modules','core')->isModuleEnabled('sitestoreintegration')) {
		$description .= "<br />Note: If you change the layout you have also set again all the content integrated widgets in the Store Profile layout.";
	}  
  $this->form->setTitle('Store Profile Layout Type');
  $this->form->setDescription($this->translate("$description"));
  $this->form->getDecorator('Description')->setOption('escape', false);
?>
<script type="text/javascript">

  function showpreview() {
    var layoutvalue = '';
    if($('sitestore_layout_setting-1').checked) {
      Smoothbox.open('<div class="sitestore-layout-popup"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/admin/tabbedlayout.png" alt=""  /></div>');
    } 
    else {
      Smoothbox.open('<div class="sitestore-layout-popup"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/admin/withouttabbedlayout.png" alt="" /></div>');
    }
  }

  function savevalues() {
    Smoothbox.open('<?php echo $this->url(array('module' => 'sitestore', 'controller' => 'defaultlayout', 'action' => 'savelayout'), 'admin_default') ?>')	
  }
</script>

<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='tabs'>
	<ul class="navigation">
		<li class="active">
			<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'defaultlayout','action'=>'index'), $this->translate('Store Profile Layout Type'), array())
			?>
		</li>

		<li>
			<?php
			echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'layout','action'=>'layout', 'store' => $this->store_id), $this->translate('Store Profile Layout Editor'), array())
		  ?>
		</li>
 
     <?php if(Engine_Api::_()->sitestore()->checkEnableForMobile('sitestore')):?>
			<li>
				<?php
					echo $this->htmlLink(array('route'=>'admin_default', 'module'=>'sitestore','controller'=>'mobile-layout','action'=>'layout', 'store' => $this->mobile_store_id), $this->translate('Store Profile Layout Editor for Mobile / Tablet'), array())
				?>
			</li>	
    <?php endif;?>
	</ul>
</div>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>


<script type="text/javascript">

  formElement = $('form-upload');

  if (typeof formElement != 'undefined' ) {
    formElement.addEvent('submit', function(event) {
      event.stop();
      Smoothbox.open('<?php echo $this->url(array('module' => 'sitestore', 'controller' => 'defaultlayout', 'action' => 'savelayout'), 'admin_default') ?>');
    })};


  function showpreview() {
    var layoutvalue = '';
    if($('sitestore_layout_setting-1').checked) {
      Smoothbox.open('<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/admin/tabbedlayout.png" alt=""  />');
    } 
    else {
      Smoothbox.open('<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/admin/withouttabbedlayout.png" alt="" />');
    }
  }

  function continuelayout() {
    $('form-upload').submit();
    parent.Smoothbox.close();	
  }

</script>
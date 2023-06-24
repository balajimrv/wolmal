<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adsettings.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>

<?php if (count($this->navigation)): ?>
	<div class='seaocore_admin_tabs'>
		<?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li>
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'settings','action'=>'adsettings'), $this->translate('Store Ad Settings'), array())
    ?>
    </li>
    <li class="active">
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'settings','action'=>'adsettings'), $this->translate('Product Ad Settings'), array())
    ?>
    </li>			
  </ul>
</div>

<div class='seaocore_settings_form'>
	<div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script type="text/javascript">

  window.addEvent('domready', function() {
    showads('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1); ?>');
  });

  function showads(option) {	 	
    if(option == 1) {
      if($('sitestoreproduct_adalbumcreate-wrapper'))
			$('sitestoreproduct_adalbumcreate-wrapper').style.display = 'block';
      if($('sitestoreproduct_addiscussionview-wrapper'))
			$('sitestoreproduct_addiscussionview-wrapper').style.display = 'block';
      if($('sitestoreproduct_addiscussioncreate-wrapper'))
			$('sitestoreproduct_addiscussioncreate-wrapper').style.display = 'block';
      if($('sitestoreproduct_addiscussionreply-wrapper'))
			$('sitestoreproduct_addiscussionreply-wrapper').style.display = 'block';		
      if($('sitestoreproduct_adtopicview-wrapper'))
      $('sitestoreproduct_adtopicview-wrapper').style.display = 'block';			
      if($('sitestoreproduct_advideocreate-wrapper'))
			$('sitestoreproduct_advideocreate-wrapper').style.display = 'block';
      if($('sitestoreproduct_advideoedit-wrapper'))
			$('sitestoreproduct_advideoedit-wrapper').style.display = 'block';
      if($('sitestoreproduct_advideodelete-wrapper'))
			$('sitestoreproduct_advideodelete-wrapper').style.display = 'block';			
      if($('sitestoreproduct_adtagview-wrapper')) 		
			$('sitestoreproduct_adtagview-wrapper').style.display = 'block';
    } 
    else {
      if($('sitestoreproduct_adalbumcreate-wrapper'))
			$('sitestoreproduct_adalbumcreate-wrapper').style.display = 'none';
      if($('sitestoreproduct_addiscussionview-wrapper'))
			$('sitestoreproduct_addiscussionview-wrapper').style.display = 'none';
      if($('sitestoreproduct_addiscussioncreate-wrapper'))
			$('sitestoreproduct_addiscussioncreate-wrapper').style.display = 'none';
      if($('sitestoreproduct_addiscussionreply-wrapper'))
			$('sitestoreproduct_addiscussionreply-wrapper').style.display = 'none';		
      if($('sitestoreproduct_adtopicview-wrapper'))
      $('sitestoreproduct_adtopicview-wrapper').style.display = 'none';			
      if($('sitestoreproduct_advideocreate-wrapper'))
			$('sitestoreproduct_advideocreate-wrapper').style.display = 'none';
      if($('sitestoreproduct_advideoedit-wrapper'))
			$('sitestoreproduct_advideoedit-wrapper').style.display = 'none';
      if($('sitestoreproduct_advideodelete-wrapper'))
			$('sitestoreproduct_advideodelete-wrapper').style.display = 'none';			
      if($('sitestoreproduct_adtagview-wrapper')) 		
			$('sitestoreproduct_adtagview-wrapper').style.display = 'none';
    } 	
  } 
</script>
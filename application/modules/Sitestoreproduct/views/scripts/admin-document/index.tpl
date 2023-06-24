<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>

<?php if (count($this->navigationStore)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigationStore)->render()
  ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigationSubStore)): ?>
  <div class='tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigationSubStore)->render()
  ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li class="active">
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'document','action'=>'index'), $this->translate('Global Settings'), array())
    ?>
    </li>
    <li>
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'document','action'=>'manage'), $this->translate('Manage Documents'), array())
    ?>
    </li>			
  </ul>
</div>
<div class='seaocore_settings_form'>
	<div class='settings'>    
    <?php 
      if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.isActivate', null)) {
        $this->form->setDescription($this->translate('These settings affect all members in your community.'));
        $this->form->getDecorator('Description')->setOption('escape', false);
      }
      echo $this->form->render($this); 
    ?>
  </div>
</div>

<script type="text/javascript">
  window.addEvent('domready',function (e)
            {
                showOtherSettings();
            });
            
     function showOtherSettings()
        {
            if($('sitestoreproduct_document_enable-1').checked)
            { 
                $('sitestoreproduct_document_auto-wrapper').style.display = 'block';
                $('sitestoreproduct_document_privacy-wrapper').style.display = 'block';
            }else{    
                $('sitestoreproduct_document_auto-wrapper').style.display = 'none';
                $('sitestoreproduct_document_privacy-wrapper').style.display = 'none';
            }
        }
</script>
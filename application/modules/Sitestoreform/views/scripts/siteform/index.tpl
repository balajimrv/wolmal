<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php
$baseUrl = $this->baseUrl();
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitestoreform/externals/styles/style_sitestoreform.css')
?>
<script type="text/javascript">
	
  window.addEvent('domready', function() { 
    $$('input[type=Checkbox]:([name=activeemail])').addEvent('click', function(e){
      $(this).getParent('.form-wrapper').getAllNext(':([id^=activeemailself-element])').setStyle('display', ($(this).get('value')>0?'none':'none'));
    });
    $('activeemail').addEvent('click', function(){
      $('activeemailself-wrapper').setStyle('display', ($(this).checked?'block':'none'));
    });
	
    $('activeemailself-wrapper').setStyle('display', ($('activeemail').checked?'block':'none'));
  });

</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitestore_viewstores_head">
<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
  <?php if(!empty($this->can_edit)):?>
		<div class="fright">
			<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id), 'sitestore_edit', true) ?>' class='buttonlink icon_sitestores_dashboard'><?php echo $this->translate('Dashboard');?></a>
		</div>
	<?php endif;?>
  <h2>	
    <?php $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null); ?>
    <?php echo $this->sitestore->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$tab_id)), $this->translate('Form')) ?>
  </h2>
</div>

<?php if ($this->formSelectData->status == 0): ?>
  <div class="tip"><span>
      <?php echo $this->translate("The Form for your Store has been disabled by the site administrator. You may contact the administrator to get it enabled, or for any queries. Though your settings below will be saved, visitors to your Store will not see the form till it is enabled.") ?></span>
  </div>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adformcreate', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)): ?>
  <div class="layout_right" id="communityad_formindex">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adformcreate', 3),"loaded_by_ajax"=>0,'widgetId'=>'store_formindex'))?>
  </div>
<?php endif; ?>

<div class="layout_middle">
  <div class="sitestore_form fleft">
    <div>
      <div>
        <div class="sitestoreform_form">
          <?php echo $this->createform->render($this) ?>
        </div>	

        <script type="text/javascript">
          var option_id = '<?php echo $this->option_id; ?>';
          var store_id = '<?php echo $this->sitestore->store_id; ?>';
        </script>
        <?php
        // Render the admin js
        echo $this->render('_jsField.tpl')
        ?>
        <div class="sitestoreform_separator"></div>
        <?php $canAddquestions = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.add.question', 1);?>
        <?php if(count($this->secondLevelMaps) ||  ($canAddquestions)):?>
          <h3><?php echo $this->translate('Manage Questions') ?></h3>
          <p><?php echo $this->translate('Below, you can see all the questions added by you and our site administrators. Note: you can only delete the questions added by you.');?></p>
        <?php endif;?>
        <div class="seaocore_add mtop10">
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.add.question', 1)):?>
						<a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate("Add a Question") ?></a>
          <?php endif;?>
          <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate("Save Order") ?></a>
        </div>
        <ul class="admin_fields">
          <?php foreach ($this->secondLevelMaps as $map): ?>
            <?php echo $this->adminFieldMeta($map) ?>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>		
</div>
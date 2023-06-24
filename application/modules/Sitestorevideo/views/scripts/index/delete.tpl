<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideodelete', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)): ?>
  <div class="layout_right" id="communityad_videodelete">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideodelete', 3),"loaded_by_ajax"=>0,'widgetId'=>"store_videodelete"))?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <div class="sitestore_viewstores_head">
    <?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>	
      <?php echo $this->sitestore->__toString() ?>	
      <?php echo $this->translate('&raquo; '); ?>
      <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Videos')) ?>
    </h2>
  </div>
  <div class='global_form'>
    <form method="post" class="global_form">
      <div>
        <div>
          <h3><?php echo $this->translate('Delete Store Video ?'); ?></h3>
          <p> 
            <?php echo $this->translate('Are you sure that you want to delete the store video titled "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->title, $this->timestamp($this->sitestorevideo->modified_date)) ?>
          </p>
          <br />
          <p>
            <input type="hidden" name="confirm" value="true"/>
            <button type='submit' ><?php echo $this->translate('Delete'); ?></button>
            <?php echo $this->translate('or'); ?> <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)),$this->translate('cancel')) ?>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>	
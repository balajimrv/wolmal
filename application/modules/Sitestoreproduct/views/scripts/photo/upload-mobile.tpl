<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php	$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');?>

<?php if ($this->can_edit): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_MobileDashboardNavigation.tpl'; ?>
<?php else:?>
<div class="sr_sitestoreproduct_view_top">
    <?php echo $this->htmlLink($this->sitestoreproduct->getHref(), $this->itemPhoto($this->sitestoreproduct, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>
        <?php echo $this->sitestoreproduct->__toString() ?>
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->sitestoreproduct->getHref(array('tab'=> $this->tab_id)), $this->translate('Photos')) ?>
    </h2>
</div>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adalbumcreate', 3) && $review_communityad_integration ): ?>
<div class="layout_right" id="communityad_albumcreate">
    <?php echo $this->content()->renderWidget("sitestoreproduct.review-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adalbumcreate', 3), 'tab' => 'adalbumcreate', 'communityadid' => 'communityad_albumcreate', 'isajax' => 0)); ?>
</div>
<div class="layout_middle">
    <?php endif; ?>
    <?php endif; ?>

    <div class="sr_sitestoreproduct_dashboard_content">
        <?php if ($this->can_edit): ?>
            <?php echo $this->partial('application/modules/Sitestoreproduct/views/scripts/dashboard/header-mobile.tpl', array('sitestoreproduct' => $this->sitestoreproduct)); ?>
        <?php endif; ?>
        <?php echo $this->form->render($this) ?>
        <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adalbumcreate', 3)  && $review_communityad_integration): ?>
    </div>
<?php endif; ?>
</div>
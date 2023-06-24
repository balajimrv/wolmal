<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: terms-and-conditions.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl';
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; 
?>
<div class="layout_middle">
  <div class="sitestore_edit_content">
    <div class="sitestore_edit_header">
      <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()),$this->translate('VIEW_STORE')) ?>
      <h3><?php echo $this->translate('Dashboard: ').$this->sitestore->title; ?></h3>
    </div>
    <div id="show_tab_content">
      <?php if( !empty($this->successTermsConditions) ) : ?>
        <ul class="form-notices" id="store_terms_conditions_success_message" style="display: none">
          <li>
            <?php echo $this->translate("Changes Saved."); ?>
          </li>                                   
        </ul>
      <?php endif; ?>
      <div>
        <?php echo $this->form->render($this); ?>
      </div>
    </div> 
  </div> 
</div>
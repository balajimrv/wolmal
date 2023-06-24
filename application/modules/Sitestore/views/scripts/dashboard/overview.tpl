<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: overview.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<div class="layout_middle">
  <?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl'; ?>

  <div class="sitestore_edit_content">
    <div class="sitestore_edit_header">
      <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id, $this->sitestore->getSlug()),$this->translate('VIEW_STORE')) ?>
      <h3><?php echo $this->translate('Dashboard: ') . $this->sitestore->title; ?></h3>
    </div>
    <div id="show_tab_content">
      <?php if (!empty($this->success)): ?>
        <ul class="form-notices" >
          <li>
            <?php echo $this->translate($this->success); ?>
          </li>
        </ul>
      <?php endif; ?>
      <div class="sitestore_overview_editor">
      	<?php echo $this->form->render($this); ?>
      </div>	
    </div>
  </div>
</div>
<script type="text/javascript">
  window.addEvent('domready', function () {
		 
    if ($('body-label')) {
      var catdiv1 = $('body-label');
      var catarea1 = catdiv1.parentNode;
      catarea1.removeChild(catdiv1);
    }
    if ($('save-label')) {
      var catdiv2 = $('save-label');  	
      var catarea2 = catdiv2.parentNode;
      catarea2.removeChild(catdiv2);
    }
  });
		
</script>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content-tabs.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="feeds_filter" id="aaf_tabs_feed">
  <div class="aaf_tabs_loader" style="display: none;" id="aaf_tabs_loader">
    <img alt="Loading" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemobile/modules/Core/externals/images/loading.gif" align="left" />
  </div>
  <?php $count = 1; ?>
    
  <?php foreach ($this->filterTabs as $key => $tab): ?>
    <?php if ($tab['filter_type'] == 'separator'): ?>
      <?php continue; ?>
    <?php endif; ?>
    <?php
    $class = array();
    $class[] = 'tab_' . $key;
    $class[] = 'tab_item_icon_feed_' . $tab['filter_type'];
    if ($this->actionFilter == $tab['filter_type'])
      $class[] = 'aaf_tab_active';
    $class = join(' ', $class);
    ?>
    <?php if ($count == 1): ?>
					<?php $sitemobileSettingsApi = Engine_Api::_()->getApi('settings', 'sitemobile');?>
					<?php $settingsDefaultValue = array();?>
					<?php $settingsDefaultValue['dafaultValue'] = 'mobile'; ?>
					<?php $sitemobile_popup_view = $sitemobileSettingsApi->getSetting('sitemobile.popup.view', $settingsDefaultValue);?>
			<select name="auth_view" class="<?php echo $class ?>" onchange="sm4.activity.getTabBaseContentFeed($(this).val(), 'sitefeed');" data-native-menu="<?php echo ($sitemobile_popup_view == 'mobile')? "true":"false" ?>"  value="<?php echo $this->actionFilter;?>">
            <?php endif; ?>
                
            <option value="<?php echo $tab['filter_type'] . '-' . $tab['list_id'] ?>" <?php echo $tab['filter_type']===$this->actionFilter?'selected="selected"':''?>> <?php echo $this->translate($tab['tab_title']) ?></option>
                
                
            <?php $count++;
          endforeach; ?>    
          <?php if ($count > 1): ?>
          </select>
        <?php endif; ?>
</div>

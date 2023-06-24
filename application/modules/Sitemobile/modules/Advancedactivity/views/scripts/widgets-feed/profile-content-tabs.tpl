<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profile-content-tabs.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="feeds_filter">
  <div class="aaf_tabs_loader" style="display: none;" id="aaf_tabs_loader">
    <img alt="Loading" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemobile/modules/Core/externals/images/loading.gif" align="left" />
  </div>

			<?php $sitemobileSettingsApi = Engine_Api::_()->getApi('settings', 'sitemobile');?>
			<?php $settingsDefaultValue = array();?>
			<?php $settingsDefaultValue['dafaultValue'] = 'mobile'; ?>
			<?php $sitemobile_popup_view = $sitemobileSettingsApi->getSetting('sitemobile.popup.view', $settingsDefaultValue);?>
			<?php if ($sitemobile_popup_view == 'mobile') : ?>   
				<select name="auth_view" class="aaf_tabs_apps_feed" onchange="sm4.activity.getTabBaseContentFeed($(this).val());" >
			<?php else:?>
				<select name="auth_view" class="aaf_tabs_apps_feed" onchange="sm4.activity.getTabBaseContentFeed($(this).val());" data-native-menu=false>
			<?php endif;?>

          <option value="owner-0" > <?php
          if (($this->subject()->getType() === 'user') || ($this->subject()->getType() === 'sitepage_page' && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
                  || ($this->subject()->getType() === 'sitebusiness_business' && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable())
           || ($this->subject()->getType() === 'sitegroup_group' && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())):
            echo $this->subject()->getTitle();
          else:
            echo $this->translate("Owner") . " (" . $this->string()->truncate($this->subject()->getTitle(), 15) . ")";
          endif;
          ?>  </option> 

          <?php if ($this->viewer()->getIdentity() && ($this->subject()->getType() != 'user')): ?>
            <option value="membership-0" > 

              <?php echo $this->translate("Friends") ?>
            </option> 
          <?php endif; ?>
          <option value="all-0" selected="selected"> 

            <?php echo $this->translate("Everyone") ?>
          </option> 

        </select> 	
</div>
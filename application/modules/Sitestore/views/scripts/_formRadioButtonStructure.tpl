<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formRadioButtonStructure.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$check_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layout.setting', 1);
if ($check_value == 1) {
  $check_1 = 'checked';
} else {
  $check_1 = 'unchecked';
}
if ($check_value == 0) {
  $check_0 = 'checked';
} else {
  $check_0 = 'unchecked';
}
$tabbed_layout='';$without_tabbed_layout='';
if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
$tabbed_layout = '<a href="https://lh5.googleusercontent.com/-I2lRdwC8ETw/UW-OE4g2g3I/AAAAAAAAASI/DPO1tfUoQUI/s576/tabbed-store-profile.png" target="_blank">' . $this->translate(' Tabbed Layout') . '</a>';
$without_tabbed_layout = '<a href="https://lh4.googleusercontent.com/-Jc9B3cy6aio/UW_XYcXjt4I/AAAAAAAAASo/OTqnYw8KMs8/s576/w-tabbed-store-profile.png" target="_blank">' . $this->translate(' Without Tabbed Layout') . '</a>';
} else {
$tabbed_layout =  '<b>' . $this->translate('Tabbed Layout') . '</b>'; 
$without_tabbed_layout = '<b>' . $this->translate(' Without Tabbed Layout'). '</b>';
}
?>
<div id="sitestore_layout_setting-wrapper" class="form-wrapper">
  <div id="sitestore_layout_setting-label" class="form-label">
    <label class="optional" for="sitestore_layout_setting"><?php echo $this->translate('Default Store Profile Layout'); ?></label>
  </div>
  <div id="sitestore_layout_setting-element" class="form-element">
    <p class="description"><?php echo $this->translate('Select a layout for the profile of stores on your site. The 2 layouts differ primarily in the way the AJAX based widgets like Info, Updates, Overview, and modules based widgets are displayed. (Note: On changing the layout, the profile layouts of all the existing stores on your site will also be reset to the one selected by you.'); ?></p>
    <ul class="form-options-wrapper">
<?php
echo '<li><div><input ' . $check_1 . ' id="sitestore_layout_setting-1" name="sitestore_sitestore_layout_setting" type="radio" value="1" ></div>' . $this->translate('Layout with main widgets as ajax based tabs in a horizontal row and in the middle column of the store.') . $tabbed_layout . '<br><span></span></li>';

echo '<li><div><input ' . $check_0 . ' id="sitestore_layout_setting-0" name="sitestore_sitestore_layout_setting" type="radio" value="0"></div>' . $this->translate('Layout with main widgets as ajax based links in a vertical order and in the right column of the store, below the store profile picture.') . $without_tabbed_layout . '<br><span></span></li>';
?>
    </ul>
  </div>
</div>
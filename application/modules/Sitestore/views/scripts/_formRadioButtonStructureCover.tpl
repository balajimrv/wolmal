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
// $check_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layout.coverphoto', 1);
// if ($check_value == 1) {
//   $check_1 = 'unchecked';
// } else {
//   $check_1 = 'unchecked';
// }
// if ($check_value == 0) {
//   $check_0 = 'unchecked';
// } else {
//   $check_0 = 'unchecked';
// }
$check_1 = 'unchecked';
$check_0 = 'unchecked';
$leftExtendablelink=$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/LeftExtendable.png';$rightExtendablelink=$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/RightExtendable.png';
$leftExtendable = "<a href='$leftExtendablelink' target='_blank'>" . $this->translate('Left Extended Column') . "</a>"; 
$rightExtendable = "<a href='$rightExtendablelink' target='_blank'>" . $this->translate('Right Extended Column') . "</a>"; 
?>
<div id="sitestore_layout_coverphoto-wrapper" class="form-wrapper">
  <div id="sitestore_layout_coverphoto-label" class="form-label">
    <label class="optional" for="sitestore_layout_coverphoto"><?php echo $this->translate('Column for Store Profile Cover Photo'); ?></label>
  </div>
  <div id="sitestore_layout_coverphoto-element" class="form-element">
    <p class="description"><?php echo $this->translate('Select the column in which you want to enable the Store Profile Cover Photo.'); ?></p>
    <ul class="form-options-wrapper">
			<?php
			echo '<li><div><input ' . $check_1 . ' id="sitestore_layout_coverphoto-1" name="sitestore_sitestore_layout_coverphoto" type="radio" value="1" ></div>' . $leftExtendable . '</li>';

			echo '<li><div><input ' . $check_0 . ' id="sitestore_layout_coverphoto-0" name="sitestore_sitestore_layout_coverphoto" type="radio" value="0"></div>' . $rightExtendable . '</li>';
			?>
    </ul>
  </div>
</div>
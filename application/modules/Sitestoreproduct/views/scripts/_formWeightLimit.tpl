<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formWeightLimit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php
$weightUnit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.weight.unit', 'lbs');
    echo '    
    <div id="weight_limit-wrapper" class="form-wrapper">
      <div id="weight_limit-label" class="form-label">
       <label for="weight_limit" class="optional"> ' . $this->translate("Weight handling range for this method (In %s)", $weightUnit) . '</label>
      </div>
      <div id="weight_limit-element" style="display : inline-block;">
        <p class="description">' . $this->translate("Enter the weight that can be handled by this shipping method. (You can enter a lower and an upper limit. Leaving this empty will imply no weight limitations for this method.)") . '</p>
        <div style="display:inline-block; margin-bottom:10px;">
          <input type="text" name="allow_weight_from" id="allow_weight_from" value="0" style="width:50px;" />
        </div>
        - <div style="display:inline-block; margin-bottom:10px;">
            <input type="text" name="allow_weight_to" id="allow_weight_to"  style="width:50px;" />
          </div>
      </div>
    </div>';
  

?>

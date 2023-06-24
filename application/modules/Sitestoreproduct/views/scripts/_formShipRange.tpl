<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formShipRange.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php
$localeObject = Zend_Registry::get('Locale');
$currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
$currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

echo '    
    <div id="ship_limit-wrapper" class="form-wrapper">
      <div id="ship_limit-label" class="form-label">
       <label for="ship_limit" class="optional"> ' . $this->translate("Shipping Range (In %s)", $currencyName) . '</label>
      </div>
      <div id="ship_limit-element" style="display : inline;">
        <div style="display:inline-block;">
          <input type="text" name="ship_start_limit" id="ship_start_limit" value="0.00" style="width:50px;" />
        </div>
        - <div style="display:inline;">
        <input type="text" name="ship_end_limit" id="ship_end_limit"  style="width:50px;" /></div>
      </div>
    </div>';
?>

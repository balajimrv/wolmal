<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSetBillingRegion.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php
  $getBillingCredentioal = Zend_Controller_Front::getInstance()->getRequest()->getParam("getBillingCredentioal", null);

  echo '<div id="billingregion_backgroundimage"> </div>';
  $tempStyle = "";
  if( !empty($getBillingCredentioal) ) {
    $getBillingCredentioal = @unserialize($getBillingCredentioal);
    
    $str = '';
     $tempFlag = false;
    foreach( $getBillingCredentioal['region'] as $key => $value) {
       if(empty($value))
        $tempFlag = true;
       
      if($key == $getBillingCredentioal['value']){ 
        $str .= '<option value="'.$key.'" selected=selected>'.$value.'</option>';
      }else {
        $str .= '<option value="'.$key.'">'.$value.'</option>';
      }
    }
        
    if(!empty($tempFlag))
      $tempStyle = "style='display:none'";
    
    echo '    
    <div id="state_billing-wrapper" class="form-wrapper"'.$tempStyle.'>
      <div id="state_billing-label" class="form-label">
       <label for="state_billing" class="optional">' . $this->translate("Region / State") . '</label>
      </div>
      <div id="state_billing-element" class="form-element">
        <select name="state_billing" id="state_billing">
         '.$str.'
        </select>
      </div>
    </div>';
  }else {
    echo '    
    <div id="state_billing-wrapper" class="form-wrapper"'.$tempStyle.'>
      <div id="state_billing-label" class="form-label">
       <label for="state_billing" class="optional">' . $this->translate("Region / State") . '</label>
      </div>
      <div id="state_billing-element" class="form-element">
        <select name="state_billing" id="state_billing">

        </select>
      </div>
    </div>';
  }
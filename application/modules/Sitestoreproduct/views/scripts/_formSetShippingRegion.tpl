<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSetShippingregion.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php
  $getShippingingCredentioal = Zend_Controller_Front::getInstance()->getRequest()->getParam("getShippingingCredentioal", null);

  echo '<div id="shippingregion_backgroundimage"> </div>';
  $tempStyle = "";
  if( !empty($getShippingingCredentioal) ) {
    $getShippingingCredentioal = @unserialize($getShippingingCredentioal);
    
    $str = '';
    $tempFlag = false;
    foreach( $getShippingingCredentioal['region'] as $key => $value) {
       if(empty($value))
        $tempFlag = true;
       
      if($key == $getShippingingCredentioal['value']){ 
        $str .= '<option value="'.$key.'" selected=selected>'.$value.'</option>';
      }else {
        $str .= '<option value="'.$key.'">'.$value.'</option>';
      }
    }
    
    
    if(!empty($tempFlag))
      $tempStyle = "style='display:none'";

    echo '

<div id="state_shipping-wrapper" class="form-wrapper" '.$tempStyle.'>
	<div id="state_shipping-label" class="form-label">
	 <label for="state_shipping" class="optional">' . $this->translate("Region / State") . '</label>
	</div>
	<div id="state_shipping-element" class="form-element">
		<select name="state_shipping" id="state_shipping">
			'.$str.'
		</select>
	</div>
</div>';
  }else {
    echo '
<div id="state_shipping-wrapper" class="form-wrapper" '.$tempStyle.'>
	<div id="state_shipping-label" class="form-label">
	 <label for="state_shipping" class="optional">' . $this->translate("Region / State") . '</label>
	</div>
	<div id="state_shipping-element" class="form-element">
		<select name="state_shipping" id="state_shipping">
			
		</select>
	</div>
</div>';
  }
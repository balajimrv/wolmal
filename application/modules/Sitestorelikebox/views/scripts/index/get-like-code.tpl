<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-like-code.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
	<h3><?php echo $this->translate( "Your Embeddable Store Badge code" ) ; ?></h3>
	<div>
	  <ul>
	    <li class="mtop10">
	      <?php echo $this->translate( "Copy this code and paste it into your web store." ) ; ?>
	    </li>
	    <li>
	      <textarea spellcheck="false" class="text-box" onclick="this.focus(); this.select()"><?php echo $this->code ; ?></textarea>
	    </li>
	    <li class="mtop10">
	      <button onclick="parent.Smoothbox.close();" ><?php echo $this->translate( 'Okay' ) ?></button>
	    </li>
	  </ul>
	</div>
</div>
<style type="text/css">
.text-box{
	border: 2px solid #ccc; 
	height: 100px;
	margin-top: 10px;
	padding: 5px;
	width: 600px;
}
</style>
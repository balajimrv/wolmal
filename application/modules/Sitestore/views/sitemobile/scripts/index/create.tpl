<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(!empty($this->sitestoreUrlEnabled) && !empty($this->show_url)):?>
	<script type="text/javascript">

//		window.addEvent('domready', function() { 
//		var e4 = $('store_url_msg-wrapper');
//		$('store_url_msg-wrapper').setStyle('display', 'none');
//		
//				var storeurlcontainer = $('store_url-element');
//				var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
//				var newdiv = document.createElement('div');
//				newdiv.id = 'url_varify';
//				newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='StoreUrlBlur();return false;' class='check_availability_button'>"+language+"</a> <br />";
//
//				storeurlcontainer.insertBefore(newdiv, storeurlcontainer.childNodes[2]);
//				checkDraft();
//		});
//
//		function checkDraft(){
//			if($('draft')){
//				if($('draft').value==0) {
//					$("search-wrapper").style.display="none";
//					$("search").checked= false;
//				} else{
//					$("search-wrapper").style.display="block";
//					$("search").checked= true;
//				}
//			}
//		}
//
//
//		function StoreUrlBlur() {
//			if ($('store_url_alert') == null) {
//				var storeurlcontainer = $('store_url-element');
//				var newdiv = document.createElement('span');
//				newdiv.id = 'store_url_alert';
//				newdiv.innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
//				storeurlcontainer.insertBefore(newdiv, storeurlcontainer.childNodes[3]);
//			}
//			else {
//				$('store_url_alert').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
//			}
//			var url = '<?php echo $this->url(array('action' => 'storeurlvalidation' ), 'sitestore_general', true);?>';
//			en4.core.request.send(new Request.JSON({
//				url : url,
//				method : 'get',
//				data : {
//					store_url : $('store_url').value,
//          check_url : 0,
//          store_id : 0,
//					format : 'html'
//				},
//
//				onSuccess : function(responseJSON) {
//					//$('store_url_msg-wrapper').setStyle('display', 'block');
//					if (responseJSON.success == 0) {
//						$('store_url_alert').innerHTML = responseJSON.error_msg;
//						if ($('store_url_alert')) {
//							$('store_url_alert').innerHTML = responseJSON.error_msg;
//						}
//					}
//					else {
//						$('store_url_alert').innerHTML = responseJSON.success_msg;
//						if ($('store_url_alert')) {
//							$('store_url_alert').innerHTML = responseJSON.success_msg;
//						}
//					}
//				}
//		}));
//	}
//
//	//<![CDATA[
//		window.addEvent('load', function()
//		{
//		  if($('store_url_address')) {
//				$('store_url_address').innerHTML = $('store_url_address').innerHTML.replace('STORE-NAME', '<span id="store_url_address_text">STORE-NAME</span>');
//			}
//      
//      $('short_store_url_address').innerHTML = $('short_store_url_address').innerHTML.replace('STORE-NAME', '<span id="short_store_url_address_text">STORE-NAME</span>');
//
//			$('store_url').addEvent('keyup', function()
//			{
//				var text = 'STORE-NAME';
//				if( this.value != '' )
//				{
//					text = this.value;
//				}
//				$('store_url_address_text').innerHTML = text;
//        $('short_store_url_address_text').innerHTML = text;
//			});
//			// trigger on store-load
//			if ($('store_url').value.length)
//					$('store_url').fireEvent('keyup');
//		});
//	//]]>
//	</script>
<?php elseif(empty($this->sitestoreUrlEnabled)):?>
  <script type="text/javascript">//
//
//		window.addEvent('domready', function() { 
//		var e4 = $('store_url_msg-wrapper');
//		$('store_url_msg-wrapper').setStyle('display', 'none');
//		
//				var storeurlcontainer = $('store_url-element');
//				var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
//				var newdiv = document.createElement('div');
//				newdiv.id = 'url_varify';
//				newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='StoreUrlBlur();return false;' class='check_availability_button'>"+language+"</a> <br />";
//
//				storeurlcontainer.insertBefore(newdiv, storeurlcontainer.childNodes[2]);
//				checkDraft();
//		});
//
//		function checkDraft(){
//			if($('draft')){
//				if($('draft').value==0) {
//					$("search-wrapper").style.display="none";
//					$("search").checked= false;
//				} else{
//					$("search-wrapper").style.display="block";
//					$("search").checked= true;
//				}
//			}
//		}
//
//
//		function StoreUrlBlur() {
//			if ($('store_url_alert') == null) {
//				var storeurlcontainer = $('store_url-element');
//				var newdiv = document.createElement('span');
//				newdiv.id = 'store_url_alert';
//				newdiv.innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
//				storeurlcontainer.insertBefore(newdiv, storeurlcontainer.childNodes[3]);
//			}
//			else {
//				$('store_url_alert').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" />';
//			}
//			var url = '<?php echo $this->url(array('action' => 'storeurlvalidation' ), 'sitestore_general', true);?>';
//			en4.core.request.send(new Request.JSON({
//				url : url,
//				method : 'get',
//				data : {
//					store_url : $('store_url').value,
//          check_url : 0,
//          store_id : 0,
//					format : 'html'
//				},
//
//				onSuccess : function(responseJSON) {
//					//$('store_url_msg-wrapper').setStyle('display', 'block');
//					if (responseJSON.success == 0) {
//						$('store_url_alert').innerHTML = responseJSON.error_msg;
//						if ($('store_url_alert')) {
//							$('store_url_alert').innerHTML = responseJSON.error_msg;
//						}
//					}
//					else {
//						$('store_url_alert').innerHTML = responseJSON.success_msg;
//						if ($('store_url_alert')) {
//							$('store_url_alert').innerHTML = responseJSON.success_msg;
//						}
//					}
//				}
//		}));
//	}
//
//	//<![CDATA[
//		window.addEvent('load', function()
//		{
//		  if($('store_url_address')) {
//				$('store_url_address').innerHTML = $('store_url_address').innerHTML.replace('STORE-NAME', '<span id="store_url_address_text">STORE-NAME</span>');
//			}
//
//			$('store_url').addEvent('keyup', function()
//			{
//				var text = 'STORE-NAME';
//				if( this.value != '' )
//				{
//					text = this.value;
//				}
//				$('store_url_address_text').innerHTML = text;
//			});
//			// trigger on store-load
//			if ($('store_url').value.length)
//					$('store_url').fireEvent('keyup');
//		});
//	//]]>
	</script>
<?php endif;?>
<?php
//$this->headScript()
//        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
//        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
//        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
//        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>  
<script type="text/javascript">
//  en4.core.runonce.add(function()
//  {
//    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
//      'postVar' : 'text',
//      'minLength': 1,
//      'selectMode': 'pick',
//      'autocompleteType': 'tag',
//      'className': 'tag-autosuggest',
//      'customChoices' : true,
//      'filterSubset' : true,
//      'multiple' : true,
//      'injectChoice': function(token){
//        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
//        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
//        choice.inputValue = token;
//        this.addChoiceEvents(choice).inject(this.choices);
//        choice.store('autocompleteChoice', token);
//      }
//    });
//  });
</script>

<?php //include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
 <div class='layout_middle sitestore_create_wrapper clr'>
	<?php if ($this->current_count >= $this->quota  && !empty($this->quota)): ?>
	  <div class="tip">
	  	<span><?php echo $this->translate('You have already created the maximum number of stores allowed.'); ?></span> 
	  </div>
	  <br/>
	<?php else: ?>
	  <?php if($this->sitestore_render == 'sitestore_form') { ?>
    <?php if(!empty($this->package)):?>
	<h3><?php echo $this->translate("Open a New Store") ?></h3>
	<p><?php echo $this->translate("Open a store using these quick, easy steps and get going.");?></p>	
    <h4 class="sitestore_create_step"><?php echo $this->translate('2. Configure your store based on the package you have chosen.'); ?></h4>
	  <div class='sitestorestore_layout_right'>      
    	<div class="sitestore_package_store p5">          
        <ul class="sitestore_package_list">
        	<li class="p5">
          	<div class="sitestore_package_list_title">
              <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->translate(ucfirst($this->package->title)); ?></h3>
            </div>           
            <div class="sitestore_package_stat"> 
              <span>
								<b><?php echo $this->translate("Price"). ": "; ?> </b>
								<?php if($this->package->price > 0):echo Engine_Api::_()->sitestore()->getPriceWithCurrency($this->package->price); else: echo $this->translate('FREE'); endif; ?>
             	</span>
             	<span>
                <b><?php echo $this->translate("Billing Cycle"). ": "; ?> </b>
                <?php echo $this->package->getBillingCycle() ?>
              </span>
              <span style="width: auto;">
              	<b><?php echo ($this->package->price > 0 && $this->package->recurrence > 0 && $this->package->recurrence_type != 'forever' ) ? $this->translate("Billing Duration"). ": ": $this->translate("Duration"). ": "; ?> </b>
               	<?php echo $this->package->getPackageQuantity() ; ?>
             	</span>
              <br />
              <span>
              	<b><?php echo $this->translate("Featured"). ": "; ?> </b>
               	<?php
                	if ($this->package->featured == 1)
                		echo $this->translate("Yes");
                	else
                  	echo $this->translate("No");
                ?>
             	</span>
              <span>
              	<b><?php echo $this->translate("Sponsored"). ": "; ?> </b>
               	<?php
                	if ($this->package->sponsored == 1)
                  	echo $this->translate("Yes");
                	else
                  	echo $this->translate("No");
             	 	?>
             	</span>
              <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')): ?>
                <span>
                  <b><?php echo $this->translate("Ads Display"). ": "; ?> </b>
                   <?php
                    if ($this->package->ads == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1))
                      echo $this->translate("Yes");
                    else
                      echo $this->translate("No");
                    ?>
                </span>
              <?php endif;?>             	
             	<span>
              	<b><?php echo $this->translate("Tell a friend"). ": "; ?> </b>
               	<?php
                  if ($this->package->tellafriend == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                ?>
             	</span>
              <span>
                <b><?php echo $this->translate("Print"). ": "; ?> </b>
                 <?php
                  if ($this->package->print == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                  ?>
              </span>
             	<span>
               <b><?php echo $this->translate("Rich Overview"). ": "; ?> </b>
               <?php
                if ($this->package->overview == 1)
                  echo $this->translate("Yes");
                else
                  echo $this->translate("No");
              	?>
             	</span>
             	<span>
              	<b><?php echo $this->translate("Map"). ": "; ?> </b>
               	<?php
                if ($this->package->map == 1)
                  echo $this->translate("Yes");
                else
                  echo $this->translate("No");
              	?>
             	</span>
             	<span>
              	<b><?php echo $this->translate("Insights"). ": "; ?> </b>
               	<?php
                if ($this->package->insights == 1)
                  echo $this->translate("Yes");
                else
                  echo $this->translate("No");
                ?>
             	</span>
              <span>
                  <b><?php echo $this->translate("Contact Details"). ": "; ?> </b>
                   <?php
                    if ($this->package->contact_details == 1)
                      echo $this->translate("Yes");
                    else
                      echo $this->translate("No");
                    ?>
              </span>
              <span>
                <b><?php echo $this->translate("Send an Update"). ": "; ?> </b>
                 <?php
                  if ($this->package->sendupdate == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                  ?>
              </span>
<!--              <span>
                <b><?php echo $this->translate("Save To Foursquare Button"). ": "; ?> </b>
                 <?php
                  if ($this->package->foursquare == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                  ?>
              </span> -->
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) :?>
                <span>
                  <b><?php echo $this->translate("Display Twitter Updates"). ": "; ?> </b>
                  <?php
                    if ($this->package->twitter == 1)
                      echo $this->translate("Yes");
                    else
                      echo $this->translate("No");
                    ?>
                </span>
              <?php endif;?>
							<?php  $module= unserialize($this->package->modules);
               if(!empty($module)):
                    $subModuleStr=$this->package->getSubModulesString();
             		if(!empty($this->package->modules) && !empty ($subModuleStr)):?>
				        <span class="sitestore_package_stat_apps">
				           <b><?php echo $this->translate("Apps available"). ": "; ?> </b>
				           <?php echo $subModuleStr; ?>
				        </span>
				      <?php endif; ?>
              <?php endif; ?> 
						</div>
						<div class="sitestore_list_details">
							<?php echo $this->translate($this->package->description); ?>
		        </div>
          	<div class="sitestore_create_link mtop10 clr">
           		<a href="<?php echo $this->url(array('action'=>'index'), 'sitestore_packages', true) ?>">&laquo; <?php echo $this->translate("Choose a different package"); ?></a>
          	</div>
          </li>
        </ul>
      </div>
    </div>
    <div class="sitestorestore_layout_left">
  <?php endif; ?>
  <?php echo $this->form->render($this); ?>
  <?php if(!empty($this->package)):?>
  	</div>
  <?php endif; ?>
  <?php } else { echo $this->translate($this->sitestore_formrender); } ?>
  <?php endif; ?> 
</div>

<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.profile.fields', 1)): ?>
	<?php
		/* Include the common user-end field switching javascript */
		echo $this->partial('_jsSwitch.tpl', 'fields', array( 
		))
	?>
	<script type="text/javascript">

//		var getProfileType = function(category_id) {
//			var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('profilemaps', 'sitestore')->getMapping()); ?>;
//			for(i = 0; i < mapping.length; i++) {
//				if(mapping[i].category_id == category_id)
//					return mapping[i].profile_type;
//			}
//			return 0;
//		}
//
//		var defaultProfileId = '<?php echo '0_0_1' ?>'+'-wrapper';
//		if($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') { 
//			$(defaultProfileId).setStyle('display', 'none');
//		}
	</script>
<?php endif; ?>
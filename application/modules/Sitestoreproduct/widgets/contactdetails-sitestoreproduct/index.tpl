<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="sitestore_profile_contect_details sr_sitestoreproduct_side_widget">
  <?php if ($this->show_phone && isset($this->options_create['phone']) && $this->options_create['phone'] == 'Phone') : ?>    
    <?php if (empty($this->sitestoreProductOtherInfo->phone) && $this->viewer()->getIdentity() && $this->can_edit) : ?>
      <div class="sitestore_contect_field" onmouseover="displayImage('phone');" onmouseout="hideImage('phone');">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/icons/mobile.png" alt="Phone" />
        <div class="sitestore_contect_det">        
          <input type="text" name="phone" value="<?php if(empty($this->sitestoreProductOtherInfo->phone)): ?><?php echo $this->translate("Contact Number");?><?php else:?><?php echo $this->sitestoreProductOtherInfo->phone ?><?php endif;?>" id="phone" onblur="saveContactDetails('phone')" onclick="onFocus('phone')" />   
          <div id="showPhoneNumber" style="display:none;"></div> 
        </div>  
        <?php if ($this->can_edit) : ?>	
          <div class="edit_icon" id="phoneimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
            <a href="javascript:void(0);" onclick="onFocus('phone')"></a>
          </div>	
        <?php endif; ?>
      </div>   
    <?php elseif (!empty($this->sitestoreProductOtherInfo->phone)) : ?>
      <div class="sitestore_contect_field" onmouseover="displayImage('phone');" onmouseout="hideImage('phone');">
        <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/icons/mobile.png" alt="Phone" />
        <div class="sitestore_contect_det">
          <input type="text" name="phone" value="<?php echo $this->sitestoreProductOtherInfo->phone ?>" id="phone" onblur="saveContactDetails('phone')" style="display:none;" onclick="onFocus('phone')" />  
          <div id="showPhoneNumber" style="display:block;"><?php echo $this->sitestoreProductOtherInfo->phone ?></div>     
        </div>
        <?php if ($this->can_edit) : ?>	
          <div class="edit_icon" id="phoneimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
            <a href="javascript:void(0);" onclick="onFocus('phone')"></a>
          </div>	
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($this->show_email && isset($this->options_create['email']) && $this->options_create['email'] == 'Email') : ?>   
    <?php if (empty($this->sitestoreProductOtherInfo->email) && $this->viewer()->getIdentity() && $this->can_edit) : ?>
      <div class="sitestore_contect_field" onmouseover="displayImage('email');" onmouseout="hideImage('email');">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/invite.png" alt="E-mail" />
        <div class="sitestore_contect_det">
          <input type="text" name="email" value="<?php if(empty($this->sitestoreProductOtherInfo->email)): ?><?php echo $this->translate("Email ID");?><?php else:?><?php echo $this->sitestoreProductOtherInfo->email ?><?php endif;?>" id="email" onblur="saveContactDetails('email')" onclick="onFocus('email')" />
          <div id="showEmailAddress" style="display:none;"></div>
        </div>
        <?php if ($this->can_edit) : ?>
          <div id="showerrormessage" style="display:none;" class="sitestore_contect_error">  </div> 
          <div class="edit_icon" id="emailimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
            <a href="javascript:void(0);" onclick="onFocus('email')"></a>
          </div>  
        <?php endif; ?>
      </div>
    <?php elseif (!empty($this->sitestoreProductOtherInfo->email)) : ?>
      <div class="sitestore_contect_field" onmouseover="displayImage('email');" onmouseout="hideImage('email');">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/invite.png" alt="E-mail" />
        <div class="sitestore_contect_det">
          <input type="text" name="email" value="<?php echo $this->sitestoreProductOtherInfo->email ?>" id="email" onblur="saveContactDetails('email')"  style="display:none;" onclick="onFocus('email')" />
          <div id="showEmailAddress">       
            <a href='mailto:<?php echo $this->sitestoreProductOtherInfo->email ?>'><?php echo $this->translate('Email Me') ?></a>
          </div>
        </div>        
        <?php if ($this->can_edit) : ?>
          <div id="showerrormessage" style="display:none;" class="sitestore_contect_error">  </div> 
          <div class="edit_icon" id="emailimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
            <a href="javascript:void(0);" onclick="onFocus('email')"></a>
          </div>  
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($this->show_website && isset($this->options_create['website']) && $this->options_create['website'] == 'Website') : ?>    
    <?php if (empty($this->sitestoreProductOtherInfo->website) && $this->viewer()->getIdentity() && $this->can_edit) : ?>
      <div class="sitestore_contect_field" onmouseover="displayImage('website');" onmouseout="hideImage('website');">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/icons/web.png" alt="Website" />
        <div class="sitestore_contect_det">          
          <input type="text" name="website" value="<?php if(empty($this->sitestoreProductOtherInfo->website)): ?><?php echo $this->translate("Website");?><?php else:?><?php echo $this->sitestoreProductOtherInfo->website ?><?php endif;?>" id="website" onblur="saveContactDetails('website')" onclick="onFocus('website')" />        
          <div id="showWebsite" style="display:none;"></div>
        </div>
        <?php if ($this->can_edit) : ?>
          <div class="edit_icon" id="websiteimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
            <a href="javascript:void(0);" onclick="onFocus('website')"></a>
          </div>
        <?php endif; ?>
      </div>
    <?php elseif (!empty($this->sitestoreProductOtherInfo->website) ) : ?>
      <div class="sitestore_contect_field" onmouseover="displayImage('website');" onmouseout="hideImage('website');">
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/icons/web.png" alt="Website" />
        <div class="sitestore_contect_det">
          <input type="text" name="website" value="<?php echo $this->sitestoreProductOtherInfo->website ?>" id="website" onblur="saveContactDetails('website')"  style="display:none;" onclick="onFocus('website')" />          
          <div id="showWebsite">   
            <?php if (strstr($this->sitestoreProductOtherInfo->website, 'http://') || strstr($this->sitestoreProductOtherInfo->website, 'https://')): ?>
              <a href='<?php echo $this->sitestoreProductOtherInfo->website ?>' target="_blank" title='<?php echo $this->sitestoreProductOtherInfo->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a>
            <?php else: ?>
              <a href='http://<?php echo $this->sitestoreProductOtherInfo->website ?>' target="_blank" title='<?php echo $this->sitestoreProductOtherInfo->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a>
            <?php endif; ?>
          </div>
        </div>
        <?php if ($this->can_edit) : ?>
          <div class="edit_icon" id="websiteimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
            <a href="javascript:void(0);" onclick="onFocus('website')"></a>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>    
  <?php endif;?>
</div>
<script type="text/javascript">
 
  function displayImage(showimage) {
      
    if(showimage == 'phone') {
      if($('phoneimage'))
        $('phoneimage').style.display = "block";      
    } 
    else if(showimage == 'email') {
      if($('emailimage'))
        $('emailimage').style.display = "block";
    } 
    else if(showimage == 'website') {
      if($('websiteimage'))
        $('websiteimage').style.display = "block";
    }
  }
  
  function hideImage(hideImage) {
    if(hideImage == 'phone') {
      if($('phoneimage'))
        $('phoneimage').style.display = "none";
    } 
    else if(hideImage == 'email') {
      if($('emailimage'))
        $('emailimage').style.display = "none";
    } 
    else if(hideImage == 'website') {
      if($('websiteimage'))
        $('websiteimage').style.display = "none";
    }
  }
  
  function onFocus(focusid){
    if(focusid == 'phone') {  
      if($('showPhoneNumber'))
        $('showPhoneNumber').style.display = "none";
      if($('phone')) {
        if($('phone').value == '<?php echo $this->string()->escapeJavascript($this->translate("Contact Number")) ?>') {
          $('phone').value="";
        }
        $('phone').style.display = "block";
        $('phone').focus();
      }
    }
    else if(focusid == 'email') {
      if($('email')) {
        if($('email').value == '<?php echo $this->string()->escapeJavascript($this->translate("Email ID")) ?>') {
          $('email').value="";
        }
        $('email').style.display = "block";
        $('email').focus();  
      }
      if($('showEmailAddress'))
        $('showEmailAddress').style.display = "none";
    }
    else if(focusid == 'website') {
      if($('website')) {
        if($('website').value == '<?php echo $this->string()->escapeJavascript($this->translate("Website")) ?>') {
          $('website').value="";
        }
        $('website').style.display = "block";
        $('website').focus();  
      }
      if($('showWebsite'))
        $('showWebsite').style.display = "none";      
    }
  }
  
  function validateEmail(email) { 
    if(email != '') {
      var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return filter.test(email);
    } else {
      return true;
    }
  }  

  function saveContactDetails(blurid) {
    if(blurid == 'phone') {     
      if($('phoneimage'))
        $('phoneimage').src = "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/edit.png";
    } 
    else if(blurid == 'email') {     
      if(validateEmail($('email').value)){
        if($('emailimage'))
          $('emailimage').src = "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/edit.png";
      } else {
        if($('showerrormessage')) {
          $('showerrormessage').style.display = "block";
          $('showerrormessage').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Please enter a valid email address.")) ?>';
        }
        return false;
      }      
    } 
    else if(blurid == 'website') {
      if($('websiteimage')) {
        $('websiteimage').src = "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/edit.png";
      }      
    }
    
    if($('phone')) {
      var phone = $('phone').value;        
      if($('phone').value == '') { 
        $('phone').value = '<?php echo $this->string()->escapeJavascript($this->translate("Contact Number")) ?>';
      }
  
      if(phone == '<?php echo $this->string()->escapeJavascript($this->translate("Contact Number")) ?>') {
        phone="";
      }      
    }
    if($('email')) {
      var email = $('email').value;
      if($('email').value == '') { 
        $('email').value = '<?php echo $this->string()->escapeJavascript($this->translate("Email ID")) ?>';        
      }
      
      if(email == '<?php echo $this->string()->escapeJavascript($this->translate("Email ID")) ?>') {
        email="";
      } 
    }   
   
    if($('website')) {
      var website = $('website').value;
      if($('website').value == '') { 
        $('website').value = '<?php echo $this->string()->escapeJavascript($this->translate("Website")) ?>';       
      }
      
      if(website == '<?php echo $this->string()->escapeJavascript($this->translate("Website")) ?>') {
        website="";
      } 
    }    

    en4.core.request.send(new Request.HTML({
      url :'<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'dashboard', 'action' => 'contact'), 'default', true) ?>',
      data : {
        format : 'html',
        phone : phone,
        email : email,
        website : website,
        product_id : '<?php echo $this->sitestoreproduct->product_id ?>',
        isajax: 1
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {  

        if(blurid == 'phone') {
          if($('showPhoneNumber')) {
            $('showPhoneNumber').style.display = "block";
            $('showPhoneNumber').innerHTML = phone;            
          }
          if($('phone') && phone != '')
            $('phone').style.display = "none";   
          if($('phoneimage')) 
            $('phoneimage').style.display = "none";             

        } 
        else if(blurid == 'email') {          
          if($('showEmailAddress')) {    
            $('showEmailAddress').style.display = "block";
            $('showEmailAddress').innerHTML = '<a href="mailto:'+$('email').value+'">'+'<?php echo $this->string()->escapeJavascript($this->translate("Email Me")) ?>'+'</a>';
          }
          if($('email') && email != '')
            $('email').style.display = "none";
          if($('showerrormessage'))
            $('showerrormessage').style.display = "none";
          if($('emailimage'))
            $('emailimage').style.display = "none";
        } else if(blurid == 'website') {
          if($('showWebsite')) { 
            $('showWebsite').style.display = "block";
            var str = website;
            var splitFromHttp = str.split("http://"); 
            var splitFromHttps = str.split("https://");  
            if(typeof splitFromHttp != 'undefined' && splitFromHttp[0] == '') {
              $('showWebsite').innerHTML = '<a href="http://'+splitFromHttp[1]+'" target="_blank" title="'+website+'">'+'<?php echo $this->string()->escapeJavascript($this->translate("Visit Website")) ?>'+'</a>';
            } 
            else if(typeof splitFromHttps != 'undefined' && splitFromHttps[0] == ''){
              $('showWebsite').innerHTML = '<a href="https://'+splitFromHttps[1]+'" target="_blank" title="'+website+'">'+'<?php echo $this->string()->escapeJavascript($this->translate("Visit Website")) ?>'+'</a>';
            }
            else {
              $('showWebsite').innerHTML = '<a href="http://'+str+'" target="_blank" title="'+website+'">'+'<?php echo $this->string()->escapeJavascript($this->translate("Visit Website")) ?>'+'</a>';
            }            
          }
          if($('website') && website != '')
            $('website').style.display = "none";   
          if($('websiteimage'))
            $('websiteimage').style.display = "none";
        }
      }
    }));
  }
</script>  
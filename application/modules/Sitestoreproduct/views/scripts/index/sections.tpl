<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sections.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
if (empty($this->view_product)):
  ?>
<?php if (empty($this->show_sections)): ?>
<div class="sitestore_manage_product">
  <h3 class="mbot5"><?php echo $this->translate("Store Sections") ?></h3>
  <p>
    <?php echo $this->translate("Store Sections help buyers to easily browse your store. Below, you can add and manage various Sections for this store. If you will take the mouse hover at the icon before the section name, then a small hand like cursor will appear and you can drag and drop the sections then to re-order them according to you and these will appear in the same order to users on your Store's Profile.") ?>  
  </p>
  <div class="sitestore_section_manage clr mtop10">
<?php
endif;

include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_showSection.tpl';
endif;
if (COUNT($this->getProductsBySectionObj) && empty($this->show_sections)):
include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_showProduct.tpl';
endif;
?>
<?php if (empty($this->show_sections)): ?>
  </div>
  </div>
<?php endif; ?>
<script type="text/javascript">  
  function deleteSection(sectionId){
    var show_delete_section_message = '';
    show_delete_section_message = '<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this section?")); ?>';
    if(confirm(show_delete_section_message)){
      $('show_tab_content').innerHTML = '<div class="seaocore_content_loader"></div>';
      var request = new Request.HTML({    
        url : '<?php echo $baseurl ?>'+'/sitestoreproduct/index/section-delete',
        method: 'get',
        data : {
          format : 'html',
          sectionId: sectionId,
          store_id: <?php echo $this->store_id; ?>
        },    
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          $('show_tab_content').innerHTML = responseHTML;  
        }
      });  
      request.send();
    }
  }
  
  
  function showSectionProducts(sectionId){
    $('show_section_products').innerHTML = '<div class="seaocore_content_loader"></div>';
    var request = new Request.HTML({    
      url : '<?php echo $baseurl ?>'+'/sitestoreproduct/index/sections',
      method: 'get',
      data : {
        format : 'html',      
        view_product: 1,
        store_id: <?php echo $this->store_id; ?>,
        sections: sectionId
      },    
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('show_section_products').innerHTML = responseHTML;  
      }
    });  
    request.send();
  }
  
  function createSection() 
  {
    var secarea = $('sections');
    var newdiv = document.createElement('div');
    newdiv.id = 'sec_new';
    newdiv.className="sitestore_section_list";
    newdiv.innerHTML ='<div class="sections_name o_hiden"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/drag.png" border="0" class="sitestore_subcat_handle handle handle_cat"><span id="sec_new_span"><input type="text" id="sec_new_input" maxlength="100" onBlur="saveSection(\'new\')" onkeypress="return noenter_sec(\'new\', event)"><span id="loading_image"></span></span></div>';
    secarea.appendChild(newdiv);
    var secinput = $('sec_new_input');
    secinput.focus();
  }
  
  // THIS FUNCTION RUNS THE APPROPRIATE SAVING ACTION
  function saveSection(sectionId)
  {
    var secInput = $('sec_' + sectionId + '_input');
  
    if(secInput.value == "" && sectionId == "new") {
      refreshDiv();
    } else if(secInput.value == "" && sectionId != "new") {
      deleteSection(sectionId);
    } else {
      var is_new_section = sectionId;
      if(sectionId == "new")
        is_new_section = 0;
       $('loading_image').innerHTML=( '<img class="pleft10" src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" />');
      var request = new Request.HTML({    
        url : '<?php echo $baseurl ?>'+'/sitestoreproduct/index/sections',
        method: 'get',
        data : {
          format: 'html',
          store_id: '<?php echo $this->store_id; ?>',
          task:'save',
          sections: '<?php echo $this->sections_id ?>',
          section_title: secInput.value,
          is_new_section: is_new_section
        },    
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          $('show_tab_content').innerHTML = responseHTML;  
        }
      });  
      request.send();
    }
  }

  // THIS FUNCTION PREVENTS THE ENTER KEY FROM SUBMITTING THE FORM
  function noenter_sec(secid, e) 
  { 
    if (window.event) keycode = window.event.keyCode;
    else if (e) keycode = e.which;
    if(keycode == 13) {
      var secinput = $('sec_'+secid+'_input'); 
      secinput.blur();
      return false;
    }
  }

  function editSection(secid,count)
  {
    var secspan = $('sec_'+secid+'_span'); 
    var sectitle = $('sec_'+secid+'_title');
    var replacedsectitle = sectitle.innerHTML.replace(/'/g, "&amp;#039;");
    var parsesectitle = replacedsectitle.replace(/"/g, "&amp;#039;");
    
    secspan.innerHTML = '<input type="text" id="sec_'+secid+'_input" maxlength="100" onBlur="saveSection(\''+secid+'\')" onkeypress="return noenter_sec(\''+secid+'\', event)" >' ;
    secspan.innerHTML = 	secspan.innerHTML + " [" + count + "] <span id ='loading_image'></span>";
    var secinput = $('sec_'+secid+'_input');
    secinput.value=sectitle.innerHTML;
    secinput.focus();			
  }
</script>  

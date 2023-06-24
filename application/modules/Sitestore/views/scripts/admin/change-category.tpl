<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: changecategory.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var subcategory = function(category_id, sub, subcatname,subsubcate)
  {
  	var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitestore_general', true);?>';
    changesubcategory(sub, subsubcate);
    en4.core.request.send(new Request.JSON({      	
      url : url,
      data : {
        format : 'json',
        category_id_temp : category_id
      },
      onSuccess : function(responseJSON) {
        clear('subcategory_id');
        var  subcatss = responseJSON.subcats;
        addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']); 
          $('subcategory_id').value = sub;
        }					
        if(category_id == 0) {
          clear('subcategory_id');
          $('subcategory_id').style.display = 'none';
          $('subcategory_id-label').style.display = 'none';
          $('subsubcategory_id').style.display = 'none';
          $('subsubcategory_id-label').style.display = 'none';
        }
      }
    }));
  };
  function clear(ddName)
  { 
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
    { 
      document.getElementById(ddName).options[ i ]=null; 	      
    } 
  }
  function addOption(selectbox,text,value )
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;			
    if(optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'block';
      $('subcategory_id-label').style.display = 'block';
      selectbox.options.add(optn);
    } 
    else {
      $('subcategory_id').style.display = 'none';
      $('subcategory_id-label').style.display = 'none';
    }
  }	
  var cat = '<?php echo $this->category_id ?>';		
  if(cat != '') {			
    var sub = '<?php echo $this->subcategory_id; ?>';
    var subsubcate = '<?php echo $this->subsubcategory_id; ?>';
    var subcatname = '<?php echo $this->subcategory_name; ?>';
    subcategory(cat, sub, subcatname, subsubcate);
  }

  function addSubOption(selectbox,text,value )
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
      if(optn.text != '' && optn.value != '') {
        $('subsubcategory_id').style.display = 'block';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'block';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'block';
        selectbox.options.add(optn);
      } else {
        $('subsubcategory_id').style.display = 'none';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'none';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'none';
        selectbox.options.add(optn);
      }
    }
    function changesubcategory(subcatid,subsubcate) {
      if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
      var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitestore_general', true);?>';        
      var request = new Request.JSON({
        url : url,
        data : {
          format : 'json',
          subcategory_id_temp : subcatid
        },
        onSuccess : function(responseJSON) {
          if($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'block';
          }
          clear('subsubcategory_id');
          var  subsubcatss = responseJSON.subsubcats;
          addSubOption($('subsubcategory_id')," ", '0');
          for (i=0; i< subsubcatss.length; i++) {
            addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
              $('subsubcategory_id').value = subsubcate;
          }
        }    
      });
      request.send();
    }
</script>
<div class="sitestore_admin_popup">
  <div class="sitestore_change_cat_popup">
    <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  </div>
</div>
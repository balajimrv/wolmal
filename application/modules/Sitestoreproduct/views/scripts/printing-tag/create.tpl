<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div id="store_back_to_tax" class="paginator_previous">
  <a href="javascript:void(0);" onclick="manage_store_dashboard(62, 'manage', 'product');" class="buttonlink icon_previous mbot10 mright5"><?php echo $this->translate('Back to Manage Products Page'); ?></a>
  <a href="javascript:void(0);" onclick="manage_store_dashboard(62, 'manage', 'printing-tag');" class="buttonlink icon_previous mbot10 mright5"><?php echo $this->translate('Back to Manage Printing Tags Page'); ?></a>
 
</div>

<script type="text/javascript">
//  function savePrintingTag() {
//    var fontSettings = [];
//    
//    fontSettings['title_size'] = $('title_container').style.fontSize;
//    fontSettings['title_color'] = $('title_container').style.color;
//    fontSettings['title_fontFamily'] = $('title_container').style.fontFamily;
//    
//    $('font_settings').value = fontSettings;
//    
////    $('create_printing_tag').submit();
//  }
  
  function editStyle(element_type){
    var url = en4.core.baseUrl + 'sitestoreproduct/printing-tag/font-family/element_type/'+ element_type;    
    Smoothbox.open(url);
  }
  en4.core.runonce.add(function() {
    var title_new_position="19px,30px",category_new_position="81px,30px",size_new_position="110px,30px",price_new_position="105px,225px",qr_new_position="16px,215px";
   
    new Drag.Move($('title_container'), {
      container: $('detail_container'),
      //          onEnter: function(element, droppable){},      
      //          onLeave: function(element, droppable){},      
      onDrop: function(element, droppable){
        var title_position_top = $('title_container').style.top; 
        var title_position_left = $('title_container').style.left;
        title_new_position = title_position_top+","+title_position_left;
      }
    });
    
    new Drag.Move($('category_container'), {
      container: $('detail_container'),
      //          onEnter: function(element, droppable){},      
      //          onLeave: function(element, droppable){},      
      onDrop: function(element, droppable){
        var category_position_top = $('category_container').style.top;
        var category_position_left = $('category_container').style.left;
        category_new_position = category_position_top+","+category_position_left;
       
      }
    });
    
    new Drag.Move($('price_container'), {
      container: $('detail_container'),
      //          onEnter: function(element, droppable){},      
      //          onLeave: function(element, droppable){},      
      onDrop: function(element, droppable){
        var price_position_top = $('price_container').style.top;
        var price_position_left = $('price_container').style.left;
        price_new_position = price_position_top+","+price_position_left;
      }
    });
    
    new Drag.Move($('qr_container'), {
      container: $('detail_container'),
      //          onEnter: function(element, droppable){},      
      //          onLeave: function(element, droppable){},      
      onDrop: function(element, droppable){
        var qr_position_top = $('qr_container').style.top;
        var qr_position_left = $('qr_container').style.left;
        qr_new_position = qr_position_top+","+qr_position_left;
        //        alert(qr_position.left);alert(qr_position.right);alert(qr_position.top);alert(qr_position.bottom);
      }
    });
    
    checkDetail();
    $('create_printing_tag').addEvent('submit', function(e) {

    
    // $('font_settings').value = fontSettings;
 
      e.stop();

      var font_settings = {
        title: { 
          family: $('title_container').style.fontFamily,
          color: $('title_container').style.color,
          size: $('title_container').style.fontSize
        },

        category: { 
          family: $('category_container').style.fontFamily,
          color: $('category_container').style.color,
          size: $('category_container').style.fontSize
        },

        price: { 
          family: $('price_container').style.fontFamily,
          color: $('price_container').style.color,
          size: $('price_container').style.fontSize
        },

        qr: { 
          size: $('qr_container').style.height
        }
      };

      font_settings = JSON.encode(font_settings);

      $('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/spinner.gif" /></center>';
      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'sitestoreproduct/printing-tag/savetag',
        method: 'POST',
        data: {
          format: 'json',
          font_settings: font_settings,
          tag_details: $('create_printing_tag').toQueryString(),
          coordinates: title_new_position+"|"+category_new_position+"|"+price_new_position+"|"+size_new_position+"|"+qr_new_position, 
          store_id: <?php echo $this->store_id; ?>          
        },
        onSuccess: function(responseJSON) {
          $('spiner-image').innerHTML = '';
          if ($('create_printing_tag').getElement('.form-errors'))
            $('create_printing_tag').getElement('.form-errors').destroy();
          // IF THERE ARE NO ERROR FOUND THEN REDIRECT TO MANAGE SHIPPING METHODS PAGE.
          if (responseJSON.errorFlag === '0') {
            new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);
            manage_store_dashboard(62, "manage/notice/1", "printing-tag");
          } else {
            var addErrors = new Element('ul', {
              'class': 'form-errors',
              'html': responseJSON.errorMsgStr
            });
            addErrors.inject($('create_printing_tag').getElement('.form-elements'), 'before');
            new Fx.Scroll(window).start(0, $('create_printing_tag').getElement('.form-errors').getCoordinates().top);
          }
        }
      }));
    });
    showDependency();
    showRegions(<?php echo $this->store_id ?>, 0, null, 0, null);
  });
</script>
<?php echo $this->form->render($this); ?>
<script type="text/javascript">
  function checkDetail(){
    if($('details-title').checked){
      $('title_container').style.display = 'block';
    }
    else{
      $('title_container').style.display = 'none';
    }
    if($('details-category').checked)
      $('category_container').style.display = 'block';
    else
      $('category_container').style.display = 'none';
    if($('details-price').checked)
      $('price_container').style.display = 'block';
    else
      $('price_container').style.display = 'none';
    if($('details-qr').checked)
      $('qr_container').style.display = 'block';
    else
      $('qr_container').style.display = 'none';
  }
</script>

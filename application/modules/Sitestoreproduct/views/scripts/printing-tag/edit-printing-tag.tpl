<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-printing-tag.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id="store_back_to_tax" class="paginator_previous">
  <a href="javascript:void(0);" onclick="manage_store_dashboard(62, 'manage', 'product');" class="buttonlink icon_previous mbot10 mright5"><?php echo $this->translate('Back to Manage Products Page'); ?></a>
  <a href="javascript:void(0);" onclick="manage_store_dashboard(62, 'manage', 'printing-tag');" class="buttonlink icon_previous mbot10 mright5"><?php echo $this->translate('Back to Manage Printing Tags Page'); ?></a>
  
</div>

<?php
$fontSettingsArray = Zend_Json::decode($this->printingTagItem->font_settings);
// WORK TO SHOW THE POSITION OF DETAILS IN CONFIGURATION PANEL USING COORDINATES
$coordinateString = $this->printingTagItem->coordinates;
$coordinates = @explode("|", $coordinateString);

$titleCoodinatesArray = $coordinates[0];
if ($titleCoodinatesArray != null) {
  $title_Array = @explode(",", $titleCoodinatesArray);
  $title_top = $title_Array[0];
  $title_left = $title_Array[1];
}

$categoryCoodinatesArray = $coordinates[1];
if ($categoryCoodinatesArray != null) {
  $category_Array = @explode(",", $categoryCoodinatesArray);
  $category_top = $category_Array[0];
  $category_left = $category_Array[1];
}

$priceCoodinatesArray = $coordinates[2];
if ($priceCoodinatesArray != null) {
  $price_Array = @explode(",", $priceCoodinatesArray);
  $price_top = $price_Array[0];
  $price_left = $price_Array[1];
}

$sizeCoodinatesArray = $coordinates[3];
if ($sizeCoodinatesArray != null) {
  $size_Array = @explode(",", $sizeCoodinatesArray);
  $size_top = $size_Array[0];
  $size_left = $size_Array[1];
}

$qrCoodinatesArray = $coordinates[4];
if ($qrCoodinatesArray != null) {
  $qr_Array = @explode(",", $qrCoodinatesArray);
  $qr_top = $qr_Array[0];
  $qr_left = $qr_Array[1];
}
?>

<script type="text/javascript">
  
  function editStyle(element_type){
    var url = en4.core.baseUrl + 'sitestoreproduct/printing-tag/font-family/element_type/'+ element_type;    
    Smoothbox.open(url);
  }
  
  en4.core.runonce.add(function() {
    
    $('title_container').setStyle('color', '<?php echo $fontSettingsArray['title']['color']; ?>');
    $('title_container').setStyle('fontSize', '<?php echo $fontSettingsArray['title']['size']; ?>');
    $('title_container').setStyle('fontFamily', '<?php echo $fontSettingsArray['title']['family']; ?>');
    $('title_container').style.top = '<?php echo $title_top; ?>';
    $('title_container').style.left = '<?php echo $title_left; ?>';
    
    $('category_container').setStyle('color', '<?php echo $fontSettingsArray['category']['color']; ?>');
    $('category_container').setStyle('fontSize', '<?php echo $fontSettingsArray['category']['size']; ?>');
    $('category_container').setStyle('fontFamily', '<?php echo $fontSettingsArray['category']['family']; ?>');
    $('category_container').style.top = '<?php echo $category_top; ?>';
    $('category_container').style.left = '<?php echo $category_left; ?>';
    
    $('price_container').setStyle('color', '<?php echo $fontSettingsArray['price']['color']; ?>');
    $('price_container').setStyle('fontSize', '<?php echo $fontSettingsArray['price']['size']; ?>');
    $('price_container').setStyle('fontFamily', '<?php echo $fontSettingsArray['price']['family']; ?>');
    $('price_container').style.top = '<?php echo $price_top; ?>';
    $('price_container').style.left = '<?php echo $price_left; ?>';
    
    $('qr_container').setStyle('height', '<?php echo $fontSettingsArray['qr']['size']; ?>');
    $('qr_container').style.top = '<?php echo $qr_top; ?>';
    $('qr_container').style.left = '<?php echo $qr_left; ?>';

    var title_new_position=null,category_new_position=null,size_new_position=null,price_new_position=null,qr_new_position=null;
    var title_position_top,title_position_left,category_position_top,category_position_left,size_position_top,size_position_left,price_position_top,price_position_left,qr_position_top,qr_position_left;
   
<?php if ($titleCoodinatesArray != 'null') : ?>
      title_position_top = '<?php echo $title_top; ?>';
      title_position_left = '<?php echo $title_left; ?>';
      title_new_position = title_position_top+","+title_position_left;
<?php endif; ?>
<?php if ($categoryCoodinatesArray != 'null') : ?>
      category_position_top = '<?php echo $category_top; ?>';
      category_position_left = '<?php echo $category_left; ?>';
      category_new_position = category_position_top+","+category_position_left;
<?php endif; ?>
<?php if ($sizeCoodinatesArray != 'null') : ?>
      size_position_top = '<?php echo $size_top; ?>';
      size_position_left = '<?php echo $size_left; ?>';
      size_new_position = size_position_top+","+size_position_left;
<?php endif; ?>
<?php if ($priceCoodinatesArray != 'null') : ?>
      price_position_top = '<?php echo $price_top; ?>';
      price_position_left = '<?php echo $price_left; ?>';
      price_new_position = price_position_top+","+price_position_left;
<?php endif; ?>
<?php if ($qrCoodinatesArray != 'null') : ?>
      qr_position_top = '<?php echo $qr_top; ?>';
      qr_position_left = '<?php echo $qr_left; ?>';
      qr_new_position = qr_position_top+","+qr_position_left;
<?php endif; ?>
    new Drag.Move($('title_container'), {
      container: $('detail_container'),
      //          onEnter: function(element, droppable){},      
      //          onLeave: function(element, droppable){},      
      onDrop: function(element, droppable){
        title_position_top = $('title_container').style.top;
        title_position_left = $('title_container').style.left;
        title_new_position = title_position_top+","+title_position_left;
      }
    });
    new Drag.Move($('category_container'), {
      container: $('detail_container'),
      //          onEnter: function(element, droppable){},      
      //          onLeave: function(element, droppable){},      
      onDrop: function(element, droppable){
        category_position_top = $('category_container').style.top;
        category_position_left = $('category_container').style.left;
        category_new_position = category_position_top+","+category_position_left;
      }
    });
    
    new Drag.Move($('price_container'), {
      container: $('detail_container'),
      //          onEnter: function(element, droppable){},      
      //          onLeave: function(element, droppable){},      
      onDrop: function(element, droppable){
        price_position_top = $('price_container').style.top;
        price_position_left = $('price_container').style.left;
        price_new_position = price_position_top+","+price_position_left;
      }
    });
    
    new Drag.Move($('qr_container'), {
      container: $('detail_container'),
      //          onEnter: function(element, droppable){},      
      //          onLeave: function(element, droppable){},      
      onDrop: function(element, droppable){
        qr_position_top = $('qr_container').style.top;
        qr_position_left = $('qr_container').style.left;
        qr_new_position = qr_position_top+","+qr_position_left;
      }
    });
    
    checkDetail();
    $('create_printing_tag').addEvent('submit', function(e) {
      
    
//    $('font_settings').value = fontSettings;
    
    
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
          store_id: <?php echo $this->store_id; ?>,
          tag_id: <?php echo $this->tag_id; ?>
        },
        onSuccess: function(responseJSON) {
          $('spiner-image').innerHTML = '';
          if ($('create_printing_tag').getElement('.form-errors'))
            $('create_printing_tag').getElement('.form-errors').destroy();
          // IF THERE ARE NO ERROR FOUND THEN REDIRECT TO MANAGE SHIPPING METHODS PAGE.
          if (responseJSON.errorFlag === '0') {
            new Fx.Scroll(window).start(0, $('global_wrapper').getCoordinates().top);
            manage_store_dashboard(62, "manage/notice/2", "printing-tag");
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

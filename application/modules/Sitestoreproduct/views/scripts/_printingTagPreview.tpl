<div class="form-wrapper" id="dummy_sitestoreproduct_title-wrapper">
  <div class="form-label" id="dummy_sitestoreproduct_title-label">
    <label class="optional" for="dummy_sitestoreproduct_title"><?php echo $this->translate("Configuration Panel"); ?></label>
  </div>
  <div class="form-element" id="dummy_sitestoreproduct_title-element">
    <p class="description"><?php echo $this->translate("Drag and drop your selected Product Fields to generate a unique printing tag in the configuration panel below:"); ?></p>

    <div id="detail_container" style="width:100px; height: 100px; background: #DDD; position: relative; border:1px solid #000;">
      <div id="title_container" style="display:none; top:19px;left:30px; font-size: 18px; font-weight: bold; color: #666; font-family: Arial">
        <?php echo $this->translate("Product Title"); ?>
      </div>
      <div id="category_container" style="display:none; top:81px;left:30px; font-size: 12px; font-weight: bold; font-style: italic; color: #666; font-family: Arial">
        <?php echo $this->translate("Product Category"); ?>
      </div>
<!--      <div id="size_container" style="display:none; top:110px;left:30px; font-size: 12px; font-weight: bold; color: #666; font-family: Arial">
        <?php //echo $this->translate("Product Size"); ?>
      </div>-->
      <div id="price_container" style="display:none; top:105px;left:225px; font-size: 12px; font-weight: bold; color: #666; font-family: Arial">
        <?php echo $this->translate("Product Price"); ?>
      </div>
      <div id="qr_container" style="display:none; top:16px; left:215px; height: 72px;">
        <img style="height:100%" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/qr.png"/> 
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    previewSize();
  });

  function previewSize() {
    if(!$('width').value || $('width').value < 1){
      $('div_width_message').style.display = 'block';
      $('div_width_message').addClass('seaocore_txt_red');
      $('detail_container').style.width = 37.795 + "px";
    }else if($('width').value && $('width').value > 16){
      $('div_width_message').style.display = 'block';
      $('div_width_message').addClass('seaocore_txt_red');
      $('detail_container').style.width = 16 * 37.795 + "px";
    }else {
      $('div_width_message').style.display = 'none';
      $('div_width_message').removeClass('seaocore_txt_red');
      $('detail_container').style.width = $('width').value * 37.795 + "px";
    }
    
    if(!$('height').value || $('height').value < 1){
      $('div_height_message').style.display = 'block';
      $('div_height_message').addClass('seaocore_txt_red');
      $('detail_container').style.height = 37.795 + "px";
    }else if($('height').value && $('height').value > 25){
      $('div_height_message').style.display = 'block';
      $('div_height_message').addClass('seaocore_txt_red');
      $('detail_container').style.height = 25 * 37.795 + "px";
    }else {
      $('div_height_message').style.display = 'none';
      $('div_height_message').removeClass('seaocore_txt_red');
      $('detail_container').style.height = $('height').value * 37.795 + "px";
    }
      
//    if ($('width').value && IsNumeric($('width').value))
//      $('detail_container').style.width = $('width').value * 37.795 + "px";
//
//    if ($('height').value && IsNumeric($('height').value))
//      $('detail_container').style.height = $('height').value * 37.795 + "px";
//    $('qr_container').style.height = $('qr_container').style.width = $('height').value*37.795*0.5+"px";
  }

  function IsNumeric(input)
  {
    return (input - 0) == input && ('' + input).replace(/^\s+|\s+$/g, "").length > 0;
  }
</script>

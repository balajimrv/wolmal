
<?php
$font_size_list = array("2px" => "2px", "4px" => "4px", "6px" => "6px", "7px" => "7px", "8px" => "8px", "9px" => "9px", "10px" => "10px", "11px" => "11px", "12px" => "12px", "14px" => "14px", "16px" => "16px", "18px" => "18px", "20px" => "20px", "22px" => "22px", "24px" => "24px", "26px" => "26px", "28px" => "28px", "36px" => "36px", "48px" => "48px", "72px" => "72px");

$font_fmily_list = array("Arial" => "Arial", "Comic Sans MS" => "Comic Sans MS", "Courier New" => "Courier New", "Georgia" => "Georgia", "Tahoma" => "Tahoma", "Times New Roman" => "Times New Roman", "Trebuchet MS" => "Trebuchet MS", "Verdana" => "Verdana", "Lucida Sans Unicode" => "Lucida Sans Unicode");

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<script type="text/javascript">
  function hexcolorTonumbercolor(hexcolor) {
    var hexcolorAlphabets = "0123456789ABCDEF";
    var valueNumber = new Array(3);
    var j = 0;
    if (hexcolor.charAt(0) == "#")
      hexcolor = hexcolor.slice(1);
    hexcolor = hexcolor.toUpperCase();
    for (var i = 0; i < 6; i += 2) {
      valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i + 1));
      j++;
    }
    return(valueNumber);
  }


  window.addEvent('domready', function() {


    if (!window.parent.$('details-<?php echo $this->elementType; ?>').checked) {
      $('form_error_description').innerHTML = 'Changes will not reflect on preview because element disabled.<br />';
    }

<?php if (!empty($this->elementType) && $this->elementType != 'qr'): ?>
      $('font').value = window.parent.$('<?php echo $this->elementType; ?>_container').getStyle('fontFamily');
      $('print_tag_font_color').value = window.parent.$('<?php echo $this->elementType; ?>_container').getStyle('color');
      $('fontsize').value = window.parent.$('<?php echo $this->elementType; ?>_container').getStyle('fontSize');

      var r = new MooRainbow('myRainbow1', {
        id: 'myDemo1',
        'startColor': hexcolorTonumbercolor($('print_tag_font_color').value),
        'onChange': function(color) {
          $('print_tag_font_color').value = color.hex;
        }
      });

      $('print_tag_font_color').value = $('print_tag_font_color').value;

      r.layout.inject($('tempRainbow'), 'after');
      r.layout.setStyles({'position': 'relative', 'clear': 'both'});
      r.show();
<?php else: ?>
      $('fontsize').value = window.parent.$('<?php echo $this->elementType; ?>_container').getStyle('height');
      $('fontsize').value = $('fontsize').value.replace('px', '');
<?php endif; ?>
  });

</script>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>
<div class="global_form global_form_popup printing_tag_edit_font_popup" id="font_form" style="width: <?php if ($this->elementType != 'qr'): echo '480px';
else: echo '370px';
endif; ?>;">
  <div>
    <div>
      <h3><?php echo $this->translate('Edit Tag Style'); ?></h3>
      <p><span id='form_error_description' class="seaocore_txt_red"></span><?php echo $this->translate('Manage the Style of the product field for your printing tag.'); ?></p>


      <div class="form-elements">

<?php if (!empty($this->elementType) && $this->elementType != 'qr'): ?>
          <div id="font-wrapper" class="form-wrapper">
            <div id="font-label" class="form-label">
              <label for="font"><?php echo $this->translate('Font'); ?></label>
            </div>
            <div id="font-element" class="form-element">
              <select  name="font_family"  id="font" style="width:100px;" title="Font Type">
                <?php foreach ($font_fmily_list as $key => $value): ?>
                  <option value="<?php echo $key; ?>" <?php if ($this->chapter_fontFamily == $key) echo $this->translate("selected"); ?> title="<?php echo $this->translate($value); ?>" style="font-family: <?php echo $key; ?>;"  ><?php echo $this->translate($value); ?></option>
  <?php endforeach; ?>
              </select> 
            </div>
          </div>        


          <div id="fontsize-wrapper" class="form-wrapper">
            <div id="fontsize-label" class="form-label">
              <label for="fontsize"><?php echo $this->translate('Size'); ?></label>
            </div>
            <div id="fontsize-element" class="form-element">
              <select  name="fontsize" id="fontsize" title="Font Size">
                <?php foreach ($font_size_list as $key => $value): ?>
                  <option value="<?php echo $key; ?>" <?php if ($this->chapter_fontSize == $key) echo $this->translate("selected"); ?> title="<?php echo $this->translate($value); ?>"  ><?php echo $this->translate($value); ?></option>
  <?php endforeach; ?>
              </select>
            </div>
          </div>



          <div id="print_tag_font_color-wrapper" class="form-wrapper">          
            <div id="print_tag_font_color-label" class="form-label">
              <label for="print_tag_font_color" class="optional">Color</label>
            </div>
            <div id="print_tag_font_color-element" class="form-element">
              <input name="myRainbow1" id="myRainbow1" style="display: none" />
              <input name="print_tag_font_color" id="print_tag_font_color" type="text" value=""/>
            </div>
          </div>
          <div  class="form-wrapper">    
            <div  class="form-label">

            </div>
            <div  class="form-element"><span id="tempRainbow"></span></div>
          </div>
<?php else: ?>
          <div id="fontsize-wrapper" class="form-wrapper">
            <div id="fontsize-label" class="form-label">
              <label for="fontsize"><?php echo $this->translate('Size'); ?></label>
            </div>
            <div id="fontsize-element" class="form-element">
              <p class='description'><?php echo $this->translate("Please insert the size of the QR code in number."); ?></p>
              <input type='Text' name='fontsize' id='fontsize' style='width: 50px;' />&nbsp;px
            </div>
          </div>
<?php endif; ?>

        <div class="form-wrapper">
          <button onclick="setValue()"><?php echo $this->translate('Save'); ?></button>

<?php echo $this->translate('or'); ?>

          <a href="javascript:void(0)" onClick="parent.Smoothbox.close();"><?php echo $this->translate('cancel'); ?></a>
        </div>

      </div>
    </div>
  </div>
</div>

<script type="text/javascript" >
  function setValue() {
<?php if (!empty($this->elementType) && $this->elementType != 'qr'): ?>
      window.parent.$('<?php echo $this->elementType; ?>_container').setStyle('color', $('print_tag_font_color').value);
      window.parent.$('<?php echo $this->elementType; ?>_container').setStyle('fontFamily', $('font').value);
      window.parent.$('<?php echo $this->elementType; ?>_container').setStyle('fontSize', $('fontsize').value);
<?php else: ?>
      window.parent.$('<?php echo $this->elementType; ?>_container').setStyle('height', $('fontsize').value + 'px');
<?php endif; ?>

    parent.Smoothbox.close();
  }
</script>

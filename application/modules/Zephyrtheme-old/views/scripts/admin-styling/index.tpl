<?php
$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/jquery.min.js')  
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/colorpicker.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/codemirror.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/scripts/codemirror_css.js');
$this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/styles/colorpicker.css')
    ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Zephyrtheme/externals/styles/codemirror.css'); 
?>

<?php echo $this->partial('_header.tpl', 'zephyrtheme', '') ?>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<div class="clear">
  <div class="settings">
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function ($) {
  $('#color_scheme-wrapper > .form-element > ul > li > input').each(function(){
	if($(this).is(':checked')){ $(this).parent().addClass('selected'); }
	$(this).click(function () {
	  $('#color_scheme-wrapper > .form-element > ul > li > input:not(:checked)').parent().removeClass('selected');
	  $('#color_scheme-wrapper > .form-element > ul > li > input:checked').parent().addClass('selected');
	});
  });
	
  CodeMirror.fromTextArea(document.getElementById('custom_css'), {
	 mode: "css",
	 lineNumbers: true,
  }).setSize('600px', '300px');

  var $pickerInput;
  $('#colors_group input[type="text"]').ColorPicker({ 
      onSubmit: function(hsb, hex, rgb, el) {
          $(el).val('#' + hex);
          $(el).ColorPickerHide();
      },
      onBeforeShow: function () {
          $(this).ColorPickerSetColor(this.value);
          $pickerInput = $(this);
      },
      onHide: function(picker) {
          $pickerInput.val('#' + $(picker).find('.colorpicker_hex input').val());
      }
  })
  .bind('keyup', function(){
      $(this).ColorPickerSetColor(this.value);
  });
  
  // Color Schemes - change the color codes on form inputs
  $('#color_scheme-wrapper input[type="radio"]').change(function() {
    if (this.value === 'color_one') {
	  $('#body_bgcolor').val('#E8EBF2');
	  $('#font_color').val('#333333');
	  $('#font_colorlight').val('#777777');
	  $('#link_color').val('#328dc6');
	  $('#link_colorhover').val('#555555');
	  $('#input_bgcolor').val('#f7f8fa');
	  $('#content_bgcolor').val('#FFFFFF');
	  $('#content_bordercolor').val('#D2DBE8');
	  $('#button_bgcolor').val('#328dc6');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#333333');
	  $('#comment_bgcolor').val('#f7f8fa');
	  $('#header_bgcolor').val('#FFFFFF');
	  $('#header_bordercolor').val('#29ABE2');
	  $('#headermenu_bgcolor').val('#F7F7F7');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#555555');
	  $('#headermenu_colorhover').val('#111111');
	  $('#headermenu_bghover').val('#F2F2F2');
	  $('#header_searchcolor').val('#BBBBBB');
	  $('#footer_bgcolor').val('#2E2E2E');
	  $('#footer_color').val('#888888');
	  $('#footer_linkcolor').val('#328dc6');
	  $('#footer_bordercolor').val('transparent');
	  $('#header_border').val('0px');
	  $('#footer_border').val('0px');
	  $('#header_icons').val('1');
    } else if (this.value === 'color_two') {
	  $('#body_bgcolor').val('#454545');
	  $('#font_color').val('#333333');
	  $('#font_colorlight').val('#777777');
	  $('#link_color').val('#328dc6');
	  $('#link_colorhover').val('#555555');
	  $('#input_bgcolor').val('#f7f8fa');
	  $('#content_bgcolor').val('#FFFFFF');
	  $('#content_bordercolor').val('#353535');
	  $('#button_bgcolor').val('#328dc6');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#999999');
	  $('#comment_bgcolor').val('#f7f8fa');
	  $('#header_bgcolor').val('#353535');
	  $('#header_bordercolor').val('#151515');
	  $('#headermenu_bgcolor').val('#252525');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#DDDDDD');
	  $('#headermenu_colorhover').val('#FFFFFF');
	  $('#headermenu_bghover').val('#252525');
	  $('#header_searchcolor').val('#AAAAAA');
	  $('#footer_bgcolor').val('#333333');
	  $('#footer_color').val('#999999');
	  $('#footer_linkcolor').val('#328dc6');
	  $('#footer_bordercolor').val('#252525');
	  $('#header_border').val('1px');
	  $('#footer_border').val('3px');
	  $('#header_icons').val('2');
    } else if (this.value === 'color_three') {
	  $('#body_bgcolor').val('#252525');
	  $('#font_color').val('#DEDEDE');
	  $('#font_colorlight').val('#EFEFEF');
	  $('#link_color').val('#ffa500');
	  $('#link_colorhover').val('#FFFFFF');
	  $('#input_bgcolor').val('#232323');
	  $('#content_bgcolor').val('#121212');
	  $('#content_bordercolor').val('#000000');
	  $('#button_bgcolor').val('#ffa500');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#EEEEEE');
	  $('#comment_bgcolor').val('#171717');
	  $('#header_bgcolor').val('#151515');
	  $('#header_bordercolor').val('#111111');
	  $('#headermenu_bgcolor').val('#111111');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#DDDDDD');
	  $('#headermenu_colorhover').val('#FFFFFF');
	  $('#headermenu_bghover').val('#252525');
	  $('#header_searchcolor').val('#AAAAAA');
	  $('#footer_bgcolor').val('#101010');
	  $('#footer_color').val('#999999');
	  $('#footer_linkcolor').val('#ffa500');
	  $('#footer_bordercolor').val('#ffa500');
	  $('#header_border').val('1px');
	  $('#footer_border').val('3px');
	  $('#header_icons').val('2');
	} else if (this.value === 'color_four') {
	  $('#body_bgcolor').val('#e5f3fd');
	  $('#font_color').val('#333333');
	  $('#font_colorlight').val('#777777');
	  $('#link_color').val('#FF4900');
	  $('#link_colorhover').val('#333333');
	  $('#input_bgcolor').val('#f7f8fa');
	  $('#content_bgcolor').val('#FFFFFF');
	  $('#content_bordercolor').val('#cde7fc');
	  $('#button_bgcolor').val('#FF4900');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#333333');
	  $('#comment_bgcolor').val('#f5fafe');
	  $('#header_bgcolor').val('#0C70BE');
	  $('#header_bordercolor').val('#075693');
	  $('#headermenu_bgcolor').val('#0e7ed6');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#b8defa');
	  $('#headermenu_colorhover').val('#e8f4fd');
	  $('#headermenu_bghover').val('#0a62a6');
	  $('#header_searchcolor').val('#b8defa');
	  $('#footer_bgcolor').val('#222222');
	  $('#footer_color').val('#888888');
	  $('#footer_linkcolor').val('#FF4900');
	  $('#footer_bordercolor').val('transparent');
	  $('#header_border').val('0px');
	  $('#footer_border').val('0px');
	  $('#header_icons').val('2');
	} else if (this.value === 'color_five') {
	  $('#body_bgcolor').val('#ffecdb');
	  $('#font_color').val('#333333');
	  $('#font_colorlight').val('#777777');
	  $('#link_color').val('#3E59B3');
	  $('#link_colorhover').val('#555555');
	  $('#input_bgcolor').val('#f7f8fa');
	  $('#content_bgcolor').val('#FFFFFF');
	  $('#content_bordercolor').val('#ffdfc2');
	  $('#button_bgcolor').val('#3E59B3');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#333333');
	  $('#comment_bgcolor').val('#fff9f5');
	  $('#header_bgcolor').val('#FD8411');
	  $('#header_bordercolor').val('#CC6400');
	  $('#headermenu_bgcolor').val('#FF9E42');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#ffecdb');
	  $('#headermenu_colorhover').val('#fff9f5');
	  $('#headermenu_bghover').val('#f27702');
	  $('#header_searchcolor').val('#ffecdb');
	  $('#footer_bgcolor').val('#2E2E2E');
	  $('#footer_color').val('#888888');
	  $('#footer_linkcolor').val('#FD8411');
	  $('#footer_bordercolor').val('transparent');
	  $('#header_border').val('0px');
	  $('#footer_border').val('0px');
	  $('#header_icons').val('2');
	} else if (this.value === 'color_six') {
	  $('#body_bgcolor').val('#fdd0d6');
	  $('#font_color').val('#333333');
	  $('#font_colorlight').val('#777777');
	  $('#link_color').val('#0BA381');
	  $('#link_colorhover').val('#555555');
	  $('#input_bgcolor').val('#f7f8fa');
	  $('#content_bgcolor').val('#FFFFFF');
	  $('#content_bordercolor').val('#fcb8c1');
	  $('#button_bgcolor').val('#0BA381');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#333333');
	  $('#comment_bgcolor').val('#fee8eb');
	  $('#header_bgcolor').val('#ef1033');
	  $('#header_bordercolor').val('#d70e2e');
	  $('#headermenu_bgcolor').val('#f12847');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#fee8eb');
	  $('#headermenu_colorhover').val('#FFFFFF');
	  $('#headermenu_bghover').val('#d70e2e');
	  $('#header_searchcolor').val('#fee8eb');
	  $('#footer_bgcolor').val('#2E2E2E');
	  $('#footer_color').val('#888888');
	  $('#footer_linkcolor').val('#f12847');
	  $('#footer_bordercolor').val('transparent');
	  $('#header_border').val('0px');
	  $('#footer_border').val('0px');
	  $('#header_icons').val('2');
	} else if (this.value === 'color_seven') {
	  $('#body_bgcolor').val('#ffdbd5');
	  $('#font_color').val('#333333');
	  $('#font_colorlight').val('#777777');
	  $('#link_color').val('#0E6C90');
	  $('#link_colorhover').val('#555555');
	  $('#input_bgcolor').val('#f7f8fa');
	  $('#content_bgcolor').val('#FFFFFF');
	  $('#content_bordercolor').val('#ffc6bb');
	  $('#button_bgcolor').val('#0E6C90');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#333333');
	  $('#comment_bgcolor').val('#fff0ee');
	  $('#header_bgcolor').val('#BA2000');
	  $('#header_bordercolor').val('#6C1300');
	  $('#headermenu_bgcolor').val('#971A00');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#ffdbd4');
	  $('#headermenu_colorhover').val('#fff0ee');
	  $('#headermenu_bghover').val('#a11c00');
	  $('#header_searchcolor').val('#ffdbd4');
	  $('#footer_bgcolor').val('#2E2E2E');
	  $('#footer_color').val('#888888');
	  $('#footer_linkcolor').val('#FFFFFF');
	  $('#footer_bordercolor').val('transparent');
	  $('#header_border').val('0px');
	  $('#footer_border').val('0px');
	  $('#header_icons').val('2');
	} else if (this.value === 'color_eight') {
	  $('#body_bgcolor').val('#fcf0d4');
	  $('#font_color').val('#333333');
	  $('#font_colorlight').val('#777777');
	  $('#link_color').val('#481DA5');
	  $('#link_colorhover').val('#555555');
	  $('#input_bgcolor').val('#f7f8fa');
	  $('#content_bgcolor').val('#FFFFFF');
	  $('#content_bordercolor').val('#fbe8bc');
	  $('#button_bgcolor').val('#481DA5');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#333333');
	  $('#comment_bgcolor').val('#fef8ec');
	  $('#header_bgcolor').val('#F1AF14');
	  $('#header_bordercolor').val('#966900');
	  $('#headermenu_bgcolor').val('#BE8706');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#ffffff');
	  $('#headermenu_colorhover').val('#fef8ec');
	  $('#headermenu_bghover').val('#dea00d');
	  $('#header_searchcolor').val('#fef8ec');
	  $('#footer_bgcolor').val('#2E2E2E');
	  $('#footer_color').val('#888888');
	  $('#footer_linkcolor').val('#F1AF14');
	  $('#footer_bordercolor').val('#F1AF14');
	  $('#header_border').val('0px');
	  $('#footer_border').val('3px');
	  $('#header_icons').val('2');
	} else if (this.value === 'color_nine') {
	  $('#body_bgcolor').val('#f1e5f5');
	  $('#font_color').val('#333333');
	  $('#font_colorlight').val('#777777');
	  $('#link_color').val('#7FAA3A');
	  $('#link_colorhover').val('#555555');
	  $('#input_bgcolor').val('#f7f8fa');
	  $('#content_bgcolor').val('#FFFFFF');
	  $('#content_bordercolor').val('#e7d3ee');
	  $('#button_bgcolor').val('#7FAA3A');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#333333');
	  $('#comment_bgcolor').val('#fbf7fc');
	  $('#header_bgcolor').val('#642D7A');
	  $('#header_bordercolor').val('#340547');
	  $('#headermenu_bgcolor').val('#3e134f');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#f1e5f5');
	  $('#headermenu_colorhover').val('#fbf7fc');
	  $('#headermenu_bghover').val('#4E1864');
	  $('#header_searchcolor').val('#f1e5f5');
	  $('#footer_bgcolor').val('#2E2E2E');
	  $('#footer_color').val('#888888');
	  $('#footer_linkcolor').val('#7FAA3A');
	  $('#footer_bordercolor').val('#7FAA3A');
	  $('#header_border').val('3px');
	  $('#footer_border').val('3px');
	  $('#header_icons').val('2');
	} else if (this.value === 'color_ten') {
	  $('#body_bgcolor').val('#e5f9e3');
	  $('#font_color').val('#333333');
	  $('#font_colorlight').val('#777777');
	  $('#link_color').val('#AC5F25');
	  $('#link_colorhover').val('#555555');
	  $('#input_bgcolor').val('#f7f8fa');
	  $('#content_bgcolor').val('#FFFFFF');
	  $('#content_bordercolor').val('#d2f5ce');
	  $('#button_bgcolor').val('#AC5F25');
	  $('#button_color').val('#FFFFFF');
	  $('#headline_color').val('#333333');
	  $('#comment_bgcolor').val('#f9fef8');
	  $('#header_bgcolor').val('#288D1E');
	  $('#header_bordercolor').val('#074E00');
	  $('#headermenu_bgcolor').val('#137009');
	  $('#headermenu_border').val('transparent');
	  $('#headermenu_color').val('#d2f5ce');
	  $('#headermenu_colorhover').val('#f9fef8');
	  $('#headermenu_bghover').val('#22781a');
	  $('#header_searchcolor').val('#d2f5ce');
	  $('#footer_bgcolor').val('#222222');
	  $('#footer_color').val('#888888');
	  $('#footer_linkcolor').val('#AC5F25');
	  $('#footer_bordercolor').val('transparent');
	  $('#header_border').val('0px');
	  $('#footer_border').val('0px');
	  $('#header_icons').val('2');
    }
  });
});
</script>

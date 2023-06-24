window.addEvent('domready', function() {
    $$('select.menu-item-icon').each(function(el) {
        var src = el.get('value'),
            img = el.getParent().getPrevious().getFirst('img');

            img.set('src', (src != '') ? src : 'http://placehold.it/16x16');
    });

    $$('select.menu-item-icon').addEvent('change', function(){
        var src = $$(this).get('value'),
            img = $$(this).getParent().getPrevious().getFirst('img');

        img.set('src', (src != '') ? src : 'http://placehold.it/16x16');
    });
});

jQuery(document).ready(function($){
  $('#header_layout-wrapper > .form-element > ul > li > input, #footer_logged-wrapper > .form-element > ul > li > input, #footer_notlogged-wrapper > .form-element > ul > li > input').each(function(){
	  if($(this).is(':checked')){ $(this).parent().addClass('selected'); }
	  $(this).click(function () {
		  $('#header_layout-wrapper > .form-element > ul > li > input:not(:checked), #footer_logged-wrapper > .form-element > ul > li > input:not(:checked), #footer_notlogged-wrapper > .form-element > ul > li > input:not(:checked)').parent().removeClass('selected');
		  $('#header_layout-wrapper > .form-element > ul > li > input:checked, #footer_logged-wrapper > .form-element > ul > li > input:checked, #footer_notlogged-wrapper > .form-element > ul > li > input:checked').parent().addClass('selected');
	  });
  });
});
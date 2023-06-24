window.addEvent('domready', function() {
    $$('#admin_slider_additem').addEvents({
        click: function(event){
            event.preventDefault();
            var current_id = parseInt($$('#slider_items_no').get('value')),
                new_count = current_id + 1;
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'zephyrtheme/index/add-slider',
                method : 'POST',
                data : {
                    format : 'html',
                    item_no : new_count
                },
                onComplete : function(responseTree, responseElements, responseHTML, responseJavaScript)
                {
                    var slider_items = $$('#slider_items').getFirst('tbody'),
                        current_items = slider_items.get('html');
                    slider_items.set('html', current_items + responseHTML);

                    $$('#slider_items_no').set('value', new_count);

                    window.location.reload()
                }
            }));
        }
    });
	
    $$('select[id^="item_image_path_"]').each(function(el) {
        var src = el.get('value'),
            img = el.getParent().getPrevious().getFirst('img');

            img.set('src', (src != '') ? src : 'http://placehold.it/1x1');
    });

    $$('select[id^="item_image_path_"]').addEvent('change', function(){
        var src = $$(this).get('value'),
            img = $$(this).getParent().getPrevious().getFirst('img');

        img.set('src', (src != '') ? src : 'http://placehold.it/1x1');
    });

});
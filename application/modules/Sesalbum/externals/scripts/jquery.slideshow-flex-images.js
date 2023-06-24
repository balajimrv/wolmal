/*
	jQuery flexImages v1.0.1
    Copyright (c) 2014 Simon Steinberger / Pixabay
    GitHub: https://github.com/Pixabay/jQuery-flexImages
	License: http://www.opensource.org/licenses/mit-license.php
*/
var itemCountSlideshow,isComplete = false,identitySlideshow,heightSlideContent,maxRowsSlideshow;
(function($){
		var idDiv = 0;
    $.fn.flexImagesSlideshow = function(options){
        var o = $.extend({ container: '.slideshow_sesalbum_plugin', object: 'img', rowHeight: 197, maxRows: 0, truncate: false}, options);
        return this.each(function(){
            var $this = $(this), $items = $(o.container, $this), items = [], i = $items.eq(0), t = new Date().getTime();
            o.margin = i.outerWidth(true) - i.innerWidth();
            $items.each(function(){
                var w = parseInt($(this).data('w')),
                    h = parseInt($(this).data('h')),
                    norm_w = w*(o.rowHeight/h), // normalized width
                    obj = $(this).find(o.object);
                items.push([$(this), w, h, norm_w, obj, obj.data('src')]);
            });
            makeGridSlideshow($this, items, o);
            $this.data('flex-t', t)
        });
    }
    function makeGridSlideshow(container, items, o, noresize){
        var getX = 0, x,newHtml = [], new_w, ratio = 1, rows = 1, max_w = container.width(), row = [], row_width = 0, row_h = o.rowHeight;
        // define inside makeGrid to access variables in scope
        function _helper(lastRow){
            if (o.maxRows && rows > o.maxRows || o.truncate && lastRow) {
							if(getX == 0){
								idDiv++;
								if(isComplete)
									return;
								sesJqueryObject('#sesalbum_slideshow'+identitySlideshow).append('<div id="idDiv_'+idDiv+'" style="float:left"></div>');
							 }
							sesJqueryObject('#idDiv_'+idDiv).append(sesJqueryObject('#'+row[x][0].attr('id'))[0].outerHTML);
							getX++;
							if(row[x][0].attr('id') == 'sesalbum_slideshow_id_'+itemCountSlideshow){
								getX = 0;
								row[x][0].remove();
									sesJqueryObject("#idDiv_"+idDiv).flexImagesSlideshow({maxRows:maxRowsSlideshow});
									return;
							}
							row[x][0].remove();
						}else {
                if (row[x][5]) { row[x][4].attr('src', row[x][5]); row[x][5] = ''; }
								if(row[x][0].attr('id') == 'sesalbum_slideshow_id_'+itemCountSlideshow){
									isComplete = true;	
								}
                row[x][0].css({ width: new_w, height:heightSlideContent }).show();
            }
        }
        for (i=0; i<items.length; i++) {
            row.push(items[i]);
            row_width += items[i][3] + o.margin;
            if (row_width >= max_w) {
                ratio = max_w / row_width, row_h = Math.ceil(o.rowHeight*ratio), exact_w = 0, new_w;
                for (x=0; x<row.length; x++) {
                    new_w = Math.ceil(row[x][3]*ratio);
                    exact_w += new_w + o.margin;
                    if (exact_w > max_w) new_w -= exact_w - max_w + 1;
                    _helper();
                }
                // reset for next row
                row = [], row_width = 0;
                rows++;
            }
        }
        // layout last row - match height of last row to previous row
        for (x=0; x<row.length; x++) {
            new_w = Math.floor(row[x][3]*ratio), h = Math.floor(o.rowHeight*ratio);
            _helper(true);
        }
        // scroll bars added or removed during rendering new layout?
        if (!noresize && max_w != container.width()) makeGridSlideshow(container, items, o, true);
    }
}(sesJqueryObject));

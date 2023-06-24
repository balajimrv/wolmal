//option show hide code
sesJqueryObject(document).mouseup(function (e)
{
  var container = sesJqueryObject(".sesact_pulldown_wrapper");
  if (!container.is(e.target) // if the target of the click isn't the container...
      && container.has(e.target).length === 0) // ... nor a descendant of the container
  {
    container.removeClass('sesact_pulldown_active');
    //container.hide();
  }else if(sesJqueryObject(e.target).hasClass('sesact_pulldown_wrapper') || sesJqueryObject(e.target).closest('.sesact_pulldown_wrapper').length){
      if(sesJqueryObject(e.target).hasClass('sesact_pulldown_wrapper')){
        if( sesJqueryObject(e.target).hasClass('sesact_pulldown_active'))
          sesJqueryObject(e.target).removeClass('sesact_pulldown_active');
        else{
          container.removeClass('sesact_pulldown_active');
          sesJqueryObject(e.target).addClass('sesact_pulldown_active');
        }
      }
      else{
        if( sesJqueryObject(e.target).closest('.sesact_pulldown_wrapper').hasClass('sesact_pulldown_active'))
          sesJqueryObject(e.target).closest('.sesact_pulldown_wrapper').removeClass('sesact_pulldown_active');
        else{
          container.removeClass('sesact_pulldown_active');
          sesJqueryObject(e.target).closest('.sesact_pulldown_wrapper').addClass('sesact_pulldown_active');
        }
      }
  }
});
//tooltip code
var sestooltipOrigin;
sesJqueryObject(document).on('mouseover mouseout', '.ses_tooltip', function(event) {
	if(sesbasicdisabletooltip)
		return false;
	
	sesJqueryObject(this).tooltipster({
					interactive: true,				
					content: '<div class="sesbasic_tooltip_loading">Loading...</div>',
					contentCloning: false,
					contentAsHTML: true,
					animation: 'fade',
					updateAnimation:false,
					functionBefore: function(origin, continueTooltip) {
						//get attr
						if(typeof sesJqueryObject(origin).attr('data-rel') == 'undefined')
							var guid = sesJqueryObject(origin).attr('data-src');
						else
							var guid = sesJqueryObject(origin).attr('data-rel');
							// we'll make this function asynchronous and allow the tooltip to go ahead and show the loading notification while fetching our data.
							continueTooltip();
						       sestooltipOrigin = sesJqueryObject(this);
							if (origin.data('ajax') !== 'cached') {
								sesJqueryObject.ajax({
											type: 'POST',
											url: en4.core.baseUrl+'sesbasic/tooltip/index/guid/'+guid,
											success: function(data) {
												// update our tooltip content with our returned data and cache it
												origin.tooltipster('content', data).data('ajax', 'cached');
											}
								});
							}
					}
	});
	sesJqueryObject(this).tooltipster('show');
});
sesJqueryObject(document).on('change','#myonoffswitch',function(){
	ses_view_adultContent();
})
//adult content switch
var isActiveRequest;
function ses_view_adultContent(){
	var	url = en4.core.baseUrl+'sesbasic/index/adult/';
	var isActiveRequest =	(new Request.HTML({
      method: 'post',
      'url': url,
      'data': {
        format: 'html',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        //keep Silence
				location.reload();
      }
    }));
		isActiveRequest.send();
}

function socialSharingPopUp(url,title){
  if(title == 'Facebook')
    url = url+encodeURI('%26fbrefresh%3Drefresh');
  if(title == 'Google')
    window.open(url, title ,'height=500,width=850');
  else
    window.open(url, title ,'height=500,width=500');
	return false;
}
function openSmoothBoxInUrl(url){
	Smoothbox.open(url);
	parent.Smoothbox.close;
	return false;
}
//send quick share link
function sesAjaxQuickShare(url){
	if(!url)
		return;
	sesJqueryObject('.sesbasic_popup_slide_close').trigger('click');
	(new Request.HTML({
      method: 'post',
      'url': url,
      'data': {
        format: 'html',
				is_ajax : 1
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        //keep Silence
				showTooltip('10','10','<i class="fa fa-envelope"></i><span>'+(en4.core.language.translate("Quick share successfully"))+'</span>','sesbasic_message_notification');
      }
    })).send();
}
//make href in tab container
function tabContainerHrefSesbasic(tabId){
	if(sesJqueryObject('#main_tabs').length){
		var tab = sesJqueryObject('#main_tabs').find('.tab_'+tabId);
		if(tab.length){
			var hrefTab = window.location.href;
			var queryString = '';
			if(hrefTab.indexOf('?') > 0){
				var splitStringQuery = hrefTab.split('?');
				hrefTab = splitStringQuery[0];
				if(typeof splitStringQuery[1] != 'undefined'){
					queryString = '?'+splitStringQuery[1];
				}
			}
			if(hrefTab.indexOf('/tab/') > 0){
				hrefTab = hrefTab.split('/');
				hrefTab.pop();
				hrefTab.pop();
				hrefTab = hrefTab.join('/');
			}
			hrefTab = hrefTab+'/tab/'+tabId+queryString
			tab.find('a').attr('href',hrefTab);
			var clickElem = tab.find('a').attr('onclick')+';return false;';
			tab.find('a').attr('onclick',clickElem);
		}	
	}
}
//content like, favourite, rated and follow auto tooltip from left bottom.
function showTooltipSesbasic(x, y, contents, className) {
	if(sesJqueryObject('.sesbasic_notification').length > 0)
		sesJqueryObject('.sesbasic_notification').hide();
		sesJqueryObject('<div class="sesbasic_notification '+className+'">' + contents + '</div>').css( {
		display: 'block',
	}).appendTo("body").fadeOut(5000,'',function(){
		sesJqueryObject(this).remove();	
	});
}
//common function for like comment ajax
function like_favourite_data(element,functionName,itemType,moduleName,likeNoti,unLikeNoti,className){
		if(!sesJqueryObject(element).attr('data-url'))
			return;
		if(sesJqueryObject(element).hasClass('button_active')){
				sesJqueryObject(element).removeClass('button_active');
		}else
				sesJqueryObject(element).addClass('button_active');
		 (new Request.HTML({
      method: 'post',
      'url':  en4.core.baseUrl + moduleName+'/index/'+functionName,
      'data': {
        format: 'html',
        id: sesJqueryObject(element).attr('data-url'),
				type:itemType,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var response =jQuery.parseJSON( responseHTML );
				if(response.error)
					alert(en4.core.language.translate('Something went wrong,please try again later'));
				else{
					sesJqueryObject(element).find('span').html(response.count);
					if(response.condition == 'reduced'){
							sesJqueryObject(element).removeClass('button_active');
							showTooltip(10,10,unLikeNoti)
							return true;
					}else{
							sesJqueryObject(element).addClass('button_active');
							showTooltip(10,10,likeNoti,className)
							return false;
					}
				}
      }
    })).send();
}
sesJqueryObject(document).on('click','.sesbasic_favourite_sesbasic_video',function(){
	like_favourite_data(this,'favourite',itemType,'<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Video added as Favourite successfully"))+'</span>','<i class="fa fa-heart"></i><span>'+(en4.core.language.translate("Video Unfavourited successfully"))+'</span>','sesbasic_favourites_notification');
});

sesJqueryObject(document).on('click','.openSmoothbox',function(e){
  var url = sesJqueryObject(this).attr('href');
  openSmoothBoxInUrl(url);
  return false;
});
sesJqueryObject(document).on('click','.opensmoothboxurl',function(e){
  var url = sesJqueryObject(this).attr('href');
  openSmoothBoxInUrl(url);
  return false;
});
//open url in smoothbox
function opensmoothboxurl(openURLsmoothbox){
  openSmoothBoxInUrl(openURLsmoothbox);
	return false;
}

sesJqueryObject(document).on('click','#sesbasic_btn_currency',function(){
	if(sesJqueryObject(this).hasClass('active')){
		sesJqueryObject(this).removeClass('active');
		sesJqueryObject('#sesbasic_currency_change').hide();
	}else{
		sesJqueryObject(this).addClass('active');
		sesJqueryObject('#sesbasic_currency_change').show();	
	}
});
//currency change
sesJqueryObject(document).on('click','ul#sesbasic_currency_change_data li > a',function(){
	var currencyId = sesJqueryObject(this).attr('data-rel');
	setSesCookie('sesbasic_currencyId',currencyId,365);
	location.reload();
});
function setSesCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires+';path=/;';
} 
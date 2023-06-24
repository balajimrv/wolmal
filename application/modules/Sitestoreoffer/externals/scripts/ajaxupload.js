/* $Id: ajaxupload.js 2013-09-02 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */

function $m(theVar){
  return document.getElementById(theVar)
}
function remove(theVar){
  var theParent = theVar.parentNode;
  theParent.removeChild(theVar);
}
function addEventImage(obj, evType, fn){
  if(obj.addEventListener)
    obj.addEventListener(evType, fn, true)
  if(obj.attachEvent)
    obj.attachEvent("on"+evType, fn)
}
function removeEventImage(obj, type, fn){
  if(obj.detachEvent){
    obj.detachEvent('on'+type, fn);
  }else{
   // obj.removeEventListener(type, fn, false);
  }
}
function isWebKit(){
  return RegExp(" AppleWebKit/").test(navigator.userAgent);
}
function ajaxUpload(form,url_action,id_element,html_show_loading,html_error_http){
  var detectWebKit = isWebKit();
  form = typeof(form)=="string"?$m(form):form;
  var erro="";
  if(form==null || typeof(form)=="undefined"){
    erro += "The form of 1st parameter does not exists.\n";
  }else if(form.nodeName.toLowerCase()!="form"){
    erro += "The form of 1st parameter its not a form.\n";
  }
//   if($m(id_element)==null){
//     erro += "The element of 3rd parameter does not exists.\n";
//   }
  if(erro.length>0){
    alert("Error in call ajaxUpload:\n" + erro);
    return;
  }
  var iframe = document.createElement("iframe");
  iframe.setAttribute("id","ajax-temp_image");
  iframe.setAttribute("name","ajax-temp_image");
  iframe.setAttribute("width","0");
  iframe.setAttribute("height","0");
  iframe.setAttribute("border","0");
  iframe.setAttribute("style","width: 0; height: 0; border: none;");
  form.parentNode.appendChild(iframe);
  window.frames['ajax-temp_image'].name="ajax-temp_image";
  var doUpload = function() {
     $m("photo").style.visibility="visible";
     $m("loading_image").style.display="none";
    removeEventImage($m('ajax-temp_image'),"load", doUpload);
    var cross = "javascript: ";
    cross += "window.parent.$m('"+id_element+"').innerHTML = document.body.innerHTML; void(0);";
    $m(id_element).innerHTML = html_error_http;
    $m('ajax-temp_image').src = cross; 
    if(detectWebKit){
      remove($m('ajax-temp_image'));
    }else{
      setTimeout(function(){
        remove($m('ajax-temp_image'))
      }, 250);
    }
    form.setAttribute("target",""); 
    form.setAttribute("action","");
    setTimeout(function(){
    }, 100);
  }
  addEventImage($m('ajax-temp_image'),"load", doUpload);
  form.setAttribute("target","ajax-temp_image");
  form.setAttribute("action",url_action);
  form.setAttribute("method","post");
  form.setAttribute("enctype","multipart/form-data");
  form.setAttribute("encoding","multipart/form-data");
  if(html_show_loading.length > 0){
    $m(id_element).innerHTML = html_show_loading;
  }
  form.submit();
}

/* $Id: core.js 2013-09-02 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */

var tab_content_id_sitestoreproduct=0;
var OrderproductselectArray=[];
en4.sitestoreproduct ={  
  maps:[],
  infowindow:[],
  markers:[]
};
  
en4.sitestoreproduct.ajaxTab ={
  click_elment_id:'',
  attachEvent : function(widget_id,params){
		

    params.requestParams.content_id = widget_id;
    var element;
    
    $$('.tab_'+widget_id).each(function(el){
      if(el.get('tag') == 'li'){
        element =el;
        return;
      }
    });
    var onloadAdd = true;
		
    if(element){
      if(element.retrieve('addClickEvent',false))
        return;
      element.addEvent('click',function(){
        if(en4.sitestoreproduct.ajaxTab.click_elment_id == widget_id)
          return;
        en4.sitestoreproduct.ajaxTab.click_elment_id = widget_id;
        en4.sitestoreproduct.ajaxTab.sendReq(params);
      });
      element.store('addClickEvent',true);
			
			
				
      var attachOnLoadEvent = false; 
      if( tab_content_id_sitestoreproduct == widget_id){ 
        attachOnLoadEvent=true;
      }else{
        $$('.tabs_parent').each(function(element){
          var addActiveTab= true;
          element.getElements('ul > li').each(function(el){
            if(el.hasClass('active')){
              addActiveTab = false;
              return;
            }
          }); 
          element.getElementById('main_tabs').getElements('li:first-child').each(function(el){
            el.get('class').split(' ').each(function(className){
              className = className.trim();
              if( className.match(/^tab_[0-9]+$/) && className =="tab_"+widget_id  ) {
                attachOnLoadEvent=true;
                if(addActiveTab || tab_content_id_sitestoreproduct == widget_id){
                  element.getElementById('main_tabs').getElements('ul > li').removeClass('active');
                  el.addClass('active');
                  element.getParent().getChildren('div.' + className).setStyle('display', null);        
                }
                return;
              }
            });          
          });
        });
      }
      if(!attachOnLoadEvent)
        return;
      onloadAdd = false;
      
    }
      
    en4.core.runonce.add(function() {
      if(onloadAdd)
        params.requestParams.onloadAdd=true;
      en4.sitestoreproduct.ajaxTab.click_elment_id = widget_id;
      en4.sitestoreproduct.ajaxTab.sendReq(params);
    });

    
  },
  sendReq: function(params){    
    params.responseContainer.each(function(element){
      element.empty();
      new Element('div', {      
        'class' : 'sr_sitestoreproduct_profile_loading_image'      
      }).inject(element);
    });
    var url = en4.core.baseUrl+'widget';
   
    if(params.requestUrl)
      url= params.requestUrl;

    var request = new Request.HTML({
      url : url,
      data : $merge(params.requestParams,{
        format : 'html',
        subject: en4.core.subject.guid,
        is_ajax_load:true
      }),
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        params.responseContainer.each(function(container){
          container.empty();
          Elements.from(responseHTML).inject(container);
          en4.core.runonce.trigger();
          Smoothbox.bind(container);
        });
      }
    });
    request.send();
  }
};
var compareSitestoreproductDefault={
  enabel:false,
  compareUrl:en4.core.baseUrl+'stores/products/compare'
};
var compareSitestoreproductContent;
var compareSitestoreproduct = new Class({
  productTypes:{},
  scrollCarousel :{},
  products:{},
  activeType:'st1',
  initialize: function(params){
    this.compareDashboard =  this.setCompareDashboard();
    this.getProductsFromCookie();
    var self = this;
  
    Object.each(this.productTypes, function(productType, key){  
      var type = 'st'+key;        
      self.createTab(key,productType); 
      var  scrollContentList = $('stProduct_'+type);    
      Object.each(self.products[type], function(contentList, key){   
        self.setItem(scrollContentList,contentList);          
      });
      self.displayTabsContainer(type); 
      self.toggoleTabsContainer();
    });
    
    this.getHideCampareBarCookie();
  },
  setCompareDashboard : function(){
    if(this.compareDashboard) return;
    var self = this;
    this.compareDashboard =  new Element('div', {
      'id' : 'sr_sitestoreproduct_compare_dashboard',
      'styles' :{
        'display' : 'block'
      }
    });
    this.compareHeader =  new Element('div', {
      'id' : 'sr_sitestoreproduct_compare_header',
      'styles' :{
        'cursor' : 'pointer'
      }
    });
    this.compareHeaderDown =  new Element('div', {
      'id' : 'sr_sitestoreproduct_compareArrow',
      'events' : {
        'click' : self.toggoleTabsContainer.bind(this)
      }
    }).inject(this.compareHeader);
    this.compareBarHide =  new Element('div', {
      'id' : 'sr_sitestoreproduct_compareBarHide',
      'class':'sr_sitestoreproduct_compareBarHide',
      'title':en4.core.language.translate('Hide Compare Bar'),
      'html':'',
      'events' : {
        'click' : self.toggoleCompareBar.bind(this)
      }
    }).inject(this.compareHeader);
    this.compareHeader.inject(this.compareDashboard);
    
    this.compareTabs =  new Element('div', {
      'id' : 'sr_sitestoreproduct_compare_tabs',
      'class':'sr_sitestoreproduct_ui_tabs'
    });
    
    this.compareTitle =  new Element('div', {
      'id' : 'sr_sitestoreproduct_compareTitle',
      'html':en4.core.language.translate('Compare')
    }).inject(this.compareTabs);
    
    this.tabsNav = new Element('ul', {
      'class':'sr_sitestoreproduct_tabsNav sr_sitestoreproduct_ui_tabs_nav sr_sitestoreproduct_ui_widget_header sr_sitestoreproduct_uiCornerAll' ,
      'styles' : {
        'cursor' : 'pointer' 
      }
    }).inject(this.compareTabs)
    this.tabsContainer =  new Element('div', {
      'id' : 'sr_sitestoreproduct_tabs_container'  
    }).inject(this.compareTabs);
     
    this.compareTabs.inject(this.compareDashboard);
    this.compareDashboard.inject(document.body);
    this.compareDashboardMin = new Element('div', {
      'id' : 'sr_sitestoreproduct_compare_dashboard_min',
      'class':'sr_sitestoreproduct_compare_dashboard_min',
      'title':en4.core.language.translate('Show Compare Bar'),
      'html':'<span class="fleft">'+en4.core.language.translate('Compare')+'</span><i></i>',
      'styles' :{
        'display' : 'none'
      },
      'events' : {
        'click' : self.toggoleCompareBar.bind(this)
      }
    });
    
    this.compareDashboardMin.inject(document.body);
  },
  toggoleCompareBar : function(event){
    // var el =  $(event.target);
    if(this.compareDashboardMin.style.display =='none'){
      $('sr_sitestoreproduct_compare_dashboard').style.display = 'none'; 
      this.compareDashboardMin.style.display = 'block';
      this.setHideCampareBarCookie(1);
    }else{
      this.compareDashboardMin.style.display = 'none';
      $('sr_sitestoreproduct_compare_dashboard').style.display = 'block';
      this.setHideCampareBarCookie(0);
    }
  },
  toggoleTabsContainer: function(){
    if(this.compareHeaderDown.hasClass("down")){
      this.tabsContainer.style.display = 'none';
      this.compareHeaderDown.removeClass('down');       
    }else {           
      this.tabsContainer.style.display = 'block';
      this.compareHeaderDown.addClass('down');
    }
  },
  setCampareItem: function(event){
  
    var el =  $(event.target);    
    var  list_id = el.get('value');
    var typeContent= $('productID'+list_id);    
    var type_id =typeContent.get('class').substr(11);
    var type ="st"+type_id;
    this.createTab(type_id,typeContent.innerHTML);


    var listUrl=$('productUrl'+list_id).innerHTML;
    var productImgSrc=$('productImgSrc'+list_id).innerHTML;
    var title=el.get('name');
    
    if($('product'+list_id)){
      $('product'+list_id).destroy();  
    }
    var  scrollContentList = $('stProduct_'+type);
    var contentList ={
      type :type,
      type_id : type_id,
      list_id :list_id,
      listTitile: title,
      listUrl: listUrl,
      imgSrc :productImgSrc
    }
    this.setItem(scrollContentList,contentList);
    this.products[type][list_id]=contentList;   
    this.setProductIntoCookie(type);
     
    this.displayTabsContainer(type);
  },
  setItem : function(scrollContentList , params){
    var self = this; 
    self.updateCompareButton(params.list_id, true);
    var scrollContentListItem = new Element('li', {
      'class':'stItem' ,
      'id':'product'+params.list_id,
      'styles' :{
        'display' : 'list-item'
      }      
    });
    
    var compareThumb= new Element('div', {
      'class': 'compareThumb'
    });
    
    
    var compareThumbLink= new Element('a', {    
      'href' : params.listUrl 
    });
    
    new Element('img', {
      'src':params.imgSrc  
    }).inject(compareThumbLink);
    
    compareThumbLink.inject(compareThumb);
    
    compareThumb.inject(scrollContentListItem);
    
    var compareItemTitle = new Element('span', {     
      'class': 'compareItemTitle'
    }).inject(scrollContentListItem);
    
    new Element('a', {    
      'href' : params.listUrl,
      'html' :params.listTitile  
      
    }).inject(compareItemTitle);
    new Element('span', {
      'html' :params.type_id,
      'id':'removeproduct'+params.list_id,
      'class': 'removeItem',
      'events':{
        'click': self.removeProductFromComparison.bind(this)
      }
    }).inject(scrollContentListItem);
    compareItemTitle.inject(scrollContentListItem); 
    

  
    scrollContentListItem.inject(scrollContentList);
    var horizontalScroll = this.scrollCarousel[params.type];
    horizontalScroll.cacheElements(); 
    if(horizontalScroll.elements.length > horizontalScroll.options.noOfItemPerPage){
      horizontalScroll.toNext();
    }
    horizontalScroll.resetScroll();
    var count = scrollContentList.getElements("li").length;
    $('numSelected_'+params.type).innerHTML ='('+count+')';

  },
  createTab: function(type_id , title) {
  
    var type ='st'+type_id; 
    if($('stProduct_'+type))
      return;
   
    var self = this;
    var choice = new Element('li', {
      'class': 'sr_sitestoreproduct_uiStateDefault',
      'id': 'sr_sitestoreproduct_compareTab_'+type,
      'rel': type,
      'rev' :title,
      'events' : {
        'click' : self.displayTabsContainer.bind(this,type)
      } 
    });
    
    var link= new Element('a', {
      'html':title,
      'class': 'productTypeCompare',
      'href' : 'javascript:void(0);'         
      
    });
 
    new Element('span', {        
      'class' : 'numSelected',
      'id':'numSelected_'+type,
      'html':'(1)'
    }).inject(link);
  
    link.inject(choice);    
    choice.inject(this.tabsNav);  
   
    var content= new Element('div', {
      'id' : 'tabContent_'+type,
      'class':'sr_sitestoreproduct_tabsPanel sr_sitestoreproduct_ui_tabs_panel sr_sitestoreproduct_uiWidgetContent sr_sitestoreproduct_uiCornerBottom'
    });
    
    var prevLink =  new Element('a', {  
      'id' : 'tabContentPreviousLink_'+type,
      'class': 'comparePrev compareBrowse compareLeft'
    }).inject(content);
    
    var scrollContent= new Element('div', {    
      'id' :'sr_sitestoreproduct_compareScroll_'+type,
      'class': 'sr_sitestoreproduct_compareScroll'     
    });
    
    var nextLink= new Element('a', {      
      'id' : 'tabContentNextLink_'+type,
      'class': 'compareNext compareBrowse compareRight'
    });
    var scrollContentList = new Element('ul', {
      'class':'stProduct',
      'id' : 'stProduct_'+type            
    });
      
    scrollContentList.inject(scrollContent);    
    scrollContent.inject(content);
    
    nextLink.inject(content);
    
    var rightSideContent= new Element('div', {
      'id' :'rightSideContent_'+type,
      'class': 'sr_sitestoreproduct_compare_buttons'
    });
    
    new Element('button', {
      'html': en4.core.language.translate('Compare All'),
      'id' :'compare_all_'+type,
      'class': 'sr_sitestoreproduct_button',  
      'events' : {
        'click' : self.compareAll.bind(this,type_id)
      }
    }).inject(rightSideContent);
    new Element('button', {
      'html':en4.core.language.translate('Remove All'),
      'class': 'sr_sitestoreproduct_button',         
      'events' : {
        'click' : self.removeAll.bind(this,type)
      }
      
    }).inject(rightSideContent);
    
    rightSideContent.inject(content);  
    content.inject(this.tabsContainer); 

    this.scrollCarousel[type] = new Fx.Scroll.Carousel('sr_sitestoreproduct_compareScroll_'+type,{
      mode: 'horizontal',
      navs:{
        frwd:'tabContentNextLink_'+type,
        prev:'tabContentPreviousLink_'+type
      }
    });
   
    this.productTypes[type_id]=title;
    if(typeOf(this.products[type]) == 'null')
      this.products[type]={};
  },
  displayTabsContainer:function(type){  
    this.compareHeaderDown.addClass('down');
    this.tabsContainer.style.display = 'block';

    this.tabsContainer.getElements('.sr_sitestoreproduct_tabsPanel').setStyle('display','none');       
    $('tabContent_'+type).setStyle('display','block');
    
    $('sr_sitestoreproduct_compare_tabs').getElements('ul > li').removeClass('sr_sitestoreproduct_ui_tabs_selected sr_sitestoreproduct_uiStateActive');  
    $('sr_sitestoreproduct_compareTab_'+type).addClass('sr_sitestoreproduct_ui_tabs_selected sr_sitestoreproduct_uiStateActive');
    
  },
  compareAll: function(type_id){
    var type= "st"+type_id;
    var compareCount= $('stProduct_'+type).getElements("li").length;

    if (compareCount < 2) { 
      var el = $('compare_all_'+type); 
      var p_msg= new Element('p', {
        'html': en4.core.language.translate('Please select more than one product for the comparison.'),        
        'class': 'comparisonMessage sr_sitestoreproduct_tooltipBox'  // sr_sitestoreproduct_uiTabsSelected             
      }).inject(el,'before');
     
      p_msg.fade('in');
      (function(){ 
        p_msg.fade('out');
        (function(){ 
          p_msg.destroy();
        }).delay(2000);
      }).delay(5000);
      
    }else{
      window.location.href = this.compareUrl+'/id/'+type_id+'/'+$('sr_sitestoreproduct_compareTab_'+type).get('rev');  
    }
  },
  removeProductFromComparison : function(event){
   
    var el = $(event.target);   
    var type,list_id,li_el;
    if(el.hasClass('removeProduct')){
      list_id= el.get('id').substr(18); 
      type_id = el.get('rel');
      type ="st"+type_id;
    }else if(el.hasClass('checkProduct') && el.get('tag') == 'input'){    
      list_id = el.get('value');     
      var typeContent= $('productID'+list_id);    
      var type_id =typeContent.get('class').substr(11);
      type ="st"+type_id;
    }else{
      type = "st"+el.innerHTML;
      li_el= el.getParent('li');
      list_id = li_el.get('id').substring(7);
    }
    li_el = $('product'+list_id);
    li_el.fade('out');
    
    var self = this;
    (function(){ 
      var count= $('stProduct_'+type).getElements("li").length;
      if(count <=1){
        self.removeAll(type);
      }else{
        li_el.destroy();        
        count = $('stProduct_'+type).getElements("li").length;
        $('numSelected_'+type).innerHTML ='('+count+')';
        delete self.products[type][list_id];
        var horizontalScroll = self.scrollCarousel[type];
        horizontalScroll.cacheElements();
        horizontalScroll.resetScroll();
        if((horizontalScroll.elements.length > horizontalScroll.options.noOfItemPerPage && horizontalScroll.currentIndex > 0)|| horizontalScroll.currentIndex > 0){
          horizontalScroll.toPrevious();
        }
        if(horizontalScroll.currentIndex==0)
          horizontalScroll.resetScroll();
        self.setProductIntoCookie(type);      
      }    
    }).delay(500);
    self.updateCompareButton(list_id, false);
  },
  removeAll:function(type){
    var self = this;
    $('stProduct_'+type).getElements("li").each(function(li_el){
      var list_id = li_el.get('id').substring(7);
      self.updateCompareButton(list_id, false);    
    });
    $("tabContent_"+type).destroy();
    var els= this.tabsNav.getElements("li");
    var tab = $("sr_sitestoreproduct_compareTab_"+type);
  
    for(i=0; i< els.length; i++){      
      if(els[i].get('id') == tab.get('id')){
        break;
      }
    }
    tab.destroy();
    if(this.tabsNav.getElements("li").length <1){
      this.toggoleTabsContainer();
    }else{
      var nextType;
      if(i==0){
        nextType=els[1].get('rel');
      } else {
        i =i-1;
        nextType=els[i].get('rel');
      }
   
      this.displayTabsContainer(nextType);
    }
   
    delete this.products[type]; 
    var type_id =type.substring(2);
    delete this.productTypes[type_id];   
    this.setProductIntoCookie(type);
  },
  getProductsFromCookie: function(){
    var cookiesSuffix='';
    if(en4.user.viewer.id){
      cookiesSuffix='_'+en4.user.viewer.id;
    }
    var productTypes= Cookie.read('srCompareProductTypes'+cookiesSuffix);
   
    var lists = {};
    if(productTypes){
      productTypes = JSON.decode(productTypes);
    
      Object.each(productTypes, function(value, key){
        var type= 'st'+key;       
        cookiesName='stCompareProduct'+type+cookiesSuffix;    
        var products=Cookie.read(cookiesName);       
        if(products){
          lists[type]  = JSON.decode(products);       
        }
      });
      this.productTypes=productTypes;      
      this.products=lists;
            
    }
  }, 
  setProductIntoCookie: function(type){
        
    var lists= this.products[type];
    var cookiesSuffix='';
    var duration=1;
    if(en4.user.viewer.id){
      cookiesSuffix='_'+en4.user.viewer.id;
      duration=30;
    }
    cookiesName='stCompareProduct'+type+cookiesSuffix;
    Cookie.write(cookiesName, JSON.encode(lists), {
      duration: duration //save for a day
    }); 
    Cookie.write('srCompareProductTypes'+cookiesSuffix, JSON.encode(this.productTypes), {
      duration: duration //save for a day
    }); 

  },
  getHideCampareBarCookie: function(){
    var cookiesSuffix='';
    if(en4.user.viewer.id){
      cookiesSuffix='_'+en4.user.viewer.id;
    }
    var falge= Cookie.read('stCompareBar'+cookiesSuffix);
    if(falge==1){
      this.toggoleCompareBar();     
    }
  }, 
  setHideCampareBarCookie : function(falge){
    var cookiesSuffix='';
    var duration=1;
    if(en4.user.viewer.id){
      cookiesSuffix='_'+en4.user.viewer.id;
      duration=30;
    }
    cookiesName='stCompareBar'+cookiesSuffix;
    Cookie.write(cookiesName, falge, {
      duration: duration //save for a day
    }); 
  },
  compareButtonEvent:function(event){
    var el =  $(event.target);
    if(el.checked){
      this.setCampareItem(event);
    }else{
      this.removeProductFromComparison(event);
    }
  },
  updateCompareButton : function(list_id, checked){
    $$('.compareButtonProduct'+list_id).each(function(element){
      element.checked = checked;
    });
  },
  updateCompareButtons:function(){
    var self = this;  
    Object.each(this.productTypes, function(productType, key){  
      var type = 'st'+key;
      Object.each(self.products[type], function(contentList){  
        self.updateCompareButton(contentList.list_id, true);    
      });   
    });
  }
});


/*
---

script: Fx.Scroll.Carousel.js

description: Extends Fx.Scroll to work like a carousel

license: MIT-style license.

authors: Ryan Florence

docs: http://moodocs.net/rpflo/mootools-rpflo/Fx.Scroll.Carousel

requires:
- more/1.2.4.2: [Fx.Scroll]

provides: [Fx.Scroll.Carousel]

...
   */


Fx.Scroll.Carousel = new Class({
	
  Extends: Fx.Scroll,
	
  options: {
    mode: 'horizontal',
    childSelector: false,
    loopOnScrollEnd: true,
    noOfItemPerPage: 4,
    noOfItemScroll:1,
    navs:{
      frwd:'sr_sitestoreproduct_crousal_frwd',
      prev:'sr_sitestoreproduct_crousal_prev'
    }
  },
	
  initialize: function(element, options){
    this.parent(element, options);
    this.cacheElements();
    this.currentIndex = 0;
    this.resetScroll();
    var self=this;
    $(this.options.navs.frwd).addEvent('click', function(){
      self.toNext();  
      self.resetScroll();
    });
	
    $(this.options.navs.prev).addEvent('click', function(){
      self.toPrevious();
      self.resetScroll();
    });
  },
	
  cacheElements: function(){
    var cs = this.options.childSelector;
    if(cs){
      els = this.element.getElements(cs);
    } else if (this.options.mode == 'horizontal'){
      els = this.element.getElements('.stItem');
    } else {
      els = this.element.getChildren();
    }
    this.elements = els;
  
    return this;
  },
	
  toNext: function(){
    if(this.checkLink()) return this;
    this.currentIndex = this.getNextIndex();
    this.toElement(this.elements[this.currentIndex]);
    this.fireEvent('next');
    return this;
  },
	
  toPrevious: function(){
    if(this.checkLink()) return this;
    this.currentIndex = this.getPreviousIndex();
    this.toElement(this.elements[this.currentIndex]);
    this.fireEvent('previous');
    return this;
  },
	
  getNextIndex: function(){
    //this.currentIndex++;
    this.currentIndex = this.currentIndex + this.options.noOfItemScroll;
    if(this.currentIndex == this.elements.length || this.checkScroll()){
      this.fireEvent('loop');
      this.fireEvent('nextLoop');
      return 0;
    } else {
      return this.currentIndex;
    }
  },
	
  getPreviousIndex: function(){
    //this.currentIndex--;
    this.currentIndex = this.currentIndex- this.options.noOfItemScroll;   
    var check = this.checkScroll();
    if(this.currentIndex < 0 || check) {
      this.fireEvent('loop');
      this.fireEvent('previousLoop');
      return (check) ? this.getOffsetIndex() : this.elements.length - 1;
    } else {
      return this.currentIndex;
    }
  },
	
  getOffsetIndex: function(){   
    var visible = (this.options.mode == 'horizontal') ? 
    this.element.getStyle('width').toInt() / this.elements[0].getStyle('width').toInt() :
    this.element.getStyle('height').toInt() / this.elements[0].getStyle('height').toInt();
    return this.currentIndex + 1 - visible;
  },
	
  checkLink: function(){
    return (this.timer && this.options.link == 'ignore');
  },
	
  checkScroll: function(){
    if(!this.options.loopOnScrollEnd) return false;
    if(this.options.mode == 'horizontal'){
      var scroll = this.element.getScroll().x;
      var total = this.element.getScrollSize().x - this.element.getSize().x;
    } else {
      var scroll = this.element.getScroll().y;
      var total = this.element.getScrollSize().y - this.element.getSize().y;
    }
    return (scroll == total);
  },
	
  getCurrent: function(){
    return this.elements[this.currentIndex];
  },
  resetScroll:function(){
    if(this.elements.length <= this.options.noOfItemPerPage){
      $(this.options.navs.frwd).style.visibility = 'hidden';
      $(this.options.navs.prev).style.visibility = 'hidden';
    }else{
      var visibleflag='visible';
      if(this.currentIndex == 0 || this.elements.length <= this.options.noOfItemPerPage ){
        visibleflag = 'hidden';
      }
      $(this.options.navs.prev).style.visibility = visibleflag;
      visibleflag='visible';
      if(((this.currentIndex + this.options.noOfItemPerPage) >= this.elements.length)  ){
        visibleflag = 'hidden';
      }
      $(this.options.navs.frwd).style.visibility = visibleflag;
    }
  }
	
});

/**
 * @description dropdown Navigation
 * @param {String} id id of ul element with navigation lists
 * @param {Object} settings object with settings
 */


var NavigationSitestoreproduct = function() {
  var main = { 
    obj_nav : $(arguments[0]) || $("nav"),
    settings : {
      show_delay : 0,
      hide_delay : 0,
      _ie6 : /MSIE 6.+Win/.test(navigator.userAgent),
      _ie7 : /MSIE 7.+Win/.test(navigator.userAgent)
    },
    init : function(obj, level) {
      obj.lists = obj.getChildren();
      obj.lists.each(function(el,ind){
        main.handlNavElement(el);
        if((main.settings._ie6 || main.settings._ie7) && level){
          main.ieFixZIndex(el, ind, obj.lists.size());
        }
      });
      if(main.settings._ie6 && !level){
        document.execCommand("BackgroundImageCache", false, true);
      }
    },
    handlNavElement : function(list) {
      if(list !== undefined){
        list.onmouseover = function(){
          main.fireNavEvent(this,true);
        };
        list.onmouseout = function(){
          main.fireNavEvent(this,false);
        };
        if(list.getElement("ul")){
          main.init(list.getElement("ul"), true);
        }
      }
    },
    ieFixZIndex : function(el, i, l) {
      if(el.tagName.toString().toLowerCase().indexOf("iframe") == -1){
        el.style.zIndex = l - i;
      } else {
        el.onmouseover = "null";
        el.onmouseout = "null";
      }
    },
    fireNavEvent : function(elm,ev) {
      if(ev){
        elm.addClass("over");
        elm.getElement("a").addClass("over");
        if (elm.getChildren()[1]) {
          main.show(elm.getChildren()[1]);
        }
      } else {
        elm.removeClass("over");
        elm.getElement("a").removeClass("over");
        if (elm.getChildren()[1]) {
          main.hide(elm.getChildren()[1]);
        }
      }
    },
    show : function (sub_elm) {
      if (sub_elm.hide_time_id) {
        clearTimeout(sub_elm.hide_time_id);
      }
      sub_elm.show_time_id = setTimeout(function() {
        if (!sub_elm.hasClass("shown-sublist")) {
          sub_elm.addClass("shown-sublist");
        }
      }, main.settings.show_delay);
    },
    hide : function (sub_elm) {
      if (sub_elm.show_time_id) {
        clearTimeout(sub_elm.show_time_id);
      }
      sub_elm.hide_time_id = setTimeout(function(){
        if (sub_elm.hasClass("shown-sublist")) {
          sub_elm.removeClass("shown-sublist");
        }
      }, main.settings.hide_delay);
    }
  };
  if (arguments[1]) {
    main.settings = Object.extend(main.settings, arguments[1]);
  }
  if (main.obj_nav) {
    main.init(main.obj_nav, false);
  }
};


var smoothbox_open = function (url)
{
  Smoothbox.open (url);							
}

var manageOrder = function(actionName, item_id) {
  if($('manage_order_tab') != null) {
    $('manage_order_menu').style.display = 'none';
    $('manage_order_tab').innerHTML = '<center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif" /></center>';
  }
  if( item_id ) {
    detail_id = item_id;
  }
  var showappinfo = new Request.HTML({
    'url' : en4.core.baseUrl + 'sitestoreproduct/index/' + actionName,
    'data' : {
      'format' : 'html',
      'detail_id' : detail_id
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      $('manage_order_tab').innerHTML = responseHTML;
      $('manage_order_menu').style.display = 'block';
      en4.core.runonce.trigger();
    }
  });
  showappinfo.send ();
}


//var show_app_likes = function (id, actionName) {
//  var BaseUrl = getBaseUrl + getBaseParam + id + getMethod + actionName;
//  if(BaseUrl && typeof history.pushState != 'undefined') { 
//    history.pushState( {}, document.title, BaseUrl );
//  }
//  getBaseUrl = getBaseUrl;
//  if($('dynamic_menus_content') != null) {		
//    $('dynamic_menus_content').innerHTML = '<center><img src="application/modules/Sitestoreproduct/externals/images/loading.gif" /></center>';
//  }
//
//  if ($type($('sitestoreproduct_menu_' + activeTabId))) {
//    $('sitestoreproduct_menu_' + activeTabId).erase('class');
//  }
//
//  $('sitestoreproduct_menu_' + id).set('class', 'selected');
//  activeTabId = id;
//
//  var showappinfo = new Request.HTML({
//    'url' : en4.core.baseUrl + 'sitestoreproduct/index/' + actionName + '/' + getBaseParam + id,
//    'data' : {
//      'format' : 'html',
//      'isajax' : 1,
//      'flag_display' : 2,
//      'method' : actionName
//    },
//    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
//      $('dynamic_menus_content').innerHTML = responseHTML;
//     
//      en4.core.runonce.trigger();
//    }
//  });
//
//  showappinfo.send ();
//}


//function for showing Shipping Methods link
var show_shipping_methods = function (actionName,divid) {
  if($(divid) != null) {
    $(divid).innerHTML = '<center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif" /></center>';
  }
  
  var showappinfo = new Request.HTML({
    'url' : en4.core.baseUrl + 'sitestoreproduct/index/' + actionName,
    'data' : {
      'format' : 'html',
      'isajax' : 1
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      $(divid).innerHTML = responseHTML;
      en4.core.runonce.trigger();
    }
  });

  showappinfo.send ();
}


/**
 * Add product into viewer cart
 */
var addToCartTempflag = 0;
var tempTimeOutId;
function addToCart(product_id, store_type, widget_id, view_type, update_cart_notification)
{
  var view_loading_image_id = '';
  //NEW REQUEST ONLY WHEN PREVIOUS WILL COMPLETE
  if( addToCartTempflag == 0 )
  {
    if( view_type != "" )
      view_loading_image_id = '_'+view_type;
    
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitestoreproduct/product/addto-cart',
      onRequest: function(){
        addToCartTempflag = 1;
        if($('addToCartResponseMessage'))
          $('addToCartResponseMessage').dispose();
        if( tempTimeOutId )
          clearTimeout(tempTimeOutId);
        $('loading_image_' + widget_id + '_' + product_id+view_loading_image_id).addClass('loading');
      },
      data : {
        'format' : 'json',
        'store_type': store_type,
        'product_id' : product_id
      },
      onSuccess: function(responseJSON)
      {
        addToCartTempflag = 0;
        new Element('div', { 
          'id' : 'addToCartResponseMessage',
          'class' : 'addtocart_message'
        }).inject($('global_content') || document.body);
        
        if(responseJSON.addToCartError)
        {
          $('loading_image_' + widget_id + '_' + product_id + view_loading_image_id).removeClass('loading');
  				$('addToCartResponseMessage').addClass('addtocart_error');
          $('addToCartResponseMessage').innerHTML = responseJSON.addToCartError;
        }
        else
        {
          $('loading_image_' + widget_id + '_' + product_id + view_loading_image_id).removeClass('loading');
          $('addToCartResponseMessage').addClass('addtocart_success');
          $('addToCartResponseMessage').innerHTML = responseJSON.addToCartSuccess;
          
          if( update_cart_notification == 1 )
            sitestoreproductHandler.getCartItem();
          else if( update_cart_notification == 2 )
            getCartItemCount();
        }
        
        if($('addToCartResponseMessage') && addToCartTempflag == 0 )
          tempTimeOutId = window.setTimeout(function(){$('addToCartResponseMessage').dispose();},'5500');
      }
    })
    );
  }
}

/**
 * Product quick view
 */
var isQuickViewRequested = 0;
function productQuickView(product_id)
{
  if( isQuickViewRequested == 0 )
  {
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'sitestoreproduct/product/quick-view/product_id/'+product_id,
      onRequest: function(){
        isQuickViewRequested = 1;
        SmoothboxSEAO.open("<div id='productQuickViewContainer' class='sr_sitestoreproduct_profile_loading_image'></div>");
      },
      method : 'GET',
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) 
      {
        isQuickViewRequested = 0;
        SmoothboxSEAO.close();
        SmoothboxSEAO.open("<div id='productQuickViewContainer' class='sitestoreproduct_quick_view'><div class='popup_close fright' onclick='SmoothboxSEAO.close()'></div>"+responseHTML+"</div>");
      }
    })
    );
  }
}

/**
 * Magnify product profile picture
 */
var tempImageHref = '';
var tempFlag = true;
var tempImgWidth;
var tempImgHeight;
var productProfileImg = new Image();
var notShowImageInLightBox = '';
var tempImageZoomWidth = 380;
var tempImageZoomHeight = 480;
function sitestoreproductProfileImageMagnify()
{
  if( tempFlag )
  {
    tempFlag = false;
    tempImageHref = $('sitestoreproduct_product_image_zoom').getProperty('href');
    $('sitestoreproduct_product_image_zoom').getElements('.sitestoreproduct_magnify_image_main').destroy();
    document.body.getElements('.sitestoreproduct_magnify_main').destroy();
    productProfileImg.src = $('sitestoreproduct_product_image_zoom').getProperty('href');
    tempImgWidth = productProfileImg.width;
    tempImgHeight = productProfileImg.height;
    $("product_profile_magnify_message").style.display = 'none';
    if( tempImgWidth >= tempImageZoomWidth && tempImgHeight >= tempImageZoomHeight )
    {
      $("product_profile_magnify_message").style.display = 'none';
      new SitestoreproductZoom({
        'notShowImageInLightBox' : notShowImageInLightBox,
         classes: {
          sitestoreproductMagnifyRoll:'sitestoreproduct_magnify_search_main',
          sitestoreproductMagnifyRollImage  : 'sitestoreproduct_magnify_image_main',
          sitestoreproductMagnifiedImage: 'sitestoreproduct_magnify_main'
        }
      });
    }
  }
}

/**
 * Change product profile picture
 */
function changeProfilePicture(thisObj, newImageUrl, photo_id, changeProfilePictureFlag)
{
  tempFlag = true;
  document.body.getElements('.sitestoreproduct_magnify_main').destroy();
  if( changeProfilePictureFlag ) {      
    var onclickEventUrl = thisObj.getProperty('onclick');
// //    $('sitestoreproduct_product_image_zoom').setProperty('onclick', onclickEventUrl);
    $('sitestoreproduct_product_image_zoom').setProperty('onclick', onclickEventUrl);
  }else{
    $('sitestoreproduct_product_profile_crousal_' + photo_id).addEvent('click', function(e){
      e.stop();
    });
  }

  notShowImageInLightBox = changeProfilePictureFlag;
  var hrefPropertyUrl = thisObj.getProperty('href');
 
    $('product_profile_picture').setProperty('src', newImageUrl);
    $('sitestoreproduct_product_image_zoom').setProperty('href', hrefPropertyUrl);
 
//  if(SmoothboxSEAO.active){
//    SmoothboxSEAO.wrapper.getElementById('product_profile_picture').setProperty('src', newImageUrl);
//    SmoothboxSEAO.wrapper.getElementById('sitestoreproduct_product_image_zoom').setProperty('href', hrefPropertyUrl);
//  }else{
//    $('product_profile_picture').setProperty('src', newImageUrl);
//    $('sitestoreproduct_product_image_zoom').setProperty('href', hrefPropertyUrl);
//  }
  
  productProfileImg.src = hrefPropertyUrl;
  setTimeout(function(){
  if( productProfileImg.width >= tempImageZoomWidth && productProfileImg.height >= tempImageZoomHeight )
    $("product_profile_magnify_message").style.display = 'block';
  else
    $("product_profile_magnify_message").style.display = 'none';},100)
}

/**
 * Notify to seller about out of stock of product
 */
function notifyToSeller(product_id)
{
  var isValidEmail = false;
  if( $("notify_to_seller_email").value == '' )
    $("notify_to_me_email_error").style.display = 'block';
  else
  {
    var emailText = document.getElementById('notify_to_seller_email').value;
    var pattern = /^[a-zA-Z0-9\-_]+(\.[a-zA-Z0-9\-_]+)*@[a-z0-9]+(\-[a-z0-9]+)*(\.[a-z0-9]+(\-[a-z0-9]+)*)*\.[a-z]{2,4}$/;
    if (pattern.test(emailText)) 
    {
      $("notify_to_me_email_error").style.display = 'none';
      isValidEmail = true;
    }
    else 
    {
      $("notify_to_me_email_error").style.display = 'block';
      return;
    }
  }
  
  // IF VIEWER ENTER A VALID EMAIL ADDRESS, THEN ONLY SEND MAIL TO SELLER
  if( isValidEmail )
  {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitestoreproduct/product/notify-to-seller/product_id/'+product_id,
      onRequest: function(){
        $('notify_to_me_loading').innerHTML = '<img src="'+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif" />';
      },
      data : {
        'format' : 'json',
        'buyer_email' : $("notify_to_seller_email").value
      },
      onSuccess: function(responseJSON)
      {
        $('notify_to_me_loading').removeClass('loading');
        if( responseJSON.successMessage )
          $("notify_to_seller").innerHTML = responseJSON.successMessage;
      }
      }
    ))
  }
}

/**
 * Confirmation of product remove from my cart widget
 */
function confirmRemoveProduct(product_id)
{
  document.getElementById('sitestoreproduct_cart_product_'+product_id).style.display = 'none';
  document.getElementById('sitestoreproduct_delete_cart_product_'+product_id).style.display = 'block';
}

/**
 * Delete product from viewer cart
 */
function deleteProduct(value, product_id)
{
  if( value == 0 )
  {
    document.getElementById('sitestoreproduct_cart_product_'+product_id).style.display = 'block';
    document.getElementById('sitestoreproduct_delete_cart_product_'+product_id).style.display = 'none';
    document.getElementById('confirm_no').checked = false;
  }
  else if( value == 1 )
  {
    document.getElementById("confirm_delete_message_" + product_id).innerHTML = '<center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loader.gif" width="20" height="15"  /></center>';
    var index_id = '';
    var is_array = '';

    if( $('config_index_id') && $('config_is_array') )
    {
      index_id = $('config_index_id');
      is_array = $('config_is_array');
    }

    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sitestoreproduct/product/delete-cart-product',
      method: 'GET',
      data : {
        format : 'json',
        'index_id' : index_id,
        'is_array' : is_array,
        'cartProductId' : product_id
      },    
      onSuccess : function(responseJSON)  {
        document.getElementById("confirm_delete_message_" + product_id).innerHTML = '';
        document.getElementById("deleted_tip_message_" + product_id).style.display = 'block';
      }
      })
    )
  }
}

//function checkProductAvailability(product_id)
//{
//  var productQuantity = $("product_quantity_box").value;
//  
//  if( productQuantity == '' )
//  {
//    document.getElementById("product_quantity_box_message").style.display = 'block';
//    return;
//  }
//  
//  en4.core.request.send(new Request.JSON({
//      url : en4.core.baseUrl + 'sitestoreproduct/product/check-product-availability',
//      onRequest: function(){
//        document.getElementById('product_quantity_box_spinner').innerHTML = '<img src="application/modules/Sitestoreproduct/externals/images/loading.gif" />';
//      },
//      data : {
//        format : 'json',
//        product_id : product_id,
//        product_quantity : productQuantity
//      },    
//      onSuccess : function(responseJSON)  {
//        document.getElementById('product_quantity_box_spinner').innerHTML = '';
//        document.getElementById("product_quantity_box_message").style.display = 'block';
//        document.getElementById("product_quantity_box_message").innerHTML = responseJSON.productAvailabilityMessage;
//      }
//      })
//    )
//}

var timer;
var totalSeconds;

function createTimer(timerId, time) 
{
  timer = document.getElementById(timerId);
  totalSeconds = time;

  window.setInterval("tick()", 1000);
}

function tick() 
{
  if (totalSeconds <= 0) 
    return;

  totalSeconds -= 1;
  updateTimer();
}

function updateTimer() 
{
  var seconds = totalSeconds;

  var days = Math.floor(seconds / 86400);
  seconds -= days * 86400;

  var hours = Math.floor(seconds / 3600);
  seconds -= hours * (3600);

  var minutes = Math.floor(seconds / 60);
  seconds -= minutes * (60);

//  en4.core.language.translate(array('%s day', '%s days', days), Number(days));
  var timerStr = ((days > 0) ? days + en4.core.language.translate(" days ") : "") + LeadingZero(hours) + ":" + LeadingZero(minutes) + ":" + LeadingZero(seconds) + en4.core.language.translate(" hrs");
  timer.innerHTML = timerStr;
}

function LeadingZero(time) 
{
  return (time < 10) ? "0" + time : + time;
}

var initializeCalendarDate = function(seao_dateFormat, starttimeObj, endtimeObj, starttime, endtime, showCurrentTime, currentTime) {  

  var cal_starttime_date;
  if( seao_dateFormat == 'dmy' )
    cal_starttime_date = en4.seaocore.covertdateDmyToMdy($(starttime + '-date').value);
  else
    cal_starttime_date = $(starttime + '-date').value;
  
  endtimeObj.calendars[0].start = new Date( cal_starttime_date );
  // redraw calendar
  endtimeObj.navigate(endtimeObj.calendars[0], 'm', 1);
  endtimeObj.navigate(endtimeObj.calendars[0], 'm', -1);

  // check start date and make it the same date if it's too	

  if( showCurrentTime )
    starttimeObj.calendars[0].start = new Date( currentTime );
  else
    starttimeObj.calendars[0].start = new Date( cal_starttime_date );
  // redraw calendar
  starttimeObj.navigate(starttimeObj.calendars[0], 'm', 1);
  starttimeObj.navigate(starttimeObj.calendars[0], 'm', -1);
  starttimeObj.changed(starttimeObj.calendars[0]);
};

var cal_starttimeDate_onHideStart = function(seao_dateFormat, starttimeObj, endtimeObj, starttime, endtime) {

  var cal_starttime_date;
  var cal_endtime_date;
  // check end date and make it the same date if it's too
  if( seao_dateFormat == 'dmy' ) {
    cal_starttime_date = en4.seaocore.covertdateDmyToMdy($(starttime+'-date').value);
    cal_endtime_date = en4.seaocore.covertdateDmyToMdy($(endtime+'-date').value);
  } else {
    cal_starttime_date = $(starttime+'-date').value;
    cal_endtime_date = $(endtime+'-date').value;
  }

  // check end date and make it the same date if it's too
  endtimeObj.calendars[0].start = new Date( cal_starttime_date);
  // redraw calendar
  endtimeObj.navigate(endtimeObj.calendars[0], 'm', 1);
  endtimeObj.navigate(endtimeObj.calendars[0], 'm', -1);

  //CHECK IF THE END TIME IS LESS THEN THE START TIME THEN CHANGE IT TO THE START TIME.
   var startdatetime = new Date(cal_starttime_date);
   var enddatetime = new Date(cal_endtime_date);
   if(startdatetime.getTime() > enddatetime.getTime()) {
     $(endtime+'-date').value = $(starttime+'-date').value;
     $('calendar_output_span_'+endtime+'-date').innerHTML = $(endtime+'-date').value;
     endtimeObj.changed(endtimeObj.calendars[0]);
   }
};
  
// START WORK FOR CART RELATED FUNCTIONS FOR OTHER MODULES LIKE SITEMENU AND SITETHEME
function removeCartProduct(cartProductId, index_id, is_array, isOtherModule) {
  if( is_array == 1 )
    document.getElementById("sitestoreproduct_notification_" + cartProductId + "_" + index_id).innerHTML = '<ul class="notifications_menu"><li><div class="sitestoreproduct_mini_cart_loading"><img src="'+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loader.gif" width="20" height="15"  /></div></li></ul>';
  else
    document.getElementById("sitestoreproduct_notification_" + cartProductId).innerHTML = '<div class="sr_sitestoreproduct_profile_loading_image" style="height:60px;padding:0px;"></div>';
  
  var request = new Request.JSON({
    url : en4.core.baseUrl + 'sitestoreproduct/product/delete-cart-product',
    method: 'GET',
    data : {
      format : 'json',
      'cartProductId' : cartProductId,
      'index_id' : index_id,
      'is_array' : is_array
    },    
    onSuccess : function(responseJson) {
      var cartProductCountReq = new Request.HTML({
        url : en4.core.baseUrl + 'sitestoreproduct/product/get-cart-products/isOtherModule/'+isOtherModule,
        method : 'POST',
        data : {
          format : 'html',
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          if( isOtherModule == 1 && document.getElementById('sitemenu_mini_cart_pulldown_contents') )
            document.getElementById('sitemenu_mini_cart_pulldown_contents').innerHTML = responseHTML;
          else if( isOtherModule == 2 && document.getElementById('sitetheme_mini_cart_pulldown_contents') )
            document.getElementById('sitetheme_mini_cart_pulldown_contents').innerHTML = responseHTML;
        }
      }
    );
    cartProductCountReq.send();    
    }
  });
  request.send();    
}

function showCartProductCount(cartProductCount) {
  if( $("main_menu_cart_item_count") || $("new_item_count") ) {
    if( cartProductCount ) {
      if( $("main_menu_cart_item_count") ) {
        if( !$("main_menu_cart_item_count").hasClass('seaocore_pulldown_count') )
          $("main_menu_cart_item_count").addClass('seaocore_pulldown_count');
        $("main_menu_cart_item_count").style.display = 'block';
        $("main_menu_cart_item_count").innerHTML = cartProductCount;
      }

      if( $("new_item_count") ) {
        if( !$("new_item_count").hasClass('seaocore_pulldown_count') )
          $("new_item_count").addClass('seaocore_pulldown_count');
        $("new_item_count").style.display = 'block';
        $("new_item_count").innerHTML = cartProductCount;
      }
    } else {
      if( $("main_menu_cart_item_count") ) $("main_menu_cart_item_count").style.display = 'none';
      if( $("new_item_count") ) $("new_item_count").style.display = 'none';
    }
  }
}
// END WORK FOR CART RELATED FUNCTIONS FOR OTHER MODULES LIKE SITEMENU AND SITETHEME

/* $Id: core.js 2013-09-02 00:00:00Z SocialEngineAddOns Copyright 2012-2013 BigStep Technologies Pvt. Ltd. $ */

en4.sitestorealbum = {

  composer : false,

  getComposer : function(){ 
    if( !this.composer ){
      this.composer = new en4.sitestorealbum.compose();
    }

    return this.composer;
  }

};



en4.sitestorealbum.compose = new Class({

  Extends : en4.activity.compose.icompose,

  name : 'sitestorephoto',

  active : false,

  options : {},

  frame : false,

  photo_id : false,

  initialize : function(element, options){ 
    if( !element ) element = $('activity-compose-sitestorephoto');
    this.parent(element, options);
  },
  
  activate : function(){
    this.parent();
    this.element.style.display = '';
    $('activity-compose-sitestorephoto-input').style.display = '';
    $('activity-compose-sitestorephoto-loading').style.display = 'none';
    $('activity-compose-sitestorephoto-preview').style.display = 'none';
    $('activity-form').addEvent('beforesubmit', this.checkSubmit.bind(this));
    this.active = true;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'none';
  },

  deactivate : function(){
    if( !this.active ) return;
    this.active = false
    this.photo_id = false;
    if( this.frame ) this.frame.destroy();
    this.frame = false;
    $('activity-compose-sitestorephoto-preview').empty();
    $('activity-compose-sitestorephoto-input').style.display = '';
    this.element.style.display = 'none';
    $('activity-form').removeEvent('submit', this.checkSubmit.bind(this));;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'block';
    $('activity-compose-sitestorephoto-activate').style.display = '';
    $('activity-compose-link-activate').style.display = '';
  },

  process : function(){
    if( this.photo_id ) return;
    
    if( !this.frame ){
      this.frame = new IFrame({
        src : 'about:blank',
        name : 'albumSitestoreComposeFrame',
        styles : {
          display : 'none'
        }
      });
      this.frame.inject(this.element);
    }

    $('activity-compose-sitestorephoto-input').style.display = 'none';
    $('activity-compose-sitestorephoto-loading').style.display = '';
    $('activity-compose-sitestorephoto-form').target = 'albumSitestoreComposeFrame';
    $('activity-compose-sitestorephoto-form').submit();
  },

  processResponse : function(responseObject){
    if( this.photo_id ) return;
    
    (new Element('img', {
      src : responseObject.src,
      styles : {
        //'max-width' : '100px'
      }
    })).inject($('activity-compose-sitestorephoto-preview'));
    $('activity-compose-sitestorephoto-loading').style.display = 'none';
    $('activity-compose-sitestorephoto-preview').style.display = '';
    this.photo_id = responseObject.photo_id;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'block';
    $('activity-compose-sitestorephoto-activate').style.display = 'none';
    $('activity-compose-link-activate').style.display = 'none';
  },

  checkSubmit : function(event)
  {
    if( this.active && this.photo_id )
    {
      //event.stop();
      $('activity-form').attachment_type.value = 'sitestore_photo';
      $('activity-form').attachment_id.value = this.photo_id;
    }
  }
  
});

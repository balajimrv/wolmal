
(function() {
var $ = 'id' in document ? document.id : window.$;
Composer.Plugin.Album = new Class({

  Extends : Composer.Plugin.Interface,

  name : 'album',

  options : {
    title : 'Add Album',
    lang : {},
    requestOptions : false,
  },

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },
});
})();

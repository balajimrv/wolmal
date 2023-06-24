/* $Id: composer.js 2012-26-01 00:00:00Z SocialEngineAddOns Copyright 2011-2012 BigStep Technologies Pvt.
 Ltd. $ */



(function() { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;
  Composer = new Class({
    Implements: [Events, Options],
    elements: {},
    plugins: {},
    autogrow: null,
    highlighterBody: null,
    hiddenBody: null,
    options: {
      lang: {},
      overText: true,
      allowEmptyWithoutAttachment: false,
      allowEmptyWithAttachment: true,
      hideSubmitOnBlur: true,
      submitElement: false,
      userPhoto: false,
    },
    highlighterText: '',
    initialize: function(element, options) {
      this.setOptions(options);
      this.elements = new Hash(this.elements);
      this.plugins = new Hash(this.plugins);
      this.elements.textarea = $(element);
      this.elements.textarea.store('Composer');
      this.attach();
      this.getTray();
      this.getMenu();
      this.pluginReady = false;

      this.getForm().addEvent('submit', function(e) {
        this.fireEvent('editorSubmit');
        if (this.pluginReady) {
          if (!this.options.allowEmptyWithAttachment && this.getContent() == '') {
            e.stop();
            return;
          }
        } else {
          if (!this.options.allowEmptyWithoutAttachment && this.getContent() == '') {
            e.stop();
            return;
          }
        }
        this.saveContent();
      }.bind(this));
    },
    getMenu: function() {
      if (!$type(this.elements.menu)) {
        this.elements.menu = $try(function() {
          return $(this.options.menuElement);
        }.bind(this));
        if (!$type(this.elements.menu)) {
          this.elements.menu = new Element('div', {
            'id': 'compose-menu',
            'class': 'compose-menu'
          }).inject(this.getForm(), 'after');
        }
      }
      return this.elements.menu;
    },
    getActivatorContent: function() {
      if (!$type(this.elements.activatorContent)) {
        this.elements.activatorContent = $try(function() {
          return $(this.options.activatorContent);
        }.bind(this));
        if (!$type(this.elements.activatorContent)) {
          this.elements.menu = new Element('div', {
            'id': 'compose-activator-content',
            'class': 'adv_post_compose_menu'
          }).inject(this.getForm(), 'after');
        }
      }
      return this.elements.activatorContent;
    },
    getTray: function() {
      if (!$type(this.elements.tray)) {
        this.elements.tray = $try(function() {
          return $(this.options.trayElement);
        }.bind(this));
        if (!$type(this.elements.tray)) {
          this.elements.tray = new Element('div', {
            'id': 'compose-tray',
            'class': 'compose-tray',
            'styles': {
              'display': 'none'
            }
          }).inject(this.getForm(), 'after');
        }
      }
      return this.elements.tray;
    },
    getInputArea: function() {
      if (!$type(this.elements.inputarea)) {
        var form = this.getForm();
        this.elements.inputarea = new Element('div', {
          'styles': {
            'display': 'none'
          }
        }).inject(form);
      }
      return this.elements.inputarea;
    },
    getForm: function() {
      return this.elements.textarea.getParent('form');
    },
    // Editor
    attach: function() {
      // Create container
      this.elements.container = new Element('div', {
        'id': 'compose-container',
        'class': 'compose-container',
        'styles': {
        }
      });
      this.elements.container.wraps(this.elements.textarea);
      // Create body
      if (this.options.userPhoto) {
        this.elements.photo = new Element('div', {
          'class': 'composer-adv-photo',
          'html': this.options.userPhoto,
          'styles': {
            'display': 'inline-block',
            'position': 'absolute'
          }
        }).inject(this.elements.container, 'top');
        this.elements.container.getParent().addClass('adv-photo');
      }

      this.elements.body = this.elements.textarea;
      this.addHighlighter();
      // Attach blur event
      var self = this;
      if (self.options.hideSubmitOnBlur) {
        this.getMenu().setStyle('display', 'none');
        this.elements.textarea.addEvent('focus', function(e) {
          if (!self.getForm().hasClass('adv-active')) {
            self.elements.body.set('html', '');
            self.getForm().addClass('adv-active');
            if (self.getActivatorContent().hasClass('adv_post_compose_menu_anactive'))
              self.getActivatorContent().removeClass('adv_post_compose_menu_anactive');
          }
          self.getMenu().setStyle('display', '');
        });
      }

      this.autogrow = new Composer.Autogrow(this.elements.body, this.highlighterBody);

      this.elements.body.designMode = 'On';
      ['MouseUp', 'MouseDown', 'ContextMenu', 'Click', 'Dblclick', 'KeyPress', 'KeyUp', 'KeyDown', 'Paste', 'Cut'].each(function(eventName) {
        var method = (this['editor' + eventName] || function() {
        }).bind(this);
        this.elements.body.addEvent(eventName.toLowerCase(), method);
      }.bind(this));
      this.setContent(this.elements.textarea.value);
//      if (this.options.overText) {
//        this.elements.textarea.placeholder = this._lang('Post Something...');
//      }

      this.fireEvent('attach', this);
    },
    detach: function() {
      this.saveContent();
      this.textarea.setStyle('display', '').removeClass('compose-textarea').inject(this.container, 'before');
      this.container.dispose();
      this.fireEvent('detach', this);
      return this;
    },
    focus: function() {
      // needs the delay to get focus working
      (function() {
        this.elements.textarea.setStyle('display', '');
        this.elements.textarea.focus();
        this.fireEvent('focus', this);
      }).bind(this).delay(10);
      return this;
    },
    getContent: function() {
      return this.cleanup(this.elements.textarea.get('value'));
    },
    setContent: function(newContent) {
      this.elements.textarea.set('value', newContent);
      this.autogrow.handle();
      this.setHighlighterContent();
      return this;
    },
    saveContent: function() {
      this.elements.textarea.set('value', this.getContent());
      return this;
    },
    cleanup: function(html) {
      // @todo
      return html
        .replace(/<(br|p|div)[^<>]*?>/ig, "\r\n")
        .replace(/<[^<>]+?>/ig, ' ');
    },
    getCaretPosition: function() {
      var caretPosition = 0;
      if (document.selection) {
        this.elements.textarea.focus();
        var Sel = document.selection.createRange();
        Sel.moveStart('character', -this.elements.textarea.value.length);
        caretPosition = Sel.text.length;
      } else if (this.elements.textarea.selectionStart || this.elements.textarea.selectionStart == '0') {
        caretPosition = this.elements.textarea.selectionStart;
      }
      return caretPosition;
    },
    setCaretPosition: function(pos) {
      if (this.elements.textarea.createTextRange) {
        var range = this.elements.textarea.createTextRange();
        range.move("character", pos);
        range.select();
      } else if (this.elements.textarea.selectionStart) {
        this.elements.textarea.focus();
        this.elements.textarea.setSelectionRange(pos, pos);
      }
    },
    addHighlighter: function() {
      var wapper = new Element('div', {
        'id': this.elements.body.id + '-highlighter-wapper',
        'class': 'compose-highlighter-wapper',
      }).inject(this.elements.container, 'before');

      this.highlighterBody = new Element('div', {
        'id': this.elements.body.id + '-hightlighter',
        'class': 'compose-highlighter',
        'styles': {
          'height': this.elements.body.getSize().y + 'px'
        }
      }).inject(wapper, 'bottom');

      this.hiddenBody = new Element('input', {
        'type': 'hidden'
      }).inject(wapper);

      this.addEvent('editorKeyUp', this.setHighlighterContent);
      this.elements.container.style.position = 'relative';
    },
    setHighlighterContent: function() {
      var content = this.getContent();
      this.highlighterText = content;
      this.highlighterSegment = content;
      this.fireEvent('editorHighlighter');
      this.highlighterBody.set('html', this.highlighterText);
      this.hiddenBody.set('value', this.highlighterSegment);
    },
    getHighlightString: function(str) {
      return '<span class="aaf_feed_composer_highlight_tag">' + str + '</span>';
    },
    // Add Emotion Icon
    attachEmotionIcon: function(iconCode) {
      var pos = this.getCaretPosition();
      var text = this.elements.textarea.get('value');
      var t1 = text.substr(0, pos);
      var t2 = text.substr(pos);
      this.setContent(t1 + iconCode + t2);
      this.setCaretPosition(pos + iconCode.length);
    },
    // Plugins

    addPlugin: function(plugin) {
      var key = plugin.getName();
      this.plugins.set(key, plugin);
      plugin.setComposer(this);
      return this;
    },
    addPlugins: function(plugins) {
      plugins.each(function(plugin) {
        this.addPlugin(plugin);
      }.bind(this));
    },
    getPlugin: function(name) {
      return this.plugins.get(name);
    },
    activate: function(name) {
      this.deactivate();
      this.getMenu().setStyle();
      this.plugins.get(name).activate();
    },
    deactivate: function() {
      this.plugins.each(function(plugin) {
        plugin.deactivate();
      });
      this.getTray().empty();
    },
    signalPluginReady: function(state) {
      this.pluginReady = state;
    },
    hasActivePlugin: function() {
      var active = false;
      this.plugins.each(function(plugin) {
        active = active || plugin.active;
      });
      return active;
    },
    // Key events
    editorMouseUp: function(e) {
      this.fireEvent('editorMouseUp', e);
    },
    editorMouseDown: function(e) {
      this.fireEvent('editorMouseDown', e);
    },
    editorContextMenu: function(e) {
      this.fireEvent('editorContextMenu', e);
    },
    editorClick: function(e) {
      // make images selectable and draggable in Safari
      if (Browser.Engine.webkit) {
        var el = e.target;
        if (el.get('tag') == 'img') {
          this.selection.selectNode(el);
        }
      }

      this.fireEvent('editorClick', e);
    },
    editorDoubleClick: function(e) {
      this.fireEvent('editorDoubleClick', e);
    },
    editorKeyPress: function(e) {
      this.keyListener(e);
      this.fireEvent('editorKeyPress', e);
    },
    editorKeyUp: function(e) {
      this.fireEvent('editorKeyUp', e);
    },
    editorKeyDown: function(e) {

      this.fireEvent('editorKeyDown', e);
    },
    editorPaste: function(e) {
      getLinkContent();
      this.fireEvent('editorPaste', e);
    },
    keyListener: function(e) {

    },
    _lang: function() {
      try {
        if (arguments.length < 1) {
          return '';
        }

        var string = arguments[0];
        if ($type(this.options.lang) && $type(this.options.lang[string])) {
          string = this.options.lang[string];
        }

        if (arguments.length <= 1) {
          return string;
        }

        var args = new Array();
        for (var i = 1, l = arguments.length; i < l; i++) {
          args.push(arguments[i]);
        }

        return string.vsprintf(args);
      } catch (e) {
        alert(e);
      }
    },
    _supportsContentEditable: function() {
      return false;
    }
  });

  Composer.Autogrow = new Class({
    Implements: [Events, Options],
    resizing: false,
    element: null,
    highlighter: null,
    process: false,
    initialize: function(element, highlighter) {
      this.element = element;
      this.highlighter = highlighter;
      this.setStyles();
      this.attach();
      this.handle();
    },
    setStyles: function() {
      this.element.setStyles({
        'overflow-x': 'auto',
        'overflow-y': 'hidden',
        '-mox-box-sizing': 'border-box',
        '-ms-box-sizing': 'border-box',
        'resize': 'none'
      });
    },
    handle: function() {
      if (this.process) {
        return;
      }
      this.process = true;
      this.resetHeight();
      if (Browser.Engine.webkit || Browser.Engine.gecko) {
        this.shrink();
      }
      this.process = false;
    },
    resetHeight: function() {
      if (this.element.getScrollSize().y) {
        var newHeight = this.getHeight();
        if (newHeight !== this.element.getSize().y) {
          var height = newHeight + 'px';
          this.element.setStyles({
            maxHeight: height,
            height: height
          });
          this.setHighlighterHeight(height);
        }
      } else {
        this.element.setStyles({
          maxHeight: '',
          height: 'auto'
        });
        this.element.rows = (this.element.value.match(/(\r\n?|\n)/g) || []).length + 1;
      }
    },
    setHighlighterHeight: function(height) {
      if (this.highlighter) {
        this.highlighter.setStyles({
          maxHeight: height,
          height: height
        });
      }
    },
    shrink: function() {
      var useNullHeightShrink = true;
      if (useNullHeightShrink) {
        this.element.style.height = '0px';
        this.resetHeight();
      } else {
        var scrollHeight = this.element.getScrollSize().y;
        var paddingBottom = this.element.getStyle('padding-bottom').toInt();
        this.element.style.paddingBottom = paddingBottom + 1 + "px";
        var newHeight = this.getHeight() - 1;
        if (this.element.getStyle('max-height').toInt() != newHeight) {
          this.element.style.paddingBottom = paddingBottom + scrollHeight + "px";
          this.element.scrollTop = 0;
          var h = _getHeight() - scrollHeight + "px";
          this.element.style.maxHeight = h;
        }
        this.element.style.paddingBottom = paddingBottom + 'px';
      }
    },
    attach: function() {
      ['keyup', 'focus', 'paste', 'cut'].each(function(eventName) {
        this.element.addEvent(eventName, this.handle.bind(this));
      }.bind(this));
      if (Browser.Engine.webkit || Browser.Engine.trident) {
        this.element.addEvent('scroll', this.handle.bind(this));
      }
    },
    getHeight: function() {
      var height = this.element.getScrollSize().y;
      if (Browser.Engine.gecko || Browser.Engine.trident) {
        height += this.element.offsetHeight - this.element.clientHeight;
      } else if (Browser.Engine.webkit) {
        height += this.element.getStyle('border-top-width').toInt() + this.element.getStyle('border-bottom-width').toInt();
      } else if (Browser.Engine.presto) {
        height += this.element.getStyle('padding-bottom').toInt();
      }
      return height;
    }
  });


  Composer.Plugin = {};
  Composer.Plugin.Interface = new Class({
    Implements: [Options, Events],
    name: 'interface',
    active: false,
    composer: false,
    options: {
      loadingImage: en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif'
    },
    elements: {},
    persistentElements: ['activator', 'loadingImage'],
    params: {},
    initialize: function(options) {
      this.params = new Hash();
      this.elements = new Hash();
      this.reset();
      this.setOptions(options);
    },
    getName: function() {
      return this.name;
    },
    setComposer: function(composer) {
      this.composer = composer;
      this.attach();
      return this;
    },
    getComposer: function() {
      if (!this.composer)
        throw "No composer defined";
      return this.composer;
    },
    attach: function() {
      this.reset();
    },
    detach: function() {
      this.reset();
      if (this.elements.activator) {
        this.elements.activator.destroy();
        this.elements.erase('menu');
      }
    },
    reset: function() {
      this.elements.each(function(element, key) {
        if ($type(element) == 'element' && !this.persistentElements.contains(key)) {
          element.destroy();
          this.elements.erase(key);
        }
      }.bind(this));
      this.params = new Hash();
      this.elements = new Hash();
    },
    activate: function() {
      if (this.active)
        return;
      this.active = true;
      this.reset();
      this.getComposer().getTray().setStyle('display', '');
      this.getComposer().getMenu().setStyle('display', 'none');
      var submitButtonEl = $(this.getComposer().options.submitElement);
      if (submitButtonEl) {
        submitButtonEl.setStyle('display', 'none');
      }

      this.getComposer().getMenu().setStyle('border', 'none');
      this.getComposer().getForm().addClass('adv-active');
      this.getComposer().getActivatorContent().addClass('adv_post_compose_menu_anactive');
      this.getComposer().plugins.each(function(plugin) {
        if (plugin.name != 'advanced_facebook' && plugin.name != 'advanced_twitter' && plugin.name != 'advanced_linkedin' && plugin.name != 'advanced_instagram')
          plugin.active = true;
      });
      if (this.getName() == "questionpoll" || this.getName() == "question") {
        this.getComposer().getTray().inject(this.getComposer().getForm(), "after");
      }
      switch ($type(this.options.loadingImage)) {
        case 'element':
          break;
        case 'string':
          this.elements.loadingImage = new Asset.image(this.options.loadingImage, {
            'id': 'compose-' + this.getName() + '-loading-image',
            'class': 'compose-loading-image'
          });
          break;
        default:
          this.elements.loadingImage = new Asset.image('loading.gif', {
            'id': 'compose-' + this.getName() + '-loading-image',
            'class': 'compose-loading-image'
          });
          break;
      }
    },
    deactivate: function() {
      if (!this.active)
        return;
      this.active = false;
      this.reset();
      this.getComposer().getTray().setStyle('display', 'none');
      this.getComposer().getMenu().setStyle('display', '');
      var submitButtonEl = $(this.getComposer().options.submitElement);
      if (submitButtonEl) {
        submitButtonEl.setStyle('display', '');
      }
      this.getComposer().getActivatorContent().removeClass('adv_post_compose_menu_anactive');
      if (this.getName() == "questionpoll" || this.getName() == "question") {
        this.getComposer().getTray().inject(this.getComposer().getMenu(), "before");
      }

      this.getComposer().getMenu().set('style', '');
      this.getComposer().signalPluginReady(false);
    },
    ready: function() {
      this.getComposer().signalPluginReady(true);
      this.getComposer().getMenu().setStyle('display', '');
      var submitEl = $(this.getComposer().options.submitElement);
      if (submitEl) {
        submitEl.setStyle('display', '');
      }
    },
    // Utility

    makeActivator: function() {
      if (!this.elements.activator) {
        this.elements.activator = new Element('span', {
          'id': 'compose-' + this.getName() + '-activator',
          'class': 'compose-activator',
          'href': 'javascript:void(0);',
          'events': {
            'click': this.activate.bind(this)
          }
        }).inject(this.getComposer().getActivatorContent().getElement(".aaf_activaor_end"), "before");
        create_tooltip(this).inject(this.elements.activator);
      }
    },
    makeMenu: function() {
      if (!this.elements.menu) {
        var tray = this.getComposer().getTray();
        this.elements.menu = new Element('div', {
          'id': 'compose-' + this.getName() + '-menu',
          'class': 'compose-menu'
        }).inject(tray);
        this.elements.menuTitle = new Element('span', {
          'html': this._lang(this.options.title) + ' ('
        }).inject(this.elements.menu);
        this.elements.menuClose = new Element('a', {
          'href': 'javascript:void(0);',
          'html': this._lang('cancel'),
          'events': {
            'click': function(e) {
              e.stop();
              this.getComposer().deactivate();
            }.bind(this)
          }
        }).inject(this.elements.menuTitle);
        this.elements.menuTitle.appendText(')');
        if (showVariousTabs == 0 && this.getName() == 'photo' && typeof sitealbumInstalled != 'undefined') {

          this.elements.albumMenu = new Element('div', {
            'id': 'compose-album-' + this.getName() + '-menu',
            'class': 'compose-menu'
          }).inject(tray);
          this.elements.albumMenuseperator = new Element('span', {
            'class': 'aaf_media_sep',
          }).inject(this.elements.albumMenu);
          this.elements.albumMenuseperator.innerHTML = en4.core.language.translate("OR");
          if(showAddPhotoInLightbox) {
          this.elements.albumMenuTitle = new Element('a', {
            'href': 'javascript:void(0)',
            'class': 'seao_smoothbox item_icon_photo',
            'html': this._lang(this.options.albumTitle),
            'events': {
              'click': function(e) {
                e.stop();
                SmoothboxSEAO.open({
                  class: 'seao_add_photo_lightbox',   
                  request: {
                    url: en4.core.baseUrl + 'albums/upload/'
                  }
                });
              }.bind(this)
            }
          }).inject(this.elements.albumMenu);
      } else {
          this.elements.albumMenuTitle = new Element('a', {
            'href': 'javascript:void(0)',
            'class': 'item_icon_photo',
            'html': this._lang(this.options.albumTitle),
            'events': {
              'click': function(e) {
                e.stop();
                
                window.location.href= en4.core.baseUrl + 'albums/upload/';
            
              }.bind(this)
            }
          }).inject(this.elements.albumMenu);
      }
        }
      }
    },
    makeBody: function() {
      if (!this.elements.body) {
        var tray = this.getComposer().getTray();
        this.elements.body = new Element('div', {
          'id': 'compose-' + this.getName() + '-body',
          'class': 'compose-body'
        }).inject(tray);
      }
    },
    makeLoading: function(action) {
      if (!this.elements.loading) {
        if (action == 'empty') {
          this.elements.body.empty();
        } else if (action == 'hide') {
          this.elements.body.getChildren().each(function(element) {
            element.setStyle('display', 'none')
          });
        } else if (action == 'invisible') {
          this.elements.body.getChildren().each(function(element) {
            element.setStyle('height', '0px').setStyle('visibility', 'hidden')
          });
        }

        this.elements.loading = new Element('div', {
          'id': 'compose-' + this.getName() + '-loading',
          'class': 'compose-loading'
        }).inject(this.elements.body);
        var image = this.elements.loadingImage || (new Element('img', {
          'id': 'compose-' + this.getName() + '-loading-image',
          'class': 'compose-loading-image'
        }));
        image.inject(this.elements.loading);
        new Element('span', {
          'html': this._lang('Loading...')
        }).inject(this.elements.loading);
      }
    },
    makeError: function(message, action) {
      if (!$type(action))
        action = 'empty';
      message = message || 'An error has occurred';
      message = this._lang(message);
      this.elements.error = new Element('div', {
        'id': 'compose-' + this.getName() + '-error',
        'class': 'compose-error',
        'html': message
      }).inject(this.elements.body);
    },
    makeFormInputs: function(data) {
      this.ready();
      this.getComposer().getInputArea().empty();
      data.type = this.getName();
      $H(data).each(function(value, key) {
        this.setFormInputValue(key, value);
      }.bind(this));
    },
    setFormInputValue: function(key, value) {
      var elName = 'attachmentForm' + key.capitalize();
      if (!this.elements.has(elName)) {
        this.elements.set(elName, new Element('input', {
          'type': 'hidden',
          'name': 'attachment[' + key + ']',
          'value': value || ''
        }).inject(this.getComposer().getInputArea()));
      }
      this.elements.get(elName).value = value;
    },
    _lang: function() {
      try {
        if (arguments.length < 1) {
          return '';
        }

        var string = arguments[0];
        if ($type(this.options.lang) && $type(this.options.lang[string])) {
          string = this.options.lang[string];
        }

        if (arguments.length <= 1) {
          return string;
        }

        var args = new Array();
        for (var i = 1, l = arguments.length; i < l; i++) {
          args.push(arguments[i]);
        }

        return string.vsprintf(args);
      } catch (e) {
        alert(e);
      }
    }

  });
  //http://mootools-users.660466.n2.nabble.com/Moo-onPaste-td4655487.html
  $extend(Element.NativeEvents, {
    'paste': 2,
    'input': 2
  });
  Element.Events.paste = {
    base: (Browser.Engine.presto || (Browser.Engine.gecko && Browser.Engine.version < 19)) ? 'input' : 'paste',
    condition: function(e) {
      this.fireEvent('paste', e, 1);
      return false;
    }
  };
})(); // END NAMESPACE

var create_tooltip = function(plugin_temp) {
  return new Element('p', {
    'class': 'adv_post_compose_menu_show_tip adv_composer_tip',
    'html': plugin_temp.options.title + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png">'
  });
};
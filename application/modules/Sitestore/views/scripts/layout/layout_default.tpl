<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: layout.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitestore/externals/styles/storelayout.css'); 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">
	var hideWidgetIds=new Array();
  window.addEvent ('domready', function () {
		if ($$('.storelayout_layoutbox_header')) {
      <?php $var = $this->translate('Global Header'); ?>
			$('global_content').getElement('.storelayout_layoutbox_header').innerHTML = '<span><?php echo $var ?></span>'

    }
   
		if ($$('.storelayout_layoutbox_footer')) {
      <?php $var1 = $this->translate('Global Footer'); ?>
			$('global_content').getElement('.storelayout_layoutbox_footer').innerHTML = '<span><?php echo $var1 ?></span>'

    }	
  });
  var NestedDragMove = new Class({
    Extends : Drag.Move,
    
    checkDroppables: function(){
      //var overed = this.droppables.filter(this.checkAgainst, this).getLast();
      var overedMulti = this.droppables.filter(this.checkAgainst, this);
      
      // Pick the smallest one
      var overed;
      var smallestOvered = false;
      var overedSizes = [];
      overedMulti.each(function(currentOvered, index) {
        var overedSize = currentOvered.getSize().x * currentOvered.getSize().y;
        if( smallestOvered === false || overedSize < smallestOvered ) {
          overed = currentOvered;
          smallestOvered = overedSize;
        }
      });

      if (this.overed != overed){
        if (this.overed) {
          this.fireEvent('leave', [this.element, this.overed]);
        }
        if (overed) {
          this.fireEvent('enter', [this.element, overed]);
        }
        this.overed = overed;
      }
    }
  });
  
  var NestedSortables = new Class({
    Extends : Sortables,

    getDroppables: function(){
            var droppables = this.list.getChildren();
            if (!this.options.constrain) {
              droppables = this.lists.concat(droppables);
              if( !this.list.hasClass('sortablesForceInclude') ) droppables.erase(this.list);
            }
            return droppables.erase(this.clone).erase(this.element);
    },
    
    start: function(event, element){
            if (!this.idle) return;
            for(var i=0; i< hideWidgetIds.length;i++){
             if(element.getAttribute('id') ==hideWidgetIds[i]){
                return;
             }
           }
            this.idle = false;
            this.element = element;
            this.opacity = element.get('opacity');
            this.list = element.getParent();
            this.clone = this.getClone(event, element);

            this.drag = new NestedDragMove(this.clone, {
                    snap: this.options.snap,
                    container: this.options.constrain && this.element.getParent(),
                    droppables: this.getDroppables(),
                    onSnap: function(){
                            event.stop();
                            this.clone.setStyle('visibility', 'visible');
                            this.element.set('opacity', this.options.opacity || 0);
                            this.fireEvent('start', [this.element, this.clone]);
                    }.bind(this),
                    onEnter: this.insert.bind(this),
                    onCancel: this.reset.bind(this),
                    onComplete: this.end.bind(this)
            });

            this.clone.inject(this.element, 'before');
            this.drag.start(event);
    },

    insert : function(dragging, element) {
      if( this.element.hasChild(element) ) return;
      //this.parent(dragging, element);
      
      //insert: function(dragging, element){
      var where = 'inside';
      if (this.lists.contains(element)){
        if( element.hasClass('sortablesForceInclude') && element == this.list ) return;
        this.list = element;
        this.drag.droppables = this.getDroppables();
      } else {
              where = this.element.getAllPrevious().contains(element) ? 'before' : 'after';
      }
      this.element.inject(element, where);
      this.fireEvent('sort', [this.element, this.clone]);
      //},
    }
  })
</script>

<script type="text/javascript">
  var currentStore = '<?php echo $this->store ?>';
  var newContentIndex = 1;
  var currentParent;
  var currentNextSibling;
  var contentByName = <?php echo Zend_Json::encode($this->contentByName) ?>;
  var currentModifications = [];
  var currentLayout = '<?php echo $this->storeObject->layout ?>';
  var ContentSortables;
  var ContentTooltips;

  window.onbeforeunload = function(event) {
    if( currentModifications.length > 0 ) {
      return '<?php echo $this->string()->escapeJavascript($this->translate(' - All unsaved changes to stores or widgets will be lost - ')) ?>'
      //return 'I\'m sorry Dave, I can\'t do that.';
    }
  }

  /* modifications */
  var pushModification = function(type) {
    if( !currentModifications.contains(type) ) {
      currentModifications.push(type);

      // Add CSS class for save button while active modifications
      if( type == 'info' ) {
        $('storelayout_layoutbox_menu_storeinfo').addClass('storelayout_content_modifications_active');
      } else if( type == 'main' ) {
        $('storelayout_layoutbox_menu_savechanges').addClass('storelayout_content_modifications_active');
      }
    }
  }

  var eraseModification = function(type) {
    currentModifications.erase(type);
    // Remove active notifications CSS class
      if( type == 'info' ) {
        $('storelayout_layoutbox_menu_storeinfo').removeClass('storelayout_content_modifications_active');
      } else if( type == 'main' ) {
        $('storelayout_layoutbox_menu_savechanges').removeClass('storelayout_content_modifications_active');
      }
  }
  /* Attach javascript to existing elements */
  window.addEvent('load', function() {
    // Add info
    $$('li.storelayout_content_draggable').each(function(element) {
      var elClass = element.get('class');
      var matches = elClass.match(/storelayout_content_widget_([^ ]+)/i);
      if( !$type(matches) || !$type(matches[1])) return;
      var name = matches[1];
      var info = contentByName[name] || {};

      element.store('contentInfo', info);

      // Add info for tooltips
      element.store('tip:title', info.title || 'Missing widget: ' + matches[1]);
      element.store('tip:text', info.description || 'Missing widget: ' + matches[1]);
    });

    // Monitor form inputs for changes
    $$('#storelayout_layoutbox_menu_storeinfo input').addEvent('change', function(event) {
      if( event.target.get('tag') != 'input' ) return;
      pushModification('info');
    });

    // Add tooltips
    ContentTooltips = new Tips($$('ul#column_stock li.storelayout_content_draggable'), {
      
    });

    // Make sortable
    ContentSortables = new NestedSortables($$('ul.storelayout_content_sortable'), {
      constrain : false,
      clone: function(event, element, list) {
        var tmp = element.clone(true).setStyles({
          margin: '0px',
          position: 'absolute',
          visibility: 'hidden',
          zIndex: 9000,
          'width': element.getStyle('width')
        }).inject(this.list).setPosition(element.getPosition(element.getOffsetParent()));
        return tmp;
      },
      onStart : function(element, clone) {
        element.addClass('storelayout_content_dragging');
        currentParent = element.getParent();
        currentNextSibling = element.getNext();
      },
      onComplete : function(element, clone) {
        element.removeClass('storelayout_content_dragging');
        if( !currentParent ) {
          //alert('missing parent error');
          return;
        }
        
        // If it's coming from stock and going into stock, destroy and insert back into original location
        if( currentParent.hasClass('storelayout_content_stock_sortable') && element.getParent().hasClass('storelayout_content_stock_sortable') ) {
          if( currentNextSibling ) {
            element.inject(currentNextSibling, 'before');
          } else {
            element.inject(currentParent);
          }
        }

        // If it's not coming from stock, and going into stock, just destroy it
        else if( element.getParent().hasClass('storelayout_content_stock_sortable') ) {
          element.destroy();

          // Signal modification
          pushModification('main');
        }

        // If it's coming from stock, and not going into stock, put back into stock, clone, and insert
        else if( currentParent.hasClass('storelayout_content_stock_sortable') && !element.getParent().hasClass('storelayout_content_stock_sortable') ) {
          var elClone = element.clone();

          // Make it buildable, add info, and give it a temp id
          elClone.inject(element, 'after');
          elClone.addClass('storelayout_content_buildable');
          elClone.addClass('storelayout_content_cell');
          elClone.removeClass('storelayout_content_stock_draggable');
          elClone.getElement('span').setStyle('display', '');
          // @todo
          elClone.set('id', 'storelayout_content_new_' + (newContentIndex++));

          // Make it draggable
          ContentSortables.addItems(elClone);

          // Remove tips
          ContentTooltips.detach(elClone);

          // Put original back
          if( currentNextSibling ) {
            element.inject(currentNextSibling, 'before');
          } else {
            element.inject(currentParent);
          }

          // Try to expand special blocks
          expandSpecialBlock(elClone);

          // Check for autoEdit
          checkForAutoEdit(elClone);

          // Signal modification
          pushModification('main');
        }

        // It's coming from cms to cms
        else if( !currentParent.hasClass('storelayout_content_stock_sortable') && !element.getParent().hasClass('storelayout_content_stock_sortable') ) {
          // Signal modification
          pushModification('main');
        }
        
        // Something strange happened
        else {
          alert('error in widget placement');
        }

        currentParent = false;
        currentNextSibling = false;
      }
    });

    // Remove disabled stock items
    ContentSortables.removeItems($$('#column_stock li.disabled'));
  });

  /* Lazy confirm box */
  var confirmStoreChangeLoss = function() {
    if( currentModifications.length == 0 ) return true; // Don't ask if nothing to lose
    // @todo check if there are any changes that would be lost
    return confirm("<?php echo $this->string()->escapeJavascript($this->translate("Any unsaved changes will be lost. Are you sure you want to leave this store?")); ?>");
  }

  /* Remove widget */
  var removeWidget = function(element) {
    if( !element.hasClass('storelayout_content_buildable') ) {
      element = element.getParent('.storelayout_content_buildable');
    }
    element.destroy();

    // Signal modification
    pushModification('main');
  }

  /* Switch the active menu item */
   var switchStoreMenu = function(event, activator) {
    var element = activator.getParent('li');
    $$('.storelayout_layoutbox_menu_generic').each(function(otherElement) {
      var otherWrapper = otherElement.getElement('.storelayout_layoutbox_menu_wrapper_generic');
      if( otherElement.get('id') == element.get('id') && !otherElement.hasClass('active') ) {
        otherElement.addClass('active');
        otherWrapper.setStyle('display', 'block');
        var firstInput = otherElement.getElement('input');
        if( firstInput ) {
          firstInput.focus();
        }
      } else {
        otherElement.removeClass('active');
        otherWrapper.setStyle('display', 'none');
      }
    });
  }

  /* Load a different store */
  var loadStore = function(store_id) {
    if( confirmStoreChangeLoss() ) {
      window.location.search = '?store=' + store_id;
      //window.location = window.location.href
    }
  }

  /* Save current store changes */
  var saveChanges = function()
  {
    var data = [];
    $$('.storelayout_content_buildable').each(function(element) {
      var parent = element.getParent('.storelayout_content_buildable');

      var elData = {
        'element' : {},
        'parent' : {},
        'info' : {},
        'params' : {}
      };

      // Get element identity
      elData.element.id = element.get('id');
      if( elData.element.id.indexOf('storelayout_content_new_') === 0 ) {
        elData.tmp_identity = elData.element.id.replace('storelayout_content_new_', '');
      } else {
        elData.identity = elData.element.id.replace('storelayout_content_', '');
      }

      // Get element class
      elData.element.className = element.get('class');

      // Get element type and name
      if( element.hasClass('storelayout_content_cell') ) {
        var m = element.get('class').match(/storelayout_content_widget_([^ ]+)/i);
        if( $type(m) && $type(m[1]) ) {
          elData.type = 'widget';
          elData.name = m[1];
        }
      } else if( element.hasClass('storelayout_content_block') ) {
        var m = element.get('class').match(/storelayout_content_container_([^ ]+)/i);
        if( $type(m) && $type(m[1]) ) {
          elData.type = 'container';
          elData.name = m[1];
        }
      } else if( element.hasClass('storelayout_content_column') ) {
        var m = element.get('class').match(/storelayout_content_container_([^ ]+)/i);
        if( $type(m) && $type(m[1]) ) {
          elData.type = 'container';
          elData.name = m[1];
        }
      } else {
        
      }


      if( parent ) {
        // Get parent identity
        elData.parent.id = parent.get('id');
        if( elData.parent.id.indexOf('storelayout_content_new_') === 0 ) {
          elData.parent_tmp_identity = elData.parent.id.replace('storelayout_content_new_', '');
        } else {
          elData.parent_identity = elData.parent.id.replace('storelayout_content_', '');
        }
      }

      elData.info = element.retrieve('contentInfo');
      elData.params = (element.retrieve('contentParams') || {params:{}}).params;

      // Merge with defaults
      if( $type(contentByName[elData.name]) && $type(contentByName[elData.name].defaultParams) ) {
        elData.params = $merge(contentByName[elData.name].defaultParams, elData.params);
      }
      
      data.push(elData);
    });

    var url = '<?php echo $this->url(array('action' => 'update', 'controller' => 'layout', 'module' => 'sitestore'), 'default', true)?>';
    var request = new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'store' : currentStore,
        'structure' : data,
        'layout' : currentLayout
      },
      //responseTree, responseElements, responseHTML, responseJavaScript
      onComplete : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $H(responseHTML.newIds).each(function(data, index) {
          var newContentEl = $('storelayout_content_new_' + index);
          if( !newContentEl ) throw "missing new content el";
          newContentEl.set('id', 'storelayout_content_' + data.identity);
          newContentEl.store('contentParams', data);
        });
        eraseModification('main');
        alert('<?php echo $this->string()->escapeJavascript($this->translate("Your changes to this store have been saved.")) ?>');
      }
    });

    request.send();
  }

  /* Open the edit store for a widget */
  var currentEditingElement;
  var openWidgetParamEdit = function(name, element) {
    //event.stop();
    
    currentEditingElement = $(element);
    var content_id;
    if( element.get('id').indexOf('storelayout_content_new_') !== 0 && element.get('id').indexOf('storelayout_content_') === 0 ) {
      content_id = element.get('id').replace('storelayout_content_', '');
    }
    <?php $store_id = '';?>
    <?php if(isset($this->sitestore) && !empty($this->sitestore->store_id)):?>
      <?php $store_id = $this->sitestore->store_id; ?>
    <?php endif;?>
    var url = '<?php echo $this->url(array('action' => 'widget', 'controller' => 'layout', 'module' => 'sitestore', 'store_id'=> $store_id), 'default', true)?>';
    var urlObject = new URI(url);

    var fullParams = element.retrieve('contentParams');
    if( $type(fullParams) && $type(fullParams.params) ) {
      //urlObject.setData(fullParams.params);
    }

    urlObject.setData({'name' : name}, true);

    Smoothbox.open(urlObject.toString());
  }

  var pullWidgetParams = function() {
    if( currentEditingElement ) {
      var fullParams = currentEditingElement.retrieve('contentParams');
      if( $type(fullParams) && $type(fullParams.params) ) {
        return fullParams.params;
      }
    }
    return {};
  }

  var pullWidgetTypeInfo = function() {
    if( currentEditingElement ) {
      var info = currentEditingElement.retrieve('contentInfo');
      if( $type(info) ) {
        return info;
      }
    }
    return {};
  }

  /* Set the params in the widget */
  var setWidgetParams = function(params) {
    if( !currentEditingElement ) return;
    var oldParams = currentEditingElement.retrieve('contentParams') || {};
    oldParams.params = params
    currentEditingElement.store('contentParams', oldParams);
    currentEditingElement = false;

    // Signal modification
    pushModification('main');
  }

  /* Save the store info */
  var saveCurrentStoreInfo = function(formElement) {
    var url = '<?php echo $this->url(array('action' => 'save', 'controller' => 'layout', 'module' => 'sitestore'), 'default', true)?>';
    var request = new Form.Request(formElement, formElement.getParent(), {
      requestOptions : {
        url : url
      },
      onComplete: function() {
        eraseModification('info');
      }
    });

    request.send();
  }

  /* Change the layout */
  var changeCurrentLayoutType = function(type) {
    var availableAreas = ['top', 'bottom', 'left', 'middle', 'right'];
    var types = type.split(',');


    // Build negative areas
    var negativeAreas = [];
    availableAreas.each(function(currentAvailableArea) {
      if( !types.contains(currentAvailableArea) ) {
        negativeAreas.push(currentAvailableArea);
      }
    });

    // Build positive areas
    var positiveAreas = [];
    types.each(function(currentType) {
      var el = document.getElement('.storelayout_content_container_'+currentType);
      if( !el ) {
        positiveAreas.push(currentType);
      }
    });
    
    // Check to see if any columns containing widgets are going to be destroyed
    var contentLossCount = 0;
    negativeAreas.each(function(currentType) {
      var el = document.getElement('.storelayout_content_container_'+currentType);
      if( el && el.getChildren().length > 0 ) {
        contentLossCount++;
      }
    });

    // Notify user of potential data loss
    if( contentLossCount > 0 ) {
      <?php $replace = $this->translate("Changing to this layout will cause %s area(s) containing widgets to be destroyed. Are you sure you want to continue?", "' + contentLossCount + '") ?>
      if( !confirm('<?php echo $this->string()->escapeJavascript($replace) ?>') ) {
        return false;
      }
    }

    // Destroy areas
    negativeAreas.each(function(currentType) {
      var el = document.getElement('.storelayout_content_container_'+currentType);
      if( el ) {
        el.destroy();
      }
    });

    // Create areas
    var levelOneReference = document.getElement('.storelayout_layoutbox table.storelayout_content_container_main');
    
    // Create level one areas
    $H({'top' : 'before', 'bottom' : 'after'}).each(function(placement, currentType) {
      if( !positiveAreas.contains(currentType) ) return;

      var newTable = new Element('table', {
        'id' : 'storelayout_content_new_' + (newContentIndex++),
        'class' : 'storelayout_content_block storelayout_content_buildable storelayout_content_container_' + currentType
      }).inject(levelOneReference, placement);

      var newTbody = new Element('tbody', {
      }).inject(newTable);

      var newTr = new Element('tr', {
      }).inject(newTbody);

      // L2
      var newTdContainer = new Element('td', {
        'id' : 'storelayout_content_new_' + (newContentIndex++),
        'class' : 'storelayout_content_column storelayout_content_buildable storelayout_content_container_middle'
      }).inject(newTr);

      // L3
      var newUlContainer = new Element('ul', {
        'class' : 'storelayout_content_sortable'
      }).inject(newTdContainer);

      ContentSortables.addLists(newUlContainer);
    });

    // Create level two areas
    var mainParent = document.getElement('.storelayout_layoutbox .storelayout_content_container_main tr');
    $H({'left' : 'top', 'right' : 'bottom'}).each(function(placement, currentType) {
      if( !positiveAreas.contains(currentType) ) return;
      
      // L2
      var newTdContainer = new Element('td', {
        'id' : 'storelayout_content_new_' + (newContentIndex++),
        'class' : 'storelayout_content_column storelayout_content_buildable storelayout_content_container_' + currentType
      }).inject(mainParent, placement);

      // L3
      var newUlContainer = new Element('ul', {
        'class' : 'storelayout_content_sortable'
      }).inject(newTdContainer);

      ContentSortables.addLists(newUlContainer);
    });

    // Signal modification
    pushModification('main');
  }

  /* Tab container and other special block handling */
  var expandSpecialBlock = function(element)
  {
    if( element.hasClass('storelayout_content_widget_core.container-tabs') ) {
      element.addClass('storelayout_layoutbox_widget_tabbed_wrapper');
      // Empty
      element.empty();
      // Title/edit
      new Element('span', {
        'class' : 'storelayout_layoutbox_widget_tabbed_top',
        'html' : '<?php echo $this->string()->escapeJavascript($this->translate("Tab Container")) ?><span class="open"> | <a href=\'javascript:void(0);\' onclick="openWidgetParamEdit(\'core.container-tabs\', $(this).getParent(\'li.storelayout_content_cell\')); (new Event(event).stop()); return false;"><?php echo $this->string()->escapeJavascript($this->translate("edit")) ?></a></span> <span class="remove"><a href="javascript:void(0)" onclick="removeWidget($(this));">x</a></span>'
      }).inject(element);
      // Desc
      new Element('span', {
        'class' : 'storelayout_layoutbox_widget_tabbed_overtext',
        'html' : contentByName["core.container-tabs"].childAreaDescription
      }).inject(element);
      // Edit area
      var tmpDivContainer = new Element('div', {
        'class' : 'storelayout_layoutbox_widget_tabbed'
      }).inject(element);
      var list = new Element('ul', {
        'class' : 'sortablesForceInclude storelayout_content_sortable storelayout_layoutbox_widget_tabbed_contents'
      }).inject(tmpDivContainer);
      
      ContentSortables.addLists(list);
    }
  }

  /* Checks for autoEdit */
  var checkForAutoEdit = function(element) {
    var m = element.get('class').match(/storelayout_content_widget_([^ ]+)/i);
    if( $type(m) && $type(m[1]) ) {
      //console.log(m[1], contentByName[m[1]]);
      if( $type(contentByName[m[1]].autoEdit) && contentByName[m[1]].autoEdit ) {
        openWidgetParamEdit(m[1], element);
      }
    }
  }

  /* This will hide (or show) the global layout for this store */
  var toggleGlobalLayout = function(element) {
    pushModification('main');

    var headerContainer = $$('div.storelayout_layoutbox_header');
    var footerContainer = $$('div.storelayout_layoutbox_footer');

    // Hide
    if( currentLayout == 'default' || currentLayout == '' ) {
      headerContainer.addClass('storelayout_layoutbox_header_hidden');
      footerContainer.addClass('storelayout_layoutbox_footer_hidden');
      headerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate("show on this store")) ?>)');
      footerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate("show on this store")) ?>)');
      currentLayout = 'default-simple';
    }

    // Show
    else
    {
      headerContainer.removeClass('storelayout_layoutbox_header_hidden');
      footerContainer.removeClass('storelayout_layoutbox_footer_hidden');
      headerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate("hide on this store")) ?>)');
      footerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate('hide on this store')) ?>)');
      currentLayout = 'default';
    }
  }

  /* Delete the current store */
  var deleteCurrentStore = function() {
     
    if( !confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this store?")) ?>') ) {
      return false;
    }

    var redirectUrl = '<?php echo $this->url(array()) ?>';
    var url = '<?php echo $this->url(array('action' => 'delete', 'controller' => 'layout', 'module' => 'sitestore'), 'default', true)?>';
    var request = new Request.JSON({
      'url' : url,
      'data' : {
        'format' : 'json',
        'store' : currentStore
      },
      onComplete : function(responseJSON) {
        window.location.href = redirectUrl;
      }
    });

    request.send();
  }


</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<h2>
  <?php echo $this->sitestore->__toString(); ?>
  <?php echo $this->translate('&raquo; Edit Layout');?>
</h2>

<h2><?php //echo $this->translate('Layout Editor'); ?></h2>
<p>
		<?php echo $this->translate('Use the layout editor to decide what content appears on main profile of your Store. Click and drag the colored "blocks" to arrange the content you want. Drag blocks to and from the "Available Blocks" area to add or remove them from your Store profile. Use "HTML Blocks" if you want to drop in raw HTML or other content.'); ?>
		<?php if (Engine_Api::_()->sitestore()->hasPackageEnable()) : ?>
			<?php echo $this->translate('Note: Some blocks won\'t appear if their apps or features are not available in your package.'); ?>
		<?php else : ?>
			<?php echo $this->translate('Note: Some blocks won\'t appear if their apps or features are not available to your member level.'); ?>
		<?php endif; ?>
</p>

<div id='storelayout_cms_wrapper'>
  <div class="storelayout_layoutbox_menu">
    <ul>
      <li id="storelayout_layoutbox_menu_savechanges">
        <a href="javascript:void(0);" onClick="saveChanges()">
          <?php echo $this->translate("Save Changes") ?>
        </a>
      </li>
       <li id="storelayout_layoutbox_menu_viewstore">
        <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->sitestore->store_id, $this->sitestore->owner_id ,$this->sitestore->getSlug()), $this->translate("View Store"));?>
       </li>

      <li id="storelayout_layoutbox_menu_backeditstore">
        <?php echo $this->htmlLink(array('route' => 'sitestore_edit', 'store_id' => $this->sitestore->store_id), $this->translate('Back to Store Dashboard'))
				?>
       
      </li>
    </ul>
  </div>

  <div class="storelayout_layoutbox_wrapper">
    <div class="storelayout_layoutbox_sub_menu">
      <h3>
        <?php echo $this->translate('Store Profile Block Placement') ?>
      </h3>
      <ul>
        
        <?php if( $this->storeObject->name !== 'header' && $this->storeObject->name !== 'footer'): ?>
          <li class="storelayout_layoutbox_menu_generic" id="storelayout_layoutbox_menu_storeinfo">
          <div class="storelayout_layoutbox_menu_wrapper_generic storelayout_layoutbox_menu_editinfo_wrapper" id="storelayout_layoutbox_menu_editinfo_wrapper">
            <div class="storelayout_layoutbox_menu_editinfo">
              <span>
                <?php echo $this->storeForm->render($this) ?>
              </span>
              <div class="storelayout_layoutbox_menu_editinfo_submit">
                <button onclick="saveCurrentStoreInfo($('storelayout_content_storeinfo')); return false;"><?php echo $this->translate("Save Changes") ?></button> or <a href="javascript:void(0);" onClick="switchStoreMenu(new Event(event), $(this));"><?php echo $this->translate("cancel") ?></a>
              </div>
            </div>
          </div>
          <a href="javascript:void(0);" onClick="switchStoreMenu(new Event(event), $(this));"><?php echo $this->translate("Edit Store Info") ?></a>
        </li>
        <?php endif ;?>
      </ul>
    </div>

    <?php // Normal editing ?>
    <?php if( $this->storeObject->name != 'header' && $this->storeObject->name != 'footer' ): ?>

      <div class='storelayout_layoutbox'>
        <div class='storelayout_layoutbox_header<?php echo ( empty($this->storeObject->layout) || $this->storeObject->layout == 'default' ? '' : ' storelayout_layoutbox_header_hidden' ) ?>'>
          <span>
            <?php echo $this->translate("Global Header") ?>
            <span>
              <a href="javascript:void(0);" onclick="toggleGlobalLayout($(this).getParent('div.storelayout_layoutbox_header'));">
                <?php echo ( empty($this->storeObject->layout) || $this->storeObject->layout == 'default' ? "({$this->translate('hide on this store')})" : "({$this->translate('show on this store')})" ) ?>
              </a>
            </span>
          </span>
        </div>

        <?php // LEVEL 0 - START (SANITY) ?>
        <?php
          ob_start();
          try {
        ?>

          <?php
            // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
            <table id="storelayout_content_<?php echo $structOne['identity'] ?>" class="storelayout_content_block storelayout_content_buildable storelayout_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <script type="text/javascript">
                    window.addEvent('domready', function() {
                      $("storelayout_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                    });
                  </script>
                  <?php
                    // LEVEL 2 - START (LEFT, MIDDLE, RIGHT)
                    foreach( (array) @$structOne['elements'] as $structTwo ):
                      $structTwoNE = $structTwo;
                      unset($structTwoNE['elements']);
                  ?>
                    <td id="storelayout_content_<?php echo $structTwo['identity'] ?>" class="storelayout_content_column storelayout_content_buildable storelayout_content_<?php echo $structTwo['type'] . '_' . $structTwo['name'] ?>">
                      <script type="text/javascript">
                        window.addEvent('domready', function() {
                          $("storelayout_content_<?php echo $structTwo['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structTwoNE) ?>);
                        });
                      </script>
                      <ul class="storelayout_content_sortable">
                        <?php
                          // LEVEL 3 - START (WIDGETS)
                          foreach( (array) $structTwo['elements'] as $structThree ):
                            $structThreeNE = $structThree;
                            $structThreeInfo = @$this->contentByName[$structThree['name']];
                            unset($structThreeNE['elements']);
                        ?>
                          <script type="text/javascript">
                            window.addEvent('domready', function() {
                              $("storelayout_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                            });
                          </script>
                          <?php if( empty($structThreeInfo) ): // Missing widget ?>
                            <li id="storelayout_content_<?php echo $structThree['identity'] ?>" class="disabled storelayout_content_cell storelayout_content_buildable storelayout_content_draggable storelayout_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                              <?php
                              if($structThree['name'] == 'socialengineaddon.feed'){
                                echo $this->translate('activity.feed');
                              } else {
                                echo $this->translate($structThree['name']);
                              }
                              ?>

                                <script type="text/javascript">
                                   hideWidgetIds.push("storelayout_content_<?php echo $structThree['identity'] ?>");
                                  </script>
                              <span class="open"></span>
                              <span class="remove"><b><?php echo $this->translate("Locked"); ?></b></span>
                            </li>
                          <?php elseif( empty($structThreeInfo['canHaveChildren']) ): ?>
                            <li id="storelayout_content_<?php echo $structThree['identity'] ?>" class="storelayout_content_cell storelayout_content_buildable storelayout_content_draggable storelayout_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>  <?php if(in_array($structThree['name'], $this->hideWidgets)) echo  " disabled" ?> ">
                              <?php echo $this->translate($this->contentByName[$structThree['name']]['title']) ?>
                                <?php if((in_array($structThree['name'], $this->showeditinwidget) && !in_array($structThree['name'], $this->hideWidgets)) &&  ($structThree['name'] != 'core.ad-campaign' &&  $structThree['name'] != 'core.html-block')) :?>
                                <span class="open">
                                | 
                                  <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.storelayout_content_cell')); (new Event(event).stop()); return false;">
                                     <?php echo $this->translate('edit') ?>
                                  </a>
                                </span>
                              <?php elseif(empty($structThree['widget_admin'])):?>
                                <span class="open">
                                  | 
                                  <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.storelayout_content_cell')); (new Event(event).stop()); return false;">
                                     <?php echo $this->translate('edit') ?>
                                  </a>
                                </span>
                              <?php endif;?>
                              <?php if(!in_array($structThree['name'], $this->hideWidgets)):?>
                              	<span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                              <?php else: ?>
                               <span class="remove"><b><?php echo $this->translate("Locked"); ?></b></span>
                                <script type="text/javascript">
                                   hideWidgetIds.push("storelayout_content_<?php echo $structThree['identity'] ?>"); 
                                  </script>
                               <?php endif;?>
                            </li>
                          <?php else: ?>
                            <!-- tabbed widgets -->
                            <li id="storelayout_content_<?php echo $structThree['identity'] ?>" class="storelayout_content_cell storelayout_content_buildable storelayout_content_draggable storelayout_layoutbox_widget_tabbed_wrapper storelayout_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?>">
                              <span class="storelayout_layoutbox_widget_tabbed_top">
                                <?php echo $this->translate('Tab Container') ?>
                                <span class="open">
                                  <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.storelayout_content_cell')); (new Event(event).stop()); return false;">
                                    <?php echo $this->translate('edit') ?>
                                  </a>
                                </span>
                               <span class="remove"><b><?php echo $this->translate("Locked"); ?></b></span>
                                <script type="text/javascript">
                                   hideWidgetIds.push("storelayout_content_<?php echo $structThree['identity'] ?>");
                                 </script>
                              </span>
                              <span class="storelayout_layoutbox_widget_tabbed_overtext">
                                <?php echo $this->translate($structThreeInfo['childAreaDescription']) ?>
                              </span>
                              <div class="storelayout_layoutbox_widget_tabbed">
                                <ul class="sortablesForceInclude storelayout_content_sortable storelayout_layoutbox_widget_tabbed_contents">
                                  <?php
                                    // LEVEL 4 - START (WIDGETS)
                                    foreach( (array) $structThree['elements'] as $structFour ):
                                      $structFourNE = $structFour;
                                      $structFourInfo = @$this->contentByName[$structFour['name']];
                                      unset($structFourNE['elements']);
                                  ?>
                                    <script type="text/javascript">
                                      window.addEvent('domready', function() {
                                        $("storelayout_content_<?php echo $structFour['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structFourNE) ?>);
                                      });
                                    </script>
                                    <?php if( empty($structFourInfo) ): ?>
                                      <li id="storelayout_content_<?php echo $structFour['identity'] ?>" class="disabled storelayout_content_cell storelayout_content_buildable storelayout_content_draggable storelayout_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                        <?php
                                         if($structFour['name'] == 'socialengineaddon.feed') {
                                           $structFour['name'] = 'activity.feed';
                                           echo $this->translate( $structFour['name']);
                                         } else {
                                           echo $this->translate( $structFour['name']);
                                         }
                                         ?>
                                        <span></span>
                                      <script type="text/javascript">
                                         hideWidgetIds.push("storelayout_content_<?php echo $structFour['identity'] ?>");
                                        </script>
                                         <span class="remove"><b><?php echo $this->translate("Locked"); ?></b></span>
                                      </li>
                                    <?php else: ?>
                                      <li id="storelayout_content_<?php echo $structFour['identity'] ?>" class="storelayout_content_cell storelayout_content_buildable storelayout_content_draggable storelayout_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?> <?php if(in_array($structFour['name'], $this->hideWidgets)) echo  " disabled" ?>">
                                        <?php echo $this->translate($this->contentByName[$structFour['name']]['title']) ?>
                                        <?php if(!in_array($structFour['name'], $this->hideWidgets)):?>
                                        <?php if(!empty($structFour['widget_admin']) && ($structFour['name'] != 'core.html-block' && $structFour['name'] != 'core.ad-campaign')):?>
                                        <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structFour['name'] ?>', $(this).getParent('li.storelayout_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate('edit') ?></a></span>                                        
                                        <?php elseif(empty($structFour['widget_admin'])):?>
                                          <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structFour['name'] ?>', $(this).getParent('li.storelayout_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate('edit') ?></a></span>
                                        <?php endif;?>
                                        <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                                         <?php else: ?>
                                         <span class="remove"><b><?php echo $this->translate("Locked"); ?></b></span>
                                              <script type="text/javascript">
                                                 hideWidgetIds.push("storelayout_content_<?php echo $structFour['identity'] ?>");
                                                </script>
                                        <?php endif; ?>
                                      </li>
                                    <?php endif; ?>
                                  <?php
                                    endforeach;
                                    // LEVEL 4 - END
                                  ?>
                                </ul>
                              </div>
                            </li>
                            <!-- end tabbed widgets -->
                          <?php endif; ?>

                        <?php
                          endforeach;
                          // LEVEL 3 - END
                        ?>

                      </ul>
                    </td>
                  <?php
                    endforeach;
                    // LEVEL 2 - END
                  ?>

                </tr>
              </tbody>
            </table>
          <?php
            endforeach;
            // LEVEL 1 - END
          ?>

        <?php // LEVEL 0 - END (SANITY) ?>
        <?php
            ob_end_flush();
          } catch( Exception $e ) {
            ob_end_clean();
            echo "An error has occurred.";
          }
        ?>

        <div class='storelayout_layoutbox_footer<?php echo ( empty($this->storeObject->layout) || $this->storeObject->layout == 'default' ? '' : ' storelayout_layoutbox_footer_hidden' ) ?>'>
          <span>
            <?php echo $this->translate('Global Footer') ?>
            <span>
              <a href="javascript:void(0);" onclick="toggleGlobalLayout($(this).getParent('div.storelayout_layoutbox_footer'));">
                <?php echo ( empty($this->storeObject->layout) || $this->storeObject->layout == 'default' ? "({$this->translate('hide on this store')})" : "({$this->translate('show on this store')})" ) ?>
              </a>
            </span>
          </span>
        </div>
      </div>

    <?php // Header/Footer editing ?>
    <?php else: ?>

      <div class='storelayout_layoutbox'>
        <?php if( $this->storeObject->name == 'footer' ): ?>
          <div class='storelayout_layoutbox_header'>
            <span>Global Header</span>
          </div>
        <?php else: ?>
          <?php
            // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
            <table id="storelayout_content_<?php echo $structOne['identity'] ?>" class="storelayout_content_block storelayout_content_block_headerfooter storelayout_content_buildable storelayout_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <td class="storelayout_content_column_headerfooter">
                    <span class="storelayout_layoutbox_note">
                      Drop things here to add them to the global header.
                    </span>
                    <script type="text/javascript">
                      window.addEvent('domready', function() {
                        $("storelayout_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                      });
                    </script>
                    <ul class="storelayout_content_sortable">
                      <?php
                        // LEVEL 3 - START (WIDGETS)
                        foreach( (array) $structOne['elements'] as $structThree ):
                          $structThreeNE = $structThree;
                          $structThreeInfo = $this->contentByName[$structThree['name']];
                          unset($structThreeNE['elements']);
                      ?>
                        <script type="text/javascript">
                          window.addEvent('domready', function() {
                            $("storelayout_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                          });
                        </script>
                        <li id="storelayout_content_<?php echo $structThree['identity'] ?>" class="storelayout_content_cell storelayout_content_buildable storelayout_content_draggable storelayout_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                          <?php echo $this->translate($this->contentByName[$structThree['name']]['title']) ?>
                          <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.storelayout_content_cell')); (new Event(event).stop()); return false;">edit</a></span>
                          <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </td>
                </tr>
              </tbody>
            </table>
          <?php
            endforeach;
            // LEVEL 1 - END
          ?>
        <?php endif; ?>

        <div class='storelayout_layoutbox_center_placeholder'>
          <span><?php echo $this->translate("Main Content Area") ?></span>
        </div>

        <?php if( $this->storeObject->name == 'header' ): ?>
        <div class='storelayout_layoutbox_footer'>
          <span><?php echo $this->translate("Global Footer") ?></span>
        </div>
        <?php else: ?>
          <?php
            // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
            <table id="storelayout_content_<?php echo $structOne['identity'] ?>" class="storelayout_content_block storelayout_content_block_headerfooter storelayout_content_buildable storelayout_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <td class="storelayout_content_column_headerfooter">
                    <span class="storelayout_layoutbox_note">
                      <?php echo $this->translate("Drop things here to add them to the global footer.") ?>
                    </span>
                    <script type="text/javascript">
                      window.addEvent('domready', function() {
                        $("storelayout_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                      });
                    </script>
                    <ul class="storelayout_content_sortable">
                      <?php
                        // LEVEL 3 - START (WIDGETS)
                        foreach( (array) $structOne['elements'] as $structThree ):
                          $structThreeNE = $structThree;
                          $structThreeInfo = $this->contentByName[$structThree['name']];
                          unset($structThreeNE['elements']);
                      ?>
                        <script type="text/javascript">
                          window.addEvent('domready', function() {
                            $("storelayout_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                          });
                        </script>
                        <li id="storelayout_content_<?php echo $structThree['identity'] ?>" class="storelayout_content_cell storelayout_content_buildable storelayout_content_draggable storelayout_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                          <?php echo $this->translate($this->contentByName[$structThree['name']]['title']) ?>
                          <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.storelayout_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate("edit") ?></a></span>
                          <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </td>
                </tr>
              </tbody>
            </table>
          <?php
            endforeach;
            // LEVEL 1 - END
          ?>
        <?php endif; ?>
      </div>

    <?php endif; ?>

    <!--<div class="storelayout_layoutbox_footnotes">
      <?php //echo $this->translate("Note: Some blocks won't appear if you're not signed-in or if they don't belong on this store."); ?>
    </div>-->
  </div>


  <div class="storelayout_layoutbox_pool_wrapper">
    <h3><?php echo $this->translate("Available Blocks") ?></h3>
    <div class='storelayout_layoutbox_pool'>
      <div id='stock_div'></div>
      <ul id='column_stock'>
      	<?php if(isset($this->contentAreas['Uncategorized']))?>
        <?php unset($this->contentAreas['Uncategorized']);?>
        <?php foreach( $this->contentAreas as $category => $categoryAreas ): ?>
          <li>
              <div class="storelayout_layoutbox_pool_category_wrapper" onclick="$(this); $(this).getElement('.storelayout_layoutbox_pool_category_show').toggle(); $(this).getElement('.storelayout_layoutbox_pool_category_hide').toggle(); this.getParent('li').getElement('ul').style.display = ( this.getParent('li').getElement('ul').style.display == 'none' ? '' : 'none' );">
              <div class="storelayout_layoutbox_pool_category">
                <div class="storelayout_layoutbox_pool_category_hide">
                  &nbsp;
                </div>
                <div class="storelayout_layoutbox_pool_category_show">
                  &nbsp;
                </div>
                <div class="storelayout_layoutbox_pool_category_label">
                  <?php echo $this->translate($category) ?>
                </div>
              </div>
            </div>
            <ul class='storelayout_content_sortable storelayout_content_stock_sortable'>
            <?php $storelayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layout.setting', 1); ?>
              <?php foreach( $categoryAreas as $info ):
               if($info['name']=='core.container-tabs')
                 continue;
               if($info['name']=='activity.feed')
                 continue;
               if($info['name']=='sitestore.widgetlinks-sitestore' && $storelayout)
                 continue;
                $class = 'storelayout_content_widget_' . $info['name'];
                $class .= ' storelayout_content_draggable storelayout_content_stock_draggable';
                $onmousedown = false;
                if( !empty($info['disabled']) ) {
                  $class .= ' disabled';
                  if( !empty($info['requireItemType']) ) {
                    $onmousedown = 'alert(\'Disabled due to missing item type(s): '.join(', ', (array)$info['requireItemType']) . '\'); return false;';
                  } else {
                    $onmousedown = 'alert(\'Disabled due to missing dependency.\'); return false;';
                  }
                }
                if( !empty($info['special']) ) {
                  $class .= ' htmlblock special';
                }
                if( !empty($info['storelayoutCssClass']) ) {
                  $class .= ' ' . $info['storelayoutCssClass'];
                }

                ?>
                <?php //if( empty($info['canHaveChildren']) ): ?>
                <?php if(!in_array($info['name'], $this->hideWidgets)):?>
                  <li class="<?php echo $class ?>" title="<?php echo $this->escape($info['description']) ?>"<?php if( $onmousedown ): ?> onmousedown="<?php echo $onmousedown ?>"<?php endif; ?>>
                    
                      <?php echo $this->translate($info['title']) ?>
                    
                    <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $info['name'] ?>', $(this).getParent('li.storelayout_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate("edit") ?></a></span>
                    <span class="remove"><a href='javascript:void(0);' onclick="removeWidget($(this));">x</a></span>
                  </li>                  
                  <?php endif;?>  
                <?php /* //else: ?>
                  <li class="storelayout_layoutbox_widget_tabbed_wrapper">
                    <span class="storelayout_layoutbox_widget_tabbed_top">
                      Tabbed Blocks <a href="#">(edit)</a>
                    </span>
                    <div class="storelayout_layoutbox_widget_tabbed">
                      <ul class="storelayout_layoutbox_widget_tabbed_contents">
                        <?php echo $structThreeInfo['childAreaDescription'] ?>
                      </ul>
                    </div>
                  </li>
                <?php //endif; */ ?>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="sitestorelayoutnote">
  	<?php echo $this->translate("Note: Some blocks won't appear if you're not signed-in or if they don't belong on this store."); ?>
  </div>
</div>

<style type="text/css">
div.storelayout_layoutbox_menu li#storelayout_layoutbox_menu_openstore.active > a span.more
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/more_active.png);
  border-color: transparent;
}
#storelayout_layoutbox_menu_storeinfo > a
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/editinfo.png);
}
#storelayout_layoutbox_menu_savechanges > a
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/savechanges.png);
}
#storelayout_layoutbox_menu_editcolumns > a
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/editcolumns.png);
}
#storelayout_layoutbox_menu_deletestore > a
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/deletestore.png);
}
div.storelayout_layoutbox_header,
div.storelayout_layoutbox_footer{ background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/placeholder.png);}
ul.storelayout_content_sortable li.storelayout_content_draggable,
ul.storelayout_content_sortable li.special{ background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/static.png);}
ul.storelayout_content_sortable li.special{ border: 1px solid #dccca0;background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/dynamic.png);}
ul.storelayout_content_sortable li.disabled{border: 1px solid #dcdcdc;background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/disabled.png);}
div.storelayout_layoutbox_pool_category_hide
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/blocks_hide.png);
}
div.storelayout_layoutbox_pool_category_show
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/blocks_show.png);
}
div.storelayout_layoutbox_center_placeholder
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/placeholder.png);
}
div.storelayout_layoutbox li.storelayout_layoutbox_widget_tabbed_wrapper
{
 background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/dynamic.png);
}
#storelayout_layoutbox_menu_savechanges > a {
	background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/savechanges.png);
}
#storelayout_layoutbox_menu_viewstore > a{
 background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/icons/store.png);
}
#storelayout_layoutbox_menu_storeinfo > a{
 background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/editstore.png);
}
#storelayout_layoutbox_menu_backeditstore > a{
 background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/dashboard.png);
}
#storelayout_layoutbox_menu_editcolumns > a{
 background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestore/externals/images/layout/editcolumns.png);
}
</style>
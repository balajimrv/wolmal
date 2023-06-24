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

<?php $showHideHeaderFooter = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.show.hide.header.footer', 'default');?>
<h2 class="fleft"><?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

	<div class='tabs'>
		<ul class="navigation">
		  <li >
		 	<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'defaultlayout','action'=>'index'), $this->translate('Store Profile Layout Type'), array())
		  ?>
			</li>

			<li class="active">
		  <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'layout','action'=>'layout', 'store' => $this->store_id), $this->translate('Store Profile Layout Editor'), array())
		  ?>
			</li>
<!--      <li>
        <?php
        //echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'layout','action'=>'layout-block'), $this->translate('Store Profile Layout Settings'), array())
        ?>
		  </li>-->
		</ul>
	</div>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0)):?>
<script type="text/javascript">
  var hideWidgetIds=new Array();
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
  var currentLayout = '<?php echo $showHideHeaderFooter ?>';

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
        $('admin_layoutbox_menu_storeinfo').addClass('admin_content_modifications_active');
      } else if( type == 'main' ) {
        $('admin_layoutbox_menu_savechanges').addClass('admin_content_modifications_active');
      }
    }
  }

  var eraseModification = function(type) {
    currentModifications.erase(type);
    // Remove active notifications CSS class
      if( type == 'info' ) {
        $('admin_layoutbox_menu_storeinfo').removeClass('admin_content_modifications_active');
      } else if( type == 'main' ) {
        $('admin_layoutbox_menu_savechanges').removeClass('admin_content_modifications_active');
      }
  }

  /* Attach javascript to existing elements */
  window.addEvent('load', function() {
    // Add info
    $$('li.admin_content_draggable').each(function(element) {
      var elClass = element.get('class');
      var matches = elClass.match(/admin_content_widget_([^ ]+)/i);
      if( !$type(matches) || !$type(matches[1])) return;
      var name = matches[1];
      var info = contentByName[name] || {};

      element.store('contentInfo', info);

      // Add info for tooltips
      element.store('tip:title', info.title || 'Missing widget: ' + matches[1]);
      element.store('tip:text', info.description || 'Missing widget: ' + matches[1]);
    });

    // Monitor form inputs for changes
    $$('#admin_layoutbox_menu_storeinfo input').addEvent('change', function(event) {
      if( event.target.get('tag') != 'input' ) return;
      pushModification('info');
    });

    // Add tooltips
    ContentTooltips = new Tips($$('ul#column_stock li.admin_content_draggable'), {
      
    });

    // Make sortable
    ContentSortables = new NestedSortables($$('ul.admin_content_sortable'), {
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
        element.addClass('admin_content_dragging');
        currentParent = element.getParent();
        currentNextSibling = element.getNext();
      },
      onComplete : function(element, clone) {
        element.removeClass('admin_content_dragging');
        if( !currentParent ) {
          //alert('missing parent error');
          return;
        }
        
        // If it's coming from stock and going into stock, destroy and insert back into original location
        if( currentParent.hasClass('admin_content_stock_sortable') && element.getParent().hasClass('admin_content_stock_sortable') ) {
          if( currentNextSibling ) {
            element.inject(currentNextSibling, 'before');
          } else {
            element.inject(currentParent);
          }
        }

        // If it's not coming from stock, and going into stock, just destroy it
        else if( element.getParent().hasClass('admin_content_stock_sortable') ) {
          element.destroy();

          // Signal modification
          pushModification('main');
        }

        // If it's coming from stock, and not going into stock, put back into stock, clone, and insert
        else if( currentParent.hasClass('admin_content_stock_sortable') && !element.getParent().hasClass('admin_content_stock_sortable') ) {
          var elClone = element.clone();

          // Make it buildable, add info, and give it a temp id
          elClone.inject(element, 'after');
          elClone.addClass('admin_content_buildable');
          elClone.addClass('admin_content_cell');
          elClone.removeClass('admin_content_stock_draggable');
          elClone.getElement('span').setStyle('display', '');
          // @todo
          elClone.set('id', 'admin_content_new_' + (newContentIndex++));

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
        else if( !currentParent.hasClass('admin_content_stock_sortable') && !element.getParent().hasClass('admin_content_stock_sortable') ) {
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
    if( !element.hasClass('admin_content_buildable') ) {
      element = element.getParent('.admin_content_buildable');
    }
    element.destroy();

    // Signal modification
    pushModification('main');
  }

  /* Switch the active menu item */
  var switchStoreMenu = function(event, activator) {
    var element = activator.getParent('li');
    $$('.admin_layoutbox_menu_generic').each(function(otherElement) {
      var otherWrapper = otherElement.getElement('.admin_layoutbox_menu_wrapper_generic');
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
    $$('.admin_content_buildable').each(function(element) {
      var parent = element.getParent('.admin_content_buildable');

      var elData = {
        'element' : {},
        'parent' : {},
        'info' : {},
        'params' : {}
      };

      // Get element identity
      elData.element.id = element.get('id');
      if( elData.element.id.indexOf('admin_content_new_') === 0 ) {
        elData.tmp_identity = elData.element.id.replace('admin_content_new_', '');
      } else {
        elData.identity = elData.element.id.replace('admin_content_', '');
      }

      // Get element class
      elData.element.className = element.get('class');

      // Get element type and name
      if( element.hasClass('admin_content_cell') ) {
        var m = element.get('class').match(/admin_content_widget_([^ ]+)/i);
        if( $type(m) && $type(m[1]) ) {
          elData.type = 'widget';
          elData.name = m[1];
        }
      } else if( element.hasClass('admin_content_block') ) {
        var m = element.get('class').match(/admin_content_container_([^ ]+)/i);
        if( $type(m) && $type(m[1]) ) {
          elData.type = 'container';
          elData.name = m[1];
        }
      } else if( element.hasClass('admin_content_column') ) {
        var m = element.get('class').match(/admin_content_container_([^ ]+)/i);
        if( $type(m) && $type(m[1]) ) {
          elData.type = 'container';
          elData.name = m[1];
        }
      } else {
        
      }


      if( parent ) {
        // Get parent identity
        elData.parent.id = parent.get('id');
        if( elData.parent.id.indexOf('admin_content_new_') === 0 ) {
          elData.parent_tmp_identity = elData.parent.id.replace('admin_content_new_', '');
        } else {
          elData.parent_identity = elData.parent.id.replace('admin_content_', '');
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

    var url = '<?php echo $this->url(array('action' => 'update', 'controller' => 'layout', 'module' => 'sitestore'), 'admin_default', true)?>';
    var store_reload = '<?php echo Zend_Controller_Front::getInstance()->getRequest()->getParam('store_reload', 1);?>';
    var reload_count = '<?php echo round($this->totalStores / 150);?>';
    Smoothbox.open('<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" /></center>');
    var request = new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'store' : currentStore,
        'structure' : '(' + JSON.encode(data) + ')',
        'admin_sitestore_layout' : currentLayout
      },
      //responseTree, responseElements, responseHTML, responseJavaScript
      onComplete : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $H(responseHTML.newIds).each(function(data, index) {
          var newContentEl = $('admin_content_new_' + index);
          if( !newContentEl ) throw "missing new content el";
          newContentEl.set('id', 'admin_content_' + data.identity);
          newContentEl.store('contentParams', data);
        });
        eraseModification('main');
        if(store_reload > reload_count) {
          Smoothbox.close();
          alert('<?php echo $this->string()->escapeJavascript($this->translate("Your changes to this store have been saved.")) ?>');
          window.location.reload(true);
        }
        
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
    if( element.get('id').indexOf('admin_content_new_') !== 0 && element.get('id').indexOf('admin_content_') === 0 ) {
      content_id = element.get('id').replace('admin_content_', '');
    }

    var url = '<?php echo $this->url(array('action' => 'widget', 'controller' => 'layout', 'module' => 'sitestore'), 'admin_default', true)?>';
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
    var url = '<?php echo $this->url(array('action' => 'save', 'controller' => 'layout', 'module' => 'sitestore'), 'admin_default', true)?>';
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
      var el = document.getElement('.admin_content_container_'+currentType);
      if( !el ) {
        positiveAreas.push(currentType);
      }
    });
    
    // Check to see if any columns containing widgets are going to be destroyed
    var contentLossCount = 0;
    negativeAreas.each(function(currentType) {
      var el = document.getElement('.admin_content_container_'+currentType);
      if( el && el.getChildren().length > 0 ) {
        contentLossCount++;
      }
    });

    // Notify user of potential data loss
    if( contentLossCount > 0 ) {
      <?php $replace = $this->translate("Changing to this layout will cause %s area(s) containing widgets to be destroyed. Are you sure you want to continue?", "' + contentLossCount + '") ?>
      <?php // if( !confirm('<?php echo $this->string()->escapeJavascript($replace) ?\>') ) {?>
        if( !confirm('<?php echo $replace ?>') ) {
        return false;
      }
    }

    // Destroy areas
    negativeAreas.each(function(currentType) {
      var el = document.getElement('.admin_content_container_'+currentType);
      if( el ) {
        el.destroy();
      }
    });

    // Create areas
    var levelOneReference = document.getElement('.admin_layoutbox table.admin_content_container_main');
    
    // Create level one areas
    $H({'top' : 'before', 'bottom' : 'after'}).each(function(placement, currentType) {
      if( !positiveAreas.contains(currentType) ) return;

      var newTable = new Element('table', {
        'id' : 'admin_content_new_' + (newContentIndex++),
        'class' : 'admin_content_block admin_content_buildable admin_content_container_' + currentType
      }).inject(levelOneReference, placement);

      var newTbody = new Element('tbody', {
      }).inject(newTable);

      var newTr = new Element('tr', {
      }).inject(newTbody);

      // L2
      var newTdContainer = new Element('td', {
        'id' : 'admin_content_new_' + (newContentIndex++),
        'class' : 'admin_content_column admin_content_buildable admin_content_container_middle'
      }).inject(newTr);

      // L3
      var newUlContainer = new Element('ul', {
        'class' : 'admin_content_sortable'
      }).inject(newTdContainer);

      ContentSortables.addLists(newUlContainer);
    });

    // Create level two areas
    var mainParent = document.getElement('.admin_layoutbox .admin_content_container_main tr');
    $H({'left' : 'top', 'right' : 'bottom'}).each(function(placement, currentType) {
      if( !positiveAreas.contains(currentType) ) return;
      
      // L2
      var newTdContainer = new Element('td', {
        'id' : 'admin_content_new_' + (newContentIndex++),
        'class' : 'admin_content_column admin_content_buildable admin_content_container_' + currentType
      }).inject(mainParent, placement);

      // L3
      var newUlContainer = new Element('ul', {
        'class' : 'admin_content_sortable'
      }).inject(newTdContainer);

      ContentSortables.addLists(newUlContainer);
    });

    // Signal modification
    pushModification('main');
  }

  /* Tab container and other special block handling */
  var expandSpecialBlock = function(element)
  {
    if( element.hasClass('admin_content_widget_core.container-tabs') ) {
      element.addClass('admin_layoutbox_widget_tabbed_wrapper');
      // Empty
      element.empty();
      // Title/edit
      new Element('span', {
        'class' : 'admin_layoutbox_widget_tabbed_top',
        'html' : '<?php echo $this->string()->escapeJavascript($this->translate("Tab Container")) ?><span class="open"> | <a href=\'javascript:void(0);\' onclick="openWidgetParamEdit(\'core.container-tabs\', $(this).getParent(\'li.admin_content_cell\')); (new Event(event).stop()); return false;"><?php echo $this->string()->escapeJavascript($this->translate("edit")) ?></a></span> <span class="remove"><a href="javascript:void(0)" onclick="removeWidget($(this));">x</a></span>'
      }).inject(element);
      // Desc
      new Element('span', {
        'class' : 'admin_layoutbox_widget_tabbed_overtext',
        'html' : contentByName["core.container-tabs"].childAreaDescription
      }).inject(element);
      // Edit area
      var tmpDivContainer = new Element('div', {
        'class' : 'admin_layoutbox_widget_tabbed'
      }).inject(element);
      var list = new Element('ul', {
        'class' : 'sortablesForceInclude admin_content_sortable admin_layoutbox_widget_tabbed_contents'
      }).inject(tmpDivContainer);
      
      ContentSortables.addLists(list);
    }
  }

  /* Checks for autoEdit */
  var checkForAutoEdit = function(element) {
    var m = element.get('class').match(/admin_content_widget_([^ ]+)/i);
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

    var headerContainer = $$('div.admin_layoutbox_header');
    var footerContainer = $$('div.admin_layoutbox_footer');

    // Hide
    if( currentLayout == 'default' || currentLayout == '' ) {
      headerContainer.addClass('admin_layoutbox_header_hidden');
      footerContainer.addClass('admin_layoutbox_footer_hidden');
      headerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate('show on this store')) ?>)');
      footerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate('show on this store')) ?>)');
      currentLayout = 'default-simple';
    }

    // Show
    else
    {
      headerContainer.removeClass('admin_layoutbox_header_hidden');
      footerContainer.removeClass('admin_layoutbox_footer_hidden');
      headerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate('hide on this store')) ?>)');
      footerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate('hide on this store')) ?>)');
      currentLayout = 'default';
    }
  }



</script>

<h2><?php echo $this->translate('Store Profile Layout Editor'); ?></h2>
<p>
  <?php echo $this->translate('Store Profile Layout DESCRIPTION'); ?>
</p>

<div id='admin_cms_wrapper'>

  <div class="admin_layoutbox_menu">
    <ul>
      <li class="admin_layoutbox_menu_generic" id="admin_layoutbox_menu_openstore">
        <div class="admin_layoutbox_menu_wrapper_generic admin_layoutbox_menu_stores_wrapper" id="admin_layoutbox_menu_stores_wrapper">
        </div>
        <a href="javascript:void(0);">         
           <?php echo $this->storeObject->displayname ?>
        </a>
      </li>
      <li id="admin_layoutbox_menu_savechanges">
        <a href="javascript:void(0);" onClick="saveChanges()">
          <?php echo $this->translate("Save Changes") ?>
        </a>
      </li>

    </ul>
  </div>

  <div class="admin_layoutbox_wrapper">

    <div class="admin_layoutbox_sub_menu">
      <h3>
        <?php echo $this->translate('Store Block Placement') ?>
      </h3>
      <ul>
        
        <?php if( substr($this->storeObject->name, 0, 6) !== 'header' && substr($this->storeObject->name, 0, 6) !== 'footer'): ?>
        <li class="admin_layoutbox_menu_generic" id="admin_layoutbox_menu_storeinfo">
          <div class="admin_layoutbox_menu_wrapper_generic admin_layoutbox_menu_editinfo_wrapper" id="admin_layoutbox_menu_editinfo_wrapper">
            <div class="admin_layoutbox_menu_editinfo">
              <span>
                <?php echo $this->storeForm->render($this) ?>
              </span>
              <div class="admin_layoutbox_menu_editinfo_submit">
                <button onclick="saveCurrentStoreInfo($('admin_content_storeinfo')); return false;"><?php echo $this->translate("Save Changes") ?></button> or <a href="javascript:void(0);" onClick="switchStoreMenu(new Event(event), $(this));"><?php echo $this->translate("cancel") ?></a>
              </div>
            </div>
          </div>
          <a href="javascript:void(0);" onClick="switchStoreMenu(new Event(event), $(this));"><?php echo $this->translate("Edit Store Info") ?></a>
        </li>
        <li class="admin_layoutbox_menu_generic" id="admin_layoutbox_menu_editcolumns">
          <div class="admin_layoutbox_menu_wrapper_generic admin_layoutbox_menu_columnchoices_wrapper" id="admin_layoutbox_menu_columnchoices_wrapper">
            <div class="admin_layoutbox_menu_columnchoices">
              <div class="admin_layoutbox_menu_columnchoices_instructions">
                <?php echo $this->translate("Select a new column layout for this store.") ?>
              </div>
              <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols1_3.png', '3 columns', array('onClick' => "changeCurrentLayoutType('left,middle,right');switchStoreMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols1_2left.png', '2 columns - Left', array('onClick' => "changeCurrentLayoutType('left,middle');switchStoreMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols1_2right.png', '2 columns - Right', array('onClick' => "changeCurrentLayoutType('middle,right');switchStoreMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols1_1.png', '1 columns', array('onClick' => "changeCurrentLayoutType('middle');switchStoreMenu(new Event(event), $(this));")) ?></li>
              </ul>
              <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols2_3.png', '3 columns', array('onClick' => "changeCurrentLayoutType('top,left,middle,right');switchStoreMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols2_2left.png', '2 columns - Left', array('onClick' => "changeCurrentLayoutType('top,left,middle');switchStoreMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols2_2right.png', '2 columns - Right', array('onClick' => "changeCurrentLayoutType('top,middle,right');switchStoreMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols2_1.png', '1 columns', array('onClick' => "changeCurrentLayoutType('top,middle');switchStoreMenu(new Event(event), $(this));")) ?></li>
              </ul>
              <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols3_3.png', '3 columns', array('onClick' => "changeCurrentLayoutType('left,middle,right,bottom');switchStoreMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols3_2left.png', '2 columns - Left', array('onClick' => "changeCurrentLayoutType('left,middle,bottom');switchStoreMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols3_2right.png', '2 columns - Right', array('onClick' => "changeCurrentLayoutType('middle,right,bottom');switchStoreMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols3_1.png', '1 columns', array('onClick' => "changeCurrentLayoutType('middle,bottom');switchStoreMenu(new Event(event), $(this));")) ?></li>
              </ul>
              <div class="admin_layoutbox_menu_columnchoices_cancel">
                Or, <a href="javascript:void(0);" onClick="switchStoreMenu(new Event(event), $(this));"><?php echo $this->translate("cancel") ?></a> <?php echo $this->translate("and keep your current layout.") ?>
              </div>
            </div>
          </div>
          <a href="javascript:void(0);" onClick="switchStoreMenu(new Event(event), $(this));"><?php echo $this->translate("Edit Columns") ?></a>
        </li>
        <?php endif ;?>
      </ul>
    </div>

    <?php // Normal editing ?>
    <?php if( substr($this->storeObject->name, 0, 6) !== 'header' && substr($this->storeObject->name, 0, 6) !== 'footer' && ($showHideHeaderFooter == 'default' || $showHideHeaderFooter == 'default-simple' || $showHideHeaderFooter == '')): ?>

      <div class='admin_layoutbox'>
        <div class='admin_layoutbox_header<?php echo ( empty($showHideHeaderFooter) || $showHideHeaderFooter == 'default' ? '' : ' admin_layoutbox_header_hidden' ) ?>'>
          <span>
            <?php echo $this->translate('Global Header') ?>
            <span>
              <a href="javascript:void(0);" onclick="toggleGlobalLayout($(this).getParent('div.admin_layoutbox_header'));">
               
                <?php echo ( empty($showHideHeaderFooter) || $showHideHeaderFooter == 'default' ? "({$this->translate('hide on this store')})" : "({$this->translate('show on this store')})" ) ?>
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
            <table id="admin_content_<?php echo $structOne['identity'] ?>" class="admin_content_block admin_content_buildable admin_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <script type="text/javascript">
                    window.addEvent('domready', function() {
                      $("admin_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                    });
                  </script>
                  <?php
                    // LEVEL 2 - START (LEFT, MIDDLE, RIGHT)
                    foreach( (array) @$structOne['elements'] as $structTwo ):
                      $structTwoNE = $structTwo;
                      unset($structTwoNE['elements']);
                  ?>
                    <td id="admin_content_<?php echo $structTwo['identity'] ?>" class="admin_content_column admin_content_buildable admin_content_<?php echo $structTwo['type'] . '_' . $structTwo['name'] ?>">
                      <script type="text/javascript">
                        window.addEvent('domready', function() {
                          $("admin_content_<?php echo $structTwo['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structTwoNE) ?>);
                        });
                      </script>
                      <ul class="admin_content_sortable">
                        <?php
                          // LEVEL 3 - START (WIDGETS)
                          foreach( (array) $structTwo['elements'] as $structThree ):
                            $structThreeNE = $structThree;
                            $structThreeInfo = @$this->contentByName[$structThree['name']];
                            unset($structThreeNE['elements']);
                        ?>
                          <script type="text/javascript">
                            window.addEvent('domready', function() {
                              $("admin_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                            });
                          </script>
                          <?php if( empty($structThreeInfo) ): // Missing widget ?>
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="disabled admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                              <?php echo $this->translate('Missing widget: %s', $structThree['name']) ?>
                              <span class="open"></span>
                              <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                            </li>
                          <?php elseif( empty($structThreeInfo['canHaveChildren']) ): ?>
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                              <?php echo $this->translate($this->contentByName[$structThree['name']]['title']) ?>
                              
                              <?php if($structThree['name'] != 'core.ad-campaign' && $structThree['name'] != 'core.html-block') :?>
                                <span class="open">
                                  |
                                  <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">
                                    <?php echo $this->translate('edit') ?>
                                    
                                  </a>
                                </span>
                              <?php else:?>                              
                              <script type="text/javascript">
                                 hideWidgetIds.push("admin_content_<?php echo $structThree['identity'] ?>");
                              </script>
                              <?php endif;?>
                              <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                            </li>
                          <?php else: ?>
                            <!-- tabbed widgets -->
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_layoutbox_widget_tabbed_wrapper admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?>">
                              <span class="admin_layoutbox_widget_tabbed_top">
                                <?php echo $this->translate('Tab Container') ?>
                                
                                <span class="open">
                                  <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">
                                    <?php echo $this->translate('edit') ?>
                                  </a>
                                </span>
                                <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                              </span>
                              <span class="admin_layoutbox_widget_tabbed_overtext">
                                <?php echo $this->translate($structThreeInfo['childAreaDescription']) ?>
                              </span>
                              <div class="admin_layoutbox_widget_tabbed">
                                <ul class="sortablesForceInclude admin_content_sortable admin_layoutbox_widget_tabbed_contents">
                                  <?php
                                    // LEVEL 4 - START (WIDGETS)
                                    foreach( (array) $structThree['elements'] as $structFour ):
                                      $structFourNE = $structFour;
                                      $structFourInfo = @$this->contentByName[$structFour['name']];
                                      unset($structFourNE['elements']);
                                  ?>
                                    <script type="text/javascript">
                                      window.addEvent('domready', function() {
                                        $("admin_content_<?php echo $structFour['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structFourNE) ?>);
                                      });
                                    </script>
                                    <?php if( empty($structFourInfo) ): ?>
                                      <li id="admin_content_<?php echo $structFour['identity'] ?>" class="disabled admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                        <?php echo $this->translate('Missing widget: %s', $structFour['name']) ?>
                                        <span></span>
                                      </li>
                                    <?php else: ?>
                                      <li id="admin_content_<?php echo $structFour['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                        <?php echo $this->translate($this->contentByName[$structFour['name']]['title']) ?>
                                        <?php if($structFour['name'] != 'core.ad-campaign' && $structFour['name'] != 'core.html-block') :?>
                                        <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structFour['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate('edit') ?></a></span>
                                             
                                        <?php else: ?>
                                        <script type="text/javascript">
                                          hideWidgetIds.push("admin_content_<?php echo $structFour['identity'] ?>");
                                        </script>     
                                        
                                        <?php endif;?>
                                        <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
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

        <div class='admin_layoutbox_footer<?php echo ( empty($showHideHeaderFooter) || $showHideHeaderFooter == 'default' ? '' : ' admin_layoutbox_footer_hidden' ) ?>'>
          <span>
            <?php echo $this->translate('Global Footer') ?>
            <span>
              <a href="javascript:void(0);" onclick="toggleGlobalLayout($(this).getParent('div.admin_layoutbox_footer'));">
                <?php echo ( empty($this->storeObject->layout) || $this->storeObject->layout == 'default' ? "({$this->translate('hide on this store')})" : "({$this->translate('show on this store')})" ) ?>
              </a>
            </span>
          </span>
        </div>
      </div>

    <?php // Header/Footer editing ?>
    <?php elseif( (substr($this->storeObject->name, 0, 6) == 'header' || substr($this->storeObject->name, 0, 6) == 'footer') && ($showHideHeaderFooter == 'default' || $showHideHeaderFooter == 'default-simple' || $showHideHeaderFooter == '')): ?>

      <div class='admin_layoutbox'>
        <?php if( substr($this->storeObject->name, 0, 6) == 'footer' ): ?>
          <div class='admin_layoutbox_header'>
            <span>Global Header</span>
          </div>
        <?php else: ?>
          <?php
            // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
            <table id="admin_content_<?php echo $structOne['identity'] ?>" class="admin_content_block admin_content_block_headerfooter admin_content_buildable admin_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <td class="admin_content_column_headerfooter">
                    <span class="admin_layoutbox_note">
                      Drop things here to add them to the global header.
                    </span>
                    <script type="text/javascript">
                      window.addEvent('domready', function() {
                        $("admin_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                      });
                    </script>
                    <ul class="admin_content_sortable">
                      <?php
                        // LEVEL 3 - START (WIDGETS)
                        foreach( (array) $structOne['elements'] as $structThree ):
                          $structThreeNE = $structThree;
                          $structThreeInfo = $this->contentByName[$structThree['name']];
                          unset($structThreeNE['elements']);
                      ?>
                        <script type="text/javascript">
                          window.addEvent('domready', function() {
                            $("admin_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                          });
                        </script>
                        <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                          <?php echo $this->translate($this->contentByName[$structThree['name']]['title']) ?>
                          <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">edit</a></span>
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

        <div class='admin_layoutbox_center_placeholder'>
          <span><?php echo $this->translate("Main Content Area") ?></span>
        </div>

        <?php if( substr($this->storeObject->name, 0, 6) == 'header' ): ?>
        <div class='admin_layoutbox_footer'>
          <span><?php echo $this->translate("Global Footer") ?></span>
        </div>
        <?php else: ?>
          <?php
            // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
            <table id="admin_content_<?php echo $structOne['identity'] ?>" class="admin_content_block admin_content_block_headerfooter admin_content_buildable admin_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <td class="admin_content_column_headerfooter">
                    <span class="admin_layoutbox_note">
                      <?php echo $this->translate("Drop things here to add them to the global footer.") ?>
                    </span>
                    <script type="text/javascript">
                      window.addEvent('domready', function() {
                        $("admin_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                      });
                    </script>
                    <ul class="admin_content_sortable">
                      <?php
                        // LEVEL 3 - START (WIDGETS)
                        foreach( (array) $structOne['elements'] as $structThree ):
                          $structThreeNE = $structThree;
                          $structThreeInfo = $this->contentByName[$structThree['name']];
                          unset($structThreeNE['elements']);
                      ?>
                        <script type="text/javascript">
                          window.addEvent('domready', function() {
                            $("admin_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                          });
                        </script>
                        <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                          <?php echo $this->translate((string) $this->contentByName[$structThree['name']]['title']) ?>
                          <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate("edit") ?></a></span>
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


    <?php // Non-standard layout editing ?>
    <?php elseif( substr($this->storeObject->name, 0, 6) != 'header' && substr($this->storeObject->name, 0, 6) != 'footer' && $this->storeObject->layout != 'default' && $this->storeObject->layout != 'default-simple' && $this->storeObject->layout != ''): ?>

      <div class='admin_layoutbox'>

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
            <table id="admin_content_<?php echo $structOne['identity'] ?>" class="admin_content_block admin_content_buildable admin_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <script type="text/javascript">
                    window.addEvent('domready', function() {
                      $("admin_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                    });
                  </script>
                  <?php
                    // LEVEL 2 - START (LEFT, MIDDLE, RIGHT)
                    foreach( (array) @$structOne['elements'] as $structTwo ):
                      $structTwoNE = $structTwo;
                      unset($structTwoNE['elements']);
                  ?>
                    <td id="admin_content_<?php echo $structTwo['identity'] ?>" class="admin_content_column admin_content_buildable admin_content_<?php echo $structTwo['type'] . '_' . $structTwo['name'] ?>">
                      <script type="text/javascript">
                        window.addEvent('domready', function() {
                          $("admin_content_<?php echo $structTwo['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structTwoNE) ?>);
                        });
                      </script>
                      <ul class="admin_content_sortable">
                        <?php
                          // LEVEL 3 - START (WIDGETS)
                          foreach( (array) $structTwo['elements'] as $structThree ):
                            $structThreeNE = $structThree;
                            $structThreeInfo = @$this->contentByName[$structThree['name']];
                            unset($structThreeNE['elements']);
                        ?>
                          <script type="text/javascript">
                            window.addEvent('domready', function() {
                              $("admin_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                            });
                          </script>
                          <?php if( empty($structThreeInfo) ): // Missing widget ?>
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="disabled admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                              <?php echo $this->translate('Missing widget: %s', $structThree['name']) ?>
                              <span class="open"></span>
                              <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                            </li>
                          <?php elseif( empty($structThreeInfo['canHaveChildren']) ): ?>
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                              <?php echo $this->translate($this->contentByName[$structThree['name']]['title']) ?>
                              <span class="open">
                                |
                                <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">
                                  <?php echo $this->translate('edit') ?>
                                </a>
                              </span>
                              <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                            </li>
                          <?php else: ?>
                            <!-- tabbed widgets -->
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_layoutbox_widget_tabbed_wrapper admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?>">
                              <span class="admin_layoutbox_widget_tabbed_top">
                                <?php echo $this->translate('Tab Container') ?>
                                <span class="open">
                                  <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">
                                    <?php echo $this->translate('edit') ?>
                                  </a>
                                </span>
                                <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                              </span>
                              <span class="admin_layoutbox_widget_tabbed_overtext">
                                <?php echo $this->translate($structThreeInfo['childAreaDescription']) ?>
                              </span>
                              <div class="admin_layoutbox_widget_tabbed">
                                <ul class="sortablesForceInclude admin_content_sortable admin_layoutbox_widget_tabbed_contents">
                                  <?php
                                    // LEVEL 4 - START (WIDGETS)
                                    foreach( (array) $structThree['elements'] as $structFour ):
                                      $structFourNE = $structFour;
                                      $structFourInfo = @$this->contentByName[$structFour['name']];
                                      unset($structFourNE['elements']);
                                  ?>
                                    <script type="text/javascript">
                                      window.addEvent('domready', function() {
                                        $("admin_content_<?php echo $structFour['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structFourNE) ?>);
                                      });
                                    </script>
                                    <?php if( empty($structFourInfo) ): ?>
                                      <li id="admin_content_<?php echo $structFour['identity'] ?>" class="disabled admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                        <?php echo $this->translate('Missing widget: %s', $structFour['name']) ?>
                                        <span></span>
                                      </li>
                                    <?php else: ?>
                                      <li id="admin_content_<?php echo $structFour['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                        <?php echo $this->translate($this->contentByName[$structFour['name']]['title']) ?>
                                        <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structFour['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">edit</a></span>
                                        <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
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

      </div>

    <?php endif; ?>

    <div class="admin_layoutbox_footnotes">
      <?php echo $this->translate("Note: Some blocks won't appear if you're not signed-in or if they don't belong on this store."); ?>
    </div>
  </div>


  <div class="admin_layoutbox_pool_wrapper">
    <h3><?php echo $this->translate("Available Blocks") ?></h3>
    <div class='admin_layoutbox_pool'>
      <div id='stock_div'></div>
      <ul id='column_stock'>
        <?php foreach( $this->contentAreas as $category => $categoryAreas ): ?>
          <li>
            <div class="admin_layoutbox_pool_category_wrapper" onclick="$(this); $(this).getElement('.admin_layoutbox_pool_category_show').toggle(); $(this).getElement('.admin_layoutbox_pool_category_hide').toggle(); this.getParent('li').getElement('ul').style.display = ( this.getParent('li').getElement('ul').style.display == 'none' ? '' : 'none' );">
              <div class="admin_layoutbox_pool_category">
                <div class="admin_layoutbox_pool_category_hide">
                  &nbsp;
                </div>
                <div class="admin_layoutbox_pool_category_show">
                  &nbsp;
                </div>
                <div class="admin_layoutbox_pool_category_label">
                  <?php echo $this->translate($category) ?>
                </div>
              </div>
            </div>
            <ul class='admin_content_sortable admin_content_stock_sortable'>
              <?php $storelayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layout.setting', 1); ?>
              <?php foreach( $categoryAreas as $info ):
                if($info['name']=='sitestore.widgetlinks-sitestore' && $storelayout)
                continue;
                $class = 'admin_content_widget_' . $info['name'];
                $class .= ' admin_content_draggable admin_content_stock_draggable';
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
                if( !empty($info['adminCssClass']) ) {
                  $class .= ' ' . $info['adminCssClass'];
                }

                ?>
                <?php //if( empty($info['canHaveChildren']) ): ?>
                  <li class="<?php echo $class ?> admin_sitestore_content_draggable" title="<?php echo $this->escape($info['description']) ?>"<?php if( $onmousedown ): ?> onmousedown="<?php echo $onmousedown ?>"<?php endif; ?>>
                      <?php if($category == 'Store Profile') :?>
                        <div>
                          <?php if(!empty($info['title'])):?>
                        	  <?php echo $this->translate($info['title'])?> 
                          <?php endif; ?>
                        </div>  
                        <div>  &nbsp; | &nbsp;  </div> 
                        <div id="backgroundimage_<?php echo $this->translate($info['name'])?>"></div>                 
                        <div id="hide_<?php echo $this->translate($info['name'])?>" <?php if(!in_array($info['name'], $this->hideWidgets)) :?>style="display:block;"<?php else:?> style="display:none;"<?php endif;?>  title='<?php echo $this->translate('If you have NOT placed this widget in the Store Profile Placement area and you "lock" it, then this widget will NOT be available to Store Owners for placing on the Store. If you have placed this widget in the Store Profile Placement area and you "lock" it, then Store Owners will NOT be able to remove it or drag-n-drop it.')?>'>
	                        <a href="javascript:void(0);" onclick="widgetshowhide('<?php echo $this->translate($info['name'])?>', 1);"><?php echo $this->translate('lock')?></a>
                       	</div> 
                       
                       	<div id="show_<?php echo $this->translate($info['name'])?>" <?php if(in_array($info['name'], $this->hideWidgets)) :?>style="display:block;"<?php else:?> style="display:none;"<?php endif;?> title="<?php echo $this->translate('Unlock this widget and make it available to Store Owners for arranging, adding on, or removing from their Store Profile.')?>">
	                        <a href="javascript:void(0);" onclick="widgetshowhide('<?php echo $this->translate($info['name'])?>', 0);"><?php echo $this->translate('unlock')?></a>
                       	</div> 

                      <?php else: ?>
                        <?php if(!empty($info['title'])):?>
	                        <?php echo $this->translate($info['title'])?>
                        <?php endif;?>
	                    <?php endif;?>
                    <span class="open"> &nbsp;| <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $info['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate("edit") ?></a></span>
                    <span class="remove"><a href='javascript:void(0);' onclick="removeWidget($(this));">x</a></span>
                  </li>
                <?php /* //else: ?>
                  <li class="admin_layoutbox_widget_tabbed_wrapper">
                    <span class="admin_layoutbox_widget_tabbed_top">
                      Tabbed Blocks <a href="#">(edit)</a>
                    </span>
                    <div class="admin_layoutbox_widget_tabbed">
                      <ul class="admin_layoutbox_widget_tabbed_contents">
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

</div>

<script type="text/javascript">

	var widgetshowhide = function(widgetname, option) {
			
		  if(option == 1) {
		  	var confirmation = confirm("<?php echo $this->string()->escapeJavascript($this->translate("You are about to Lock this widget. If you have placed this widget in the main Store area, then the Store Admins of the Stores where this widget is placed, will NOT be able to remove it or drag-and-drop to arrange it on their Stores. If you have not placed this widget in the main Store area, then this will be removed from all the Stores where it has been placed, and will not be available to Store Admins in the layout managing area.")); ?>");
		  	if(!confirmation) {
		  		return;
		  	}
		  	$('backgroundimage_' + widgetname).style.display = 'block';
		  	$('show_' + widgetname).style.display = 'none';	
		  	$('hide_' + widgetname).style.display = 'none';	
		  }
		   if(option == 0) {
		  	$('backgroundimage_' + widgetname).style.display = 'block';
		  	$('hide_' + widgetname).style.display = 'none';	
		  	$('show_' + widgetname).style.display = 'none';	
		  }
		  $('backgroundimage_' + widgetname).innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loading.gif" /></center></div>';
			var url = '<?php echo $this->url(array('action' => 'show-hide-widget', 'controller' => 'layout', 'module' => 'sitestore'), 'admin_default', true)?>';
    var request = new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'widgetname' : widgetname,
        'option' : option
      },
      onComplete : function(responseTree, responseElements, responseHTML, responseJavaScript) {    
      	$('backgroundimage_' + widgetname).style.display = 'none';
        if(option == 1){
        	confirmation
        	$('show_' + widgetname).style.display = 'block';	
        	$('hide_' + widgetname).style.display = 'none';        	
        }
        else{
        	$('show_' + widgetname).style.display = 'none';
          $('hide_' + widgetname).style.display = 'block';
        }
      }
    });
    request.send();
  }
</script>
<?php else :?>

		<div class="tip">
	  	<span><?php echo $this->translate('You have disabled Store Profile Layout editing by their owners from the "Edit Store Layout" field in Global Settings. If you enable it, then from here you will be able to choose which widgets should be available to users on their Store Profile, and which ones will they be able to arrange, add or remove.Currently, you can configure Store Profile Layout from the "Layout" > "Layout Editor" section by selecting "Store Profile" from the "Editing" dropdown.'); ?></span> 
	  </div>

<?php endif;?>

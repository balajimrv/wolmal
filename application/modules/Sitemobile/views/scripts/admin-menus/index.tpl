<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">

  var SortablesInstance;

  window.addEvent('domready', function() {
    $$('.item_label').addEvents({
      mouseover: showPreview,
      mouseout: showPreview
    });
  });

  var showPreview = function(event) {
    try {
      element = $(event.target);
      element = element.getParent('.admin_menus_item').getElement('.item_url');
      if( event.type == 'mouseover' ) {
        element.setStyle('display', 'block');
      } else if( event.type == 'mouseout' ) {
        element.setStyle('display', 'none');
      }
    } catch( e ) {
      //alert(e);
    }
  }


  window.addEvent('load', function() {
    SortablesInstance = new Sortables('menu_list', {
      clone: true,
      constrain: false,
      handle: '.item_label',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

  var reorder = function(e) {
    var menuitems = e.parentNode.childNodes;
    var ordering = {};
    var i = 1;
    for (var menuitem in menuitems)
    {
      var child_id = menuitems[menuitem].id;

      if ((child_id != undefined) && (child_id.substr(0, 5) == 'admin'))
      {
        ordering[child_id] = i;
        i++;
      }
    }
    ordering['menu'] = '<?php echo $this->selectedMenu->name; ?>';
    ordering['format'] = 'json';

    // Send request
    var url = '<?php echo $this->url(array('action' => 'order')) ?>';
    var request = new Request.JSON({
      'url' : url,
      'method' : 'POST',
      'data' : ordering,
      onSuccess : function(responseJSON) {
      }
    });

    request.send();
  }

  function ignoreDrag()
  {
    event.stopPropagation();
    return false;
  }

</script>
<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl'; ?>

<h3><?php echo $this->translate('Mobile / Tablet Menu Editor'); ?></h3>
<p>
  <?php echo $this->translate('Use this area to manage the various navigation menus that appear in the mobile / tablet view of your community. When you select the menu you wish to edit, a list of the menu items it contains will be shown. You can drag these items up and down to change their order. You can also add a separator to visually categorize the "Dashboard / Panel Navigation Menu" items.') ?>
</p>
<br>
<div class="admin_menus_filter">
  <form action="<?php echo $this->url() ?>" method="get">
    <b><?php echo $this->translate("Editing:") ?></b>
    <?php echo $this->formSelect('name', $this->selectedMenu->name, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->menuList) ?>
  </form>
</div>

<br />

<div class="admin_menus_options">
  <?php echo $this->htmlLink(array('reset' => false, 'action' => 'create', 'addType' => 'Item', 'name' => $this->selectedMenu->name), $this->translate('Add Item'), array('class' => 'buttonlink admin_menus_additem smoothbox')) ?>
<!--  Add separator link will be shown only for "Dashboard / Panel Navigatiion Menu"-->
  <?php if (in_array($this->selectedMenu->name, array('core_main' , 'sitemobileapp_settings'))): ?>
    <?php echo $this->htmlLink(array('reset' => false, 'action' => 'create', 'addType' => 'Separator', 'name' => $this->selectedMenu->name), $this->translate('Add Separator'), array('class' => 'buttonlink admin_menus_additem smoothbox')) ?>
  <?php endif; ?>
</div>

<br />

<ul class="admin_menus_items sm_admin_menus_items" id='menu_list'>
  <?php foreach ($this->menuItems as $menuItem): ?>
    <li class="admin_menus_item<?php if (isset($menuItem->enabled) && !$menuItem->enabled)
    echo ' disabled' ?>" id="admin_menus_item_<?php echo $menuItem->name ?>"  >

      <span class="item_wrapper <?php if (isset($menuItem->params['isseparator']) && $menuItem->params['isseparator']): ?> sm_admin_menu_sep<?php endif; ?>">
            <span class="item_options">
<!-- The addType varible is to identify either editing or deleting "Item" or "Separator".              -->
            <?php
                if (isset($menuItem->params['isseparator']) && $menuItem->params['isseparator'])
                  $addType = 'Separator';
                else
                  $addType = 'Item'
                  ?>
                <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit', 'addType' => $addType, 'name' => $menuItem->name), $this->translate('edit'), array('class' => 'smoothbox')) ?>
                <?php if ($menuItem->custom): ?>
            | <?php echo $this->htmlLink(array('reset' => false, 'action' => 'delete', 'addType' => $addType, 'name' => $menuItem->name), $this->translate('delete'), array('class' => 'smoothbox')) ?>
          <?php endif; ?>
            |
           <?php //echo ( $menuItem->enable_mobile ? $this->htmlImage('application/modules/Sitemobile/externals/images/admin/phone.png', '', array('title' => $this->translate('Enabled in Mobile'))) : $this->htmlImage('application/modules/Sitemobile/externals/images/admin/phone-disable.png', '', array('title' => $this->translate('Disabled in Mobile')))) ?>
             <!--|-->
           <?php //echo ( $menuItem->enable_tablet ? $this->htmlImage('application/modules/Sitemobile/externals/images/admin/tablet.png', '', array('title' => $this->translate('Enabled in Tablet'))) : $this->htmlImage('application/modules/Sitemobile/externals/images/admin/tablet-disable.png', '', array('title' => $this->translate('Disabled in Tablet')))) ?>


           <?php echo ( ($menuItem->enable_mobile) ? $this->htmlLink(array('reset' => false, 'action' => 'enable-mobile', 'enable_mobile' =>'0', 'name' => $menuItem->name), $this->htmlImage('application/modules/Sitemobile/externals/images/admin/phone.png', '', array('title' => $this->translate('Disable Menus for Mobile'))), array()) : $this->htmlLink(array('reset' => false, 'action' => 'enable-mobile', 'enable_mobile' =>'1', 'name' => $menuItem->name), $this->htmlImage('application/modules/Sitemobile/externals/images/admin/phone-disable.png', '', array('title' => $this->translate('Enable Menus for Mobile'))))) ?>
            |
           <?php echo ( ($menuItem->enable_tablet) ? $this->htmlLink(array('reset' => false, 'action' => 'enable-tablet', 'enable_tablet' =>'0', 'name' => $menuItem->name), $this->htmlImage('application/modules/Sitemobile/externals/images/admin/tablet.png', '', array('title' => $this->translate('Disable Menus for Tablet'))), array()) : $this->htmlLink(array('reset' => false, 'action' => 'enable-tablet', 'enable_tablet' =>'1', 'name' => $menuItem->name), $this->htmlImage('application/modules/Sitemobile/externals/images/admin/tablet-disable.png', '', array('title' => $this->translate('Enable Menus for Tablet'))))) ?>
           <?php if(Engine_Api::_()->hasModuleBootstrap('sitemobileapp')):?>
							| <?php echo ( (isset($menuItem->enable_mobile_app) && $menuItem->enable_mobile_app) ? $this->htmlLink(array('reset' => false, 'action' => 'enable-mobile-app', 'enable_mobile_app' =>'0', 'name' => $menuItem->name), $this->htmlImage('application/modules/Sitemobile/externals/images/admin/phone.png', '', array('title' => $this->translate('Disable Menus for Mobile Application'))), array()) : $this->htmlLink(array('reset' => false, 'action' => 'enable-mobile-app', 'enable_mobile_app' =>'1', 'name' => $menuItem->name), $this->htmlImage('application/modules/Sitemobile/externals/images/admin/phone-disable.png', '', array('title' => $this->translate('Enable Menus for Mobile Application'))))) ?>
							|
							<?php echo ( (isset($menuItem->enable_tablet_app) && $menuItem->enable_tablet_app) ? $this->htmlLink(array('reset' => false, 'action' => 'enable-tablet-app', 'enable_tablet_app' =>'0', 'name' => $menuItem->name), $this->htmlImage('application/modules/Sitemobile/externals/images/admin/tablet.png', '', array('title' => $this->translate('Disable Menus for Tablet Application'))), array()) : $this->htmlLink(array('reset' => false, 'action' => 'enable-tablet-app', 'enable_tablet_app' =>'1', 'name' => $menuItem->name), $this->htmlImage('application/modules/Sitemobile/externals/images/admin/tablet-disable.png', '', array('title' => $this->translate('Enable Menus for Tablet Application'))))) ?>
           <?php endif;?>
        </span>
        <span class="item_label">
          <?php echo $this->translate($menuItem->label) ?>
        </span>
        <span class="item_url">
          <?php
          $href = '';
          if (isset($menuItem->params['uri'])) {
            echo $this->htmlLink($menuItem->params['uri'], $menuItem->params['uri']);
          } else if (!empty($menuItem->plugin)) {
            echo '<a>(' . $this->translate('variable') . ')</a>';
          } else {
            echo $this->htmlLink($this->htmlLink()->url($menuItem->params), $this->htmlLink()->url($menuItem->params));
          }
          ?>
        </span>
      </span>
    </li>
  <?php endforeach; ?>
</ul>

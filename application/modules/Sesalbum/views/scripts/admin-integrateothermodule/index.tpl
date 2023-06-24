<?php ?>

<?php include APPLICATION_PATH .  '/application/modules/Sesalbum/views/scripts/dismiss_message.tpl';?>
<h2>
  <?php echo $this->translate("Advanced Photos & Albums Plugin") ?>
</h2>
<div class="sesbasic_nav_btns">
  <a href="<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'settings', 'action' => 'contact-us'),'admin_default',true); ?>" class="request-btn">Feature Request</a>
</div>
<?php if( count($this->navigation) ): ?>
  <div class='sesbasic-admin-navgation'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>


<h3 style="margin-bottom:6px;"><?php echo $this->translate("Integrate and Manage Other Plugins"); ?></h3>
<p>In this page, you can enable the integration of Lightbox of this plugin with photos of other plugins. Below you can add the integration with any plugin using “Add New Plugin” button.<br /><br />
This process is very easy, but still if you face any difficulty, then please contact our support team for Free integration of other plugins from here: <a href="http://www.socialenginesolutions.com/tickets" target="_blank">http://www.socialenginesolutions.com/tickets</a> .</p>
<br class="clear" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesalbum', 'controller' => 'integrateothermodule', 'action' => 'addmodule', 'type' => 'lightbox'), $this->translate("Add New Plugin"), array('class'=>'buttonlink sesbasic_icon_add'));
?>
<br /><br />

<?php if(count($this->paginator)): ?>
<form id='multidelete_form'>
  <table class='admin_table'>
    <thead>
      <tr>
        <th align="left">
          <?php echo $this->translate("Plugin Name"); ?>
        </th>
        <th align="left">
          <?php echo $this->translate("Item Type for Photo Parent"); ?>
        </th>
        <th align="left">
          <?php echo $this->translate("Item Type for Photo"); ?>
        </th>
        <th class="left">
          <?php echo $this->translate("Enabled"); ?>
        </th>
        <th align="left">
          <?php echo $this->translate("Options"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
      <?php 
      $module_name = $item->module_name; 
      $enabledModules = $this->enabledModules;
      ?>
      <?php if(in_array($module_name, $enabledModules )): ?>
      <tr>
        <td><?php if( !empty($item->module_name) ){ echo $item->module_name; }else { echo '-'; } ?></td>
        <td><?php if( !empty($item->content_type) ){ echo $item->content_type; }else { echo '-'; } ?></td>
        <td><?php if( !empty($item->content_type_photo) ){ echo $item->content_type_photo; }else { echo '-'; } ?></td>
        <td><?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesalbum', 'controller' => 'integrateothermodule', 'action' => 'enabled', 'integrateothermodule_id' => $item->integrateothermodule_id ), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title' => $this->translate('Disable'))), array())  : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesalbum', 'controller' => 'integrateothermodule', 'action' => 'enabled', 'integrateothermodule_id' => $item->integrateothermodule_id ), $this->htmlImage('application/modules/Sesbasic/externals/images/icons/error.png', '', array('title' => $this->translate('Enable')))) ) ?></td >
        <td>
          <?php if(empty($item->default)):?>
          <a href="<?php echo $this->url(array('action' => 'delete', 'integrateothermodule_id' => $item->integrateothermodule_id)) ?>" class="smoothbox"><?php echo $this->translate("Delete") ?>
          </a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endif; ?>
      <?php  endforeach; ?>
    </tbody>
  </table>
</form>
<br />
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
<div class="tip">
  <span>
    <?php echo $this->translate("No plugins integrated yet.") ?>
  </span>
</div>
<?php endif; ?>
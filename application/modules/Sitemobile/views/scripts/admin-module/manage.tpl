<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--ADD NAVIGATION-->
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/adminNav.tpl'; ?>

<h3>
  <?php echo $this->translate("Manage Modules") ?>
</h3>
<p>
  <?php echo $this->translate('Below, you can manage the modules to be displayed in mobiles and tablets.'); ?>
</p>
<br/>

<table class='admin_table' width="100%">
  <thead>
    <tr>
      <th><?php echo $this->translate("Module Name") ?></th>
      <th align="center"><?php echo $this->translate("Enabled for Mobile Site") ?></th>
      <th align="center"><?php echo $this->translate("Enabled for Tablet Site") ?></th>
      <?php if (Engine_Api::_()->hasModuleBootstrap('sitemobileapp')): ?>  
        <th align="center"><?php echo $this->translate("Enabled for Mobile Application") ?></th>
        <th align="center"><?php echo $this->translate("Enabled for Tablet Application") ?></th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->modulesList as $item): ?>
      <tr>
        <?php if ($item->name == 'sitereview' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewlistingtype')): ?>
          <td><?php echo ucfirst($item->title); ?></td> 
        <?php elseif ($item->name == 'sitereview' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewlistingtype')): ?>
          <td><?php echo $this->translate("Reviews & Ratings - Multiple Listing Types Plugin"); ?></td> 
        <?php else: ?>
          <td><?php echo ucfirst($item->title); ?></td>
        <?php endif; ?>

        <!--        If module enable then display disable and vice-versa.-->
        <td class="admin_table_centered">
          <?php echo ( ($item->enable_mobile && $item->integrated) ? $this->htmlLink(array('reset' => false, 'action' => 'enable-mobile', 'enable_mobile' => '0', 'name' => $item->name, 'integrated' => $item->integrated), $this->htmlImage('application/modules/Sitemobile/externals/images/enable.png', '', array('title' => $this->translate('Disable Module for Mobile'))), array()) : $this->htmlLink(array('reset' => false, 'action' => 'enable-mobile', 'enable_mobile' => '1', 'name' => $item->name, 'integrated' => $item->integrated), $this->htmlImage('application/modules/Sitemobile/externals/images/disable.png', '', array('title' => $this->translate('Enable Module for Mobile'))))) ?>

        </td>
        <td class="admin_table_centered">

          <?php echo($item->enable_tablet && $item->integrated) ? $this->htmlLink(array('reset' => false, 'action' => 'enable-tablet', 'enable_tablet' => '0', 'name' => $item->name, 'integrated' => $item->integrated), $this->htmlImage('application/modules/Sitemobile/externals/images/enable.png', '', array('title' => $this->translate('Disable Module for Tablet'))), array()) : $this->htmlLink(array('reset' => false, 'action' => 'enable-tablet', 'enable_tablet' => '1', 'name' => $item->name, 'integrated' => $item->integrated), $this->htmlImage('application/modules/Sitemobile/externals/images/disable.png', '', array('title' => $this->translate('Enable Module for Tablet')))) ?>
        </td>
        <?php if (Engine_Api::_()->hasModuleBootstrap('sitemobileapp')): ?>  
          <td class="admin_table_centered">


            <?php echo ( ($item->enable_mobile_app && $item->integrated) ? $this->htmlLink(array('reset' => false, 'action' => 'enable-mobile-app', 'enable_mobile_app' => '0', 'name' => $item->name, 'integrated' => $item->integrated), $this->htmlImage('application/modules/Sitemobile/externals/images/enable.png', '', array('title' => $this->translate('Disable Module for Mobile Application'))), array()) : $this->htmlLink(array('reset' => false, 'action' => 'enable-mobile-app', 'enable_mobile_app' => '1', 'name' => $item->name, 'integrated' => $item->integrated), $this->htmlImage('application/modules/Sitemobile/externals/images/disable.png', '', array('title' => $this->translate('Enable Module for Mobile Application'))))) ?>

          </td>

          <td class="admin_table_centered">

            <?php echo($item->enable_tablet_app && $item->integrated) ? $this->htmlLink(array('reset' => false, 'action' => 'enable-tablet-app', 'enable_tablet_app' => '0', 'name' => $item->name, 'integrated' => $item->integrated), $this->htmlImage('application/modules/Sitemobile/externals/images/enable.png', '', array('title' => $this->translate('Disable Module for Tablet Application'))), array()) : $this->htmlLink(array('reset' => false, 'action' => 'enable-tablet-app', 'enable_tablet_app' => '1', 'name' => $item->name, 'integrated' => $item->integrated), $this->htmlImage('application/modules/Sitemobile/externals/images/disable.png', '', array('title' => $this->translate('Enable Module for Tablet Application')))) ?>

          </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
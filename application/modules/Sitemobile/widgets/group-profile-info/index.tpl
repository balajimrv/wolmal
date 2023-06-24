<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if (!empty($this->group_info_collapsible)) : ?>
  <div class="sm_ui_item_profile_details" data-role="collapsible" <?php if(!empty($this->group_info_collapsible_default)):?> data-collapsed='false' <?php else:?> data-collapsed='true' <?php endif;?> id="collapsibles" data-mini="true">
	<h3><?php echo $this->translate('Group Details'); ?></h3>
<?php else: ?>
	<div class="sm_ui_item_profile_details">
<?php endif; ?>
  <table>
    <tbody>
      <?php if (!empty($this->group->description)): ?>
        <tr valign="top">
          <td class="label"><div><?php echo $this->translate('Details') ?></div></td>
          <td><?php echo nl2br($this->group->description) ?></td>
        </tr>
      <?php endif ?>
      <?php
      if (!empty($this->group->category_id) &&
              ($category = $this->group->getCategory()) instanceof Core_Model_Item_Abstract &&
              !empty($category->title)):
        ?>
        <tr valign="top">
          <td class="label"><div><?php echo $this->translate('Category') ?></div></td>
          <td>
        <?php echo $this->htmlLink(array('route' => 'group_general', 'action' => 'browse', 'category_id' => $this->group->category_id), $this->translate((string) $category->title)) ?>
          </td>
        </tr>
			<?php endif ?>
      <tr valign="top">
        <td class="label"><div><?php echo $this->translate('Owner') ?></div></td>
        <td><?php echo $this->group->getParent()->__toString() ?></td>
      </tr>
    </tbody>
  </table>
</div>
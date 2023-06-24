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
<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
  <div class="listing" >
    <ul id='profile_groups'>
      <?php foreach ($this->paginator as $group): ?>
        <li>
          <a href="<?php echo $group->getHref(); ?>" class="list-photo">
            <?php $url = $this->layout()->staticBaseUrl . 'application/modules/Group/externals/images/nophoto_group_thumb_profile.png';
              $temp_url = $group->getPhotoUrl('thumb.profile');
              if (!empty($temp_url)): $url = $group->getPhotoUrl('thumb.profile');
                endif; ?>
            <span style="background-image: url(<?php echo $url; ?>);"> </span>
            <h3 class="list-title"><?php echo $group->getTitle() ?> </h3>
          </a>
          <div class="related-info">	
            <p class="f_small">
              <span class="fleft"><?php echo $this->translate(array('%s member', '%s members', $group->member_count), $this->locale()->toNumber($group->member_count)) ?></span>
            </p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php else :?>
  <div class="sm-content-list">
    <ul id="profile_groups" data-role="listview" data-icon="arrow-r">
      <?php foreach ($this->paginator as $group): ?>
        <li>
          <a href="<?php echo $group->getHref(); ?>">
            <?php echo $this->itemPhoto($group, 'thumb.normal'); ?>
            <h3><?php echo $group->getTitle() ?></h3>
            <p><strong> <?php echo $this->translate(array('%s member', '%s members', $group->member_count), $this->locale()->toNumber($group->member_count)) ?></strong></p>
          </a> 
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<?php if ($this->paginator->count() > 1): ?>
  <?php
  echo $this->paginationAjaxControl(
          $this->paginator, $this->identity, 'profile_groups');
  ?>
<?php endif; ?>
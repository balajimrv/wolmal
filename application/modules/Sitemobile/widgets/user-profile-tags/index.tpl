<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <ul class="sm-ui-thumbs thumbs thumbs_nocaptions"  id="profile_tags">
    <?php foreach ($this->paginator as $tagmap): $resource = $tagmap->getResource(); ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $resource->getHref(); ?>">
          <span style="background-image: url(<?php echo $resource->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php if ($this->paginator->count() > 1): ?>
  <?php
  echo $this->paginationAjaxControl(
          $this->paginator, $this->identity, 'profile_tags');
  ?>
<?php endif; ?>
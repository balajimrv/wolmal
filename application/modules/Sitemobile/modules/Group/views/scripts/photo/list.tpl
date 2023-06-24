<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: list.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div data-role="navbar" role="navigation" data-iconpos="right">
  <ul>
    <li><a data-icon="arrow-r" href="<?php echo $this->group->getHref(); ?>"><?php echo $this->group->getTitle(); ?></a></li>
    <li><a data-icon="arrow-d" class="ui-btn-active ui-state-persist"><?php echo $this->translate('Photos'); ?></a></li>
  </ul>
</div>

<?php if ($this->paginator->count() > 0): ?>
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>
<ul class="thumbs thumbs_nocaptions">
  <?php foreach ($this->paginator as $photo): ?>
    <li>
      <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
        <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
<?php if ($this->paginator->count() > 0): ?>
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>

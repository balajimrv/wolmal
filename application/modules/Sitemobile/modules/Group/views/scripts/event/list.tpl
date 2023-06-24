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


<div data-role="navbar">
  <ul>
    <li><a href="<?php echo $this->group->getHref(); ?>"><?php echo $this->group->getTitle(); ?></a></li>
    <li><a class="ui-btn-active ui-state-persist"><?php echo $this->translate('Photos'); ?></a></li>
  </ul>
</div>

<div>
  <?php if ($this->canUpload): ?>
    <?php
    echo $this->htmlLink(array(
        'route' => 'group_extended',
        'controller' => 'photo',
        'action' => 'upload',
        'subject' => $this->subject()->getGuid(),
            ), $this->translate('Upload Photos'), array(
        'class' => 'buttonlink icon_group_photo_new'
    ))
    ?>
<?php endif; ?>
</div>

<?php if ($this->paginator->count() > 0): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
  <br />
<?php endif; ?>

<ul class='group_thumbs'>
<?php foreach ($this->paginator as $photo): ?>
    <li class="group_album_thumb_notext">
      <div class='group_album_thumb_wrapper'>
  <?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo, 'thumb.normal')) ?>
      </div>
    </li>
<?php endforeach; ?>
</ul>

<?php if ($this->paginator->count() > 0): ?>
  <?php echo $this->paginationControl($this->paginator); ?>
  <br />
<?php endif; ?>
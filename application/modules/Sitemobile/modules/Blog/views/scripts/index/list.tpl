<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: list.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<h2>
  <?php echo $this->translate('Recent Entries') ?>
</h2>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <ul data-role="listview" data-inset="false" data-icon="arrow-r">
    <?php foreach ($this->paginator as $blog): ?>
      <li>
        <a href="<?php echo $blog->getHref(); ?>">
          <h3><?php echo $blog->getTitle() ?></h3>
          <p>
            <?php echo $this->translate('Posted by'); ?>
            <strong><?php echo $blog->getOwner()->getTitle(); ?></strong>
          </p>
          <p>
            <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
          </p>
        </a> 
      </li>
    <?php endforeach; ?>
  </ul>
  <?php
  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
          //'params' => $this->formValues,
  ));
  ?>
<?php elseif ($this->category || $this->tag): ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('%1$s has not published a blog entry with that criteria.', $this->owner->getTitle()); ?>
    </span>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('%1$s has not written a blog entry yet.', $this->owner->getTitle()); ?>
    </span>
  </div>
<?php endif; ?>
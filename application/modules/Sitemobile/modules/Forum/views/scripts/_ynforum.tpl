<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @author     SocialEngineAddOns
 */
?>

<?php // On the client requirements,We have extended template part of Sub Category and Sub Forum from the Package name  "Ynforum"(Advanced Forum) in our "Mobile / Tablet Plugin".
?>
<li data-icon="arrow-r">
  <a href="<?php echo $this->forum->getHref(); ?>">
    <?php echo $this->itemPhoto($this->forum, 'thumb.icon') ?>
    <h3>
      <?php echo $this->translate($this->forum->getTitle()) ?>
    </h3>
    <p>
      <?php echo $this->locale()->toNumber($this->forum->approved_topic_count) ?> <?php echo $this->translate(array('topic', 'topics', $this->forum->approved_topic_count)) ?> -

      <?php echo $this->locale()->toNumber($this->forum->approved_post_count) ?> <?php echo $this->translate(array('post', 'posts', $this->forum->approved_post_count)) ?>
    </p>  
  </a>
</li>
<?php
$subForums = $this->forum->getSubForums();
?>
<?php if ($subForums) : ?>
  <?php foreach ($subForums as $subForum) : ?>
    <li class="ul-sub-list" data-icon="arrow-r">
      <a href="<?php echo $subForum->getHref(); ?>">
        <img alt="" src="application/modules/Ynforum/externals/images/advforum_unread_small.png">
        <h3>
          <?php echo $this->translate($subForum->getTitle()) ?>
        </h3>

      </a>

    </li>
  <?php endforeach; ?>
<?php endif; ?>
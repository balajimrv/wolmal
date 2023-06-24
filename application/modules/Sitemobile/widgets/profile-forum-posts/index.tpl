<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Char
 */
?>
<div class="sm-content-list" id="forum_topic_posts">
  <ul data-role="listview" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>
    <?php foreach ($this->paginator as $post):
      if (!isset($signature))
        $signature = $post->getSignature();
      $topic = $post->getParent();
      $forum = $topic->getParent();?>
      <li>
        <a href="<?php echo $topic->getHref(); ?>">          
          <h3><?php echo $topic->getTitle() ?></h3>
          <p><?php echo $this->translate("in "); ?>
            <b><?php echo $forum->getTitle() ?></b>
          </p>
          <p>
          <p><?php echo $this->locale()->toDateTime(strtotime($post->creation_date)); ?>
          </p>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php if ($this->paginator->count() > 1): ?>
  <?php
  echo $this->paginationAjaxControl(
          $this->paginator, $this->identity, 'forum_topic_posts');
  ?>
<?php endif; ?>

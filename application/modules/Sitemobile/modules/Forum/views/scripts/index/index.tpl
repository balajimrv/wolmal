<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9832 2012-11-28 01:31:18Z richard $
 * @author     John
 */
?>
<?php if(Engine_Api::_()->hasModuleBootstrap('ynforum')):?>
<?php include APPLICATION_PATH . '/application/modules/Sitemobile/modules/Forum/views/scripts/index/ynindex.tpl'; ?>
<?php else: ?>
<div class="sm-content-list">
  <ul data-role="listview" data-inset="false" data-icon="false" >
    <?php
    foreach ($this->categories as $category):
      if (empty($this->forums[$category->category_id])) {
        continue;
      }
      ?>
      <li data-role="list-divider" role="heading"  class="ui-bar-d">
        <b><?php echo $this->translate($category->getTitle()) ?></b>
      </li>
      <?php
      foreach ($this->forums[$category->category_id] as $forum):
        ?>
        <li <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>
          <a href="<?php echo $forum->getHref();?>">
            <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/images/forum.png') ?>
            <h3>
              <?php echo $this->translate($forum->getTitle()) ?>
            </h3>
            <p>
              <?php echo $forum->post_count; ?>
              <?php echo $this->translate(array('post', 'posts', $forum->post_count), $this->locale()->toNumber($forum->post_count)) ?>
              -
              <?php echo $forum->topic_count; ?>

              <?php echo $this->translate(array('topic', 'topics', $forum->topic_count), $this->locale()->toNumber($forum->topic_count)) ?>
            </p>
          </a>
        </li>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
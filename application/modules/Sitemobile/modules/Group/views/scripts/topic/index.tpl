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

<div data-role="navbar" role="navigation" data-iconpos="right">
  <ul>
    <li><a data-icon="arrow-r" href="<?php echo $this->group->getHref(); ?>"><?php echo $this->group->getTitle(); ?></a></li>
    <li><a data-icon="arrow-d" class="ui-btn-active ui-state-persist"><?php echo $this->translate('Discussions'); ?></a></li>
  </ul>
</div>

<div class="group_discussions_options">
  <?php
  echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
      'class' => 'buttonlink icon_back'
  ))
  ?>
  <?php
  if ($this->can_post) {
    echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'topic', 'action' => 'create', 'subject' => $this->group->getGuid()), $this->translate('Post New Topic'), array(
        'class' => 'buttonlink icon_group_post_new'
    ));
  }
  ?>
</div>

<?php if ($this->paginator->count() > 1): ?>
  <div>
    <br />
    <?php echo $this->paginationControl($this->paginator) ?>
    <br />
  </div>
<?php endif; ?>

<ul class="group_discussions">
  <?php
  foreach ($this->paginator as $topic):
    $lastpost = $topic->getLastPost();
    $lastposter = $topic->getLastPoster();
    ?>
    <li>
      <div class="group_discussions_replies">
        <span>
        <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
        </span>
  <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
      </div>
      <div class="group_discussions_lastreply">
          <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
        <div class="group_discussions_lastreply_info">
          <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by'); ?> <?php echo $lastposter->__toString() ?>
          <br />
  <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'group_discussions_lastreply_info_date')) ?>
        </div>
      </div>
      <div class="group_discussions_info">
        <h3<?php if ($topic->sticky): ?> class='group_discussions_sticky'<?php endif; ?>>
  <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
        </h3>
        <div class="group_discussions_blurb">
  <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
        </div>
      </div>
    </li>
<?php endforeach; ?>
</ul>

  <?php if ($this->paginator->count() > 1): ?>
  <div>
  <?php echo $this->paginationControl($this->paginator) ?>
  </div>
<?php endif; ?>
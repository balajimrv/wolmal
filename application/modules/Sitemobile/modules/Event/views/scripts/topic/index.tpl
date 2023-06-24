<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div data-role="navbar" role="navigation" data-iconpos="right">
  <ul>
    <li><a data-icon="arrow-r" href="<?php echo $this->event->getHref(); ?>"><?php echo $this->event->getTitle(); ?></a></li>
    <li><a data-icon="arrow-d" class="ui-btn-active ui-state-persist"><?php echo $this->translate('Discussions'); ?></a></li>
  </ul>
</div>

<div style="float: right; margin-right: 3px;">
  <a href="#options_<?php echo $this->event->getGuid() ?>"  data-rel="popup" data-icon="cog" data-role="button" data-iconpos="notext"></a>
  <div data-role="popup" id="options_<?php echo $this->event->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?>  style="margin-right: 20px;">
    <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right"><?php echo $this->translate('Close'); ?></a>

    <ul data-role="listview" data-inset="true" data-coners="false" >
      <li data-role="divider" data-theme="a">
        <?php echo $this->translate('Options'); ?>
      </li>
      <li data-shadow="false">
        <?php echo $this->htmlLink(array('route' => 'event_profile', 'id' => $this->event->getIdentity()), $this->translate('Back to Event'), array('class' => 'buttonlink icon_back')) ?>
      </li>
      <?php if ($this->can_post): ?>
        <li data-shadow="false">
          <?php
          echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'topic', 'action' => 'create', 'subject' => $this->event->getGuid()), $this->translate('Post New Topic'), array(
              'class' => 'buttonlink icon_event_post_new'
          ));
          ?>
        </li>
<?php endif; ?>
    </ul>
  </div>
</div>


<?php if ($this->paginator->count() > 1): ?>
  <div>
    <br />
  <?php echo $this->paginationControl($this->paginator) ?>
    <br />
  </div>
<?php endif; ?>

<ul class="event_discussions">
  <?php
  foreach ($this->paginator as $topic):
    $lastpost = $topic->getLastPost();
    $lastposter = $topic->getLastPoster();
    ?>
    <li>
      <div class="event_discussions_replies">
        <span>
        <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
        </span>
        <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
      </div>
      <div class="event_discussions_lastreply">
          <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
        <div class="event_discussions_lastreply_info">
          <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> by <?php echo $lastposter->__toString() ?>
          <br />
  <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'event_discussions_lastreply_info_date')) ?>
        </div>
      </div>
      <div class="event_discussions_info">
        <h3<?php if ($topic->sticky): ?> class='event_discussions_sticky'<?php endif; ?>>
          <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
        </h3>
        <div class="event_discussions_blurb">
  <?php echo $this->viewMore($topic->getDescription()) ?>
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

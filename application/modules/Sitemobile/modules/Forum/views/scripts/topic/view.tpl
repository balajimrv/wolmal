<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     John
 */
?>
<!--BREADCRUMB WORK-->
<?php
$breadcrumb = array(
    array("href" => $this->forum->getHref(array('route' => 'forum_general')), "title" => "Forums", "icon" => "arrow-r"),
    array("href" => $this->forum->getHref(array('route' => 'forum_forum', 'forum_id' => $this->forum->getIdentity())), "title" => $this->forum->getTitle(), "icon" => "arrow-r"),
    array("title" => $this->topic->getTitle(), "icon" => "arrow-d", "class" => "ui-btn-active ui-state-persist"));

echo $this->breadcrumb($breadcrumb);
?>
<?php if ($this->topic->closed): ?>
  <div class="tip">
    <?php echo $this->translate('This topic has been closed.'); ?>
  </div>
<?php endif; ?>
<div>
  <?php
  echo $this->paginationControl($this->paginator, null, null, array(
      'params' => array(
          'post_id' => null,
      ),
  ));
  ?>
</div>

<ul data-inset="true" data-role="listview" class="forum_topic_posts ui-listview ui-listview-inset ui-corner-all ui-shadow sm-ui-topic-view">
  <?php foreach ($this->paginator as $i => $post): ?>
    <?php $user = $this->user($post->user_id); ?>
    <?php $signature = $post->getSignature(); ?>
    <?php $isModeratorPost = $this->forum->isModerator($post->getOwner()) ?>
    <li id="forum_post_<?php echo $post->post_id ?>" class="ui-li-has-count forum_nth_<?php echo $i % 2 ?><?php if ($isModeratorPost): ?> forum_moderator_post<?php endif ?>">
      <div class="sm-ui-topic-view-head">
        <div class="author_photo">
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
        </div>
        <div class="thread_options" data-role="controlgroup" data-type="horizontal" data-mini="true">     
          <?php if ($this->canEdit): ?>        
            <?php
            echo $this->htmlLink(array('route' => 'forum_post', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'topic_id' => $this->topic->getIdentity()), $this->translate('Edit'), array(
                'data-role' => "button", 'data-icon' => "edit", 'data-iconpos' => 'notext'
            ))
            ?>
            <?php
            echo $this->htmlLink(array('route' => 'forum_post', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
                'data-role' => "button", 'data-icon' => "delete", 'data-iconpos' => 'notext'
            ))
            ?>
          <?php elseif ($post->user_id != 0 && $post->isOwner($this->viewer) && !$this->topic->closed): ?>
            <?php if ($this->canEdit_Post): ?>
              <?php
              echo $this->htmlLink(array('route' => 'forum_post', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'topic_id' => $this->topic->getIdentity()), $this->translate('Edit'), array(
                  'data-role' => "button", 'data-icon' => "edit", 'data-iconpos' => 'notext'
              ))
              ?>
            <?php endif; ?>
            <?php if ($this->canDelete_Post): ?>
              <?php
              echo $this->htmlLink(array('route' => 'forum_post', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
                  'data-role' => "button", 'data-icon' => "delete", 'data-iconpos' => 'notext'
              ))
              ?>
            <?php endif;
            ?>
          <?php endif; ?> 
          <?php if( $this->viewer()->getIdentity() && $post->user_id != $this->viewer()->getIdentity() ): ?>
              <?php echo $this->htmlLink(array(
                'route' => 'default',
                'module' => 'core',
                'controller' => 'report',
                'action' => 'create',
                'subject' => $post->getGuid(),
                'format' => 'smoothbox',
              ), $this->translate('Report'), array(
                'data-role' => "button", 'data-icon' => "flag", 'data-iconpos' => 'notext'
              )) ?>
            <?php endif; ?>
        </div>
        <div>
          <h3><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></h3>
          <p><?php if ($post->user_id != 0): ?>
              <?php if ($post->getOwner()): ?>
                <?php if ($isModeratorPost): ?>
                  <?php echo $this->translate('Moderator') ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>  </p>
          <p class="ui-li-desc">
            <?php if ($signature): ?>
              <?php echo $signature->post_count; ?>
              <?php echo $this->translate('posts'); ?> -
            <?php endif; ?>
            <?php echo $this->timestamp(strtotime($post->creation_date)) ?>
          </p>
        </div>
        <div class="thread_body t_l" >
           <?php
            $body = $post->body;
            $doNl2br = false;
            if( strip_tags($body) == $body ) {
              $body = nl2br($body);
            }
            if( !$this->decode_html && $this->decode_bbcode ) {
              $body = $this->BBCode($body, array('link_no_preparse' => true));
            }
            echo $body;
          ?>
          <?php if ($post->edit_id && !empty($post->modified_date)): ?>
            <br />
            <i>
              <?php echo $this->translate('This post was edited by %1$s at %2$s', $this->user($post->edit_id)->__toString(), $this->locale()->toDateTime(strtotime($post->modified_date))); ?>
            </i>
          <?php endif; ?>
        </div>
        <div class="t_l ui-li-desc">
          <?php if ($post->file_id): ?>
            <?php echo $this->itemPhoto($post,'thumb.profile'); ?>
          <?php endif; ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<div class="forum_topic_pages">
  <?php
  echo $this->paginationControl($this->paginator, null, null, array(
      'params' => array(
          'post_id' => null,
      ),
  ));
  ?>
</div>
<?php if ($this->canPost && $this->form): ?>
  <?php echo $this->form->render(); ?>
<?php endif; ?>
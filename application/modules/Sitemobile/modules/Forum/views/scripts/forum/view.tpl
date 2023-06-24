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
<?php
$breadcrumb = array(
     array("href"=>$this->forum->getHref(array('route'=>'forum_general')),"title"=>"Forums","icon"=>"arrow-r"),
    array("title" => $this->translate($this->forum->getTitle()), "icon" => "arrow-d", "class" => "ui-btn-active ui-state-persist"));

echo $this->breadcrumb($breadcrumb);
?>
<div class="sm-content-list forum-topic-page-listing">
  <?php if (count($this->paginator) > 0): ?>
    <ul class="forum_topics" data-role="listview" data-inset="false" data-icon="false">
      <?php
      foreach ($this->paginator as $i => $topic):
        $last_post = $topic->getLastCreatedPost();
        if ($last_post) {
          $last_user = $this->user($last_post->user_id);
        } else {
          $last_user = $this->user($topic->user_id);
        }
        ?>  
      <li <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>
          <a href="<?php echo $topic->getHref(); ?>" class="ui-link-inherit">
            <b class="forum-topic-listing-title"><?php echo $topic->getTitle(); ?></b>
            <p class ="ui-li-aside"><?php echo $this->translate(array('%1$s reply', '%1$s replies', $topic->post_count-1), $this->locale()->toNumber($topic->post_count-1)) ?></p>
            
            <?php if ($last_post): ?>
              <div class="forum-topic-listing-cont">
                <?php echo $this->itemPhoto($last_user, 'thumb.icon') ?>
                <p><?php echo $this->translate("Last post by ")?><b><?php echo $last_user->getTitle() ?></b></p>
                <p><?php echo $this->timestamp($topic->modified_date, array('class' => 'forum_topics_lastpost_date')) ?></p>        
              </div>
            <?php endif; ?>
          </a>
      </li>
      <?php endforeach; ?>
    </ul>
<?php elseif (preg_match("/search=/", $_SERVER['REQUEST_URI'])): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a forum with that criteria.'); ?>
    </span>
  </div> 
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no forums yet.') ?>
    </span>
  </div>
<?php endif; ?>
<div class="forum_pages">
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
  </div>
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
<?php foreach ($this->categories as $index => $category) { ?>
  <?php if ($category->level == 0) { ?>
    <?php
    $subCats = array();
    foreach ($this->categories as $cat) {
      if ($cat->parent_category_id == $category->getIdentity()) {
        $subCats[] = $cat;
      }
    }
    ?>

    <ul data-role="listview" data-inset="false" data-icon="false" >
      <li data-role="list-divider" role="heading"  class="ui-bar-d">				
        <div class="forum_boxarea_forums">   
          <b><?php echo $this->translate($category->getTitle()) ?></b> </div>				
      </li>     

      <?php foreach ($subCats as $subCat) : ?>
        <?php if ($subCat->forum_count > 0) { ?>
          <?php
          echo $this->partial('_yncategory.tpl', array(
              'category' => $subCat,
              'user' => $this->user,
              'lastTopics' => $this->lastTopics,
              'lastPosts' => $this->lastPosts,
              'forums' => $this->forums));
          ?>
        <?php }
        ?>

      <?php endforeach; ?>

      <?php if ($category->forum_count > 0) { ?>

        <li data-role="list-divider" role="heading"  class="ui-bar-d">
          <div class="forum_boxarea_forums"> 
            <?php echo $this->translate('Forums') ?> 
          </div>				
        </li>

        <?php
        if (array_key_exists($category->category_id, $this->forums)) {
          foreach ($this->forums[$category->category_id] as $forum) {
            $memberList = $forum->getMemberList();
            if ($memberList) {
              $members = $memberList->getAllChildren();
            }
            $check_user_view = true;
            if (count($members) > 0) {
              if (!$forum->isMember($this->viewer) && !$forum->isModerator($this->viewer)) {
                $check_user_view = false;
              }
            }

            if (($this->check_permission && $forum->authorization()->isAllowed($this->viewer, 'view') && $check_user_view) || !$this->check_permission) {
              if (!$forum->parent_forum_id) {
                echo $this->partial('_ynforum.tpl', array(
                    'forum' => $forum,
                    'user' => $this->user,
                    'lastTopics' => $this->lastTopics,
                    'lastPosts' => $this->lastPosts));
              }
            }
          }
        }
        ?>

      <?php } ?>
    </ul>
  <?php } ?>
<?php } ?>


<!--<div>
  <h3> <?php echo $this->translate('Forum Statistic') ?> </h3>
</div>-->
<br>
<br>
<ul data-role="listview" data-inset="false" data-icon="false" >
  <li data-role="list-divider" role="heading"  class="ui-bar-d">				
    <div class="forum_boxarea_forums"> <?php echo $this->translate('Statistic - Forums') ?> </div>				
  </li>
  <li class="forum_boxarea_body"> 
    <a href="#">
      <img alt="" src="application/modules/Ynforum/externals/images/advforum_statictic.png" />
      <p> 
        <span> <?php echo $this->translate('Topic(s):') ?> </span><?php echo $this->locale()->toNumber($this->topicCount) ?> -
        <span> <?php echo $this->translate('Post(s):') ?> </span><?php echo $this->locale()->toNumber($this->postCount) ?>
      </p>

    </a>
  </li>
</ul>


<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>

<div class="sm-content-list" id="profile_polls">
  <ul data-role="listview" <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?> >
    <?php foreach ($this->paginator as $item): ?>
      <li>
        <a href="<?php echo $item->getHref(); ?>">          
          <h3><?php echo $item->getTitle() ?></h3>
          <p class ="ui-li-aside">
            <b><?php echo $this->translate(array('%s vote', '%s votes', $item->vote_count), $this->locale()->toNumber($item->vote_count)) ?> </b>
          <p>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
          </p>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php if ($this->paginator->count() > 1): ?>
  <?php
  echo $this->paginationAjaxControl(
          $this->paginator, $this->identity, 'profile_polls');
  ?>
<?php endif; ?>


<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>

<?php if (0 == count($this->paginator)): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no polls yet.') ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'poll_general') . '">', '</a>') ?>
      <?php endif; ?>
    </span>
  </div>

<?php else: // $this->polls is NOT empty  ?>

  <div class="sm-content-list">
    <ul data-role="listview" data-inset="false" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>
      <?php foreach ($this->paginator as $poll): ?>
        <li id="poll-item-<?php echo $poll->poll_id ?>">
          <a href="<?php echo $poll->getHref(); ?>">
            <p class ="ui-li-aside">
              <b><?php echo $this->translate(array('%s vote', '%s votes', $poll->vote_count), $this->locale()->toNumber($poll->vote_count)) ?> </b>
            </p> 
            <?php echo $this->itemPhoto($poll->getOwner(), 'thumb.icon'); ?>
            <h3>
              <?php echo $poll->getTitle() ?>            
            </h3>            
            <p>
              <?php echo $this->translate('Posted By:');?>
              <b><?php echo $poll->getOwner()->getTitle(); ?></b>
            </p>
            <p>
              <?php echo $this->timestamp($poll->creation_date) ?>
            </p> 
            <?php if ($poll->closed): ?>
              <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Poll/externals/images/close.png' alt="<?php echo $this->translate('Closed') ?>" />
            <?php endif ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; // $this->polls is NOT empty  ?>

<?php
echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
    //'params' => $this->formValues,
));
?>
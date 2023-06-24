<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9987 2013-03-20 00:58:10Z john $
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
    <ul data-role="listview" data-inset="false">
      <?php foreach ($this->paginator as $poll): ?>
        <li id="poll-item-<?php echo $poll->poll_id ?>" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?> data-icon="ellipsis-vertical" <?php else : ?> data-icon="cog" <?php endif?> data-inset="true">
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
          <a href="#manage_<?php echo $poll->getGuid() ?>" data-rel="popup"></a>
          <div data-role="popup" id="manage_<?php echo $poll->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <h3><?php echo $poll->getTitle() ?></h3>
              <?php
              echo $this->htmlLink(array(
                  'route' => 'poll_specific',
                  'action' => 'edit',
                  'poll_id' => $poll->poll_id,
                  'reset' => true,
                      ), $this->translate('Edit Privacy'), array(
                  'class' => 'ui-btn-default ui-btn-action'
              ))
              ?>

              <?php if (!$poll->closed): ?>
                <?php
                echo $this->htmlLink(array(
                    'route' => 'poll_specific',
                    'action' => 'close',
                    'poll_id' => $poll->getIdentity(),
                    'closed' => 1,
                        ), $this->translate('Close Poll'), array(
                    'class' => 'ui-btn-default ui-btn-action'
                ))
                ?>
              <?php else: ?>
                <?php
                echo $this->htmlLink(array(
                    'route' => 'poll_specific',
                    'action' => 'close',
                    'poll_id' => $poll->getIdentity(),
                    'closed' => 0,
                        ), $this->translate('Open Poll'), array(
                    'class' => 'ui-btn-default ui-btn-action'
                ))
                ?>
              <?php endif; ?>

              <?php
              echo $this->htmlLink(array(
                  'route' => 'poll_specific',
                  'action' => 'delete',
                  'poll_id' => $poll->getIdentity(),
                  'format' => 'smoothbox'
                      ), $this->translate('Delete Poll'), array(
                  'class' => 'smoothbox ui-btn-default ui-btn-danger',
              ))
              ?>
              <a href="#" data-rel="back" class="ui-btn-default">
                <?php echo $this->translate('Cancel'); ?>
              </a>
            </div> 
          </div>        
        </li>
      <?php endforeach; ?>
    </ul>
    </div>
  <?php endif; // $this->polls is NOT empty ?>

  <?php
  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
       //'params' => $this->formValues,
  ));
  ?>


<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0); ?>

<div class="sitestore_view_select">
  <h3 class="sitestore_mystore_head"><?php echo $this->translate('Stores I Joined'); ?></h3>
</div>

<?php if ($this->paginator->getTotalItemCount() > 0) : ?>
  <div class="sm-content-list">
    <ul data-role="listview" data-inset="false" >
      <?php foreach ($this->paginator as $sitestore): ?>
        <li>
          <a href="<?php echo $sitestore->getHref(); ?>">
            <?php echo $this->itemPhoto($sitestore, 'thumb.icon') ?>
            <h3><?php echo $sitestore->getTitle(); ?></h3>				
            <p>
              <?php echo $this->timestamp(strtotime($sitestore->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
              <?php if ($postedBy): ?>
                <b><?php echo $sitestore->getOwner()->getTitle() ?></b>
              <?php endif; ?>
            </p>            
            <p>
              <?php if (!empty($sitestore->store_owner_id)) : ?>
                <?php if ($sitestore->store_owner_id == $sitestore->owner_id) : ?>
                  <?php echo $this->translate("STOREMEMBER_OWNER"); ?>
                <?php else: ?>
                  <?php echo $this->translate("STOREMEMBER_MEMBER"); ?>
                <?php endif; ?>
              <?php endif; ?>
            </p> 
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php if ($this->paginator->count() > 1): ?>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
        'query' => $this->formValues,
    ));
    ?>
  <?php endif; ?>
<?php else: ?>

  <div class="tip">
    <span>
  <?php echo $this->translate("There are no stores joined by you."); ?>
    </span>
  </div>
<?php endif; ?>

<div class="clr"></div>

